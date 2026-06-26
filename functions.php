<?php

define( 'LEAP_VERSION', '4.4.1' );

// Fallback so get_field() never fatal-errors when ACF isn't installed
if ( ! function_exists( 'get_field' ) ) {
	function get_field( $key, $post_id = false ) { return null; }
}
if ( ! function_exists( 'the_field' ) ) {
	function the_field( $key, $post_id = false ) { return ''; }
}

function leap_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'custom-logo', [
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	] );

	register_nav_menus( [
		'primary' => __( 'Primary Navigation', 'leap-distributors' ),
		'footer'  => __( 'Footer Navigation', 'leap-distributors' ),
		'footer-partnerships' => __( 'Footer Partnerships', 'leap-distributors' ),
	] );
}
add_action( 'after_setup_theme', 'leap_setup' );

function leap_enqueue_assets() {
	// Google Fonts — Poppins + Aleo
	wp_enqueue_style(
		'leap-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Aleo:wght@400;500;600;700&display=swap',
		[],
		null
	);

	// Main stylesheet — versioned by file mtime so edits always bust browser cache
	wp_enqueue_style(
		'leap-main',
		get_template_directory_uri() . '/assets/css/main.css',
		[ 'leap-fonts' ],
		filemtime( get_template_directory() . '/assets/css/main.css' )
	);

	// GSAP + ScrollTrigger via CDN
	wp_enqueue_script(
		'gsap',
		'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
		[],
		null,
		true
	);
	wp_enqueue_script(
		'gsap-scroll-trigger',
		'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js',
		[ 'gsap' ],
		null,
		true
	);

	// Main JS — versioned by file mtime so edits always bust browser cache
	wp_enqueue_script(
		'leap-main',
		get_template_directory_uri() . '/assets/js/main.js',
		[ 'gsap', 'gsap-scroll-trigger' ],
		filemtime( get_template_directory() . '/assets/js/main.js' ),
		true
	);

	// Pass site URL to JS
	wp_localize_script( 'leap-main', 'leapData', [
		'siteUrl'   => get_site_url(),
		'themeUrl'  => get_template_directory_uri(),
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
	] );
}
add_action( 'wp_enqueue_scripts', 'leap_enqueue_assets' );

// Hospital coverage globe — platform page (COBE WebGL)
add_action( 'wp_enqueue_scripts', function() {
	if ( is_page( 'platform' ) ) {
		wp_enqueue_script( 'leap-hospital-globe', get_template_directory_uri() . '/assets/js/hospital-globe.js', [], LEAP_VERSION, true );
	}
} );

// Hospital MapLibre GL map — about page
add_action( 'wp_enqueue_scripts', function() {
	if ( is_page( 'about' ) ) {
		wp_enqueue_style(  'maplibre-gl',       'https://unpkg.com/maplibre-gl@4/dist/maplibre-gl.css', [], '4' );
		wp_enqueue_script( 'maplibre-gl',       'https://unpkg.com/maplibre-gl@4/dist/maplibre-gl.js',                   [], '4',    true );
		wp_enqueue_script( 'topojson',          'https://unpkg.com/topojson-client@3/dist/topojson-client.min.js',       [], '3',    true );
		// Shared case data (single source for the map + dashboard)
		wp_enqueue_script( 'leap-case-data',    get_template_directory_uri() . '/assets/js/leap-case-data.js', [], LEAP_VERSION, true );
		wp_enqueue_script( 'leap-hospital-map', get_template_directory_uri() . '/assets/js/hospital-map.js', [ 'maplibre-gl', 'topojson', 'leap-case-data' ], LEAP_VERSION, true );
		wp_enqueue_script( 'leap-case-dashboard', get_template_directory_uri() . '/assets/js/case-dashboard.js', [ 'leap-case-data' ], LEAP_VERSION, true );
	}
} );

// Mark the globe script as type="module" so the ES import works
add_filter( 'script_loader_tag', function( $tag, $handle ) {
	if ( $handle === 'leap-hospital-globe' ) {
		return str_replace( '<script ', '<script type="module" ', $tag );
	}
	return $tag;
}, 10, 2 );

// Remove WordPress emoji scripts
add_action( 'init', function() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
} );

// Clean up wp_head
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

// Excerpt length
add_filter( 'excerpt_length', fn() => 20 );
add_filter( 'excerpt_more', fn() => '...' );

// ── Contact Form Handler ──────────────────────────────────────
function leap_handle_contact_form() {
	// Verify nonce
	if ( ! isset( $_POST['leap_contact_nonce'] ) || ! wp_verify_nonce( $_POST['leap_contact_nonce'], 'leap_contact_form' ) ) {
		wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
		exit;
	}

	// Sanitize fields
	$first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
	$last_name  = sanitize_text_field( $_POST['last_name'] ?? '' );
	$email      = sanitize_email( $_POST['email'] ?? '' );
	$role       = sanitize_text_field( $_POST['role'] ?? '' );
	$message    = sanitize_textarea_field( $_POST['message'] ?? '' );

	// Basic validation
	if ( empty( $first_name ) || empty( $email ) || empty( $message ) || ! is_email( $email ) ) {
		wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
		exit;
	}

	$to      = 'info@leapdistributors.com';
	$subject = "New Contact Form Submission — {$first_name} {$last_name}";
	$body    = "Name: {$first_name} {$last_name}\n";
	$body   .= "Email: {$email}\n";
	$body   .= "Role: {$role}\n\n";
	$body   .= "Message:\n{$message}";
	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		"Reply-To: {$first_name} {$last_name} <{$email}>",
	];

	$sent = wp_mail( $to, $subject, $body, $headers );

	$status = $sent ? 'success' : 'error';
	wp_redirect( add_query_arg( 'contact', $status, wp_get_referer() ) );
	exit;
}
add_action( 'admin_post_leap_contact_form',        'leap_handle_contact_form' );
add_action( 'admin_post_nopriv_leap_contact_form', 'leap_handle_contact_form' );

// ── Newsletter Form Handler ───────────────────────────────────
function leap_handle_newsletter_form() {
	if ( ! isset( $_POST['leap_newsletter_nonce'] ) || ! wp_verify_nonce( $_POST['leap_newsletter_nonce'], 'leap_newsletter_form' ) ) {
		wp_redirect( add_query_arg( 'newsletter', 'error', wp_get_referer() ) );
		exit;
	}

	$email    = sanitize_email( $_POST['email'] ?? '' );
	$audience = sanitize_text_field( $_POST['audience'] ?? '' );

	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_redirect( add_query_arg( 'newsletter', 'error', wp_get_referer() ) );
		exit;
	}

	$to      = 'info@leapdistributors.com';
	$subject = 'New Newsletter Signup — Leap Distributors';
	$body    = "Email: {$email}\nAudience: {$audience}";
	$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

	$sent   = wp_mail( $to, $subject, $body, $headers );
	$status = $sent ? 'success' : 'error';
	wp_redirect( add_query_arg( 'newsletter', $status, wp_get_referer() ) );
	exit;
}
add_action( 'admin_post_leap_newsletter_form',        'leap_handle_newsletter_form' );
add_action( 'admin_post_nopriv_leap_newsletter_form', 'leap_handle_newsletter_form' );

// ── Knowledge base / RAG ──────────────────────────────────────
require_once get_template_directory() . '/inc/rag.php';
require_once get_template_directory() . '/inc/chat-log.php';

// Auto-reindex: rebuild the knowledge base hourly *only if content changed*,
// so new deploys / documents get picked up without clicking "Rebuild".
add_action( 'init', function() {
	if ( ! wp_next_scheduled( 'leap_kb_reindex_event' ) ) {
		wp_schedule_event( time() + 300, 'hourly', 'leap_kb_reindex_event' );
	}
} );
add_action( 'leap_kb_reindex_event', 'leap_kb_maybe_rebuild' );

// ── AI Chat Handler ───────────────────────────────────────────
function leap_ai_chat() {
	check_ajax_referer( 'leap_chat_nonce', 'nonce' );

	$message  = sanitize_text_field( wp_unslash( $_POST['message'] ?? '' ) );
	$history  = isset( $_POST['history'] ) ? json_decode( stripslashes( $_POST['history'] ), true ) : [];

	if ( empty( $message ) ) {
		wp_send_json_error( 'Empty message' );
	}

	$api_key = defined( 'GOOGLE_AI_KEY' ) ? GOOGLE_AI_KEY : get_option( 'leap_google_ai_key', '' );
	if ( empty( $api_key ) ) {
		wp_send_json_error( 'AI not configured' );
	}

	// Friendly handling for greetings / thanks so they don't hit the cold refusal.
	$normalized = strtolower( trim( $message, " \t\n\r.!?," ) );
	if ( preg_match( '/^(hi|hey|hello|yo|howdy|hiya|good (morning|afternoon|evening)|greetings)$/', $normalized ) ) {
		wp_send_json_success( [ 'reply' => "Hi! I'm Leap's assistant. Ask me anything about Leap — our distribution services, the Stride platform, or how we work with surgeons, hospitals, and manufacturers." ] );
	}
	if ( preg_match( '/^(thanks|thank you|thx|ty|cheers|appreciate it)$/', $normalized ) ) {
		wp_send_json_success( [ 'reply' => "You're welcome! Anything else about Leap I can help with?" ] );
	}

	// ── Retrieve grounding context from the knowledge base ──
	$retrieval = leap_kb_search( $message, 5 );
	$matches   = $retrieval['matches'];

	// Hard refusal when nothing relevant is in our materials.
	if ( empty( $matches ) ) {
		wp_send_json_success( [
			'reply' => "I don't have that in our materials. For specifics, reach our team at info@leapdistributors.com or call +1 888-776-5553.",
		] );
	}

	$context = '';
	foreach ( $matches as $i => $m ) {
		$r = $m['record'];
		$label = $r['source'] . ': ' . $r['title'];
		$context .= '[' . ( $i + 1 ) . '] (' . $label . ")\n" . $r['text'] . "\n\n";
	}

	$system = "You are the AI assistant for Leap Distributors, a medical device distribution company in Dallas, TX.

STRICT RULES:
- Answer ONLY using the CONTEXT below. Do not use outside knowledge or assumptions.
- If the answer is not clearly supported by the CONTEXT, reply exactly: \"I don't have that in our materials. For specifics, reach our team at info@leapdistributors.com or call +1 888-776-5553.\"
- Never invent facts, numbers, names, products, prices, or capabilities.
- Be concise, confident, and professional. Keep answers short and direct.
- You may summarize and combine information across the context items, but only what is stated there.

CONTEXT:
" . trim( $context );

	// Build Gemini contents array (roles: user / model)
	$contents = [];
	if ( is_array( $history ) ) {
		foreach ( $history as $h ) {
			if ( isset( $h['role'], $h['content'] ) && in_array( $h['role'], [ 'user', 'assistant' ] ) ) {
				$contents[] = [
					'role'  => $h['role'] === 'assistant' ? 'model' : 'user',
					'parts' => [ [ 'text' => sanitize_text_field( $h['content'] ) ] ],
				];
			}
		}
	}
	$contents[] = [ 'role' => 'user', 'parts' => [ [ 'text' => $message ] ] ];

	$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . urlencode( $api_key );

	// leap_ai_post() retries transient failures (timeouts / 429 / 5xx).
	$response = leap_ai_post( $endpoint, [
		'system_instruction' => [ 'parts' => [ [ 'text' => $system ] ] ],
		'contents'           => $contents,
		'generationConfig'   => [
			'maxOutputTokens' => 1024,
			'temperature'     => 0.2,
			'thinkingConfig'  => [ 'thinkingBudget' => 0 ], // no hidden reasoning tokens; full budget goes to the answer
		],
	] );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'Request failed' );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

	if ( empty( $text ) ) {
		wp_send_json_error( 'No response' );
	}

	// Log the exchange (source labels included for review).
	$sources = [];
	foreach ( $matches as $m ) {
		$sources[] = $m['record']['source'] . ': ' . $m['record']['title'];
	}
	leap_log_chat( $message, $text, array_values( array_unique( $sources ) ) );

	wp_send_json_success( [ 'reply' => $text ] );
}
add_action( 'wp_ajax_leap_ai_chat',        'leap_ai_chat' );
add_action( 'wp_ajax_nopriv_leap_ai_chat', 'leap_ai_chat' );

// ── Human handover ────────────────────────────────────────────
function leap_chat_handover() {
	check_ajax_referer( 'leap_chat_nonce', 'nonce' );

	$name       = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email      = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$message    = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
	$transcript = sanitize_textarea_field( wp_unslash( $_POST['transcript'] ?? '' ) );

	if ( ! is_email( $email ) || $message === '' ) {
		wp_send_json_error( 'Please add a valid email and a short message.' );
	}

	leap_log_handover( $name, $email, $message, $transcript );

	// Notify the team by email.
	$to      = get_option( 'leap_handover_email', '' ) ?: 'info@leapdistributors.com';
	$subject = 'Chat handover request from ' . ( $name ?: $email );
	$body    = "A visitor asked to speak with a person via the website chat.\n\n"
		. "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}\n\n"
		. ( $transcript ? "— Conversation so far —\n{$transcript}\n" : '' );
	wp_mail( $to, $subject, $body, [ 'Reply-To: ' . $email ] );

	// Optional Slack notification.
	$slack = get_option( 'leap_slack_webhook', '' );
	if ( $slack ) {
		wp_remote_post( $slack, [
			'timeout' => 8,
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body'    => wp_json_encode( [ 'text' => "*Chat handover* from {$name} ({$email})\n>{$message}" ] ),
		] );
	}

	wp_send_json_success( [ 'reply' => "Thanks, {$name}! A Leap team member will reach out by email shortly." ] );
}
add_action( 'wp_ajax_leap_chat_handover',        'leap_chat_handover' );
add_action( 'wp_ajax_nopriv_leap_chat_handover', 'leap_chat_handover' );

// Pass chat nonce to JS
add_action( 'wp_enqueue_scripts', function() {
	wp_localize_script( 'leap-main', 'leapChat', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'leap_chat_nonce' ),
	] );
}, 20 );

// ── Leap AI settings page — store the Google AI (Gemini) key safely ──
// The chat handler reads GOOGLE_AI_KEY (wp-config constant) first, then this option.
add_action( 'admin_menu', function() {
	add_options_page(
		'Leap AI',
		'Leap AI',
		'manage_options',
		'leap-ai',
		'leap_ai_settings_page'
	);
} );

add_action( 'admin_init', function() {
	register_setting( 'leap_ai_settings', 'leap_google_ai_key', [
		'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '',
	] );
	register_setting( 'leap_ai_settings', 'leap_handover_email', [
		'type' => 'string', 'sanitize_callback' => 'sanitize_email', 'default' => '',
	] );
	register_setting( 'leap_ai_settings', 'leap_slack_webhook', [
		'type' => 'string', 'sanitize_callback' => 'esc_url_raw', 'default' => '',
	] );
} );

// Handle the "Rebuild Knowledge Base" action.
add_action( 'admin_post_leap_kb_rebuild', function() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed' ); }
	check_admin_referer( 'leap_kb_rebuild' );

	$result = leap_kb_build();
	if ( is_wp_error( $result ) ) {
		$args = [ 'page' => 'leap-ai', 'kb' => 'error', 'msg' => rawurlencode( $result->get_error_message() ) ];
	} else {
		$args = [ 'page' => 'leap-ai', 'kb' => 'ok', 'chunks' => $result['chunks'], 'sources' => $result['sources'] ];
	}
	wp_safe_redirect( add_query_arg( $args, admin_url( 'options-general.php' ) ) );
	exit;
} );

function leap_ai_settings_page() {
	$key_constant = defined( 'GOOGLE_AI_KEY' ) && GOOGLE_AI_KEY;
	$kb_exists    = file_exists( LEAP_KB_FILE );
	$kb_built     = $kb_exists ? ( json_decode( file_get_contents( LEAP_KB_FILE ), true )['built'] ?? '' ) : '';
	?>
	<div class="wrap">
		<h1>Leap AI Assistant</h1>
		<?php if ( $key_constant ) : ?>
			<div class="notice notice-info"><p>A key is currently set via the <code>GOOGLE_AI_KEY</code> constant in <code>wp-config.php</code>, which takes priority over the field below.</p></div>
		<?php endif; ?>
		<?php if ( isset( $_GET['kb'] ) && $_GET['kb'] === 'ok' ) : ?>
			<div class="notice notice-success is-dismissible"><p>Knowledge base rebuilt: <?php echo (int) ( $_GET['chunks'] ?? 0 ); ?> passages from <?php echo (int) ( $_GET['sources'] ?? 0 ); ?> sources.</p></div>
		<?php elseif ( isset( $_GET['kb'] ) && $_GET['kb'] === 'error' ) : ?>
			<div class="notice notice-error is-dismissible"><p>Rebuild failed: <?php echo esc_html( wp_unslash( $_GET['msg'] ?? '' ) ); ?></p></div>
		<?php endif; ?>

		<h2>1. API key</h2>
		<p>Paste your Google AI (Gemini) API key to power the chat widget. Get one at <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">aistudio.google.com/apikey</a>.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'leap_ai_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="leap_google_ai_key">Gemini API Key</label></th>
					<td>
						<input name="leap_google_ai_key" id="leap_google_ai_key" type="password" autocomplete="off"
							value="<?php echo esc_attr( get_option( 'leap_google_ai_key', '' ) ); ?>"
							class="regular-text" placeholder="AIza…">
						<p class="description">Stored in the database. For higher security, define <code>GOOGLE_AI_KEY</code> in <code>wp-config.php</code> instead.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="leap_handover_email">Handover email</label></th>
					<td>
						<input name="leap_handover_email" id="leap_handover_email" type="email"
							value="<?php echo esc_attr( get_option( 'leap_handover_email', '' ) ); ?>"
							class="regular-text" placeholder="info@leapdistributors.com">
						<p class="description">Where "Talk to a person" requests are emailed. Defaults to info@leapdistributors.com.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="leap_slack_webhook">Slack webhook (optional)</label></th>
					<td>
						<input name="leap_slack_webhook" id="leap_slack_webhook" type="url"
							value="<?php echo esc_attr( get_option( 'leap_slack_webhook', '' ) ); ?>"
							class="regular-text" placeholder="https://hooks.slack.com/services/…">
						<p class="description">If set, handover requests also post to this Slack channel.</p>
					</td>
				</tr>
			</table>
			<?php submit_button( 'Save Settings' ); ?>
		</form>

		<hr>
		<h2>2. Knowledge base</h2>
		<p>The chat answers <strong>only</strong> from your website content and the documents in
			<code>wp-content/themes/<?php echo esc_html( get_template() ); ?>/knowledge/</code>
			(drop <code>.txt</code> or <code>.md</code> files there). Rebuild after changing site copy or documents.</p>
		<p>
			<?php if ( $kb_exists ) : ?>
				Status: <strong style="color:#1a7f37;">Built</strong> <?php echo $kb_built ? '· last built ' . esc_html( $kb_built ) : ''; ?>.
			<?php else : ?>
				Status: <strong style="color:#b32d2e;">Not built yet</strong> — the chat will refuse all questions until you build it.
			<?php endif; ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="leap_kb_rebuild">
			<?php wp_nonce_field( 'leap_kb_rebuild' ); ?>
			<?php submit_button( 'Rebuild Knowledge Base', 'primary', 'submit', false ); ?>
			<span class="description" style="margin-left:8px;">This calls the embedding API for each passage; takes a few seconds.</span>
		</form>
	</div>
	<?php
}

// ACF Options Page (if ACF Pro is active)
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page( [
		'page_title' => 'Site Settings',
		'menu_title' => 'Site Settings',
		'menu_slug'  => 'site-settings',
		'capability' => 'edit_posts',
		'redirect'   => false,
	] );
}

// Load field group definitions
if ( function_exists( 'acf_add_local_field_group' ) ) {
	require_once get_template_directory() . '/inc/acf-fields.php';
}

// Load search index
require_once get_template_directory() . '/inc/search-index.php';

// Pass search index to JS
add_action( 'wp_enqueue_scripts', function() {
	wp_localize_script( 'leap-main', 'leapSearchIndex', leap_get_search_index() );
}, 25 );
