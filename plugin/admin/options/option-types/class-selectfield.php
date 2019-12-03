<?php
/**
 * SelectField class
 *
 * @package SGDD
 * @since 1.0.0
 */
namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-settingfield.php';

/**
 * An option containig value from select
 *
 * @see SettingField
 */
class SelectField extends SettingField {
	/**
	 * Register option into WordPress.
	 */
	public function register() {
		register_setting(
			$this->page,
			$this->id,
			[
				'type'              => 'string',
				'sanitize_callback' => [ $this, 'sanitize' ],
				'default'           => $this->default_value,
			]
		);
	}

	/**
	 * Sanitize the input.
	 *
	 * @param $value The unsanitized input.
	 * @return int Sanitized value.
	 */
	public function sanitize( $value ) {
		if ( 'pixels' === $value ) {
			return 'pixels';
		}

		if ( 'percentage' === $value ) {
			return 'percentage';
		}

		return $this->default_value;
	}

	/**
	 * Display field for updating the option
	 */
	public function display() {
		echo '<label for="sgdd-' . esc_attr( $this->id ) . '">
						<input type="radio" id="sgdd-' . esc_attr( $this->id ) . '-list" name="' . esc_attr( $this->id ) . '" value="list" ' . ( $this->get() === 'list' ? 'checked' : '' ) . '> List
					</label>
					<br>
					<label for="sgdd-' . esc_attr( $this->id ) . '">
						<input type="radio" id="sgdd-' . esc_attr( $this->id ) . '-grid" name="' . esc_attr( $this->id ) . '" value="grid" ' . ( $this->get() === 'grid' ? 'checked' : '' ) . '> Grid
					</label>
		';
	}
}
