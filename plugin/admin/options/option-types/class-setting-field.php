<?php
namespace Sgdd\Admin\Options\OptionTypes;

abstract class SettingField {

  protected $id;
  protected $title;
  protected $page;
  protected $section;
  protected $defaultValue;

  public function __construct( $id, $title, $page, $section, $defaultValue ) {
    $this->id            = 'sgdd_' . $id;
    $this->title         = $title;
    $this->page          = 'sgdd_' . $page;
    $this->section       = 'sgdd_' . $section;
    $this->defaultValue  = $defaultValue;
  }

  abstract public function register();

  abstract public function sanitize( $value );

  abstract public function display();

  public function addField() {
    $this->register();
    add_settings_field( $this->id, $this->title, [ $this, 'display' ], $this->page, $this->section );
  }

  public function get( $defaultValue = null ) {
    return get_option( $this->id, ( isset( $defaultValue ) ? $defaultValue : $this->defaultValue ) );
  }
};