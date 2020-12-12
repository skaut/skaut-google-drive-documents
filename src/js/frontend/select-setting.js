'use strict';

// eslint-disable-next-line no-redeclare
const SgddSelectSetting = function ( attributes ) {
	SgddSettingsBase.call( this, attributes );
};
SgddSelectSetting.prototype = Object.create( SgddSettingsBase.prototype );
SgddSelectSetting.prototype.renderInput = function () {
	const that = this;
	const value = this.block.getAttribute( this.name );
	const el = wp.element.createElement;

	let inputs;
	switch ( this.name ) {
		case 'orderBy':
			inputs = [
				[ 'name_asc', sgddBlockJsLocalize.name_asc ],
				[ 'name_dsc', sgddBlockJsLocalize.name_dsc ],
				[ 'time_asc', sgddBlockJsLocalize.time_asc ],
				[ 'time_dsc', sgddBlockJsLocalize.time_dsc ],
			];
			break;

		case 'folderType':
			inputs = [
				[ 'list', sgddBlockJsLocalize.list ],
				[ 'grid', sgddBlockJsLocalize.grid ],
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
