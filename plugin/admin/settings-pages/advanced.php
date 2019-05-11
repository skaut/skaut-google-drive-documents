<?php
namespace Sgdd\Admin\SettingsPages\Advanced;

require_once __DIR__ . '/advanced/embed.php';

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_menu', '\\Sgdd\\Admin\\SettingsPages\\Advanced\\add_menu' );
	Embed\register();
}

function add_menu() {
	add_submenu_page( 'sgdd_basic', __( 'Advanced options', 'skaut-google-drive-documents' ), esc_html__( 'Advanced options', 'skaut-google-drive-documents' ), 'manage_options', 'sgdd_advanced', '\\Sgdd\\Admin\\SettingsPages\\Advanced\\display' );
}

function display() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php?action=update&option_page=sgdd_advanced" method="post">
	<?php settings_fields( 'sgdd_advanced' ); ?>
		<?php do_settings_sections( 'sgdd_advanced' ); ?>
	<?php submit_button( __( 'Save Settings', 'skaut-google-drive-documents' ) ); ?>
	</form>
	</div>
	<?php
}
