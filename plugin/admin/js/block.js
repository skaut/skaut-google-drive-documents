'use strict';

var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType,
    blockStyle = { backgroundColor: '#900', color: '#fff', padding: '20px' };

registerBlockType( 'sakut-google-drive-documents/block', {
  title: 'SGDD Block',
  description: 'SGDD Test Block',
  icon: 'welcome-add-page',
  category: 'common',
  keywords: [ 'docs', 'documents', 'drive' ],

  attributes: {
    content: {
      type: 'string',
      source: 'children',
      selector: 'p',
    }
  },

  edit: function() {
    return el( 'p', { style: blockStyle }, 'Hello editor.' );
  },

  save: function() {
      null;
  },
});