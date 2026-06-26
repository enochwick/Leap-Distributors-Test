<?php
/**
 * Chat logging + admin viewer.
 *
 * Stores every Q&A exchange (and human-handover requests) in a custom table,
 * viewable under wp-admin → Leap Chat.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'LEAP_CHAT_DB_VERSION', '1.0' );

/** Create / upgrade the log table. Runs on load if the version changed. */
function leap_chat_install_table() {
	if ( get_option( 'leap_chat_db_version' ) === LEAP_CHAT_DB_VERSION ) { return; }

	global $wpdb;
	$table   = $wpdb->prefix . 'leap_chat_logs';
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		created_at DATETIME NOT NULL,
		session VARCHAR(64) DEFAULT '',
		type VARCHAR(20) NOT NULL DEFAULT 'chat',
		user_msg TEXT NULL,
		ai_reply TEXT NULL,
		sources TEXT NULL,
		contact_name VARCHAR(190) DEFAULT '',
		contact_email VARCHAR(190) DEFAULT '',
		ip VARCHAR(64) DEFAULT '',
		PRIMARY KEY  (id),
		KEY created_at (created_at),
		KEY type (type)
	) $charset;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	update_option( 'leap_chat_db_version', LEAP_CHAT_DB_VERSION );
}
add_action( 'after_setup_theme', 'leap_chat_install_table' );

/** Visitor IP (best-effort, behind proxies). */
function leap_chat_ip() {
	$keys = [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ];
	foreach ( $keys as $k ) {
		if ( ! empty( $_SERVER[ $k ] ) ) {
			$ip = explode( ',', $_SERVER[ $k ] )[0];
			return substr( trim( $ip ), 0, 64 );
		}
	}
	return '';
}

/** Insert a chat exchange. */
function leap_log_chat( $user_msg, $ai_reply, $sources = [], $session = '' ) {
	global $wpdb;
	$wpdb->insert( $wpdb->prefix . 'leap_chat_logs', [
		'created_at' => current_time( 'mysql' ),
		'session'    => substr( sanitize_text_field( $session ), 0, 64 ),
		'type'       => 'chat',
		'user_msg'   => $user_msg,
		'ai_reply'   => $ai_reply,
		'sources'    => implode( ' | ', (array) $sources ),
		'ip'         => leap_chat_ip(),
	] );
}

/** Insert a human-handover request. */
function leap_log_handover( $name, $email, $message, $transcript = '', $session = '' ) {
	global $wpdb;
	$wpdb->insert( $wpdb->prefix . 'leap_chat_logs', [
		'created_at'    => current_time( 'mysql' ),
		'session'       => substr( sanitize_text_field( $session ), 0, 64 ),
		'type'          => 'handover',
		'user_msg'      => $message,
		'ai_reply'      => $transcript,
		'contact_name'  => sanitize_text_field( $name ),
		'contact_email' => sanitize_email( $email ),
		'ip'            => leap_chat_ip(),
	] );
}

// ── Admin viewer ──────────────────────────────────────────────
add_action( 'admin_menu', function() {
	add_menu_page(
		'Leap Chat',
		'Leap Chat',
		'manage_options',
		'leap-chat-logs',
		'leap_chat_logs_page',
		'dashicons-format-chat',
		58
	);
} );

function leap_chat_logs_page() {
	global $wpdb;
	$table = $wpdb->prefix . 'leap_chat_logs';
	$filter = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
	$where  = $filter ? $wpdb->prepare( 'WHERE type = %s', $filter ) : '';
	$rows   = $wpdb->get_results( "SELECT * FROM $table $where ORDER BY id DESC LIMIT 200" );
	?>
	<div class="wrap">
		<h1>Leap Chat Logs</h1>
		<ul class="subsubsub">
			<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=leap-chat-logs' ) ); ?>" class="<?php echo $filter === '' ? 'current' : ''; ?>">All</a> |</li>
			<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=leap-chat-logs&type=chat' ) ); ?>" class="<?php echo $filter === 'chat' ? 'current' : ''; ?>">Chats</a> |</li>
			<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=leap-chat-logs&type=handover' ) ); ?>" class="<?php echo $filter === 'handover' ? 'current' : ''; ?>">Handovers</a></li>
		</ul>
		<table class="wp-list-table widefat striped" style="margin-top:12px;">
			<thead><tr>
				<th style="width:140px;">When</th>
				<th style="width:90px;">Type</th>
				<th>Visitor message</th>
				<th>AI reply / transcript</th>
				<th style="width:200px;">Contact / sources</th>
			</tr></thead>
			<tbody>
			<?php if ( ! $rows ) : ?>
				<tr><td colspan="5">No conversations yet.</td></tr>
			<?php else : foreach ( $rows as $r ) : ?>
				<tr>
					<td><?php echo esc_html( $r->created_at ); ?></td>
					<td><?php echo $r->type === 'handover'
						? '<strong style="color:#b32d2e;">Handover</strong>'
						: 'Chat'; ?></td>
					<td><?php echo esc_html( $r->user_msg ); ?></td>
					<td style="white-space:pre-wrap;"><?php echo esc_html( wp_trim_words( (string) $r->ai_reply, 60 ) ); ?></td>
					<td>
						<?php if ( $r->contact_email ) : ?>
							<strong><?php echo esc_html( $r->contact_name ); ?></strong><br>
							<a href="mailto:<?php echo esc_attr( $r->contact_email ); ?>"><?php echo esc_html( $r->contact_email ); ?></a>
						<?php else : ?>
							<span style="color:#888;font-size:12px;"><?php echo esc_html( $r->sources ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
