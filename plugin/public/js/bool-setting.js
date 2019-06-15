'use strict';
var el = wp.element.createElement;

var SgddBoolSetting = function( attributes ) {
	SgddSettingsBase.call( this, attributes );
};
SgddBoolSetting.prototype = Object.create( SgddSettingsBase.prototype );
SgddBoolSetting.prototype.renderInput = function() {
	var that = this;
	var value = this.block.getAttribute( this.name );
	return el( 'input', {checked: 'true' === this.state.value, className: 'sgdd-block-settings-boolean', disabled: undefined === value, onChange: function( e ) {
      that.change( e );
		}, type: 'checkbox'});
};
SgddBoolSetting.prototype.getValue = function( element ) {
	return element.checked ? 'true' : 'false';
};
