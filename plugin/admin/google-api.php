<?php
namespace Sgdd\Admin\GoogleAPILib;

if ( ! is_admin() ) {
	return;
}

function getGoogleClient() {
  $client = new \Sgdd\Vendor\Google_Client();
  $client->setAuthConfig(
    [
      'client_id'     => \Sgdd\Admin\Options\Options::$clientId->get(),
      'client_secret' => \Sgdd\Admin\Options\Options::$clientSecret->get(),
      'redirect_uris' => [ esc_url_raw( admin_url( 'admin.php?page=sgdd_settings&action=oauth_redirect' ) ) ]
    ]
  );
  $client->setAccessType( "offline" );
  $client->setIncludeGrantedScopes( true );
  $client->setApprovalPrompt( 'force' );
  $client->addScope( \Sgdd\Vendor\Google_Service_Drive::DRIVE );
  
  return $client;
}

function getDriveClient() {
  $client = \Sgdd\Admin\GoogleAPILib\getGoogleClient();
  $accessToken = get_option( 'sgdd_accessToken' );

  if ( ! $accessToken ) {
    throw new \Exception( __( 'Not authorized!', 'skaut-google-drive-documents' ) );
  }

  $client->setAccessToken( $accessToken );

  if ( $client->isAccessTokenExpired() ) {
		$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
		$newAccessToken = $client->getAccessToken();
		$mergedAccessToken = array_merge( $accessToken, $newAccessToken );
		update_option( 'sgdd_accessToken', $mergedAccessToken );
	}

  return new \Sgdd\Vendor\Google_Service_Drive( $client );
}

function oAuthGrant() {
  $client = getGoogleClient();
  $authUrl = $client->createAuthUrl();

  header( 'Location: ' . esc_url_raw( $authUrl ) );
}

function oAuthRedirect() {
	// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	if ( ! isset( $_GET['code'] ) ) {
		add_settings_error( 'general', 'oauthFailed', esc_html__( 'Google API hasn\'t returned an authentication code. Please try again.', 'skaut-google-drive-documents' ), 'error' );
  }

	if ( count( get_settings_errors() ) === 0 && ! get_option( 'sgdd_accessToken' ) ) {    
    $client = getGoogleClient();
    $client->authenticate( $_GET['code'] );
    $accessToken = $client->getAccessToken();
    
    add_option( 'sgdd_accessToken', $accessToken );
    //var_dump($accessToken);
		/*$client = \Sgdg\Frontend\GoogleAPILib\get_raw_client();
		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		$client->authenticate( $_GET['code'] );
		$access_token = $client->getAccessToken();
		$drive_client = new \Sgdg\Vendor\Google_Service_Drive( $client );
		try {
			\Sgdg\Admin\AdminPages\Basic\RootSelection\list_teamdrives( $drive_client );
			update_option( 'sgdg_access_token', $access_token );
		} catch ( \Sgdg\Vendor\Google_Service_Exception $e ) {
			if ( 'accessNotConfigured' === $e->getErrors()[0]['reason'] ) {
				// translators: %s: Link to the Google developers console
				add_settings_error( 'general', 'oauth_failed', sprintf( esc_html__( 'Google Drive API is not enabled. Please enable it at %s and try again after a while.', 'skaut-google-drive-gallery' ), '<a href="https://console.developers.google.com/apis/library/drive.googleapis.com" target="_blank">https://console.developers.google.com/apis/library/drive.googleapis.com</a>' ), 'error' );
			} else {
				add_settings_error( 'general', 'oauth_failed', esc_html__( 'An unknown error has been encountered:', 'skaut-google-drive-gallery' ) . ' ' . $e->getErrors()[0]['message'], 'error' );
			}
		}*/
  }
  
	if ( count( get_settings_errors() ) === 0 ) { 
    add_settings_error( 'general', 'oauthUpdated', __( 'Permission granted.', 'skaut-google-drive-documents' ), 'updated' );
  }
  
	set_transient( 'settings_errors', get_settings_errors(), 30 );
	header( 'Location: ' . esc_url_raw( admin_url( 'admin.php?page=sgdd_settings&settings-updated=true' ) ) );
}

function oAuthRevoke() {
  $client = getGoogleClient();
  $client->revokeToken();

  if ( get_option( 'sgdd_accessToken' ) ) {
    delete_option( 'sgdd_accessToken' );
  }

  add_settings_error( 'general', 'oauthUpdated', __( 'Permission revoked.', 'skaut-google-drive-documents' ), 'updated' );
	set_transient( 'settings_errors', get_settings_errors(), 30 );
	header( 'Location: ' . esc_url_raw( admin_url( 'admin.php?page=sgdd_settings&settings-updated=true' ) ) );
}

