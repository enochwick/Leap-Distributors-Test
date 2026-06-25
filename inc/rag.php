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
define( 'LEAP_EMBED_MODEL', 'text-embedding-004' );

/** Resolve the Google AI key (wp-config constant wins, then the option). */
function leap_ai_key() {
	return defined( 'GOOGLE_AI_KEY' ) && GOOGLE_AI_KEY
		? GOOGLE_AI_KEY
		: get_option( 'leap_google_ai_key', '' );
}

/**
 * Embed a single string with Gemini.
 *
 * @param string $text
 * @param string $task RETRIEVAL_DOCUMENT (indexing) or RETRIEVAL_QUERY (search).
 * @return array|WP_Error  Vector of floats.
 */
function leap_kb_embed( $text, $task = 'RETRIEVAL_QUERY' ) {
	$key = leap_ai_key();
	if ( ! $key ) {
		return new WP_Error( 'no_key', 'AI not configured' );
	}

	$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'
		. LEAP_EMBED_MODEL . ':embedContent?key=' . urlencode( $key );

	$res = wp_remote_post( $endpoint, [
		'timeout' => 30,
		'headers' => [ 'Content-Type' => 'application/json' ],
		'body'    => wp_json_encode( [
			'model'     => 'models/' . LEAP_EMBED_MODEL,
			'content'   => [ 'parts' => [ [ 'text' => $text ] ] ],
			'taskType'  => $task,
		] ),
	] );

	if ( is_wp_error( $res ) ) {
		return $res;
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	$vec  = $body['embedding']['values'] ?? null;
	if ( ! is_array( $vec ) ) {
		return new WP_Error( 'embed_failed', 'Embedding failed: ' . wp_remote_retrieve_body( $res ) );
	}
	return $vec;
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
		'model'   => LEAP_EMBED_MODEL,
		'built'   => current_time( 'mysql' ),
		'records' => $records,
	];
	file_put_contents( LEAP_KB_FILE, wp_json_encode( $payload ) );

	$titles = array_unique( wp_list_pluck( $records, 'title' ) );
	return [ 'chunks' => count( $records ), 'sources' => count( $titles ) ];
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
function leap_kb_search( $query, $k = 5, $min_score = 0.55 ) {
	if ( ! file_exists( LEAP_KB_FILE ) ) {
		return [ 'matches' => [], 'top_score' => 0.0 ];
	}
	$kb = json_decode( file_get_contents( LEAP_KB_FILE ), true );
	$records = $kb['records'] ?? [];
	if ( ! $records ) { return [ 'matches' => [], 'top_score' => 0.0 ]; }

	$qvec = leap_kb_embed( $query, 'RETRIEVAL_QUERY' );
	if ( is_wp_error( $qvec ) ) {
		return [ 'matches' => [], 'top_score' => 0.0 ];
	}

	$scored = [];
	foreach ( $records as $r ) {
		if ( empty( $r['vector'] ) ) { continue; }
		$scored[] = [ 'score' => leap_kb_cosine( $qvec, $r['vector'] ), 'record' => $r ];
	}
	usort( $scored, fn( $a, $b ) => $b['score'] <=> $a['score'] );

	$top = $scored[0]['score'] ?? 0.0;
	$matches = [];
	foreach ( array_slice( $scored, 0, $k ) as $s ) {
		if ( $s['score'] >= $min_score ) {
			$matches[] = $s;
		}
	}
	return [ 'matches' => $matches, 'top_score' => $top ];
}
