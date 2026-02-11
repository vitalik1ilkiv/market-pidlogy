import $ from "../helper/jquery.js";

class FormValidate {
  static defaultProps = {
    form: ".js-form-validate",
    errorClass: "mage-error",
    successClass: "_success",
    errorText: "mage-text",
  };

  constructor() {
    this.props = {
      ...FormValidate.defaultProps,
    };

    this.formSubmitBtn = $(this.props.form + ' [type="submit"]');
    this.isValid = true;

    this.itiInstances = new Map(); // intlTelInput storage

    this.bind();
    this.init();
  }

  bind() {
    this.formSubmitBtn.on("click", this.formSubmit.bind(this));
    $(this.props.form).on("submit", this.formSubmit.bind(this));
  }

  init() {
    let self = this;

    // ==== INIT intl-tel-input (тільки для "tel":true, не для telUa) ====
    const telInputs = $("input[data-validate*='\"tel\"']");

    telInputs.each(function () {
      const input = this;
      // Пропускаємо якщо це telUa
      const validateData = $(input).data("validate");
      if (validateData && validateData.telUa) return;

      const iti = window.intlTelInput(input, {
        initialCountry: "ua",
        preferredCountries: ["ua", "pl", "de", "us"],
        utilsScript: "/wp-content/themes/yourtheme/assets/js/lib/utils.js",
        separateDialCode: false,
        autoPlaceholder: "polite",
      });

      self.itiInstances.set(input, iti);

      $(input).on("blur", function () {
        self.validate($(this));
      });
    });

    // ==== DEFAULT validation bindings ====
    $(
      "form" +
        this.props.form +
        " :input:not([type=hidden]):not([type=checkbox]):not([type=radio]):not(button):not(a)",
    ).on("blur", function () {
      if (!$(this).val().length && !$(this).hasClass(self.props.errorClass))
        return;

      self.validate($(this));
    });

    $("form" + this.props.form + " select").on("change", function () {
      self.validate($(this));
    });

    $(document).on("clearForm", function () {
      self.clearForm();
    });
  }

  formSubmit(e) {
    let form = $(e.target).closest("form");
    this.isValid = true;

    // Перед перевіркою — нормалізуємо телефон
    form.find('input[data-validate*="tel"]').each((i, input) => {
      const iti = this.itiInstances.get(input);
      if (iti && iti.isValidNumber()) {
        $(input).val(iti.getNumber()); // +380...
      }
    });

    // Валідатор
    form
      .find(
        ':input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"])',
      )
      .each((i, el) => this.validate($(el)));

    // Блокуємо стандартний submit якщо є помилки АБО це AJAX форма
    if (!this.isValid || form.hasClass("send-form")) {
      e.preventDefault();
      e.stopImmediatePropagation();

      // Скролимо до першого поля з помилкою
      const firstError = form.find("." + this.props.errorClass).first();
      if (firstError.length) {
        firstError[0].scrollIntoView({ behavior: "smooth", block: "center" });
        firstError.focus();
      }
    }
  }

  validate(elem) {
    let validationRules = elem.data("validate");

    if (validationRules) {
      let errorMessage = this.validateInput(elem, validationRules);

      if (errorMessage) {
        this.showError(elem, errorMessage);
        this.isValid = false;
      } else {
        this.hideError(elem);
      }
    }
  }

  validateInput(elem, rules) {
    if (
      (rules.required && elem.val() === "") ||
      (rules.required && elem.is("select") && elem.val() === "---")
    ) {
      return formValidateMessages.required;
    }

    if (rules.email && !this.isValidEmail(elem.val())) {
      return formValidateMessages.email;
    }

    if (rules.tel && !this.isValidTel(elem)) {
      return formValidateMessages.tel;
    }

    if (rules.telUa && !this.isValidTelUa(elem.val())) {
      return formValidateMessages.telUa || formValidateMessages.tel;
    }

    if (rules.password && !this.isValidPassword(elem.val())) {
      return formValidateMessages.password;
    }

    if (
      rules.passwordConfirm &&
      !this.isValidConfirmPassword(
        elem.closest("form").find("input#password").val(),
        elem.val(),
      )
    ) {
      return formValidateMessages.passwordConfirm;
    }

    return null;
  }

  showError(elem, message) {
    if (elem.parent().find("." + this.props.errorText).length) {
      elem
        .parent()
        .find("." + this.props.errorText)
        .removeClass(this.props.successClass)
        .text(message);
    } else {
      let errorDiv = $('<div class="' + this.props.errorText + '">').text(
        message,
      );
      elem.addClass(this.props.errorClass).after(errorDiv);
    }
  }

  hideError(elem) {
    elem
      .removeClass(this.props.errorClass)
      .parent()
      .find("." + this.props.errorText)
      .remove();
  }

  clearForm(form) {
    let self = this;

    if (form === undefined) {
      form = $("form");
    }

    form
      .find(
        ':input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]):not(button):not(a)',
      )
      .each(function () {
        $(this).val("");
        self.hideError($(this));
      });
  }

  isValidEmail(email) {
    let emailRegex =
      /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/;

    return emailRegex.test(email);
  }

  isValidTel(elem) {
    const iti = this.itiInstances.get(elem[0]);
    if (!iti) return false;

    return iti.isValidNumber();
  }

  isValidTelUa(phone) {
    // Видаляємо всі символи крім цифр та +
    const cleaned = phone.replace(/[\s\-\(\)]/g, "");

    // Українські номери:
    // +380XXXXXXXXX (12 символів з +)
    // 380XXXXXXXXX (12 цифр)
    // 0XXXXXXXXX (10 цифр)
    const uaRegex = /^(?:\+?380|0)\d{9}$/;

    return uaRegex.test(cleaned);
  }

  isValidPassword(password) {
    let passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{5,}$/;

    return passwordRegex.test(password);
  }

  isValidConfirmPassword(password, passwordConfirm) {
    return password === passwordConfirm;
  }
}

export default FormValidate;
