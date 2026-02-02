import { Component } from "../component.js";
import { showToast } from "../helper/toast.js";

const STORAGE_KEY = "mp_favorites";

function getFavorites() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
  } catch {
    return [];
  }
}

function saveFavorites(ids) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
  document.cookie =
    STORAGE_KEY + "=" + encodeURIComponent(ids.join(",")) + ";path=/;max-age=31536000";
}

function toggleFavorite(id) {
  const favs = getFavorites();
  const index = favs.indexOf(id);

  if (index === -1) {
    favs.push(id);
  } else {
    favs.splice(index, 1);
  }

  saveFavorites(favs);
  return index === -1;
}

function updateAllButtons() {
  const favs = getFavorites();

  document.querySelectorAll(".button-favorite").forEach((btn) => {
    const id = Number(
      btn.closest("[data-product-id]")?.dataset.productId,
    );
    btn.classList.toggle("_active", favs.includes(id));
  });

  updateHeaderCount(favs.length);
}

function updateHeaderCount(count) {
  document
    .querySelectorAll(".action-icon-favorite .icon-count")
    .forEach((el) => {
      el.textContent = count;
      el.style.display = count > 0 ? "" : "none";
    });
}

function getProductInfo(el) {
  const card = el.closest(".product-item");
  if (!card) return {};

  const img = card.querySelector(".product-item__image img");
  const name = card.querySelector(".product-item__name");
  const link = card.querySelector(".product-item__body");

  return {
    image: img?.dataset.src || img?.src || "",
    title: name?.textContent?.trim() || "",
    link: link?.getAttribute("href") || "",
  };
}

document.addEventListener("DOMContentLoaded", () => {
  // sync cookie on first load
  const favs = getFavorites();
  document.cookie =
    STORAGE_KEY + "=" + encodeURIComponent(favs.join(",")) + ";path=/;max-age=31536000";
  updateAllButtons();
});

Component("favorites", ($el) => {
  $el.on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const btnEl = $el[0];
    const productId = Number(
      $el.closest("[data-product-id]").attr("data-product-id"),
    );

    if (!productId) return;

    const added = toggleFavorite(productId);
    updateAllButtons();

    const info = getProductInfo(btnEl);
    showToast({
      image: info.image,
      title: info.title,
      message: added ? "додано в улюблене" : "видалено з улюбленого",
      link: info.link,
    });
  });
});
