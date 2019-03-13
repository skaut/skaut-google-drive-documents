<?php
namespace Sgdd\Admin\SettingsPages\OAuthRevoke;

if ( ! is_admin() ) {
  return;
}

function register() {
  add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\OAuthRevoke\\addSettings' );
}

function addSettings() {
  add_settings_section( 'sgdd_auth', __( 'Step 1: Authorization', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\OAuthRevoke\\display', 'sgdd_settings' );

  \Sgdd\Admin\Options\Options::$authorizedDomain->addField( true, true );
  \Sgdd\Admin\Options\Options::$authorizedOrigin->addField( true, true );
  \Sgdd\Admin\Options\Options::$redirectUri->addField( true, true );
  \Sgdd\Admin\Options\Options::$clientId ->addField( true );
  \Sgdd\Admin\Options\Options::$clientSecret->addField( true );
}

function display() {
  settings_errors();
  echo '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdd_settings&action=oauth_revoke' ) ), 'oAuthRevoke' ) . '">' . __( 'Revoke Permission', 'skaut-google-drive-documents' ) . '</a>';
}