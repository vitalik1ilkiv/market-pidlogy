export function getAttrConfig(
  $element,
  errorTemplate,
  configAttr = "data-wp-config"
) {
  let config = {};

  if ($element[0].hasAttribute(configAttr)) {
    // Використовуємо прямий getAttribute і JSON.parse замість jQuery .data()
    // щоб уникнути автоматичної трансформації даних jQuery
    try {
      const configStr = $element[0].getAttribute(configAttr);
      config = configStr ? JSON.parse(configStr) : {};
    } catch (e) {
      window.console.error(errorTemplate ? errorTemplate(e) : e);
      // Fallback на jQuery .data() якщо JSON.parse не вдався
      config = $element.data(configAttr.substr(5)) || {};
    }
  } else if (
    $element[0].nodeName === "SCRIPT" &&
    $element[0].type === "application/json"
  ) {
    try {
      config = JSON.parse($element[0].textContent);
    } catch (e) {
      window.console.error(errorTemplate ? errorTemplate(e) : e);
    }
  }

  return config;
}
