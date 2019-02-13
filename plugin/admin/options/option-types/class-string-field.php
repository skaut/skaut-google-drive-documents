<?php

namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-setting-field.php';

class StringField extends SettingField {
  private $readOnly;

  public function __construct( $id, $title, $page, $section, $default_value ) {
    parent::__construct( $id, $title, $page, $section, $default_value );
    $this->readOnly = false;
  }

  public function register() {
    register_setting( $this->page, $this->id, [ 'type' => 'string', 'sanitize_callback' => [ $this, 'sanitize'], 'default' => $this->default_value ] );
  }

  public function sanitize( $value ) {
    return esc_html( $value );
  }

  public function add_field( $value = false ) {
    $this->readOnly = $value;
    parent::add_field();
  }

  public function display() {
    if ( $this->readOnly ) {
      echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->default_value ) ) . "' readonly class='regular-text'>";
    } else {
      echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->default_value ) ) . "' class='regular-text'>";
    }
  }
}