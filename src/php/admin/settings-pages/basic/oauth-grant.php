<?php
/**
 * Handles oAuth authentification process
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\SettingsPages\Basic\OAuthGrant;

if ( ! is_admin() ) {
	return;
}

/**
 * Register actions into WordPress
 */
function register() {
	add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\Basic\\OAuthGrant\\add_settings' );
}


/**
 * Adds settings fields to grant section of basic settings page
 */
function add_settings() {
	add_settings_section( 'sgdd_auth', __( 'Step 1: Authorization', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\Basic\\OAuthGrant\\display', 'sgdd_basic' );

	\Sgdd\Admin\Options\Options::$authorized_domain->add_field( true, true );
	\Sgdd\Admin\Options\Options::$authorized_origin->add_field( true, true );
	\Sgdd\Admin\Options\Options::$redirect_uri->add_field( true, true );
	\Sgdd\Admin\Options\Options::$client_id->add_field();
	\Sgdd\Admin\Options\Options::$client_secret->add_field();
}

/**
 * Renders oAuth grant section of basic settings page
 */
function display() {
	$help_link = 'https://github.com/skaut/skaut-google-drive-documents/wiki/N%C3%A1vod-na-nastavenie';
	$console   = 'https://console.cloud.google.com/apis/dashboard';

	// translators: 1: Start of a link to the documentation 2: End of the link to the documentation.
	add_settings_error( 'general', 'help', sprintf( esc_html__( 'See the %1$sdocumentation%2$s for more information about how to configure the plugin.', 'skaut-google-drive-documents' ), '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' ), 'notice-info' );
	// translators: 1: Start of a link to the Google Console 2: End of the link to the Google Console.
	add_settings_error( 'general', 'help', sprintf( esc_html__( 'To get access details please follow this %1$slink%2$s', 'skaut-google-drive-documents' ), '<a href="' . esc_url( $console ) . '" target="_blank">', '</a>' ), 'notice-info' );

	settings_errors();
	echo '<p>' . esc_html__( 'Create a Google app and provide the following details:', 'skaut-google-drive-documents' ) . '</p>';
	echo '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdd_basic&action=oauth_grant' ) ), 'oauth_grant' ) . '">' . esc_html__( 'Grant Permission', 'skaut-google-drive-documents' ) . '</a>';
}
