<?php

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

  abstract public function print();

  public function add_field() {
    $this->register();
    add_settings_field( $this->id, $this->title, [ $this, 'print' ], $this->page, $this->section );
  }
};