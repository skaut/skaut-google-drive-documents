"use strict";

const SgddInspector = function(attributes) {
  this.block = attributes.block;
};

SgddInspector.prototype = Object.create(wp.element.Component.prototype);
SgddInspector.prototype.render = function() {
  const el = wp.element.createElement;
  return [
    el(
      wp.components.PanelBody,
      {
        title: "File embed options",
        className: "sgdd-block-settings",
        key: "file-options"
      },
      [
        el(SgddStringSetting, {
          block: this.block,
          name: "embedWidth",
          key: "width"
        }),
        el(SgddStringSetting, {
          block: this.block,
          name: "embedHeight",
          key: "height"
        })
      ]
    ),
    el(
      wp.components.PanelBody,
      {
        title: "Folder embed options",
        className: "sgdd-block-settings",
        key: "folder-options"
      },
      [
        el(SgddStringSetting, {
          block: this.block,
          name: "listWidth",
          key: "list-width"
        }),
        el(SgddIntegerSetting, {
          block: this.block,
          name: "gridCols",
          key: "grid-cols"
        }),
        el(SgddSelectSetting, {
          block: this.block,
          name: "folderType",
          key: "folder-type"
        }),
        el(SgddSelectSetting, {
          block: this.block,
          name: "orderBy",
          key: "order-by"
        }),
        el(SgddButtonSetting, {
          block: this.block,
          name: "setPermissions",
          key: "set-permissions"
        })
      ]
    )
  ];
};
