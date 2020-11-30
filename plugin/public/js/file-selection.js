"use strict";

const SgddFileSelection = function(props) {
  this.props = props;
  this.state = { error: undefined, list: undefined };
};

SgddFileSelection.prototype = Object.create(wp.element.Component.prototype);
SgddFileSelection.prototype.componentDidMount = function() {
  this.ajax();
};

SgddFileSelection.prototype.ajax = function() {
  const that = this;
  jQuery.ajax({
    url: sgddBlockJsLocalize.ajaxUrl, //eslint-disable-line no-undef
    type: "GET",
    data: {
      action: "selectFile",
      namesPath: this.getAttribute("namesPath"),
      idsPath: this.getAttribute("idsPath"),
      _ajax_nonce: sgddBlockJsLocalize.nonce // eslint-disable-line camelcase, no-undef
    },
    success(response) {
      if (response.error) {
        that.setState({ error: response.error });
      } else {
        that.setState({ list: response });
      }
    },
    error(response) {
      that.setState({ error: response.error });
    }
  });
};

SgddFileSelection.prototype.render = function() {
  const el = wp.element.createElement;
  const that = this;
  const children = [];
  const namesPath = [
    el(
      "label",
      {
        className: "sgdd-block-settings-folder",
        key: "path-root",
        onClick(e) {
          that.pathClick(that, e);
        }
      },
      sgddBlockJsLocalize.root //eslint-disable-line no-undef
    )
  ];

  if (this.state.error) {
    return el(
      "div",
      { className: "notice notice-error" },
      el("p", {}, this.state.error)
    );
  }

  if (this.state.list) {
    for (let i = 0; i < this.state.list.length; i++) {
      const id = this.state.list[i].fileId;

      if (this.state.list[i].folder) {
        children.unshift(
          el(
            "tr",
            { className: "sgdd-block-settings-folder", key: "tr-" + id },
            el(
              "td",
              { key: "td-" + id },
              el(
                "label",
                {
                  onClick(e) {
                    that.folderClick(that, e, id);
                  }
                },
                this.state.list[i].fileName
              )
            )
          )
        );
      } else {
        var style = "sgdd-block-settings-file";
        if (id === this.getAttribute("fileId")) {
          style += "-selected";
        }
        children.push(
          el(
            "tr",
            { className: style, key: "tr-" + id },
            el(
              "td",
              { key: "td-" + id },
              el(
                "label",
                {
                  onClick() {
                    that.fileClick(that, id);
                  }
                },
                this.state.list[i].fileName
              )
            )
          )
        );
      }
    }

    for (let i = 0, len = this.getAttribute("namesPath").length; i < len; i++) {
      namesPath.push(" > ");
      namesPath.push(
        el(
          "label",
          {
            "data-id": this.getAttribute("namesPath")[i],
            className: "sgdd-block-settings-folder",
            key: "path-" + i,
            onClick(e) {
              that.pathClick(that, e);
            }
          },
          this.getAttribute("namesPath")[i]
        )
      );
    }

    if (0 < this.getAttribute("namesPath").length) {
      children.unshift(
        el(
          "tr",
          { className: "sgdd-block-settings-folder-up", key: "tr-path-up" },
          el(
            "td",
            { key: "td-path-up" },
            el(
              "label",
              {
                onClick() {
                  that.upClick(that);
                }
              },
              ".."
            )
          )
        )
      );
    }
  }

  return el(wp.element.Fragment, {}, [
    el(
      wp.blockEditor.InspectorControls,
      { key: "ic" },
      el(SgddInspector, { block: this }) //eslint-disable-line no-undef
    ),
    el("table", { className: "widefat fixed striped", key: "table" }, [
      el(
        "thead",
        { key: "thead" },
        el("tr", { key: "tr-thead" }, el("th", { key: "th-thead" }, namesPath))
      ),

      el("tbody", { key: "tbody" }, children),

      el(
        "tfoot",
        { key: "tfoot" },
        el("tr", { key: "tr-tfoot" }, el("th", { key: "th-tfoot" }, namesPath))
      )
    ])
  ]);
};

SgddFileSelection.prototype.getAttribute = function(name) {
  return this.props.attributes[name];
};

SgddFileSelection.prototype.setAttribute = function(name, value) {
  const attr = {};
  attr[name] = value;
  this.props.setAttributes(attr);
};

SgddFileSelection.prototype.upClick = function(that) {
  let namesPath;
  let idsPath;
  let folderId;

  if (that.getAttribute("namesPath").length === 1) {
    namesPath = [];
  } else {
    namesPath = that
      .getAttribute("namesPath")
      .slice(0, that.getAttribute("namesPath").length - 1);
  }

  if (that.getAttribute("idsPath").length === 1) {
    idsPath = [];
  } else {
    idsPath = that
      .getAttribute("idsPath")
      .slice(0, that.getAttribute("idsPath").length - 1);
  }

  if (idsPath.length < 1) {
    folderId = "";
  } else {
    folderId = idsPath[idsPath.length - 1];
  }

  that.setAttribute("namesPath", namesPath);
  that.setAttribute("idsPath", idsPath);
  that.setAttribute("folder", "true");
  that.setAttribute("folderId", folderId);
  that.setAttribute("fileId", "");
  that.setState({ error: undefined, list: undefined }, that.ajax);
};

SgddFileSelection.prototype.pathClick = function(that, e) {
  const $ = jQuery;
  let namesPath = that.getAttribute("namesPath");
  let idsPath = that.getAttribute("idsPath");

  namesPath = namesPath.slice(
    0,
    namesPath.indexOf($(e.currentTarget).data("id")) + 1
  );
  idsPath = idsPath.slice(
    0,
    namesPath.indexOf($(e.currentTarget).data("id")) + 1
  );

  that.setAttribute("namesPath", namesPath);
  that.setAttribute("idsPath", idsPath);
  that.setAttribute("folder", "true");
  that.setAttribute("folderId", idsPath[idsPath.length - 1]);
  that.setAttribute("fileId", "");
  that.setState({ error: undefined, list: undefined }, that.ajax);
};

SgddFileSelection.prototype.folderClick = function(that, e, id) {
  const newFolder = jQuery(e.currentTarget).html();
  const namesPath = that.getAttribute("namesPath").concat(newFolder);
  const idsPath = that.getAttribute("idsPath").concat(id);

  that.setAttribute("namesPath", namesPath);
  that.setAttribute("idsPath", idsPath);
  that.setAttribute("folderId", id);
  that.setAttribute("folder", "true");
  that.setAttribute("fileId", "");
  that.setState({ error: undefined, list: undefined }, that.ajax);
};

SgddFileSelection.prototype.fileClick = function(that, fileId) {
  that.setAttribute("fileId", fileId);
  that.setAttribute("folder", "false");
  that.setState({ error: undefined, list: undefined }, that.ajax);
};
