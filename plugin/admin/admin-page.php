<?php
namespace Sgdd\Admin\AdminPage;

require_once __DIR__ . '/settings-pages/basic.php';
require_once __DIR__ . '/settings-pages/advanced.php';

require_once __DIR__ . '/settings-pages/basic/oauth-grant.php';
require_once __DIR__ . '/settings-pages/basic/oauth-revoke.php';
require_once __DIR__ . '/settings-pages/basic/path-selection.php';

if ( ! is_admin() ) {
  return;
}

function register() {
  add_action( 'admin_menu', '\\Sgdd\\Admin\\AdminPage\\addMenu' );
  add_action( 'admin_init', '\\Sgdd\\Admin\\AdminPage\\actionHandler' );
  add_action( 'admin_enqueue_scripts', '\\Sgdd\\Admin\\AdminPage\\registerStyle' );

  \Sgdd\Admin\SettingsPages\Basic\register();
  \Sgdd\Admin\SettingsPages\Advanced\register();
}

function registerStyle() {
  \Sgdd\enqueue_style( 'sgdd_admin_page', '/admin/css/admin-page.css' );
}

function addMenu() {
  add_menu_page( 
    __('Google Drive Documents Settings', 'skaut-google-drive-documents'),
    __('Google Drive Documents', 'skaut-google-drive-documents'),
    'manage_options',
    'sgdd_basic',
    '\\Sgdd\\Admin\\SettingsPages\\Basic\\display',
    plugins_url('/skaut-google-drive-documents/admin/icon.png')
  );
}

function actionHandler() {
  if ( isset( $_GET['page'] ) && $_GET['page'] === 'sgdd_basic' ) {
    if ( isset( $_GET['action'] ) ) {
      if ( $_GET['action'] === 'oauth_grant' ) {
        wp_verify_nonce( $_GET['_wpnonce'], 'oAuthGrant' );
        \Sgdd\Admin\GoogleAPILib\oAuthGrant();
      } else if ( $_GET['action'] === 'oauth_redirect' ) {
        \Sgdd\Admin\GoogleAPILib\oAuthRedirect();
      } else if ( $_GET['action'] === 'oauth_revoke') {
        wp_verify_nonce( $_GET['_wpnonce'], 'oAuthRevoke' );
        \Sgdd\Admin\GoogleAPILib\oAuthRevoke();
      }
    }
  }
}