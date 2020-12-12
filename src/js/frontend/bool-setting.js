'use strict';

const SgddBoolSetting = function ( attributes ) {
	SgddSettingsBase.call( this, attributes );
};

SgddBoolSetting.prototype = Object.create( SgddSettingsBase.prototype );
SgddBoolSetting.prototype.renderInput = function () {
	const that = this;
	const value = this.block.getAttribute( this.name );
	const el = wp.element.createElement;
	return el( 'input', {
		checked: 'true' === this.state.value,
		className: 'sgdd-block-settings-boolean',
		disabled: undefined === value,
		onChange( e ) {
			that.change( e );
		},
		type: 'checkbox',
	} );
};
SgddBoolSetting.prototype.getValue = function ( element ) {
	return element.checked ? 'true' : 'false';
};
