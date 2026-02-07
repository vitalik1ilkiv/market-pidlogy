import $ from "../helper/jquery.js";
import { showToast } from "../helper/toast.js";

const msg = window.addToCartMessages || {};

$(document).on("click", ".j-ajax-add-to-cart", function (e) {
  e.preventDefault();
  e.stopPropagation();

  const $btn = $(this);
  const productId = $btn.data("product-id");

  if (!productId || $btn.hasClass("loading")) return;

  $btn.addClass("loading");

  $.post(
    `/?wc-ajax=add_to_cart`,
    {
      product_id: productId,
      quantity: 1,
    },
    function (response) {
      $btn.removeClass("loading");

      if (response.error) return;

      // Update cart fragments (cart count in header)
      $(document.body).trigger("wc_fragment_refresh");

      // Get product info for toast
      const $card = $btn.closest(".product-item");
      const $img = $card.find(".product-item__image img");
      const $name = $card.find(".product-item__name");
      const $link = $card.find(".product-item__body");

      showToast({
        image: $img.data("src") || $img.attr("src") || "",
        title: $name.text().trim(),
        message: msg.added,
        link: $link.attr("href") || "",
      });
    },
  );
});
