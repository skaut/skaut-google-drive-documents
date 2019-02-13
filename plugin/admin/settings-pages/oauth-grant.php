<?php

namespace Sgdd\Admin\SettingsPages\OAuthGrant;

function register() {
  add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\OAuthGrant\\add_settings' );
}

function add_settings() {
  add_settings_section( 'sgdd_auth', esc_html__( 'Step 1: Authorization', 'sgdd' ), '\\Sgdd\\Admin\\SettingsPages\\OAuthGrant\\display', 'sgdd_settings' );

  \Sgdd\Admin\Options\Options::$authorized_domain->add_field( true );
  \Sgdd\Admin\Options\Options::$authorized_origin->add_field( true);
  \Sgdd\Admin\Options\Options::$redirect_uri->add_field( true );
  \Sgdd\Admin\Options\Options::$client_id ->add_field();
  \Sgdd\Admin\Options\Options::$client_secret->add_field();
}

function display() {
  $help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-gallery';
	add_settings_error( 'general', 'help', sprintf( esc_html__( 'See the %1$sdocumentation%2$s for more information about how to configure the plugin.', 'sgdd' ), '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' ), 'notice-info' );
	settings_errors();

  echo '<p>' . __( 'Create a Google app and provide the following details:', 'sgdd' ) . '</p>' ;
	echo '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdd_settings&action=oauth_grant' ) ), 'oauth_grant' ) . '">' . __( 'Grant Permission', 'sgdd' ) . '</a>';
}