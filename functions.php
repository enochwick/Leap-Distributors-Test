<?php

define( 'LEAP_VERSION', '4.3.2' );

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

	// Main stylesheet
	wp_enqueue_style(
		'leap-main',
		get_template_directory_uri() . '/assets/css/main.css',
		[ 'leap-fonts' ],
		LEAP_VERSION
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

	// Main JS
	wp_enqueue_script(
		'leap-main',
		get_template_directory_uri() . '/assets/js/main.js',
		[ 'gsap', 'gsap-scroll-trigger' ],
		LEAP_VERSION,
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

// Hospital coverage map — platform page only
add_action( 'wp_enqueue_scripts', function() {
	if ( is_page( 'platform' ) ) {
		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4' );
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true );
		wp_enqueue_script( 'leap-hospital-map', get_template_directory_uri() . '/assets/js/hospital-globe.js', [ 'leaflet' ], LEAP_VERSION, true );
	}
} );

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

	$system = "You are the AI assistant for Leap Distributors, a premium medical device distribution company based in Dallas, TX.

ABOUT LEAP:
Leap Distributors is the new standard in medical device distribution. We serve surgeons, hospitals, and manufacturers across the south central United States with national reach. We operate on Stride — our proprietary technology platform that logs every OR case in real time, auto-generates paperwork, and sharpens data with every case.

WHAT WE DO:
- OR case coverage: Our reps are the sharpest in the room, present for every case, knowing surgeon preferences before walking in
- Multi-line distribution: One team covering every product line across every manufacturer we represent
- Stride platform: Real-time case logging, auto-generated scrub sheets, faster billing, live field data and analytics
- We are product-agnostic — we advocate for surgeon choice, not manufacturer preference

WHO WE SERVE:
- Surgeons: Know your preferences, your procedures, and your room. Broader product access without losing trusted reps.
- Hospitals & Health Systems: One team, every product line, live case data, faster billing, streamlined supply chain.
- Manufacturers: Direct rep coverage in south central hub, national distributor reach, real field data on product movement.

KEY STATS:
- 10,000+ surgeries annually
- 750+ surgeons served
- 350+ facilities, GPOs & IDNs

CONTACT:
- Email: info@leapdistributors.com
- Address: 3151 Halifax Street, Suite 160, Dallas, TX 75219

TONE: Be concise, confident, and professional. Keep answers short and direct. If asked about something outside Leap's services, politely redirect to what Leap can help with. Always offer to connect the user with the team for specific questions.";

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

	$response = wp_remote_post( $endpoint, [
		'timeout' => 30,
		'headers' => [ 'Content-Type' => 'application/json' ],
		'body'    => json_encode( [
			'system_instruction' => [ 'parts' => [ [ 'text' => $system ] ] ],
			'contents'           => $contents,
			'generationConfig'   => [ 'maxOutputTokens' => 400, 'temperature' => 0.7 ],
		] ),
	] );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'Request failed' );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

	if ( empty( $text ) ) {
		wp_send_json_error( 'No response' );
	}

	wp_send_json_success( [ 'reply' => $text ] );
}
add_action( 'wp_ajax_leap_ai_chat',        'leap_ai_chat' );
add_action( 'wp_ajax_nopriv_leap_ai_chat', 'leap_ai_chat' );

// Pass chat nonce to JS
add_action( 'wp_enqueue_scripts', function() {
	wp_localize_script( 'leap-main', 'leapChat', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'leap_chat_nonce' ),
	] );
}, 20 );

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
