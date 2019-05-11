<?php
namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-settingfield.php';

class IntegerField extends SettingField {
	public function register() {
		register_setting(
			$this->page,
			$this->id,
			[
				'type'              => 'integer',
				'sanitize_callback' => [ $this, 'sanitize' ],
				'default'           => $this->default_value,
			]
		);
	}

	public function sanitize( $value ) {
		if ( is_numeric( $value ) ) {
			return intval( $value );
		}
		return $this->default_value;
	}

	public function display() {
		echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->default_value ) ) . "' class='regular-text'>";
	}
}
