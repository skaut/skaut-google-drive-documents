'use strict';

const SgddStringSetting = function ( attributes ) {
	SgddSettingsBase.call( this, attributes ); //eslint-disable-line no-undef
};

SgddStringSetting.prototype = Object.create( SgddSettingsBase.prototype ); //eslint-disable-line no-undef
SgddStringSetting.prototype.renderInput = function () {
	const that = this;
	const value = this.block.getAttribute( this.name );
	const el = wp.element.createElement;

	return el( 'input', {
		className: 'sgdd-block-settings-string',
		disabled: undefined === value,
		key: this.name,
		onChange( e ) {
			that.change( e );
		},
		placeholder: sgddBlockJsLocalize[ this.name ][ 1 ], //eslint-disable-line no-undef
		type: 'text',
		value: this.state.value,
	} );
};
SgddStringSetting.prototype.getValue = function ( element ) {
	return element.value;
};
