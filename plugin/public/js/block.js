'use strict';

var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType;

registerBlockType( 'sakut-google-drive-documents/block', {
  title: sgddBlockJsLocalize.blockName,
  description: sgddBlockJsLocalize.blockDescription,
  icon: 'welcome-add-page',
  category: 'common',
  keywords: [ 'docs', 'documents', 'drive' ],

  attributes: {
    namesPath: {
      type: 'array',
      default: []
    },
    idsPath: {
      type: 'array',
      default: []
    },
    fileId: {
      type: 'string',
      default: undefined
    },
    embedWidth: {
      type: 'int',
      default: undefined
    },
    embedHeight: {
      type: 'int',
      default: undefined
    },
    folder: {
      type: 'string',
      default: 'false'
    }
  },

  edit: SgddFileSelection,
  save: renderFrontend
});

function renderFrontend( props ) {
  return null;
}
