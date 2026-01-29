import raf from "./utils/raf.js";
import { getDefs } from "./utils/def.js";
import { getAttrConfig } from "./utils/config.js";
import * as componentUtils from "./utils/components.js";

// component
const dataWPComponents = "data-wp",
  dataComponentsInitialized = "wpComponentsInitialized",
  dataInitializedComponentsList = "wpInitializedComponentsList",
  allComponents = {};

const checkComponent = (component) =>
    componentUtils.checkComponent(component, allComponents),
  addComponent = (component) =>
    componentUtils.addComponent(component, allComponents),
  listComponents = (container) =>
    componentUtils.listComponents(container, dataWPComponents);

export function runWPComponents(container) {
  listComponents(container).each(
    raf((_, el) => {
      const $el = jQuery(el),
        getComponentDefs = (widget) =>
          getDefs(widget, $el, dataInitializedComponentsList),
        getDefsPromise = (widget) => getComponentDefs(widget).promise();

      if (!$el.data(dataComponentsInitialized)) {
        $el.data(dataComponentsInitialized, true);

        const components = el.getAttribute(dataWPComponents);
        if (components) {
          const config = getAttrConfig(
            $el,
            (e) => `Config parse for components "${components}" ${e}`
          );

          components.split(":").forEach(
            raf((component) => {
              const def = getComponentDefs(component);
              addComponent(component).then((callback) => {
                def.resolve(callback($el, config, getDefsPromise));
              });
            })
          );
        }
      }
    })
  );
}

export function Component(component, callback) {
  checkComponent(component);

  return allComponents[component].resolve(callback);
}
