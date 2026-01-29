export function getDefs(widget, $el, listAttr) {
  const defs = $el.data(listAttr) || {};

  if (!defs[widget]) {
    defs[widget] = jQuery.Deferred();
    $el.data(listAttr, defs);
  }

  return defs[widget];
}
