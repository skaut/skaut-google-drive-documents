'use strict';

const SgddSettingsBase = function ( attributes ) {
	this.block = attributes.block;
	this.name = attributes.name;

	const value =
		undefined === this.block.getAttribute( this.name )
			? sgddBlockJsLocalize[ this.name ][ 1 ] //eslint-disable-line no-undef
			: this.block.getAttribute( this.name );

	this.state = { value };
};

SgddSettingsBase.prototype = Object.create( wp.element.Component.prototype );
SgddSettingsBase.prototype.render = function () {
	const el = wp.element.createElement;
	const that = this;
	const value = this.block.getAttribute( this.name );

	return el( 'div', { className: 'sgdd-block-settings-row' }, [
		el( wp.components.ToggleControl, {
			checked: undefined !== value,
			className: 'sgdd-block-settings-checkbox',
			key: 'checkbox',
			onChange() {
				that.toggle();
			},
		} ),
		el(
			'span',
			{ className: 'sgdd-block-settings-description', key: 'desc' },
			[
				sgddBlockJsLocalize[ this.name ][ 0 ], //eslint-disable-line no-undef
				':',
			]
		),
		this.renderInput(),
	] );
};

SgddSettingsBase.prototype.toggle = function () {
	this.block.setAttribute(
		this.name,
		undefined !== this.block.getAttribute( this.name )
			? undefined
			: this.state.value
	);
};

SgddSettingsBase.prototype.change = function ( e ) {
	const value = this.getValue( e.target );
	this.setState( { value } );
	this.block.setAttribute(
		this.name,
		undefined === value ? sgddBlockLocalize[ this.name ][ 1 ] : value //eslint-disable-line no-undef
	);
};
