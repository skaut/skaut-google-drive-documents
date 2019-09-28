<?php
namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-settingfield.php';

class SelectField extends SettingField {
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

	public function sanitize( $value ) {
		if ( 'pixels' === $value ) {
			return 'pixels';
		}

		if ( 'percentage' === $value ) {
			return 'percentage';
		}

		return $this->default_value;
	}

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
