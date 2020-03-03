"use strict";

const registerBlockType = wp.blocks.registerBlockType;

registerBlockType("skaut-google-drive-documents/block", {
  title: sgddBlockJsLocalize.blockName, //eslint-disable-line no-undef
  description: sgddBlockJsLocalize.blockDescription, //eslint-disable-line no-undef
  icon: "welcome-add-page",
  category: "common",
  keywords: ["docs", "documents", "drive"],

  attributes: {
    namesPath: {
      type: "array",
      default: []
    },
    idsPath: {
      type: "array",
      default: []
    },
    fileId: {
      type: "string",
      default: ""
    },
    folderId: {
      type: "string",
      default: ""
    },
    embedWidth: {
      type: "int",
      default: undefined
    },
    embedHeight: {
      type: "int",
      default: undefined
    },
    listWidth: {
      type: "int",
      default: undefined
    },
    gridCols: {
      type: "int",
      default: undefined
    },
    folderType: {
      type: "string",
      default: undefined
    }
  },

  edit: SgddFileSelection, //eslint-disable-line no-undef
  save: renderFrontend
});

function renderFrontend() {
  return null;
}
