'use strict';
var el = wp.element.createElement;

var SgddIntegerSetting = function( attributes ) {
	SgddIntegerSetting.call( this, attributes );
};
SgddIntegerSetting.prototype = Object.create( SgdgSettingsComponent.prototype );
SgddIntegerSetting.prototype.renderInput = function() {
	var that = this;
	var value = this.block.getAttribute( this.name );
	return el( 'input', {className: 'sgdg-block-settings-integer components-range-control__number', disabled: undefined === value, onChange: function( e ) {
			that.change( e );
		}, placeholder: sgdgBlockLocalize[this.name].default, type: 'number', value: this.state.value});
};
SgddIntegerSetting.prototype.getValue = function( element ) {
	var value = parseInt( element.value );
	if ( isNaN( value ) ) {
		return undefined;
	}
	return value;
};
