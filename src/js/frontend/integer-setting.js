'use strict';

// eslint-disable-next-line no-redeclare
const SgddIntegerSetting = function ( attributes ) {
	SgddSettingsBase.call( this, attributes );
};

SgddIntegerSetting.prototype = Object.create( SgddSettingsBase.prototype );
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
		placeholder: sgddBlockJsLocalize[ this.name ][ 1 ],
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
