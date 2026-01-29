import $ from "../helper/jquery.js";

class SendForm {
  static defaultProps = {
    formSelector: ".send-form",
    messageClass: "form-message",
    successClass: "_success",
    errorClass: "_error",
    hiddenClass: "_hidden",
    messageDuration: 5000,
  };

  constructor() {
    this.props = { ...SendForm.defaultProps };
    this.init();
  }

  init() {
    const self = this;

    // Слухаємо click на кнопці submit (FormValidate вже заблокував стандартний submit)
    $(document).on(
      "click",
      `${this.props.formSelector} [type="submit"]`,
      function () {
        const form = $(this).closest("form");

        // Невелика затримка щоб FormValidate встиг провалідувати
        setTimeout(() => {
          self.handleSubmit(form);
        }, 50);
      },
    );
  }

  handleSubmit(form) {
    // Перевіряємо чи форма валідна (інтеграція з FormValidate)
    const hasErrors = form.find(".mage-error").length > 0;
    if (hasErrors) {
      return;
    }

    // Запобігаємо подвійній відправці
    if (form.data("sending")) {
      return;
    }
    form.data("sending", true);

    const submitBtn = form.find('[type="submit"]');
    const formData = form.serializeArray();
    const formTitle =
      form.find('input[type="hidden"]').first().val() || "Форма з сайту";

    // Блокуємо кнопку
    submitBtn.prop("disabled", true).addClass("_loading");

    $.ajax({
      url: window.sendFormAjax?.ajaxurl || "/wp-admin/admin-ajax.php",
      type: "POST",
      data: {
        action: "send_form_telegram",
        nonce: window.sendFormAjax?.nonce || "",
        form_title: formTitle,
        form_data: formData,
        page_url: window.location.href,
      },
      success: (response) => {
        if (response.success) {
          this.showMessage(form, response.data.message, true);
          this.clearForm(form);
        } else {
          this.showMessage(
            form,
            response.data?.message || "Помилка відправки",
            false,
          );
        }
      },
      error: () => {
        this.showMessage(form, "Помилка з'єднання. Спробуйте ще раз.", false);
      },
      complete: () => {
        submitBtn.prop("disabled", false).removeClass("_loading");
        form.data("sending", false);
      },
    });
  }

  showMessage(form, text, isSuccess) {
    // Видаляємо попереднє повідомлення
    form.find(`.${this.props.messageClass}`).remove();

    const stateClass = isSuccess
      ? this.props.successClass
      : this.props.errorClass;

    const messageEl = $(
      `<div class="${this.props.messageClass} ${stateClass}">${text}</div>`,
    );

    form.append(messageEl);

    // Автоматично ховаємо через 5 секунд
    setTimeout(() => {
      messageEl.addClass(this.props.hiddenClass);
      setTimeout(() => messageEl.remove(), 300);
    }, this.props.messageDuration);
  }

  clearForm(form) {
    form
      .find(
        ':input:not([type="hidden"]):not([type="submit"]):not([type="checkbox"]):not([type="radio"])',
      )
      .val("");

    // Викликаємо подію для FormValidate
    $(document).trigger("clearForm");
  }
}

const sendForm = new SendForm();

export default sendForm;
