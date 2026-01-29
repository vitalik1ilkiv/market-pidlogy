import $ from "../helper/jquery.js";
import { Component } from "../component.js";
import GetBodyScroll from "../helper/bodyscroll.js";

Component("custom-modal", ($el) => {
  new CustomModal($el);
});

class CustomModal {
  constructor($el) {
    this.el = $el[0] || $el;
    this.init();
  }

  init() {
    let win = $(window);
    let width = win.width();
    let resizeFlag = false;
    const modal = $("#" + $(this.el).data("id"));

    $(this.el).on("click", function () {
      modal.arcticmodal({
        css: {
          backgroundColor: "#141414",
          opacity: 0.5,
        },

        beforeOpen: function () {
          GetBodyScroll.disable();
        },

        afterOpen: function () {
          win.resize(function () {
            if (resizeFlag) {
              if (win.width() == width) return;
              width = win.width();
              $.arcticmodal("close");
            }
          });

          setTimeout(function () {
            resizeFlag = true;
          }, 500);
        },

        afterClose: function () {
          resizeFlag = false;
          GetBodyScroll.enable();
        },
      });
    });
  }
}
