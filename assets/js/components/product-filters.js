import $ from "../helper/jquery.js";

class ProductFilters {
  static defaultProps = {
    filterContainer: ".js-product-filters",
    filterForm: ".product-filters__form",
    priceSlider: ".js-price-slider",
    priceInput: ".js-price-input",
    priceRange: ".js-price-range",
    filterToggle: ".js-filter-toggle",
    buttonFilter: ".button-filter",
    filterClose: ".js-filter-close",
    overlay: ".filter-overlay",
    openClass: "_open",
    activeClass: "_active",
    visibleClass: "_visible",
  };

  constructor() {
    this.props = { ...ProductFilters.defaultProps };
    this.filterContainer = $(this.props.filterContainer);

    if (!this.filterContainer.length) {
      return;
    }

    this.init();
    this.bindEvents();
  }

  init() {
    this.updatePriceSlider();
  }

  bindEvents() {
    const self = this;

    // Open filters sidebar
    $(document).on("click", this.props.buttonFilter, function () {
      self.openFilters();
    });

    // Close filters via close button
    this.filterContainer.on("click", this.props.filterClose, function () {
      self.closeFilters();
    });

    // Close filters via overlay
    $(document).on("click", this.props.overlay, function () {
      self.closeFilters();
    });

    // Close on Escape key
    $(document).on("keydown", function (e) {
      if (e.key === "Escape") {
        self.closeFilters();
      }
    });

    // Toggle filter sections
    this.filterContainer.on("click", this.props.filterToggle, function () {
      const target = $(this).data("target");
      const content = $(`#filter-${target}`);
      $(this).toggleClass(self.props.openClass);
      content.slideToggle(200);
    });

    // Price slider
    this.filterContainer.on("input", this.props.priceSlider, function () {
      self.handlePriceSlider($(this));
    });

    // Price inputs
    this.filterContainer.on("input", this.props.priceInput, function () {
      self.handlePriceInput();
    });

    // Form submit — convert checkboxes to comma-separated hidden inputs
    this.filterContainer.on("submit", this.props.filterForm, function () {
      self.prepareFormSubmit($(this));
    });
  }

  prepareFormSubmit($form) {
    const taxonomies = {};

    // Collect checked values per taxonomy
    $form.find(".js-attribute-filter:checked").each(function () {
      const tax = $(this).data("taxonomy");
      if (!taxonomies[tax]) taxonomies[tax] = [];
      taxonomies[tax].push($(this).val());
    });

    // Remove checkboxes from submission, add hidden inputs with comma-separated values
    $form.find(".js-attribute-filter").prop("disabled", true);

    for (const [tax, values] of Object.entries(taxonomies)) {
      $form.append(
        `<input type="hidden" name="filter_${tax}" value="${values.join(",")}">`
      );
    }
  }

  openFilters() {
    this.filterContainer.addClass(this.props.openClass);
    $(this.props.overlay).addClass(this.props.visibleClass);
    $(this.props.buttonFilter).addClass(this.props.activeClass);
    $("body").css("overflow", "hidden");
  }

  closeFilters() {
    this.filterContainer.removeClass(this.props.openClass);
    $(this.props.overlay).removeClass(this.props.visibleClass);
    $(this.props.buttonFilter).removeClass(this.props.activeClass);
    $("body").css("overflow", "");
  }

  handlePriceSlider($slider) {
    const type = $slider.data("type");
    const value = parseFloat($slider.val());
    const minSlider = this.filterContainer.find(
      `${this.props.priceSlider}[data-type="min"]`
    );
    const maxSlider = this.filterContainer.find(
      `${this.props.priceSlider}[data-type="max"]`
    );
    const minInput = this.filterContainer.find("#min-price");
    const maxInput = this.filterContainer.find("#max-price");

    let minVal = parseFloat(minSlider.val());
    let maxVal = parseFloat(maxSlider.val());

    if (type === "min" && value > maxVal - 10) {
      minSlider.val(maxVal - 10);
      minVal = maxVal - 10;
    } else if (type === "max" && value < minVal + 10) {
      maxSlider.val(minVal + 10);
      maxVal = minVal + 10;
    }

    minInput.val(Math.round(minVal));
    maxInput.val(Math.round(maxVal));

    this.updatePriceSlider();
  }

  handlePriceInput() {
    const minInput = this.filterContainer.find("#min-price");
    const maxInput = this.filterContainer.find("#max-price");
    const minSlider = this.filterContainer.find(
      `${this.props.priceSlider}[data-type="min"]`
    );
    const maxSlider = this.filterContainer.find(
      `${this.props.priceSlider}[data-type="max"]`
    );

    let minVal = parseFloat(minInput.val()) || 0;
    let maxVal = parseFloat(maxInput.val()) || 0;

    minSlider.val(minVal);
    maxSlider.val(maxVal);

    this.updatePriceSlider();
  }

  updatePriceSlider() {
    const minSlider = this.filterContainer.find(
      `${this.props.priceSlider}[data-type="min"]`
    );
    const maxSlider = this.filterContainer.find(
      `${this.props.priceSlider}[data-type="max"]`
    );
    const range = this.filterContainer.find(this.props.priceRange);

    if (!minSlider.length || !maxSlider.length) return;

    const min = parseFloat(minSlider.attr("min"));
    const max = parseFloat(maxSlider.attr("max"));
    const minVal = parseFloat(minSlider.val());
    const maxVal = parseFloat(maxSlider.val());

    const minPercent = ((minVal - min) / (max - min)) * 100;
    const maxPercent = ((maxVal - min) / (max - min)) * 100;

    range.css({
      left: `${minPercent}%`,
      width: `${maxPercent - minPercent}%`,
    });
  }
}

const productFilters = new ProductFilters();

export default productFilters;
