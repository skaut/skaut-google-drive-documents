<?php
namespace Sgdd\Admin\Options\OptionTypes;

abstract class SettingField {

	protected $id;
	protected $title;
	protected $page;
	protected $section;
	protected $default_value;

	public function __construct( $id, $title, $page, $section, $default_value ) {
		$this->id            = 'sgdd_' . $id;
		$this->title         = $title;
		$this->page          = 'sgdd_' . $page;
		$this->section       = 'sgdd_' . $section;
		$this->default_value = $default_value;
	}

	abstract public function register();

	abstract public function sanitize( $value );

	abstract public function display();

	public function add_field() {
		$this->register();
		add_settings_field( $this->id, $this->title, [ $this, 'display' ], $this->page, $this->section );
	}

	public function get( $default_value = null ) {
		return get_option( $this->id, ( isset( $default_value ) ? $default_value : $this->default_value ) );
	}
};
