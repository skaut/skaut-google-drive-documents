<?php

namespace Sgdd\Admin\AdminPage;

if ( !is_admin() ) {
  return;
}

require_once __DIR__ . '/admin-page-display.php';
require_once __DIR__ . '/settings-pages/oauth-grant.php';

function register() {
  add_action( 'admin_menu', '\\Sgdd\\Admin\\AdminPage\\add_menu');
  \Sgdd\Admin\SettingsPages\OAuthGrant\register();
}

function add_menu() {
  add_menu_page( 
    __('Google Drive Documents Settings', 'sgdd'),
    __('Google Drive Documents', 'sgdd'),
    'manage_options',
    'sgdd_settings',
    '\\Sgdd\\Admin\\AdminPageDisplay\\display',
    plugins_url('/skaut-google-drive-documents/admin/icon.png')
  );
}