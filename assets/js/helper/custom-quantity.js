import $ from "../helper/jquery.js";

export const CustomQuantity = () => {
  const init = function () {
    $(".quantity input.qty").each(function () {
      const input = $(this);

      // Якщо вже ініціалізовано — пропускаємо
      if (input.parent().hasClass("custom-qty")) return;

      // Створюємо обгортку
      const wrapper = $('<div class="custom-qty"></div>');
      const minusBtn = $(`
        <button type="button" class="qty-btn qty-minus" aria-label="Зменшити кількість">
          <svg class="icon icon--minus" width="16" height="16">
            <use xlink:href="#icon-minus"></use>
          </svg>
        </button>
      `);

      const plusBtn = $(`
        <button type="button" class="qty-btn qty-plus" aria-label="Збільшити кількість">
          <svg class="icon icon--plus" width="16" height="16">
            <use xlink:href="#icon-plus"></use>
          </svg>
        </button>
      `);

      // Обгортаємо інпут
      input.wrap(wrapper);
      input.before(minusBtn);
      input.after(plusBtn);

      // Логіка кнопок
      minusBtn.on("click", function () {
        let val = parseInt(input.val(), 10) || 0;
        let min = parseInt(input.attr("min"), 10) || 1;

        if (val > min) {
          input.val(val - 1).trigger("change");
        }
      });

      plusBtn.on("click", function () {
        let val = parseInt(input.val(), 10) || 0;
        let max = parseInt(input.attr("max"), 10) || 9999;

        if (val < max) {
          input.val(val + 1).trigger("change");
        }
      });
    });
  };

  init();
};
