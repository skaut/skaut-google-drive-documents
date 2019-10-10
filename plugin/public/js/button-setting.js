'use strict';

var el = wp.element.createElement;
var SgddButtonSetting = function( props ) {
  this.props = props;
  this.name  = props.name;
};
SgddButtonSetting.prototype = Object.create( wp.element.Component.prototype );
SgddButtonSetting.prototype.render = function() {
	var that = this;
  return el( 'div', {className: 'sgdd-block-settings-row'}, [
		el( 'input', {className: 'sgdd-block-settings-button button button-primary', key: 'permButton', type: 'button', value: sgddBlockJsLocalize[this.name], onClick: function( e ) {
      that.ajax();
    }})
	]);
};

SgddButtonSetting.prototype.ajax = function() {
	//var that = this;
	$.ajax({
		url: sgddBlockJsLocalize.ajaxUrl,
		type: 'GET',
		data: {
			action: 'setPermissions',
			fileId: this.getAttribute( 'fileId' ),
      folderId: this.getAttribute( 'folderId' ),
      folderType: this.getAttribute( 'folderType' ),
			_ajax_nonce: sgddBlockJsLocalize.noncePerm // eslint-disable-line camelcase
		},
		beforeSend: function() {},
		success: function( response ) {
			//handle success
			var $el = $(".sgdd-block-settings-button"), originalColor = $el.css("background");

			$el.prop('value', 'Success').css("background-color", "#5cb85c");
			setTimeout( function(){
				$el.prop('value', 'Set permissions').css("background", originalColor);
			}, 3000 );
		},
		error: function( response ) {
			//handle errors
			var $el = $(".sgdd-block-settings-button"), originalColor = $el.css("background");

			$el.prop('value', 'Error').css("background-color", "#d9534f");
			setTimeout( function(){
				$el.prop('value', 'Set permissions').css("background", originalColor);
			}, 3000 );
		}
	});
};

SgddButtonSetting.prototype.getAttribute = function( name ) {
	return this.props.block.props.attributes[name];
};