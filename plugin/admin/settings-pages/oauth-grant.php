<?php
namespace Sgdd\Admin\SettingsPages\OAuthGrant;

if ( ! is_admin() ) {
	return;
}

function register() {
  add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\OAuthGrant\\addSettings' );
}

function addSettings() {
  add_settings_section( 'sgdd_auth', __( 'Step 1: Authorization', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\OAuthGrant\\display', 'sgdd_settings' );

  \Sgdd\Admin\Options\Options::$authorizedDomain->addField( true, true );
  \Sgdd\Admin\Options\Options::$authorizedOrigin->addField( true, true );
  \Sgdd\Admin\Options\Options::$redirectUri->addField( true, true );
  \Sgdd\Admin\Options\Options::$clientId ->addField();
  \Sgdd\Admin\Options\Options::$clientSecret->addField();
}

function display() {
  //$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-documents';
  $help_link = 'https://github.com/skaut/skaut-google-drive-documents/wiki/N%C3%A1vod-na-nastavenie';
  $console = 'https://console.cloud.google.com/apis/dashboard';
  
  add_settings_error( 'general', 'help', sprintf( esc_html__( 'See the %1$sdocumentation%2$s for more information about how to configure the plugin.', 'skaut-google-drive-documents' ), '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' ), 'notice-info' );
  add_settings_error( 'general', 'help', sprintf( esc_html__( 'To get access details please follow this %1$slink%2$s', 'skaut-google-drive-documents' ), '<a href="' . esc_url( $console ) . '" target="_blank">', '</a>' ), 'notice-info' );

  settings_errors();
  echo '<p>' . __( 'Create a Google app and provide the following details:', 'sgdd' ) . '</p>' ;
	echo '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdd_settings&action=oauth_grant' ) ), 'oAuthGrant' ) . '">' . __( 'Grant Permission', 'skaut-google-drive-documents' ) . '</a>';
}