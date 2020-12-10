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
	 * @param $value The unsanitized input.
	 * @return int Sanitized value.
	 */
	public function sanitize( $value ) {
		return esc_html( $value );
	}

	/**
	 * Display field for updating the option
	 */
	public function display() {
		$display = '';
		$inputs;

		// Folder view type
		if ( 'sgdd_folder_type' === $this->id ) {
			$inputs = array(
				array( 'list', __( 'List', 'skaut-google-drive-documents' ) ),
				array( 'grid', __( 'Grid', 'skaut-google-drive-documents' ) ),
			);
		}
		// Order files by
		elseif ( 'sgdd_order_by' === $this->id ) {
			$inputs = array(
				array( 'name_asc', __( 'Name (ascending)', 'skaut-google-drive-documents' ) ),
				array( 'name_dsc', __( 'Name (descending)', 'skaut-google-drive-documents' ) ),
				array( 'time_asc', __( 'Time (ascending)', 'skaut-google-drive-documents' ) ),
				array( 'time_dsc', __( 'Time (descending)', 'skaut-google-drive-documents' ) ),
			);
		}

		foreach ( $inputs as &$input ) {
			if ( $display !== '' ) {
				$display .= '<br>';
			}

			$display .= '<label for="sgdd-' . esc_attr( $this->id ) . '">
					<input type="radio" id="sgdd-' . esc_attr( $this->id ) . '-' . $input[0] . '" name="' . esc_attr( $this->id ) . '" value="' . $input[0] . '" ' . ( $this->get() === $input[0] ? 'checked' : '' ) . '> ' . $input[1] .
				'</label>';
		}

		echo $display;
	}
}
