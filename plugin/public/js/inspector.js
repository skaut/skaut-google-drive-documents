'use strict';

var el = wp.element.createElement;
var SgddInspector = function( attributes ) {
	this.block = attributes.block;
};

SgddInspector.prototype = Object.create( wp.element.Component.prototype );
SgddInspector.prototype.render = function() {
	return el( wp.components.PanelBody, {title: 'Embed size', className: 'sgdd-block-settings'}, [
		el( 'h3', {}, 'Width' ),
		el( 'h3', {}, 'Height' )
	]);
};
