<?php
/**
 * Leap RAG — grounded knowledge base for the chat widget.
 *
 * Pipeline (all in-WordPress, uses the same Google AI key as the chat):
 *   1. Sources  : the curated site search index + any text files in /knowledge/.
 *   2. Chunk    : split into small passages with metadata (source, title).
 *   3. Embed    : Gemini text-embedding-004 vector per chunk (build step).
 *   4. Store    : assets/data/kb.json  (chunks + vectors).
 *   5. Retrieve : embed the question, cosine-similarity, return top matches.
 *
 * Rebuild from Settings → Leap AI → "Rebuild Knowledge Base" whenever the
 * site copy or the PDFs/text in /knowledge/ change.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'LEAP_KB_FILE',  get_template_directory() . '/assets/data/kb.json' );
define( 'LEAP_KB_DIR',   get_template_directory() . '/knowledge' );

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
 * Gather raw passages (pre-embedding) from all sources.
 * @return array<int,array{source:string,title:string,url:string,text:string}>
 */
function leap_kb_sources() {
	$passages = [];

	// 1. Website — the curated search index.
	if ( function_exists( 'leap_get_search_index' ) ) {
		foreach ( leap_get_search_index() as $entry ) {
			$text = trim(
				( $entry['title'] ?? '' ) . ". \n"
				. ( $entry['description'] ?? '' ) . " \n"
				. ( $entry['keywords'] ?? '' )
			);
			foreach ( leap_kb_chunk( $text ) as $chunk ) {
				$passages[] = [
					'source' => 'Website',
					'title'  => $entry['title'] ?? 'Page',
					'url'    => $entry['url'] ?? '',
					'text'   => $chunk,
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
	file_put_contents( LEAP_KB_FILE, wp_json_encode( $payload ) );
	update_option( 'leap_kb_hash', leap_kb_content_hash() ); // mark current content as indexed

	$titles = array_unique( wp_list_pluck( $records, 'title' ) );
	return [ 'chunks' => count( $records ), 'sources' => count( $titles ) ];
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
	$parts[] = get_option( 'leap_embed_model', '' );
	return md5( implode( '|', $parts ) );
}

/**
 * Rebuild the index only if the content changed since the last build.
 * Runs on a schedule, so new deploys / documents get picked up automatically.
 */
function leap_kb_maybe_rebuild() {
	if ( ! leap_ai_key() ) { return; }
	$current = leap_kb_content_hash();
	if ( file_exists( LEAP_KB_FILE ) && $current === get_option( 'leap_kb_hash', '' ) ) {
		return; // already up to date
	}
	leap_kb_build(); // build() stores the new hash on success
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

	$scored = [];
	foreach ( $records as $r ) {
		if ( empty( $r['vector'] ) ) { continue; }
		$scored[] = [ 'score' => leap_kb_cosine( $qvec, $r['vector'] ), 'record' => $r ];
	}
	usort( $scored, fn( $a, $b ) => $b['score'] <=> $a['score'] );

	$top = $scored[0]['score'] ?? 0.0;

	// Top-k regardless of score — used as loose grounding so Trey can still be
	// helpful (and stay grounded) instead of hard-refusing.
	$best = array_slice( $scored, 0, $k );

	// Confident matches that clear the threshold.
	$matches = array_values( array_filter( $best, fn( $s ) => $s['score'] >= $min_score ) );

	return [ 'matches' => $matches, 'best' => $best, 'top_score' => $top ];
}
