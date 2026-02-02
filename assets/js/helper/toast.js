const CONTAINER_CLASS = "toast-container";
const TOAST_CLASS = "toast";
const DURATION = 5000;

let container = null;

function getContainer() {
  if (!container || !document.body.contains(container)) {
    container = document.createElement("div");
    container.className = CONTAINER_CLASS;
    document.body.appendChild(container);
  }
  return container;
}

/**
 * showToast({
 *   image:   "/path/to/img.jpg",   // optional
 *   title:   "Паркет штучний Дуб", // product name
 *   message: "додано в улюблене",   // action text
 *   link:    "/product-url",        // optional — wraps title
 * })
 */
export function showToast({ image, title, message, link }) {
  const wrap = getContainer();

  const toast = document.createElement("div");
  toast.className = TOAST_CLASS;

  let imgHTML = "";
  if (image) {
    imgHTML = `<img class="${TOAST_CLASS}__image" src="${image}" alt="" />`;
  }

  let titleHTML = `<strong>${title}</strong>`;
  if (link) {
    titleHTML = `<a href="${link}"><strong>${title}</strong></a>`;
  }

  toast.innerHTML = `
    ${imgHTML}
    <div class="${TOAST_CLASS}__body">
      <span class="${TOAST_CLASS}__text">Товар ${titleHTML} ${message}</span>
    </div>
    <button class="${TOAST_CLASS}__close" aria-label="Закрити">&times;</button>
  `;

  toast.querySelector(`.${TOAST_CLASS}__close`).addEventListener("click", () => {
    removeToast(toast);
  });

  wrap.appendChild(toast);

  // trigger reflow for animation
  toast.offsetHeight; // eslint-disable-line no-unused-expressions
  toast.classList.add("_show");

  setTimeout(() => removeToast(toast), DURATION);
}

function removeToast(toast) {
  if (!toast || !toast.parentNode) return;
  toast.classList.remove("_show");
  toast.classList.add("_hide");
  toast.addEventListener("transitionend", () => toast.remove(), { once: true });
  // fallback if transitionend doesn't fire
  setTimeout(() => toast.remove(), 400);
}
