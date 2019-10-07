'use strict';
var el = wp.element.createElement;

var SgddSelectSetting = function( attributes ) {
	SgddSettingsBase.call( this, attributes );
};
SgddSelectSetting.prototype = Object.create( SgddSettingsBase.prototype );
SgddSelectSetting.prototype.renderInput = function() {
	var that = this;
	var value = this.block.getAttribute( this.name );
	return el( 'div', {className: 'sgdd-block-folder-type'}, [
		el( 'label', {className: 'sgdd-block-settings-radio', key: this.name + 'List', htmlFor: this.name + '_list'}, [
			el( 'input', {key: this.name + 'ListInput', checked: 'list' === this.state.value, disabled: undefined === value, id: this.name + '_list', name: this.name, onChange: function( e ) {
			that.change( e );
		}, type: 'radio', value: 'list'}),
			sgddBlockJsLocalize.list
		]),
		el( 'label', {className: 'sgdd-block-settings-radio', key: this.name + 'Grid', htmlFor: this.name + '_grid'}, [
			el( 'input', {key: this.name + 'GridInput', checked: 'grid' === this.state.value, disabled: undefined === value, id: this.name + '_grid', name: this.name, onChange: function( e ) {
			that.change( e );
		}, type: 'radio', value: 'grid'}),
			sgddBlockJsLocalize.grid
		])
	]);
};

SgddSelectSetting.prototype.change = function( e ) {
	this.setState({value: e.target.value});
	this.block.setAttribute( this.name, e.target.value );
};
