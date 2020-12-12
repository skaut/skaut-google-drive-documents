<?php
/**
 * Handles oAuth revoke process
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\SettingsPages\Basic\OAuthRevoke;

if ( ! is_admin() ) {
	return;
}

/**
 * Register actions into WordPress
 */
function register() {
	add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\Basic\\OAuthRevoke\\add_settings' );
}

/**
 * Add settings fields to revoke section of basic settings page
 */
function add_settings() {
	add_settings_section( 'sgdd_auth', __( 'Step 1: Authorization', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\Basic\\OAuthRevoke\\display', 'sgdd_basic' );

	\Sgdd\Admin\Options\Options::$authorized_domain->add_field( true, true );
	\Sgdd\Admin\Options\Options::$authorized_origin->add_field( true, true );
	\Sgdd\Admin\Options\Options::$redirect_uri->add_field( true, true );
	\Sgdd\Admin\Options\Options::$client_id->add_field( true );
	\Sgdd\Admin\Options\Options::$client_secret->add_field( true );
}

/**
 * Renders oAuth revoke section of basic settings page
 */
function display() {
	settings_errors();
	echo '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdd_basic&action=oauth_revoke' ) ) ) . '">' . esc_html__( 'Revoke Permission', 'skaut-google-drive-documents' ) . '</a>';
}
