'use strict';

wp.blocks.registerBlockType( 'sakut-google-drive-documents/block', {
  title: 'SGDD Block',
  description: 'SGDD Test Block',
  icon: 'welcome-add-page',
  category: 'common',

  attributes: {
    content: {
      type: 'array',
      source: 'children',
      selector: 'p',
    }
  },

  edit: function( props ) {
    return wp.element.createElement( wp.blocks.RichText, { 
      tagName: 'p',
      className: props.className,
      value: props.attributes.content,
      onChange: function( newContent ) {
        props.setAttributes( { content: newContent } );
      }
    } );
  },

  save: function( props ) {}
});