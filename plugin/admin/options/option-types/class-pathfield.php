<?php
namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-settingfield.php';

class PathField extends SettingField {
	public function register() {
		register_setting( $this->page, $this->id, [ 'type' => 'string', 'sanitize_callback' => [ $this, 'sanitize' ], 'default' => $this->default_value ] );
	}

	public function sanitize( $value ) {
		if ( is_string( $value ) ) {
			$value = json_decode( $value, true );
		}

		if ( null === $value ) {
			$value = $this->default_value;
		}

		return $value;
	}

	public function display() {
		echo "<input id='" . esc_attr( $this->id ) . "' type='hidden' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( wp_json_encode( $this->get(), JSON_UNESCAPED_UNICODE ) ) . "'>";
	}
}
