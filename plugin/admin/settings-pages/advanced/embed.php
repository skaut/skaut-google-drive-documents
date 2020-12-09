<?php
/**
 * Functions to handle advanced setting of plugin
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\SettingsPages\Advanced\Embed;

if ( ! is_admin() ) {
	return;
}

/**
 * Registers actions into WrodPress
 */
function register() {
	add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\Advanced\\Embed\\add' );
}

/**
 * Adds settings field to advance settings page
 */
function add() {
	add_settings_section( 'sgdd_file', esc_html__( 'File embed settings', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\Advanced\\Embed\\display', 'sgdd_advanced' );
	add_settings_section( 'sgdd_folder', esc_html__( 'Folder embed settings', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\Advanced\\Embed\\display', 'sgdd_advanced' );
	\Sgdd\Admin\Options\Options::$embed_width->add_field();
	\Sgdd\Admin\Options\Options::$embed_height->add_field();
	\Sgdd\Admin\Options\Options::$folder_type->add_field();
	\Sgdd\Admin\Options\Options::$order_by->add_field();
	\Sgdd\Admin\Options\Options::$list_width->add_field();
	\Sgdd\Admin\Options\Options::$grid_cols->add_field();
}

function display() {
	settings_errors();
}
