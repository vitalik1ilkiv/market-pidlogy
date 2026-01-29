import $ from "../helper/jquery.js";
import { getResize } from "../helper/breakpoints.js";
import { Component } from "../component.js";

Component("dropdown", ($el, props) => {
  new Dropdown($el, props);
});

class Dropdown {
  static defaultProps = {
    settings: {
      head: "[data-head]",
      headName: "[data-head-name]",
      content: "[data-content]",
      item: "[data-item]",
      select: "[data-select]",
      slideToggleOnBreakpoint: null,
      hideAfterClick: true,
      resultInfo: false,
    },
  };

  constructor($el, props) {
    this.props = {
      settings: {
        ...Dropdown.defaultProps.settings,
        ...props.settings,
      },
    };

    this.window = $(window);
    this.dropdown = $el;
    this.head = this.dropdown.find(this.props.settings.head);
    this.headName = this.dropdown.find(this.props.settings.headName);
    this.content = this.dropdown.find(this.props.settings.content);
    this.item = this.dropdown.find(this.props.settings.item);
    // Шукаємо select спочатку всередині дропдауна, якщо не знайдено - шукаємо глобально
    this.select = this.dropdown.find(this.props.settings.select);
    if (!this.select.length) {
      this.select = $(this.props.settings.select);
    }

    console.log(" this.select", this.select);
    this.init();
    this.initSelected();
  }

  init() {
    let self = this,
      easeResize = null;

    this.head.on("click", function () {
      if (!self.props.settings.slideToggleOnBreakpoint) {
        self.content.stop(true, true).slideToggle(300);
        self.dropdown.toggleClass("_active");
      } else {
        const breakpoint = getResize(
          self.props.settings.slideToggleOnBreakpoint,
        );
        breakpoint.addListener(self.mediaSize.bind(self, breakpoint));
        self.mediaSize(breakpoint);
      }
    });

    this.dropdown.on("blur", function () {
      self.hideContent();
    });

    this.item.on("click", function () {
      let value = $(this).data("value");

      $(this).addClass("_current").siblings().removeClass("_current");

      self.headName.html($(this).text());

      self.selectChange(value);

      if (self.props.settings.hideAfterClick) {
        self.hideContent();
      }

      if (self.props.settings.resultInfo) {
        let result = $(this).data("result");
        self.dropdown.next("[data-dropdown-result]")?.html(result);
      }
    });

    this.window.onResize = function () {
      clearTimeout(easeResize);
      easeResize = setTimeout(function () {
        if (self.dropdown.hasClass("_active")) {
          self.hideContent();
        }
      }, 100);
    };

    // Слухаємо зміни на select (наприклад, від WooCommerce)
    if (this.select.length) {
      // jQuery event
      this.select.on("change", function () {
        if (self._internalChange) return;
        console.log("dropdown: select change (jQuery)", self.select.val());
        self.syncFromSelect();
      });

      // Native event (якщо WooCommerce використовує нативний)
      this.select[0].addEventListener("change", function () {
        if (self._internalChange) return;
        console.log("dropdown: select change (native)", self.select.val());
        self.syncFromSelect();
      });
    }
  }

  mediaSize(breakpoint) {
    if (breakpoint.matches) {
      this.content.stop(true, true).slideToggle(300);
      this.dropdown.toggleClass("_active");
    }
  }

  hideContent() {
    if (this.dropdown.hasClass("_active")) {
      this.head.trigger("click");
    }
  }

  selectChange(value) {
    // Конвертуємо в рядок, бо jQuery .data() повертає число для числових значень
    value = String(value ?? "");
    if (this.select.length && value) {
      this._internalChange = true;
      this.select.val(value);

      let select = document.querySelector(this.props.settings.select);

      let event = new Event("change", {
        bubbles: true,
        cancelable: true,
      });

      select.dispatchEvent(event);
      this._internalChange = false;
    }
  }

  initSelected() {
    if (!this.select.length) return;

    const value = this.select.val();
    if (!value) return;

    // Шукаємо item за атрибутом (attr працює з рядками, на відміну від data)
    const currentItem = this.item.filter(function () {
      return $(this).attr("data-value") == value;
    });

    if (currentItem.length) {
      currentItem.addClass("_current").siblings().removeClass("_current");
      this.headName.html(currentItem.text());
    }
  }

  syncFromSelect() {
    if (!this.select.length) return;

    const value = this.select.val();

    // Шукаємо item за атрибутом
    const currentItem = value
      ? this.item.filter(function () {
          return $(this).attr("data-value") == value;
        })
      : $();

    if (currentItem.length) {
      currentItem.addClass("_current").siblings().removeClass("_current");
      this.headName.html(currentItem.text());
    } else {
      // Якщо значення порожнє або не знайдено - показуємо placeholder
      this.item.removeClass("_current");
      const placeholder = this.select.find("option").first().text();
      this.headName.html(placeholder || "");
    }
  }
}
