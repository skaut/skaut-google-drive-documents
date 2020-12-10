<?php
/**
 * StringField class.
 *
 * @package SGDD
 * @since 1.0.0
 */
namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-settingfield.php';

/**
 * An option containig string value.
 *
 * @see SettingField
 */
class StringField extends SettingField {
	private $read_only;
	private $selectable;

	/**
	 * StringField class constructor.
	 *
	 * @param $id An unique name of the option used as key to reference it. Prefix "sgdd_" will be added.
	 * @param @title Name of the option displayed to user.
	 * @param $page Setting page in which the option will be displayed. Prefix "sgdd_" will be added.
	 * @param $section Section within page in which the option will be displayed. Prefix "sgdd_" will be added.
	 * @param $default_value Default valur of option if user do not specify one.
	 */
	public function __construct( $id, $title, $page, $section, $default_value ) {
		parent::__construct( $id, $title, $page, $section, $default_value );
		$this->read_only  = false;
		$this->selectable = false;
	}

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
		return esc_html( $value );
	}

	/**
	 * Adds option into WordPress and specify if it is read-only and select on click.
	 *
	 * @param $read Specify if option will be read-only
	 * @param $sel Specify if value of option should be selected on click.
	 */
	public function add_field( $read = false, $sel = false ) {
		$this->read_only  = $read;
		$this->selectable = $sel;
		parent::add_field();
	}

	/**
	 * Display field for updating the option
	 */
	public function display() {
		if ( $this->read_only && $this->selectable ) {
			echo "<input onClick='this.select();' type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->default_value ) ) . "' readonly class='regular-text'>";
		} elseif ( $this->read_only && ! $this->selectable ) {
			echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->default_value ) ) . "' readonly class='regular-text'>";
		} else {
			echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->default_value ) ) . "' class='regular-text'>";
		}
	}
}
