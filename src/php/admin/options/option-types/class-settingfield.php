<?php
/**
 * SettingField class
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\Options\OptionTypes;

/**
 * An interface for plugin options
 */
abstract class SettingField {
	/**
	 * Name of option used as key to reference it.
	 *
	 * @var string $id
	 */
	protected $id;

	/**
	 * Name of option displayed to user.
	 *
	 * @var string $title
	 */
	protected $title;

	/**
	 * Setting page in which the option will be displayed.
	 *
	 * @var string $page
	 */
	protected $page;

	/**
	 * Section within page in which the option will be displayed.
	 *
	 * @var string $section
	 */
	protected $section;

	/**
	 * Default valur of option if user do not specify one.
	 *
	 * @var mixed $default_value
	 */
	protected $default_value;

	/**
	 * SettingField class constructor.
	 *
	 * @param string $id An unique name of the option used as key to reference it. Prefix "sgdd_" will be added.
	 * @param string $title Name of the option displayed to user.
	 * @param string $page Setting page in which the option will be displayed. Prefix "sgdd_" will be added.
	 * @param string $section Section within page in which the option will be displayed. Prefix "sgdd_" will be added.
	 * @param mixed  $default_value Default valur of option if user do not specify one.
	 */
	public function __construct( $id, $title, $page, $section, $default_value ) {
		$this->id            = 'sgdd_' . $id;
		$this->title         = $title;
		$this->page          = 'sgdd_' . $page;
		$this->section       = 'sgdd_' . $section;
		$this->default_value = $default_value;
	}

	/**
	 * Register option into WordPress.
	 */
	abstract public function register();

	/**
	 * Sanitize the input
	 *
	 * @param mixed $value The unsanitized input.
	 * @return mixed The sanitized value to put into database.
	 */
	abstract public function sanitize( $value );

	/**
	 * Display field for updating the option
	 */
	abstract public function display();

	/**
	 * Adds option into WordPress.
	 *
	 * This function adds the option to setting $page in sepcified $section. Output is displayed with display() function.
	 */
	public function add_field() {
		$this->register();
		add_settings_field( $this->id, $this->title, array( $this, 'display' ), $this->page, $this->section );
	}

	/**
	 * Gets value of option
	 *
	 * Gets value of option if not specified the default one is returned.
	 *
	 * @param mixed $default_value Default value to be returned if option is not specified. If null $default_value will be returned.
	 * @return mixed Value of option.
	 */
	public function get( $default_value = null ) {
		return get_option( $this->id, ( isset( $default_value ) ? $default_value : $this->default_value ) );
	}
};
