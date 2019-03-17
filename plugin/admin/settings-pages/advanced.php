<?php
namespace Sgdd\Admin\SettingsPages\Advanced;

if ( ! is_admin() ) {
  return;
}

function register() {
	add_action( 'admin_menu', '\\Sgdd\\Admin\\SettingsPages\\Advanced\\add' );
}

function add() {
  add_submenu_page( 'sgdd_basic', __( 'Advanced options', 'skaut-google-drive-documents' ), esc_html__( 'Advanced options', 'skaut-google-drive-documents' ), 'manage_options', 'sgdd_advanced', '\\Sgdd\\Admin\\SettingsPages\\Advanced\\display' );
}

function display() {
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }
?>

  Hello world!

<?php
}