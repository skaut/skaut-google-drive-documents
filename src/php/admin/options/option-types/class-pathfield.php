<?php
/**
 * PathField class
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-settingfield.php';

/**
 * An option containing root folder id.
 *
 * @see SettingField
 */
class PathField extends SettingField {
	/**
	 * Register option into WordPress.
	 */
	public function register() {
		register_setting(
			$this->page,
			$this->setting_id,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => $this->default_value,
			)
		);
	}

	/**
	 * Sanitize the input.
	 *
	 * @param string|null $value The unsanitized input.
	 *
	 * @return int Sanitized value.
	 */
	public function sanitize( $value ) {
		if ( is_string( $value ) ) {
			return intval( $value );
		}

		if ( null === $value ) {
			return $this->default_value;
		}

		return $value;
	}

	/**
	 * Display field for updating the option
	 */
	public function display() {
		echo "<input id='" . esc_attr( $this->setting_id ) . "' type='hidden' name='" . esc_attr( $this->setting_id ) . "' value='" . esc_attr( wp_json_encode( $this->get(), JSON_UNESCAPED_UNICODE ) ) . "'>";
	}
}
