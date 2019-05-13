'use strict';
var el = wp.element.createElement;

var SgddSettingsBase = function( attributes ) {
	var value;
	this.block = attributes.block;
	this.name = attributes.name;
	value = this.block.getAttribute( this.name );
	if ( undefined === value ) {
    value = sgddBlockJsLocalize[this.name][1];
	}
	this.state = {value: value};
};

SgddSettingsBase.prototype = Object.create( wp.element.Component.prototype );
SgddSettingsBase.prototype.render = function() {
	var that = this;
	var value = this.block.getAttribute( this.name );
	return el( 'div', {className: 'sgdd-block-settings-row'}, [
		el( wp.components.ToggleControl, {checked: undefined !== value, className: 'sgdd-block-settings-checkbox', onChange: function( e ) {
			that.toggle();
		}}),
		el( 'span', {className: 'sgdd-block-settings-description'}, [
      sgddBlockJsLocalize[this.name][0],
			':'
		]),
		this.renderInput()
	]);
};

SgddSettingsBase.prototype.toggle = function() {
	this.block.setAttribute( this.name, undefined !== this.block.getAttribute( this.name ) ? undefined : this.state.value );
};

SgddSettingsBase.prototype.change = function( e ) {
	var value = this.getValue( e.target );
	this.setState({value: value});
  this.block.setAttribute( this.name, undefined === value ? sgddBlockLocalize[this.name][1] : value );
};

SgddSettingsBase.prototype.setIdtesttttt = function() {
	var currentPath = sgddBlockLocalize[this.name];
	console.log( currentPath );

};