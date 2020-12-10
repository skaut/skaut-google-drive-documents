'use strict';

const SgddSelectSetting = function ( attributes ) {
	SgddSettingsBase.call( this, attributes ); //eslint-disable-line no-undef
};
SgddSelectSetting.prototype = Object.create( SgddSettingsBase.prototype ); //eslint-disable-line no-undef
SgddSelectSetting.prototype.renderInput = function () {
	const that = this;
	const value = this.block.getAttribute( this.name );
	const el = wp.element.createElement;

	var inputs;
	switch ( this.name ) {
		case 'orderBy':
			var inputs = [
				[ 'name_asc', sgddBlockJsLocalize.name_asc ], //eslint-disable-line no-undef
				[ 'name_dsc', sgddBlockJsLocalize.name_dsc ], //eslint-disable-line no-undef
				[ 'time_asc', sgddBlockJsLocalize.time_asc ], //eslint-disable-line no-undef
				[ 'time_dsc', sgddBlockJsLocalize.time_dsc ], //eslint-disable-line no-undef
			];
			break;

		case 'folderType':
			var inputs = [
				[ 'list', sgddBlockJsLocalize.list ], //eslint-disable-line no-undef
				[ 'grid', sgddBlockJsLocalize.grid ], //eslint-disable-line no-undef
			];
			break;
	}

	const elements = [];
	for ( const input of inputs ) {
		elements.push(
			el(
				'label',
				{
					className: 'sgdd-block-settings-radio',
					key: this.name + '-' + input[ 0 ] + '-Label',
					htmlFor: this.name + '_' + input[ 0 ],
				},
				[
					el( 'input', {
						key: this.name + '-' + input[ 0 ] + '-Input',
						checked: input[ 0 ] === this.state.value,
						disabled: undefined === value,
						id: this.name + '_' + input[ 0 ],
						name: this.name,
						onChange( e ) {
							that.change( e );
						},
						type: 'radio',
						value: input[ 0 ],
					} ),
					input[ 1 ],
				]
			)
		);
	}

	return el(
		'div',
		{
			className: 'sgdd-block-settings',
			key: this.name + 'Div',
		},
		elements
	);
};

SgddSelectSetting.prototype.change = function ( e ) {
	this.setState( { value: e.target.value } );
	this.block.setAttribute( this.name, e.target.value );
};
