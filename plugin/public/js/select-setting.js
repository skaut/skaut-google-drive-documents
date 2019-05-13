'use strict';
var el = wp.element.createElement;

var SgddSelectSetting = function( attributes ) {
	SgddSettingsBase.call( this, attributes );
};
SgddSelectSetting.prototype = Object.create( SgddSettingsBase.prototype );
SgddSelectSetting.prototype.renderInput = function() {
	var that = this;
	var value = this.block.getAttribute( this.name );
	return el( 'div', {className: 'sgdd-block-units'}, [
		el( 'label', {className: 'sgdd-block-settings-radio', for: this.name + '_px'}, [
			el( 'input', {checked: 'pixels' === this.state.value, disabled: undefined === value, id: this.name + '_px', name: this.name, onChange: function( e ) {
			that.change( e );
		}, type: 'radio', value: 'pixels'}),
			sgddBlockJsLocalize['pixels']
		]),
		el( 'label', {className: 'sgdd-block-settings-radio', for: this.name + '_per'}, [
			el( 'input', {checked: 'percentage' === this.state.value, disabled: undefined === value, id: this.name + '_per', name: this.name, onChange: function( e ) {
			that.change( e );
		}, type: 'radio', value: 'percentage'}),
			sgddBlockJsLocalize['percentage']
		])
	]);
};

SgddSelectSetting.prototype.change = function( e ) {
	this.setState({value: e.target.value});
	this.block.setAttribute( this.name, e.target.value );
};
