<?php
/**
 * Leap RAG — grounded knowledge base for the chat widget.
 *
 * Pipeline (all in-WordPress, uses the same Google AI key as the chat):
 *   1. Sources  : the curated site search index + any text files in /knowledge/.
 *   2. Chunk    : split into small passages with metadata (source, title).
 *   3. Embed    : Gemini text-embedding-004 vector per chunk (build step).
 *   4. Store    : wp-content/uploads/leap-kb/kb.json  (chunks + vectors).
 *   5. Retrieve : embed the question, cosine-similarity, return top matches.
 *
 * Rebuild from Settings → Leap AI → "Rebuild Knowledge Base" whenever the
 * site copy or the PDFs/text in /knowledge/ change.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// The built index lives in uploads — NOT the theme — so theme redeploys
// (git pull / reset / re-upload) can never wipe it. The old location is kept
// only so a one-time migration can move any existing file over (see below).
$leap_kb_upload = wp_upload_dir();
define( 'LEAP_KB_FILE', trailingslashit( $leap_kb_upload['basedir'] ) . 'leap-kb/kb.json' );
define( 'LEAP_KB_OLD_FILE', get_template_directory() . '/assets/data/kb.json' );
define( 'LEAP_KB_DIR',   get_template_directory() . '/knowledge' );

/**
 * One-time move of a previously-built index from the theme to uploads.
 * Safe to call repeatedly — it does nothing once the new file exists.
 */
function leap_kb_migrate_location() {
	if ( file_exists( LEAP_KB_FILE ) ) { return; }
	if ( ! file_exists( LEAP_KB_OLD_FILE ) ) { return; }
	$dir = dirname( LEAP_KB_FILE );
	if ( ! is_dir( $dir ) ) { wp_mkdir_p( $dir ); }
	if ( is_dir( $dir ) && is_writable( $dir ) ) {
		@rename( LEAP_KB_OLD_FILE, LEAP_KB_FILE );
	}
}

/** Candidate embedding models, newest first. We use whichever the key serves. */
function leap_embed_models() {
	return [ 'gemini-embedding-001', 'text-embedding-004', 'embedding-001' ];
}

/** Resolve the Google AI key (wp-config constant wins, then the option). */
function leap_ai_key() {
	return defined( 'GOOGLE_AI_KEY' ) && GOOGLE_AI_KEY
		? GOOGLE_AI_KEY
		: get_option( 'leap_google_ai_key', '' );
}

/**
 * POST JSON to a Gemini endpoint with retries on transient failures
 * (network errors, timeouts, 408/429/5xx). Returns the wp_remote response.
 */
function leap_ai_post( $endpoint, $payload, $timeout = 30, $tries = 3 ) {
	$last = null;
	for ( $i = 0; $i < $tries; $i++ ) {
		$res = wp_remote_post( $endpoint, [
			'timeout' => $timeout,
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body'    => wp_json_encode( $payload ),
		] );
		if ( ! is_wp_error( $res ) ) {
			$code = wp_remote_retrieve_response_code( $res );
			if ( $code >= 200 && $code < 300 ) {
				return $res; // success
			}
			// Non-transient error (bad key, model not found, bad request): don't retry.
			if ( ! in_array( $code, [ 408, 429, 500, 502, 503, 504 ], true ) ) {
				return $res;
			}
		}
		$last = $res;
		usleep( 350000 * ( $i + 1 ) ); // backoff: 0.35s, 0.7s
	}
	return $last;
}

/** Low-level: embed with one specific model. Returns vector or WP_Error. */
function leap_kb_embed_with( $model, $text, $task ) {
	$key = leap_ai_key();
	if ( ! $key ) {
		return new WP_Error( 'no_key', 'AI not configured' );
	}
	$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'
		. $model . ':embedContent?key=' . urlencode( $key );

	$res = leap_ai_post( $endpoint, [
		'model'    => 'models/' . $model,
		'content'  => [ 'parts' => [ [ 'text' => $text ] ] ],
		'taskType' => $task,
	] );

	if ( is_wp_error( $res ) ) {
		return $res;
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	$vec  = $body['embedding']['values'] ?? null;
	if ( ! is_array( $vec ) ) {
		return new WP_Error( 'embed_failed', wp_remote_retrieve_body( $res ) );
	}
	return $vec;
}

/**
 * Embed a string, auto-selecting a working model.
 * Once a model succeeds it's cached so query + index stay on the same one.
 *
 * @param string $text
 * @param string $task RETRIEVAL_DOCUMENT (indexing) or RETRIEVAL_QUERY (search).
 * @return array|WP_Error  Vector of floats.
 */
function leap_kb_embed( $text, $task = 'RETRIEVAL_QUERY' ) {
	// Cache query embeddings so repeat/again questions skip the API round-trip.
	$cache_key = ( $task === 'RETRIEVAL_QUERY' ) ? 'leap_qvec_' . md5( $text ) : '';
	if ( $cache_key ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) && $cached ) { return $cached; }
	}

	// Prefer the model that already worked (and that the index was built with).
	$preferred = get_option( 'leap_embed_model', '' );
	$candidates = $preferred
		? array_merge( [ $preferred ], array_diff( leap_embed_models(), [ $preferred ] ) )
		: leap_embed_models();

	$last_error = null;
	foreach ( $candidates as $model ) {
		$vec = leap_kb_embed_with( $model, $text, $task );
		if ( ! is_wp_error( $vec ) ) {
			if ( $model !== $preferred ) {
				update_option( 'leap_embed_model', $model );
			}
			if ( $cache_key ) { set_transient( $cache_key, $vec, DAY_IN_SECONDS ); }
			return $vec;
		}
		$last_error = $vec;
		// Only fall through to the next model on "not found / unsupported".
		$msg = $vec->get_error_message();
		if ( stripos( $msg, 'not found' ) === false && stripos( $msg, 'not supported' ) === false ) {
			break; // a real error (bad key, quota) — stop trying
		}
	}
	return new WP_Error( 'embed_failed', 'Embedding failed: ' . ( $last_error ? $last_error->get_error_message() : 'unknown' ) );
}

/** Split long text into overlapping word-bounded chunks. */
function leap_kb_chunk( $text, $max_chars = 1100, $overlap = 200 ) {
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );
	if ( $text === '' ) { return []; }
	if ( strlen( $text ) <= $max_chars ) { return [ $text ]; }

	$chunks = [];
	$start  = 0;
	$len    = strlen( $text );
	while ( $start < $len ) {
		$end = min( $start + $max_chars, $len );
		// Avoid splitting mid-word.
		if ( $end < $len ) {
			$space = strrpos( substr( $text, $start, $max_chars ), ' ' );
			if ( $space !== false && $space > $max_chars * 0.5 ) {
				$end = $start + $space;
			}
		}
		$chunks[] = trim( substr( $text, $start, $end - $start ) );
		if ( $end >= $len ) { break; }
		$start = max( $end - $overlap, $start + 1 );
	}
	return array_values( array_filter( $chunks ) );
}

/**
 * Fetch the real, readable copy of a page (the content inside <main>), with
 * header/footer/nav/scripts stripped. Returns '' on any failure so callers can
 * fall back to the curated keywords — indexing must never hard-fail on a fetch.
 */
function leap_kb_fetch_page_text( $url ) {
	if ( ! $url ) { return ''; }
	$res = wp_remote_get( $url, [
		'timeout'     => 20,
		'redirection' => 3,
		'sslverify'   => false, // self-request; avoids loopback cert quirks
		'headers'     => [ 'User-Agent' => 'LeapKB/1.0 (+knowledge-base indexer)' ],
	] );
	if ( is_wp_error( $res ) || (int) wp_remote_retrieve_response_code( $res ) !== 200 ) {
		return '';
	}
	$html = wp_remote_retrieve_body( $res );
	if ( ! $html || ! class_exists( 'DOMDocument' ) ) {
		return trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $html ) ) );
	}

	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
	libxml_clear_errors();

	// Content lives in <main id="main-content">; everything else is chrome.
	$main = $dom->getElementById( 'main-content' );
	if ( ! $main ) {
		$main = $dom->getElementsByTagName( 'main' )->item( 0 );
	}
	$root = $main ?: $dom->getElementsByTagName( 'body' )->item( 0 );
	if ( ! $root ) { return ''; }

	// Drop decorative / non-text nodes inside the content.
	foreach ( [ 'script', 'style', 'noscript', 'svg', 'canvas' ] as $tag ) {
		$nodes = $root->getElementsByTagName( $tag );
		for ( $i = $nodes->length - 1; $i >= 0; $i-- ) {
			$n = $nodes->item( $i );
			if ( $n && $n->parentNode ) { $n->parentNode->removeChild( $n ); }
		}
	}

	return trim( preg_replace( '/\s+/', ' ', (string) $root->textContent ) );
}

/** Break a query into meaningful lowercase terms (drops stopwords/short words). */
function leap_kb_terms( $q ) {
	$q = strtolower( preg_replace( '/[^a-z0-9\s]/i', ' ', (string) $q ) );
	$stop = array_flip( [ 'the','a','an','is','are','was','who','what','how','do','does','of','to','for','and','or','me','my','i','im','you','your','our','we','can','with','on','in','at','it','that','this','about','tell' ] );
	$terms = [];
	foreach ( preg_split( '/\s+/', trim( $q ) ) as $w ) {
		if ( strlen( $w ) >= 3 && ! isset( $stop[ $w ] ) ) { $terms[] = $w; }
	}
	return array_values( array_unique( $terms ) );
}

/** Fraction of query terms that appear in a passage (title weighted in). */
function leap_kb_keyword_score( $terms, $text, $title ) {
	if ( ! $terms ) { return 0.0; }
	$hay  = strtolower( $title . ' ' . $text );
	$hits = 0;
	foreach ( $terms as $t ) {
		if ( strpos( $hay, $t ) !== false ) { $hits++; }
	}
	return $hits / count( $terms );
}

/**
 * Gather raw passages (pre-embedding) from all sources.
 * @return array<int,array{source:string,title:string,url:string,text:string}>
 */
function leap_kb_sources() {
	$passages = [];

	// 1. Website — the real page copy, with the curated keywords kept as a
	//    compact "key facts" passage so exact terms stay reliably retrievable.
	if ( function_exists( 'leap_get_search_index' ) ) {
		foreach ( leap_get_search_index() as $entry ) {
			$title = $entry['title'] ?? 'Page';
			$url   = $entry['url'] ?? '';

			// Actual rendered content (falls back to the description on fetch failure).
			$body = leap_kb_fetch_page_text( $url );
			if ( $body === '' ) {
				$body = trim( (string) ( $entry['description'] ?? '' ) );
			}
			if ( $body !== '' ) {
				foreach ( leap_kb_chunk( $title . ". \n" . $body ) as $chunk ) {
					$passages[] = [
						'source' => 'Website',
						'title'  => $title,
						'url'    => $url,
						'text'   => $chunk,
					];
				}
			}

			// Compact key-facts passage (names, phone, address, product terms).
			$kw = trim( (string) ( $entry['description'] ?? '' ) . ' ' . (string) ( $entry['keywords'] ?? '' ) );
			if ( $kw !== '' ) {
				$passages[] = [
					'source' => 'Website',
					'title'  => $title . ' — key facts',
					'url'    => $url,
					'text'   => $title . '. ' . $kw,
				];
			}
		}
	}

	// 2. Company documents — text files in /knowledge/ (.txt, .md).
	if ( is_dir( LEAP_KB_DIR ) ) {
		foreach ( glob( LEAP_KB_DIR . '/*.{txt,md}', GLOB_BRACE ) as $file ) {
			$name = pathinfo( $file, PATHINFO_FILENAME );
			if ( strcasecmp( $name, 'README' ) === 0 ) { continue; } // skip the folder's instructions
			$raw = file_get_contents( $file );
			if ( $raw === false || trim( $raw ) === '' ) { continue; }
			foreach ( leap_kb_chunk( $raw ) as $chunk ) {
				$passages[] = [
					'source' => 'Document',
					'title'  => $name,
					'url'    => '',
					'text'   => $chunk,
				];
			}
		}
	}

	return $passages;
}

/**
 * Build the knowledge base: embed every passage and write kb.json.
 * @return array|WP_Error  Stats: ['chunks'=>n, 'sources'=>n].
 */
function leap_kb_build() {
	if ( ! leap_ai_key() ) {
		return new WP_Error( 'no_key', 'Add your Google AI key first.' );
	}
	// A single build lock stops concurrent rebuilds (cron + a click + self-heal)
	// from racing and writing a half-finished index. It auto-expires as a safety
	// net in case a build dies mid-way.
	if ( get_transient( 'leap_kb_building' ) ) {
		return new WP_Error( 'busy', 'A knowledge base build is already running. Give it a few seconds.' );
	}
	set_transient( 'leap_kb_building', 1, 5 * MINUTE_IN_SECONDS );

	try {
		// Indexing fetches pages and embeds every passage — give it room to finish.
		if ( function_exists( 'set_time_limit' ) ) { @set_time_limit( 300 ); }
		$passages = leap_kb_sources();
		if ( ! $passages ) {
			return new WP_Error( 'empty', 'No content found to index.' );
		}

		$records = [];
		foreach ( $passages as $p ) {
			$vec = leap_kb_embed( $p['text'], 'RETRIEVAL_DOCUMENT' );
			if ( is_wp_error( $vec ) ) {
				return $vec; // surface the error rather than build a partial index
			}
			$p['vector'] = $vec;
			$records[]   = $p;
			usleep( 60000 ); // be gentle on the rate limit
		}

		$dir = dirname( LEAP_KB_FILE );
		if ( ! is_dir( $dir ) ) { wp_mkdir_p( $dir ); }

		$payload = [
			'model'   => get_option( 'leap_embed_model', '' ),
			'built'   => current_time( 'mysql' ),
			'records' => $records,
		];
		// Write to a temp file then rename so the live index is never seen
		// half-written by a concurrent read (rename is atomic on the same volume).
		$tmp = $dir . '/kb.json.' . uniqid( '', true ) . '.tmp';
		if ( file_put_contents( $tmp, wp_json_encode( $payload ) ) === false || ! @rename( $tmp, LEAP_KB_FILE ) ) {
			@unlink( $tmp );
			return new WP_Error( 'write_failed', 'Could not write the knowledge base file. Check that ' . esc_html( $dir ) . ' is writable.' );
		}
		update_option( 'leap_kb_hash', leap_kb_content_hash() ); // mark current content as indexed

		$titles = array_unique( wp_list_pluck( $records, 'title' ) );
		$stats  = [ 'chunks' => count( $records ), 'sources' => count( $titles ), 'built' => current_time( 'mysql' ) ];
		update_option( 'leap_kb_stats', $stats );
		return $stats;
	} finally {
		delete_transient( 'leap_kb_building' );
	}
}

/** Fingerprint of all source content; changes when site copy or documents change. */
function leap_kb_content_hash() {
	$parts = [];
	if ( function_exists( 'leap_get_search_index' ) ) {
		$parts[] = wp_json_encode( leap_get_search_index() );
	}
	if ( is_dir( LEAP_KB_DIR ) ) {
		foreach ( glob( LEAP_KB_DIR . '/*.{txt,md}', GLOB_BRACE ) as $f ) {
			if ( strcasecmp( pathinfo( $f, PATHINFO_FILENAME ), 'README' ) === 0 ) { continue; }
			$parts[] = basename( $f ) . ':' . filemtime( $f ) . ':' . filesize( $f );
		}
	}
	// Page copy lives in the templates, so a deploy that changes them should
	// trigger a rebuild. Their mtimes make the hash reflect that.
	foreach ( glob( get_template_directory() . '/{page-*.php,front-page.php,header.php,footer.php}', GLOB_BRACE ) as $f ) {
		$parts[] = basename( $f ) . ':' . filemtime( $f );
	}
	$parts[] = get_option( 'leap_embed_model', '' );
	return md5( implode( '|', $parts ) );
}

/**
 * Rebuild the index only if the content changed since the last build.
 * Runs on a schedule, so new deploys / documents get picked up automatically.
 */
function leap_kb_maybe_rebuild() {
	if ( ! leap_ai_key() ) { return; }
	leap_kb_migrate_location(); // reuse an existing index before rebuilding from scratch
	$current = leap_kb_content_hash();
	if ( file_exists( LEAP_KB_FILE ) && $current === get_option( 'leap_kb_hash', '' ) ) {
		return; // already up to date
	}
	leap_kb_build(); // build() stores the new hash on success
}

/**
 * Self-heal: if a key is configured but the index file is missing, get it
 * rebuilding on its own — no "click Rebuild" required. WP-Cron only fires on
 * traffic, so we schedule an immediate build and nudge cron to run it now.
 * Throttled so a missing file can't trigger a loopback on every request.
 */
function leap_kb_self_heal() {
	if ( ! leap_ai_key() ) { return; }
	leap_kb_migrate_location();
	if ( file_exists( LEAP_KB_FILE ) ) { return; }
	if ( get_transient( 'leap_kb_building' ) ) { return; } // a build is already running
	if ( get_transient( 'leap_kb_heal_tick' ) ) { return; } // don't nudge more than once/2min
	set_transient( 'leap_kb_heal_tick', 1, 2 * MINUTE_IN_SECONDS );

	if ( ! wp_next_scheduled( 'leap_kb_force_reindex_event' ) ) {
		wp_schedule_single_event( time(), 'leap_kb_force_reindex_event' );
	}
	if ( function_exists( 'spawn_cron' ) ) { spawn_cron(); } // fire it promptly, don't wait for traffic
}

/** Cosine similarity between two equal-length vectors. */
function leap_kb_cosine( $a, $b ) {
	$dot = $na = $nb = 0.0;
	$n = min( count( $a ), count( $b ) );
	for ( $i = 0; $i < $n; $i++ ) {
		$dot += $a[$i] * $b[$i];
		$na  += $a[$i] * $a[$i];
		$nb  += $b[$i] * $b[$i];
	}
	if ( $na == 0.0 || $nb == 0.0 ) { return 0.0; }
	return $dot / ( sqrt( $na ) * sqrt( $nb ) );
}

/**
 * Retrieve the top matching passages for a query.
 * @return array{matches:array,top_score:float}
 */
function leap_kb_search( $query, $k = 5, $min_score = 0.42 ) {
	if ( ! file_exists( LEAP_KB_FILE ) ) {
		return [ 'matches' => [], 'best' => [], 'top_score' => 0.0 ];
	}
	$kb = json_decode( file_get_contents( LEAP_KB_FILE ), true );
	$records = $kb['records'] ?? [];
	if ( ! $records ) { return [ 'matches' => [], 'best' => [], 'top_score' => 0.0 ]; }

	$qvec = leap_kb_embed( $query, 'RETRIEVAL_QUERY' );
	if ( is_wp_error( $qvec ) ) {
		return [ 'matches' => [], 'best' => [], 'top_score' => 0.0 ];
	}

	// Hybrid scoring: semantic (cosine) blended with a keyword overlap boost so
	// exact terms — names, phone, address, product names — always surface.
	$terms = leap_kb_terms( $query );
	$scored = [];
	foreach ( $records as $r ) {
		if ( empty( $r['vector'] ) ) { continue; }
		$cos = leap_kb_cosine( $qvec, $r['vector'] );
		$kw  = leap_kb_keyword_score( $terms, $r['text'], $r['title'] );
		$scored[] = [
			'score'  => $cos + 0.15 * $kw, // ranking score (keyword nudges ties)
			'cosine' => $cos,
			'kw'     => $kw,
			'record' => $r,
		];
	}
	usort( $scored, fn( $a, $b ) => $b['score'] <=> $a['score'] );

	$top = $scored[0]['cosine'] ?? 0.0;

	// Top-k regardless of score — loose grounding so Trey can still be helpful
	// (and stay grounded) instead of hard-refusing.
	$best = array_slice( $scored, 0, $k );

	// Confident matches: clear the semantic threshold OR have a strong keyword hit.
	$matches = array_values( array_filter( $best, function ( $s ) use ( $min_score ) {
		return $s['cosine'] >= $min_score || $s['kw'] >= 0.6;
	} ) );

	return [ 'matches' => $matches, 'best' => $best, 'top_score' => $top ];
}
