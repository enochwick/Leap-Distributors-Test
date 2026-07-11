<?php

define( 'LEAP_VERSION', '4.4.1' );

// Fallback so get_field() never fatal-errors when ACF isn't installed
if ( ! function_exists( 'get_field' ) ) {
	function get_field( $key, $post_id = false ) { return null; }
}
if ( ! function_exists( 'the_field' ) ) {
	function the_field( $key, $post_id = false ) { return ''; }
}

// ── TEMPORARY MAIL DEBUG LOGGER (remove after diagnosing) ─────
// Writes to wp-content/uploads/leap-mail-debug.log
function leap_mail_debug_log( $line ) {
	$up  = wp_upload_dir();
	$f   = trailingslashit( $up['basedir'] ) . 'leap-mail-debug.log';
	$msg = '[' . gmdate( 'Y-m-d H:i:s' ) . ' UTC] ' . $line . "\n";
	@file_put_contents( $f, $msg, FILE_APPEND | LOCK_EX );
}
// Log every wp_mail attempt (recipient + subject).
add_filter( 'wp_mail', function ( $args ) {
	$to = is_array( $args['to'] ?? '' ) ? implode( ',', $args['to'] ) : ( $args['to'] ?? '' );
	leap_mail_debug_log( 'wp_mail() called → to=' . $to . ' | subject=' . ( $args['subject'] ?? '' ) );
	return $args;
}, 1 );
// Log the ACTUAL transport PHPMailer is about to use (mail vs smtp).
add_action( 'phpmailer_init', function ( $phpmailer ) {
	leap_mail_debug_log( 'phpmailer_init → Mailer=' . $phpmailer->Mailer . ' | Host=' . $phpmailer->Host . ' | From=' . $phpmailer->From );
}, 9999 );
// Log any failure with the full error.
add_action( 'wp_mail_failed', function ( $error ) {
	leap_mail_debug_log( 'wp_mail_failed → ' . $error->get_error_message() );
} );
// Detect whether WP Mail SMTP is even active and which mailer it thinks is set.
add_action( 'init', function () {
	if ( isset( $_GET['leap_mail_probe'] ) ) {
		$active  = function_exists( 'wp_mail_smtp' ) ? 'WP Mail SMTP ACTIVE' : 'WP Mail SMTP NOT found';
		$mailer  = '';
		if ( function_exists( 'wp_mail_smtp' ) ) {
			$opts   = get_option( 'wp_mail_smtp', [] );
			$mailer = $opts['mail']['mailer'] ?? '(unknown)';
		}
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$others = [];
		foreach ( (array) get_option( 'active_plugins', [] ) as $p ) {
			if ( preg_match( '/smtp|mailer|sendinblue|brevo|postman|fluent-smtp/i', $p ) ) { $others[] = $p; }
		}
		leap_mail_debug_log( 'PROBE → ' . $active . ' | mailer=' . $mailer . ' | other_smtp_plugins=' . ( $others ? implode( ',', $others ) : 'none' ) );
		wp_die( 'Mail probe logged. Active: ' . esc_html( $active ) . ' | Mailer: ' . esc_html( $mailer ) . ' | Other SMTP plugins: ' . esc_html( $others ? implode( ', ', $others ) : 'none' ) );
	}

	// reCAPTCHA status probe — confirms keys are configured (secret never shown).
	if ( isset( $_GET['leap_recaptcha_probe'] ) ) {
		$site   = leap_recaptcha_site_key();
		$secret = leap_recaptcha_secret_key();
		$lines  = [
			'Site key configured: '   . ( $site ? 'YES (ends …' . substr( $site, -6 ) . ', len ' . strlen( $site ) . ')' : 'NO' ),
			'Secret key configured: ' . ( $secret ? 'YES (len ' . strlen( $secret ) . ')' : 'NO' ),
			'Site-key source: '       . ( defined( 'LEAP_RECAPTCHA_SITE_KEY' ) && LEAP_RECAPTCHA_SITE_KEY ? 'wp-config constant' : ( get_option( 'leap_recaptcha_site_key', '' ) ? 'settings page' : '(none)' ) ),
			'Enforcement: '           . ( $secret ? 'reCAPTCHA ACTIVE (server-side verification runs)' : 'honeypot + timing only (add secret key to enable reCAPTCHA)' ),
			'v3 key format looks right: ' . ( $site && strpos( $site, '6L' ) === 0 ? 'yes (starts 6L)' : 'CHECK — v3 keys usually start with 6L' ),
		];
		wp_die( '<pre style="font:14px monospace;white-space:pre-wrap">reCAPTCHA STATUS' . "\n\n" . esc_html( implode( "\n", $lines ) ) . '</pre>' );
	}

	// WHOAMI: ask Brevo which account the stored API key belongs to.
	if ( isset( $_GET['leap_brevo_whoami'] ) && current_user_can( 'manage_options' ) ) {
		$opts   = get_option( 'wp_mail_smtp', [] );
		$key    = $opts['sendinblue']['api_key'] ?? '';
		if ( ! $key ) {
			wp_die( 'No Brevo API key found in WP Mail SMTP settings.' );
		}
		$tail = substr( $key, -6 );

		// GET /v3/account → identifies the account/company.
		$acct = wp_remote_get( 'https://api.brevo.com/v3/account', [
			'headers' => [ 'api-key' => $key, 'accept' => 'application/json' ],
			'timeout' => 15,
		] );
		// GET /v3/senders → confirms noreply@ is a valid sender in THIS account.
		$snd = wp_remote_get( 'https://api.brevo.com/v3/senders', [
			'headers' => [ 'api-key' => $key, 'accept' => 'application/json' ],
			'timeout' => 15,
		] );

		$acct_body = is_wp_error( $acct ) ? $acct->get_error_message() : wp_remote_retrieve_body( $acct );
		$acct_code = is_wp_error( $acct ) ? 'ERR' : wp_remote_retrieve_response_code( $acct );
		$snd_body  = is_wp_error( $snd ) ? $snd->get_error_message() : wp_remote_retrieve_body( $snd );

		$acct_data = json_decode( $acct_body, true );
		$company   = $acct_data['companyName'] ?? '(n/a)';
		$email     = $acct_data['email'] ?? '(n/a)';

		leap_mail_debug_log( 'WHOAMI → key_ends=' . $tail . ' | http=' . $acct_code . ' | company=' . $company . ' | email=' . $email );

		wp_die(
			'<pre style="font:14px monospace;white-space:pre-wrap">' .
			'API key ends in: ' . esc_html( $tail ) . "\n" .
			'HTTP status: ' . esc_html( $acct_code ) . "\n" .
			'Account company: ' . esc_html( $company ) . "\n" .
			'Account email: ' . esc_html( $email ) . "\n\n" .
			'--- /v3/account raw ---' . "\n" . esc_html( $acct_body ) . "\n\n" .
			'--- /v3/senders raw ---' . "\n" . esc_html( $snd_body ) .
			'</pre>'
		);
	}

	// SENDTEST: fire a real transactional send and print Brevo's raw response.
	if ( isset( $_GET['leap_brevo_sendtest'] ) && current_user_can( 'manage_options' ) ) {
		$opts = get_option( 'wp_mail_smtp', [] );
		$key  = $opts['sendinblue']['api_key'] ?? '';
		if ( ! $key ) {
			wp_die( 'No Brevo API key found.' );
		}
		$to = sanitize_email( $_GET['to'] ?? 'htadesse@totalancillary.com' );
		$resp = wp_remote_post( 'https://api.brevo.com/v3/smtp/email', [
			'headers' => [ 'api-key' => $key, 'content-type' => 'application/json', 'accept' => 'application/json' ],
			'timeout' => 20,
			'body'    => wp_json_encode( [
				'sender'      => [ 'name' => 'Leap Distributors', 'email' => 'noreply@leapdistributors.com' ],
				'to'          => [ [ 'email' => $to ] ],
				'subject'     => 'Leap Brevo direct send test',
				'textContent' => 'Direct API send test from the diagnostic probe.',
			] ),
		] );
		$code = is_wp_error( $resp ) ? 'ERR' : wp_remote_retrieve_response_code( $resp );
		$body = is_wp_error( $resp ) ? $resp->get_error_message() : wp_remote_retrieve_body( $resp );
		leap_mail_debug_log( 'SENDTEST → to=' . $to . ' | http=' . $code . ' | resp=' . $body );
		wp_die(
			'<pre style="font:14px monospace;white-space:pre-wrap">' .
			'Sent to: ' . esc_html( $to ) . "\n" .
			'HTTP status: ' . esc_html( $code ) . "\n" .
			'Brevo response: ' . esc_html( $body ) .
			'</pre>'
		);
	}
} );
// ── END TEMPORARY MAIL DEBUG LOGGER ──────────────────────────

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

// Newsletter page-flip PDF viewer — single newsletter posts only
add_action( 'wp_enqueue_scripts', function() {
	if ( is_single() && has_category( 'newsletters' ) ) {
		wp_enqueue_script( 'pdfjs',     'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/legacy/build/pdf.min.js',        [],                        '3.11.174', true );
		wp_enqueue_script( 'page-flip', 'https://cdn.jsdelivr.net/npm/page-flip@2.0.7/dist/js/page-flip.browser.js',       [],                        '2.0.7',    true );
		wp_enqueue_script( 'leap-pdf-flip', get_template_directory_uri() . '/assets/js/pdf-flip.js', [ 'pdfjs', 'page-flip' ], filemtime( get_template_directory() . '/assets/js/pdf-flip.js' ), true );
		wp_localize_script( 'leap-pdf-flip', 'leapPdf', [
			'worker' => 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/legacy/build/pdf.worker.min.js',
		] );
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

// ── Form bot protection (reCAPTCHA v3 + honeypot + timing) ────
// Keys: wp-config constants take priority, then the Leap AI settings page.
function leap_recaptcha_site_key() {
	if ( defined( 'LEAP_RECAPTCHA_SITE_KEY' ) && LEAP_RECAPTCHA_SITE_KEY ) { return LEAP_RECAPTCHA_SITE_KEY; }
	return get_option( 'leap_recaptcha_site_key', '' );
}
function leap_recaptcha_secret_key() {
	if ( defined( 'LEAP_RECAPTCHA_SECRET_KEY' ) && LEAP_RECAPTCHA_SECRET_KEY ) { return LEAP_RECAPTCHA_SECRET_KEY; }
	return get_option( 'leap_recaptcha_secret_key', '' );
}

// Output the hidden security fields inside a form. $action names the reCAPTCHA action.
function leap_form_security_fields( $action ) {
	// Honeypot: a real user never fills this; it's visually hidden and off the tab order.
	echo '<div aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;height:0;width:0;overflow:hidden;">'
		. '<label>Leave this field empty<input type="text" name="leap_hp" tabindex="-1" autocomplete="off" value=""></label>'
		. '</div>';
	// Timestamp: submissions faster than a couple seconds are almost certainly bots.
	echo '<input type="hidden" name="leap_ts" value="' . esc_attr( time() ) . '">';
	// reCAPTCHA v3 token (filled by JS just before submit).
	echo '<input type="hidden" name="leap_recaptcha_token" value="">';
}

// Required disclosure shown near the submit button when the floating badge is hidden.
// (Google allows hiding the badge only if this notice is displayed.)
function leap_recaptcha_notice() {
	if ( ! leap_recaptcha_site_key() ) { return; }
	echo '<p class="recaptcha-notice">This site is protected by reCAPTCHA and the Google '
		. '<a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a> and '
		. '<a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">Terms of Service</a> apply.</p>';
}

// Returns true if the submission looks like a bot. Fails closed on bad reCAPTCHA,
// but open on transient network errors so a Google outage never blocks real leads.
function leap_submission_is_bot( $action ) {
	// 1. Honeypot filled → bot.
	if ( ! empty( $_POST['leap_hp'] ) ) { return true; }

	// 2. Submitted implausibly fast (< 3s from page render) → bot.
	$ts = isset( $_POST['leap_ts'] ) ? absint( $_POST['leap_ts'] ) : 0;
	if ( $ts && ( time() - $ts ) < 3 ) { return true; }

	// 3. reCAPTCHA v3 — only enforced when a secret key is configured.
	$secret = leap_recaptcha_secret_key();
	if ( ! $secret ) { return false; }

	$token = sanitize_text_field( $_POST['leap_recaptcha_token'] ?? '' );
	if ( ! $token ) { return true; } // keys are set but no token came through → bot / JS-off scraper

	$resp = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
		'timeout' => 10,
		'body'    => [
			'secret'   => $secret,
			'response' => $token,
			'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
		],
	] );
	if ( is_wp_error( $resp ) ) { return false; } // can't reach Google → don't block real users

	$data = json_decode( wp_remote_retrieve_body( $resp ), true );
	if ( empty( $data['success'] ) ) { return true; }
	// v3 returns a 0..1 score; 0.5 is Google's suggested threshold.
	if ( isset( $data['score'] ) && (float) $data['score'] < 0.5 ) { return true; }
	return false;
}

// Load the reCAPTCHA v3 API and bind it to every form marked data-leap-recaptcha.
add_action( 'wp_enqueue_scripts', function () {
	$site = leap_recaptcha_site_key();
	if ( ! $site ) { return; }
	wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( $site ), [], null, true );
	// Hide the floating badge (allowed when the disclosure notice is shown on the form)
	// and style that notice.
	wp_add_inline_style( 'leap-main',
		'.grecaptcha-badge{visibility:hidden!important;}'
		. '.recaptcha-notice{font-size:12px;line-height:1.5;opacity:.6;margin:0 0 var(--space-3,12px);}'
		. '.recaptcha-notice a{color:inherit;text-decoration:underline;}'
	);
	$inline = "(function(){var k=" . wp_json_encode( $site ) . ";document.addEventListener('submit',function(e){var f=e.target;if(!f.hasAttribute||!f.hasAttribute('data-leap-recaptcha'))return;if(f.dataset.recaptchaDone==='1')return;e.preventDefault();var a=f.getAttribute('data-leap-recaptcha')||'submit';if(!window.grecaptcha){f.dataset.recaptchaDone='1';f.submit();return;}grecaptcha.ready(function(){grecaptcha.execute(k,{action:a}).then(function(t){var i=f.querySelector('input[name=\"leap_recaptcha_token\"]');if(i)i.value=t;f.dataset.recaptchaDone='1';if(typeof f.requestSubmit==='function')f.requestSubmit();else f.submit();}).catch(function(){f.dataset.recaptchaDone='1';f.submit();});});},true);})();";
	wp_add_inline_script( 'google-recaptcha', $inline );
} );

// ── Contact Form Handler ──────────────────────────────────────
function leap_handle_contact_form() {
	// Verify nonce
	if ( ! isset( $_POST['leap_contact_nonce'] ) || ! wp_verify_nonce( $_POST['leap_contact_nonce'], 'leap_contact_form' ) ) {
		wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
		exit;
	}

	// Bot check — silently accept (no email) so bots get no signal.
	if ( leap_submission_is_bot( 'contact' ) ) {
		wp_redirect( add_query_arg( 'contact', 'success', wp_get_referer() ) );
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

	$to      = 'enochwick@gmail.com';
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

	// Bot check — silently accept (no email) so bots get no signal.
	if ( leap_submission_is_bot( 'newsletter' ) ) {
		wp_redirect( add_query_arg( 'newsletter', 'success', wp_get_referer() ) );
		exit;
	}

	$email    = sanitize_email( $_POST['email'] ?? '' );
	$audience = sanitize_text_field( $_POST['audience'] ?? '' );

	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_redirect( add_query_arg( 'newsletter', 'error', wp_get_referer() ) );
		exit;
	}

	$to      = 'enochwick@gmail.com';
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

// ── Job Application Form Handler ──────────────────────────────
function leap_handle_application_form() {
	$back = wp_get_referer() ?: home_url( '/careers/' );

	if ( ! isset( $_POST['leap_application_nonce'] ) || ! wp_verify_nonce( $_POST['leap_application_nonce'], 'leap_application_form' ) ) {
		wp_redirect( add_query_arg( 'application', 'error', $back ) );
		exit;
	}

	// Bot check — silently accept (no email) so bots get no signal.
	if ( leap_submission_is_bot( 'application' ) ) {
		wp_redirect( add_query_arg( 'application', 'success', $back ) );
		exit;
	}

	$first    = sanitize_text_field( $_POST['first_name'] ?? '' );
	$last     = sanitize_text_field( $_POST['last_name'] ?? '' );
	$email    = sanitize_email( $_POST['email'] ?? '' );
	$phone    = sanitize_text_field( $_POST['phone'] ?? '' );
	$linkedin = esc_url_raw( $_POST['linkedin'] ?? '' );
	$position = sanitize_text_field( $_POST['position'] ?? 'General Application' );
	$message  = sanitize_textarea_field( $_POST['message'] ?? '' );

	if ( empty( $first ) || empty( $email ) || ! is_email( $email ) ) {
		wp_redirect( add_query_arg( 'application', 'error', $back ) );
		exit;
	}

	// Handle the resume upload (optional). Accept pdf/doc/docx up to 8 MB,
	// stash it in a temp folder to attach, then delete after sending.
	$attachments = [];
	$cleanup     = '';
	$resume_note = 'No resume attached.';
	if ( ! empty( $_FILES['resume']['name'] ) && isset( $_FILES['resume']['error'] ) && UPLOAD_ERR_OK === $_FILES['resume']['error'] ) {
		$check   = wp_check_filetype( $_FILES['resume']['name'] );
		$allowed = [ 'pdf', 'doc', 'docx' ];
		if ( in_array( strtolower( (string) $check['ext'] ), $allowed, true ) && $_FILES['resume']['size'] <= 8 * 1024 * 1024 ) {
			$upload = wp_upload_dir();
			$dir    = trailingslashit( $upload['basedir'] ) . 'applications';
			wp_mkdir_p( $dir );
			$safe   = sanitize_file_name( $first . '-' . $last . '-resume-' . time() . '.' . $check['ext'] );
			$target = trailingslashit( $dir ) . $safe;
			if ( move_uploaded_file( $_FILES['resume']['tmp_name'], $target ) ) {
				$attachments[] = $target;
				$cleanup       = $target;
				$resume_note   = 'Resume attached: ' . sanitize_file_name( $_FILES['resume']['name'] );
			}
		} else {
			$resume_note = 'A resume was submitted but rejected (only PDF/DOC/DOCX up to 8 MB are accepted).';
		}
	}

	$to      = 'htadesse@totalancillary.com';
	$subject = "New Job Application — {$position} — {$first} {$last}";
	$body    = "Position: {$position}\n";
	$body   .= "Name: {$first} {$last}\n";
	$body   .= "Email: {$email}\n";
	$body   .= "Phone: {$phone}\n";
	$body   .= "LinkedIn / Portfolio: {$linkedin}\n";
	$body   .= "{$resume_note}\n\n";
	$body   .= "Why Leap:\n" . ( $message ?: '(none provided)' );
	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		"Reply-To: {$first} {$last} <{$email}>",
	];

	$sent = wp_mail( $to, $subject, $body, $headers, $attachments );

	if ( $cleanup ) {
		@unlink( $cleanup );
	}

	$status = $sent ? 'success' : 'error';
	wp_redirect( add_query_arg( 'application', $status, $back ) );
	exit;
}
add_action( 'admin_post_leap_application_form',        'leap_handle_application_form' );
add_action( 'admin_post_nopriv_leap_application_form', 'leap_handle_application_form' );

// ── Walkthrough Request Form Handler ──────────────────────────
function leap_handle_walkthrough_form() {
	$back = wp_get_referer() ?: home_url( '/platform/' );

	if ( ! isset( $_POST['leap_walkthrough_nonce'] ) || ! wp_verify_nonce( $_POST['leap_walkthrough_nonce'], 'leap_walkthrough_form' ) ) {
		wp_redirect( add_query_arg( 'walkthrough', 'error', $back ) );
		exit;
	}

	// Bot check — silently accept (no email) so bots get no signal.
	if ( leap_submission_is_bot( 'walkthrough' ) ) {
		wp_redirect( add_query_arg( 'walkthrough', 'success', $back ) );
		exit;
	}

	$first   = sanitize_text_field( $_POST['first_name'] ?? '' );
	$last    = sanitize_text_field( $_POST['last_name'] ?? '' );
	$email   = sanitize_email( $_POST['email'] ?? '' );
	$company = sanitize_text_field( $_POST['company'] ?? '' );
	$role    = sanitize_text_field( $_POST['role'] ?? '' );
	$phone   = sanitize_text_field( $_POST['phone'] ?? '' );
	$message = sanitize_textarea_field( $_POST['message'] ?? '' );

	if ( empty( $first ) || empty( $email ) || ! is_email( $email ) ) {
		wp_redirect( add_query_arg( 'walkthrough', 'error', $back ) );
		exit;
	}

	$to      = 'enochwick@gmail.com';
	$subject = "Walkthrough Request — {$first} {$last}" . ( $company ? " ({$company})" : '' );
	$body    = "Name: {$first} {$last}\n";
	$body   .= "Email: {$email}\n";
	$body   .= "Company: {$company}\n";
	$body   .= "Role: {$role}\n";
	$body   .= "Phone: {$phone}\n\n";
	$body   .= "What they'd like to see:\n" . ( $message ?: '(none provided)' );
	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		"Reply-To: {$first} {$last} <{$email}>",
	];

	$sent   = wp_mail( $to, $subject, $body, $headers );
	$status = $sent ? 'success' : 'error';
	wp_redirect( add_query_arg( 'walkthrough', $status, $back ) );
	exit;
}
add_action( 'admin_post_leap_walkthrough_form',        'leap_handle_walkthrough_form' );
add_action( 'admin_post_nopriv_leap_walkthrough_form', 'leap_handle_walkthrough_form' );

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

// Self-heal: if the index file goes missing (e.g. wiped by a deploy), rebuild it
// automatically instead of the chat sitting on "Not built yet" until someone clicks.
add_action( 'init', 'leap_kb_self_heal' );

// Rebuild soon after content changes (page/post edits, PDF/doc uploads) so the
// chat's knowledge stays current without waiting for the hourly check.
function leap_kb_schedule_force_reindex() {
	if ( ! wp_next_scheduled( 'leap_kb_force_reindex_event' ) ) {
		wp_schedule_single_event( time() + 60, 'leap_kb_force_reindex_event' );
	}
}
add_action( 'leap_kb_force_reindex_event', 'leap_kb_build' );
add_action( 'save_post', function( $post_id, $post = null ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) { return; }
	if ( $post && $post->post_status !== 'publish' ) { return; }
	leap_kb_schedule_force_reindex();
}, 10, 2 );
add_action( 'add_attachment', 'leap_kb_schedule_force_reindex' );

// Best-effort client IP for rate limiting. Behind a proxy/CDN, REMOTE_ADDR is
// the proxy, so prefer the forwarded client IP when present.
function leap_client_ip() {
	foreach ( [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ] as $h ) {
		if ( ! empty( $_SERVER[ $h ] ) ) {
			$ip = trim( explode( ',', $_SERVER[ $h ] )[0] ); // first hop = original client
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) { return $ip; }
		}
	}
	return '0.0.0.0';
}

// ── AI Chat Handler ───────────────────────────────────────────
function leap_ai_chat() {
	check_ajax_referer( 'leap_chat_nonce', 'nonce' );

	$message  = sanitize_text_field( wp_unslash( $_POST['message'] ?? '' ) );
	$history  = isset( $_POST['history'] ) ? json_decode( stripslashes( $_POST['history'] ), true ) : [];

	if ( empty( $message ) ) {
		wp_send_json_error( 'Empty message' );
	}

	// Per-IP rate limit — protects the Gemini quota/billing from a scripted flood
	// of the public endpoint. Fixed 1-minute window, keyed by minute bucket so the
	// count resets cleanly. Returns a friendly message (as a normal reply) so the
	// widget shows it instead of a generic error.
	$rl_max    = 20;
	$rl_bucket = 'leap_rl_' . md5( leap_client_ip() ) . '_' . floor( time() / MINUTE_IN_SECONDS );
	$rl_count  = (int) get_transient( $rl_bucket );
	if ( $rl_count >= $rl_max ) {
		wp_send_json_success( [ 'reply' => "You're sending messages a bit quickly — give me a moment and try again shortly." ] );
	}
	set_transient( $rl_bucket, $rl_count + 1, 2 * MINUTE_IN_SECONDS );

	$api_key = defined( 'GOOGLE_AI_KEY' ) ? GOOGLE_AI_KEY : get_option( 'leap_google_ai_key', '' );
	if ( empty( $api_key ) ) {
		wp_send_json_error( 'AI not configured' );
	}

	// Friendly handling for greetings / thanks so they don't hit the cold refusal.
	$normalized = strtolower( trim( $message, " \t\n\r.!?," ) );
	if ( preg_match( '/^(hi|hey|hello|yo|howdy|hiya|good (morning|afternoon|evening)|greetings)$/', $normalized ) ) {
		wp_send_json_success( [ 'reply' => "Hi, I'm Trey, your Leap assistant. Ask me anything about Leap — our distribution services, the Stride platform, or how we work with surgeons, hospitals, and manufacturers." ] );
	}
	if ( preg_match( '/^(thanks|thank you|thx|ty|cheers|appreciate it)$/', $normalized ) ) {
		wp_send_json_success( [ 'reply' => "You're welcome! Anything else about Leap I can help with?" ] );
	}

	// First-turn answers are cached briefly so repeat FAQs are instant and don't
	// re-hit the API. Skip the cache once there's conversation history (follow-ups).
	$cache_answer = empty( $history );
	$ans_key      = 'leap_ans_' . md5( $normalized );
	if ( $cache_answer ) {
		$cached = get_transient( $ans_key );
		if ( is_string( $cached ) && $cached !== '' ) {
			wp_send_json_success( [ 'reply' => $cached ] );
		}
	}

	// ── Retrieve grounding context from the knowledge base ──
	$retrieval = leap_kb_search( $message, 6 );
	$matches   = $retrieval['matches'];

	// Prefer confident matches; otherwise fall back to the best loose matches so
	// Trey can still respond helpfully (conversational turns, partial info) rather
	// than hard-refusing. Only refuse outright when the KB has nothing at all.
	$use = ! empty( $matches ) ? $matches : ( $retrieval['best'] ?? [] );
	if ( empty( $use ) ) {
		wp_send_json_success( [
			'reply' => "I'm still getting up to speed on that one. For specifics, reach our team at info@leapdistributors.com or call +1 888-776-5553.",
		] );
	}

	$context = '';
	foreach ( $use as $i => $m ) {
		$r = $m['record'];
		$label = $r['source'] . ': ' . $r['title'];
		$context .= '[' . ( $i + 1 ) . '] (' . $label . ")\n" . $r['text'] . "\n\n";
	}

	$system = "You are Trey, the warm, helpful AI assistant for Leap Distributors, a medical device distribution company based in Dallas, TX. If asked your name, you're Trey.

HOW TO ANSWER:
- Ground your answers in the CONTEXT below — it's your source of truth about Leap.
- Never invent facts, numbers, names, products, prices, or medical claims. If a specific detail isn't in the CONTEXT, don't make it up.
- If the user tells you who they are (e.g. \"I'm a surgeon\", a hospital, a manufacturer, or a rep), warmly welcome them and, using the CONTEXT, explain how Leap works with that audience and offer a helpful next step.
- Always try to be useful. If the CONTEXT doesn't hold the exact answer, share the closest relevant thing Leap does and point them to info@leapdistributors.com or +1 888-776-5553 for specifics — do NOT give a blunt refusal.
- Treat conversational messages naturally; you don't need context to say hello or acknowledge someone.
- Be concise, warm, and professional — usually 1 to 4 sentences.

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

	// Try the primary model, then a lighter fallback, so a single-model blip
	// (rate limit / outage) doesn't take the chat down.
	$text = '';
	foreach ( [ 'gemini-2.5-flash', 'gemini-2.5-flash-lite' ] as $model ) {
		$result = leap_ai_generate( $system, $contents, $model );
		if ( ! is_wp_error( $result ) && $result !== '' ) {
			$text = $result;
			break;
		}
	}

	// Never surface a hard error to a visitor — degrade gracefully.
	if ( $text === '' ) {
		wp_send_json_success( [
			'reply' => "I'm having a brief hiccup on my end. Please try again in a moment — or reach our team at info@leapdistributors.com or +1 888-776-5553.",
		] );
	}

	if ( $cache_answer ) {
		set_transient( $ans_key, $text, 6 * HOUR_IN_SECONDS );
	}

	// Log the exchange (source labels included for review).
	$sources = [];
	foreach ( $use as $m ) {
		$sources[] = $m['record']['source'] . ': ' . $m['record']['title'];
	}
	leap_log_chat( $message, $text, array_values( array_unique( $sources ) ) );

	wp_send_json_success( [ 'reply' => $text ] );
}

/**
 * Call Gemini generateContent with a specific model.
 * @return string|WP_Error  The reply text, or an error to try the next model.
 */
function leap_ai_generate( $system, $contents, $model ) {
	$key = leap_ai_key();
	if ( ! $key ) { return new WP_Error( 'no_key', 'AI not configured' ); }

	$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'
		. $model . ':generateContent?key=' . urlencode( $key );

	// leap_ai_post() already retries transient failures (timeouts / 429 / 5xx).
	$response = leap_ai_post( $endpoint, [
		'system_instruction' => [ 'parts' => [ [ 'text' => $system ] ] ],
		'contents'           => $contents,
		'generationConfig'   => [
			'maxOutputTokens' => 1024,
			'temperature'     => 0.2,
			'thinkingConfig'  => [ 'thinkingBudget' => 0 ],
		],
	], 20 );

	if ( is_wp_error( $response ) ) { return $response; }
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
	return $text !== '' ? $text : new WP_Error( 'empty', 'No response' );
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
	$to      = 'htadesse@totalancillary.com';
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
	register_setting( 'leap_ai_settings', 'leap_recaptcha_site_key', [
		'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '',
	] );
	register_setting( 'leap_ai_settings', 'leap_recaptcha_secret_key', [
		'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '',
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
	if ( function_exists( 'leap_kb_migrate_location' ) ) { leap_kb_migrate_location(); }
	$key_constant = defined( 'GOOGLE_AI_KEY' ) && GOOGLE_AI_KEY;
	$kb_exists    = file_exists( LEAP_KB_FILE );
	$kb_building  = (bool) get_transient( 'leap_kb_building' );
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
				<tr>
					<th scope="row"><label for="leap_recaptcha_site_key">reCAPTCHA v3 Site Key</label></th>
					<td>
						<input name="leap_recaptcha_site_key" id="leap_recaptcha_site_key" type="text" autocomplete="off"
							value="<?php echo esc_attr( get_option( 'leap_recaptcha_site_key', '' ) ); ?>"
							class="regular-text" placeholder="6Lc…">
						<p class="description">Bot protection for all public forms (contact, newsletter, application, walkthrough). Create keys at <a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener">google.com/recaptcha</a> — choose <strong>reCAPTCHA v3</strong> and add your domain.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="leap_recaptcha_secret_key">reCAPTCHA v3 Secret Key</label></th>
					<td>
						<input name="leap_recaptcha_secret_key" id="leap_recaptcha_secret_key" type="password" autocomplete="off"
							value="<?php echo esc_attr( get_option( 'leap_recaptcha_secret_key', '' ) ); ?>"
							class="regular-text" placeholder="6Lc…">
						<p class="description">Both keys are required for verification to run. Honeypot + timing protection stays active even without keys.</p>
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
		<?php
		$kb_stats     = get_option( 'leap_kb_stats', [] );
		$kb_stale     = $kb_exists && function_exists( 'leap_kb_content_hash' )
			&& leap_kb_content_hash() !== get_option( 'leap_kb_hash', '' );
		?>
		<p>
			<?php if ( $kb_exists ) : ?>
				Status: <strong style="color:#1a7f37;">Built</strong>
				<?php if ( ! empty( $kb_stats['chunks'] ) ) : ?>
					· <?php echo (int) $kb_stats['chunks']; ?> passages from <?php echo (int) $kb_stats['sources']; ?> sources
				<?php endif; ?>
				<?php echo $kb_built ? '· last indexed ' . esc_html( $kb_built ) : ''; ?>.
				<?php if ( $kb_stale ) : ?>
					<br><span style="color:#b26a00;">Site content has changed since the last index — it will refresh automatically within the hour, or click Rebuild now.</span>
				<?php endif; ?>
			<?php elseif ( $kb_building ) : ?>
				Status: <strong style="color:#b26a00;">Building now…</strong> — this refreshes automatically; reload this page in a few seconds.
			<?php else : ?>
				Status: <strong style="color:#b32d2e;">Not built yet</strong> — it rebuilds itself automatically within a minute, or click Rebuild now to do it immediately.
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
