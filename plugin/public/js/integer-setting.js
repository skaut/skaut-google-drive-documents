'use strict';
var el = wp.element.createElement;

var SgddIntegerSetting = function( attributes ) {
	SgddSettingsBase.call( this, attributes );
};
SgddIntegerSetting.prototype = Object.create( SgddSettingsBase.prototype );
SgddIntegerSetting.prototype.renderInput = function() {
	var that = this;
	var value = this.block.getAttribute( this.name );
	return el( 'input', {className: 'sgdd-block-settings-integer components-range-control__number', disabled: undefined === value, onChange: function( e ) {
			that.change( e );
		}, placeholder: sgddBlockJsLocalize[this.name][1], type: 'number', value: this.state.value});
};
SgddIntegerSetting.prototype.getValue = function( element ) {
	var value = parseInt( element.value );
	if ( isNaN( value ) ) {
		return undefined;
	}
	return value;
};
