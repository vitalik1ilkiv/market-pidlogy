import "./js/import-components.js";
import { Header } from "./js/layout/header.js";
import { CustomQuantity } from "./js/helper/custom-quantity.js";
import FormValidate from "./js/helper/form-validate.js";
import { runWPComponents } from "./js/component.js";

class App {
  constructor() {
    this.init();
  }

  init() {
    Header();
    CustomQuantity();
    this.getComponents();
    this.resolveComponents();
    new FormValidate();

    jQuery(document.body).on("updated_cart_totals", function () {
      CustomQuantity();
    });

    jQuery(document.body).on("updated_checkout", function () {
      CustomQuantity();
    });
  }

  getComponents() {
    runWPComponents(document.body);
  }

  resolveComponents() {
    jQuery(window).on("rp.DOMChanged", (e) => runWPComponents(e.target));
  }
}

window.addEventListener("DOMContentLoaded", function () {
  new App();
});
