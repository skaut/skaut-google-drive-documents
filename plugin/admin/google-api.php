<?php
/**
 * Google API connections.
 *
 * Handles connecting and managing connection to Google API.
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\GoogleAPILib;

if ( ! is_admin() ) {
	return;
}

/**
 * Sets up Google Client object.
 *
 * @return object Google Client object.
 */
function get_google_client() {
	$client = new \Sgdd\Vendor\Google_Client();
	$client->setAuthConfig(
		[
			'client_id'     => \Sgdd\Admin\Options\Options::$client_id->get(),
			'client_secret' => \Sgdd\Admin\Options\Options::$client_secret->get(),
			'redirect_uris' => [ esc_url_raw( admin_url( 'admin.php?page=sgdd_basic&action=oauth_redirect' ) ) ],
		]
	);
	$client->setAccessType( 'offline' );
	$client->setIncludeGrantedScopes( true );
	$client->setApprovalPrompt( 'force' );
	$client->addScope( \Sgdd\Vendor\Google_Service_Drive::DRIVE );

	return $client;
}

/**
 * Sets up Google Drive Client object.
 *
 * @throws \Exception If access token is not defined.
 * @return object Google Drive Client object.
 */
function get_drive_client() {
	$client       = \Sgdd\Admin\GoogleAPILib\get_google_client();
	$access_token = get_option( 'sgdd_access_token' );

	if ( ! $access_token ) {
		throw new \Exception( __( 'Not authorized!', 'skaut-google-drive-documents' ) );
	}

	$client->setAccessToken( $access_token );

	if ( $client->isAccessTokenExpired() ) {
		$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
		$new_access_token    = $client->getAccessToken();
		$merged_access_token = array_merge( $access_token, $new_access_token );
		update_option( 'sgdd_access_token', $merged_access_token );
	}

	return new \Sgdd\Vendor\Google_Service_Drive( $client );
}

/**
 * Handles oAuth grant proccess.
 */
function oauth_grant() {
	$client   = get_google_client();
	$auth_url = $client->createAuthUrl();

	header( 'Location: ' . esc_url_raw( $auth_url ) );
}

/**
 * Handles redirect from Google API.
 */
function oauth_redirect() {
	// phpcs:ignore
	if ( ! isset( $_GET['code'] ) ) {
		add_settings_error( 'general', 'oauth_failed', esc_html__( 'Google API hasn\'t returned an authentication code. Please try again.', 'skaut-google-drive-documents' ), 'error' );
	}

	if ( count( get_settings_errors() ) === 0 && ! get_option( 'sgdd_access_token' ) ) {
		$client = get_google_client();
		// phpcs:ignore
		$client->authenticate( $_GET['code'] );
		$access_token = $client->getAccessToken();

		add_option( 'sgdd_access_token', $access_token );

		$service = \Sgdd\Admin\GoogleAPILib\get_google_client();
		// phpcs:ignore WordPress.Security.NonceVerification
		$client->authenticate( $_GET['code'] );
		$access_token = $client->getAccessToken();
		$drive_client = new \Sgdd\Vendor\Google_Service_Drive( $client );
		try {
			\Sgdd\Admin\SettingsPages\Basic\PathSelection\get_team_drives( $drive_client );
			update_option( 'sgdd_access_token', $access_token );
		} catch ( \Sgdd\Vendor\Google_Service_Exception $e ) {
			if ( 'accessNotConfigured' === $e->getErrors()[0]['reason'] ) {
				// translators: %s: Link to the Google developers console
				add_settings_error( 'general', 'oauth_failed', sprintf( esc_html__( 'Google Drive API is not enabled. Please enable it at %s and try again after a while.', 'skaut-google-drive-documents' ), '<a href="https://console.developers.google.com/apis/library/drive.googleapis.com" target="_blank">https://console.developers.google.com/apis/library/drive.googleapis.com</a>' ), 'error' );
			} else {
				add_settings_error( 'general', 'oauth_failed', esc_html__( 'An unknown error has been encountered:', 'skaut-google-drive-documents' ) . ' ' . $e->getErrors()[0]['message'], 'error' );
			}
		}
	}

	if ( count( get_settings_errors() ) === 0 ) {
		add_settings_error( 'general', 'oauth_updated', __( 'Permission granted.', 'skaut-google-drive-documents' ), 'updated' );
	}

	set_transient( 'settings_errors', get_settings_errors(), 30 );
	header( 'Location: ' . esc_url_raw( admin_url( 'admin.php?page=sgdd_basic&settings-updated=true' ) ) );
}

/**
 * Handles oAuth revoke proccess.
 */
function oauth_revoke() {
	$client = get_google_client();
	$client->revokeToken();

	if ( get_option( 'sgdd_access_token' ) ) {
		delete_option( 'sgdd_access_token' );
	}

	add_settings_error( 'general', 'oauth_updated', __( 'Permission revoked.', 'skaut-google-drive-documents' ), 'updated' );
	set_transient( 'settings_errors', get_settings_errors(), 30 );
	header( 'Location: ' . esc_url_raw( admin_url( 'admin.php?page=sgdd_basic&settings-updated=true' ) ) );
}
