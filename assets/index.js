import "./js/import-components.js";
import { Header } from "./js/layout/header.js";
import FormValidate from "./js/helper/form-validate.js";
import { runWPComponents } from "./js/component.js";

class App {
  constructor() {
    this.init();
  }

  init() {
    Header();
    this.getComponents();
    this.resolveComponents();
    new FormValidate();
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
