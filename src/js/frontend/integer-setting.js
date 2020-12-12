'use strict';

// eslint-disable-next-line no-redeclare
const SgddIntegerSetting = function ( attributes ) {
	SgddSettingsBase.call( this, attributes ); //eslint-disable-line no-undef
};

SgddIntegerSetting.prototype = Object.create( SgddSettingsBase.prototype ); //eslint-disable-line no-undef
SgddIntegerSetting.prototype.renderInput = function () {
	const that = this;
	const value = this.block.getAttribute( this.name );
	const el = wp.element.createElement;

	return el( 'input', {
		className:
			'sgdd-block-settings-integer components-range-control__number',
		disabled: undefined === value,
		key: this.name,
		onChange( e ) {
			that.change( e );
		},
		placeholder: sgddBlockJsLocalize[ this.name ][ 1 ], //eslint-disable-line no-undef
		type: 'number',
		value: this.state.value,
	} );
};
SgddIntegerSetting.prototype.getValue = function ( element ) {
	const value = parseInt( element.value );
	if ( isNaN( value ) ) {
		return undefined;
	}
	return value;
};
