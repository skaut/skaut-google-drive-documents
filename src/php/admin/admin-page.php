<?php
/**
 * Admin page functions
 *
 * Function that controll actions on admin pages
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\AdminPage;

require_once __DIR__ . '/settings-pages/basic.php';
require_once __DIR__ . '/settings-pages/advanced.php';

require_once __DIR__ . '/settings-pages/basic/oauth-grant.php';
require_once __DIR__ . '/settings-pages/basic/oauth-revoke.php';
require_once __DIR__ . '/settings-pages/basic/path-selection.php';

require_once __DIR__ . '/editor.php';

if ( ! is_admin() ) {
	return;
}

/**
 * Registers actions into WordPress.
 */
function register() {
	add_action( 'admin_menu', '\\Sgdd\\Admin\\AdminPage\\add_menu' );
	add_action( 'admin_init', '\\Sgdd\\Admin\\AdminPage\\action_handler' );
	add_action( 'admin_enqueue_scripts', '\\Sgdd\\Admin\\AdminPage\\register_style' );

	\Sgdd\Admin\SettingsPages\Basic\register();
	\Sgdd\Admin\SettingsPages\Advanced\register();
	\Sgdd\Admin\Editor\register();
}

/**
 * Registers styles and scripts used in admin pages.
 */
function register_style() {
}

/**
 * Adds menu to admin section.
 */
function add_menu() {
	add_menu_page(
		__( 'Google Drive Documents Settings', 'skaut-google-drive-documents' ),
		__( 'Google Drive Documents', 'skaut-google-drive-documents' ),
		'manage_options',
		'sgdd_basic',
		'\\Sgdd\\Admin\\SettingsPages\\Basic\\display',
		plugins_url( trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . '/icon.png' )
	);
}

/**
 * Handles redirects from Google
 */
function action_handler() {
	// phpcs:ignore WordPress.Security.NonceVerification
	if ( isset( $_GET['page'] ) && 'sgdd_basic' === $_GET['page'] ) {
		if ( isset( $_GET['action'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			if ( 'oauth_grant' === $_GET['action'] ) {
				if ( isset( $_GET['_wpnonce'] ) ) {
					wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'oauth_grant' );
					\Sgdd\Admin\GoogleAPILib\oauth_grant();
				} else {
					add_settings_error( 'general', 'oauth_failed', esc_html__( 'Verification error!', 'skaut-google-drive-documents' ), 'error' );
				}
			} elseif ( 'oauth_redirect' === $_GET['action'] ) {
				\Sgdd\Admin\GoogleAPILib\oauth_redirect();
			} elseif ( 'oauth_revoke' === $_GET['action'] ) {
				if ( isset( $_GET['_wpnonce'] ) ) {
					wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'oauth_revoke' );
					\Sgdd\Admin\GoogleAPILib\oauth_revoke();
				} else {
					add_settings_error( 'general', 'oauth_failed', esc_html__( 'Verification error!', 'skaut-google-drive-documents' ), 'error' );
				}
			}
		}
	}
}
