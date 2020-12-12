'use strict';

// eslint-disable-next-line no-redeclare
const SgddStringSetting = function ( attributes ) {
	SgddSettingsBase.call( this, attributes );
};

SgddStringSetting.prototype = Object.create( SgddSettingsBase.prototype );
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
		placeholder: sgddBlockJsLocalize[ this.name ][ 1 ],
		type: 'text',
		value: this.state.value,
	} );
};
SgddStringSetting.prototype.getValue = function ( element ) {
	return element.value;
};
