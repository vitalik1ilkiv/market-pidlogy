import $ from "../helper/jquery.js";

export function checkComponent(component, allComponents) {
  if (!allComponents[component]) {
    allComponents[component] = $.Deferred();
  }
}

export function addComponent(component, allComponents) {
  checkComponent(component, allComponents);

  return allComponents[component].promise();
}

export function listComponents(container, dataName) {
  const $container = jQuery(container),
    $elements = $container.find(`[${dataName}]`);

  if ($container[0] && $container[0].hasAttribute(dataName)) {
    $elements.splice(0, 0, $container[0]);
  }

  return $elements;
}
