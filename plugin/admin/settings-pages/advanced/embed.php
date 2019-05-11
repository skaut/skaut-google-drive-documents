<?php
namespace Sgdd\Admin\SettingsPages\Advanced\Embed;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\Advanced\\Embed\\add' );
}

function add() {
	add_settings_section( 'sgdd_embed', esc_html__( 'Embed', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\Advanced\\Embed\\display', 'sgdd_advanced' );
	\Sgdd\Admin\Options\Options::$embed_width->add_field();
	\Sgdd\Admin\Options\Options::$embed_height->add_field();
	\Sgdd\Admin\Options\Options::$size_unit->add_field();
}

function display() {
	settings_errors();
}
