"use strict";

const SgddSelectSetting = function(attributes) {
  SgddSettingsBase.call(this, attributes); //eslint-disable-line no-undef
};
SgddSelectSetting.prototype = Object.create(SgddSettingsBase.prototype); //eslint-disable-line no-undef
SgddSelectSetting.prototype.renderInput = function() {
  const that = this;
  const value = this.block.getAttribute(this.name);
  const el = wp.element.createElement;

  return el(
    "div",
    { className: "sgdd-block-folder-type", key: "select-setting" },
    [
      el(
        "label",
        {
          className: "sgdd-block-settings-radio",
          key: this.name + "List",
          htmlFor: this.name + "_list"
        },
        [
          el("input", {
            key: this.name + "ListInput",
            checked: "list" === this.state.value,
            disabled: undefined === value,
            id: this.name + "_list",
            name: this.name,
            onChange(e) {
              that.change(e);
            },
            type: "radio",
            value: "list"
          }),
          sgddBlockJsLocalize.list //eslint-disable-line no-undef
        ]
      ),
      el(
        "label",
        {
          className: "sgdd-block-settings-radio",
          key: this.name + "Grid",
          htmlFor: this.name + "_grid"
        },
        [
          el("input", {
            key: this.name + "GridInput",
            checked: "grid" === this.state.value,
            disabled: undefined === value,
            id: this.name + "_grid",
            name: this.name,
            onChange(e) {
              that.change(e);
            },
            type: "radio",
            value: "grid"
          }),
          sgddBlockJsLocalize.grid //eslint-disable-line no-undef
        ]
      )
    ]
  );
};

SgddSelectSetting.prototype.change = function(e) {
  this.setState({ value: e.target.value });
  this.block.setAttribute(this.name, e.target.value);
};
