<?php
/**
 * Functions and template for basic settings page
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\SettingsPages\Basic;

require_once __DIR__ . '/basic/oauth-grant.php';
require_once __DIR__ . '/basic/oauth-revoke.php';
require_once __DIR__ . '/basic/path-selection.php';

if ( ! is_admin() ) {
	return;
}

/**
 * Registers actions into WordPress
 */
function register() {
	add_action( 'admin_menu', '\\Sgdd\\Admin\\SettingsPages\\Basic\\add_menu' );

	if ( ! get_option( 'sgdd_access_token' ) ) {
		\Sgdd\Admin\SettingsPages\Basic\OAuthGrant\register();
	} else {
		\Sgdd\Admin\SettingsPages\Basic\OAuthRevoke\register();
		\Sgdd\Admin\SettingsPages\Basic\PathSelection\register();
	}
}

/**
 * Adds basic settings page to admin menu
 */
function add_menu() {
	add_submenu_page( 'sgdd_basic', __( 'Basic options', 'skaut-google-drive-documents' ), esc_html__( 'Basic options', 'skaut-google-drive-documents' ), 'manage_options', 'sgdd_basic', '\\Sgdd\\Admin\\SettingsPages\\Basic\\display' );
}

/**
 * Renders settings page
 */
function display() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>

	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php?action=update&option_page=sgdd_basic" method="post">
			<?php settings_fields( 'sgdd_basic' ); ?>
			<?php do_settings_sections( 'sgdd_basic' ); ?>
			<?php submit_button( __( 'Save Settings', 'skaut-google-drive-documents' ) ); ?>
		</form>
	</div>

	<?php
}
