<?php
/**
 * IntegerField class
 *
 * @package SGDD
 * @since 1.0.0
 */
namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-settingfield.php';

/**
 * An option containing an integer value.
 *
 * @see SettingField
 */
class IntegerField extends SettingField {
	/**
	 * Register option into WordPress.
	 */
	public function register() {
		register_setting(
			$this->page,
			$this->id,
			array(
				'type'              => 'integer',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => $this->default_value,
			)
		);
	}

	/**
	 * Sanitize the input.
	 *
	 * @param $value The unsanitized input.
	 * @return int Sanitized value.
	 */
	public function sanitize( $value ) {
		if ( is_numeric( $value ) ) {
			return intval( $value );
		}
		return $this->default_value;
	}

	/**
	 * Display field for updating the option
	 */
	public function display() {
		echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->default_value ) ) . "' class='regular-text'>";
	}
}
