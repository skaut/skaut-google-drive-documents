<?php
namespace Sgdd\Admin\Options\OptionTypes;

require_once __DIR__ . '/class-setting-field.php';

class StringField extends SettingField {
  private $readOnly;
  private $selectable;

  public function __construct( $id, $title, $page, $section, $defaultValue ) {
    parent::__construct( $id, $title, $page, $section, $defaultValue );
    $this->readOnly = false;
    $this->selectable = false;
  }

  public function register() {
    register_setting( $this->page, $this->id, [ 'type' => 'string', 'sanitize_callback' => [ $this, 'sanitize'], 'default' => $this->defaultValue ] );
  }

  public function sanitize( $value ) {
    return esc_html( $value );
  }

  public function addField( $read = false, $sel = false ) {
    $this->readOnly = $read;
    $this->selectable = $sel;
    parent::addField();
  }

  public function display() {
    if ( $this->readOnly && $this->selectable ) {
      echo "<input onClick='this.select();' type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->defaultValue ) ) . "' readonly class='regular-text'>";
    } else if ( $this->readOnly && !$this->selectable ) {
      echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->defaultValue ) ) . "' readonly class='regular-text'>";
    } else {
      echo "<input type='text' name='" . esc_attr( $this->id ) . "' value='" . esc_attr( get_option( $this->id, $this->defaultValue ) ) . "' class='regular-text'>";
    }
  }
}