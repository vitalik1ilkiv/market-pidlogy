import $ from "../helper/jquery.js";
import { Component } from "../component.js";

const msg = window.liveSearchMessages || {};

Component("live-search", ($el, props) => {
  new LiveSearch($el, props);
});

class LiveSearch {
  static defaultProps = {
    settings: {
      toggle: ".j-live-search-toggle",
      input: ".j-live-search-input",
      results: ".j-live-search-results",
      panel: ".live-search__panel",
      btn: ".j-live-search-btn",
      loadingClass: "is-loading",
      openClass: "is-open",
      delay: 300,
      ajaxUrl:
        (window.marketData && window.marketData.ajaxUrl) ||
        "/wp-admin/admin-ajax.php",
    },
  };

  constructor($el, props) {
    this.$el = $el;
    this.props = {
      settings: {
        ...LiveSearch.defaultProps.settings,
        ...props.settings,
      },
    };

    this.$toggle = this.$el.find(this.props.settings.toggle);
    this.$panel = this.$el.find(this.props.settings.panel);
    this.$input = this.$el.find(this.props.settings.input);
    this.$results = this.$el.find(this.props.settings.results);
    this.$btn = this.$el.find(this.props.settings.btn);
    this.timer = null;
    this.lastQuery = "";
    this.isDesktop = this.$el.hasClass("live-search--desktop");

    this.init();
  }

  init() {
    if (this.isDesktop) {
      this.$toggle.on("click", (e) => {
        e.preventDefault();
        this.togglePanel();
      });

      $(document).on("click", (e) => {
        if (!this.$el.is(e.target) && this.$el.has(e.target).length === 0) {
          this.closePanel();
        }
      });

      $(document).on("keydown", (e) => {
        if (e.key === "Escape") {
          this.closePanel();
        }
      });
    }

    this.$input.on("input", (e) => {
      const query = e.target.value.trim();

      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        if (query.length < 2) {
          this.$results.empty();
          this.lastQuery = "";
          return;
        }
        this.search(query);
      }, this.props.settings.delay);
    });

    this.$btn.on("click", () => {
      const query = this.$input.val().trim();
      if (query.length >= 2) {
        window.location.href = `/?s=${encodeURIComponent(query)}`;
      }
    });

    this.$input.on("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        const query = this.$input.val().trim();
        if (query.length >= 2) {
          window.location.href = `/?s=${encodeURIComponent(query)}`;
        }
      }
    });
  }

  togglePanel() {
    if (this.$el.hasClass(this.props.settings.openClass)) {
      this.closePanel();
    } else {
      this.openPanel();
    }
  }

  openPanel() {
    this.$el.addClass(this.props.settings.openClass);
    setTimeout(() => this.$input.focus(), 100);
  }

  closePanel() {
    this.$el.removeClass(this.props.settings.openClass);
    this.$input.val("");
    this.$results.empty();
    this.lastQuery = "";
  }

  search(query) {
    this.$results.addClass(this.props.settings.loadingClass);
    this.lastQuery = query;

    $.ajax({
      url: this.props.settings.ajaxUrl,
      type: "POST",
      data: {
        action: "live_search",
        query: query,
      },
      success: (response) => {
        if (this.lastQuery !== query) return;

        this.$results.removeClass(this.props.settings.loadingClass);

        const hasCategories =
          response.categories && response.categories.length;
        const hasProducts = response.data && response.data.length;

        if (!hasCategories && !hasProducts) {
          this.$results.html(
            `<div class="live-search__results-inner"><p class="live-search__empty">${msg.notFound}</p></div>`,
          );
          return;
        }

        let html = '<div class="live-search__results-inner">';

        if (hasCategories) {
          html += `<div class="live-search__section">`;
          html += `<div class="live-search__heading">${msg.categories}</div>`;
          html += `<div class="live-search__cat-list">${response.categories.join("")}</div>`;
          html += `</div>`;
        }

        if (hasProducts) {
          html += `<div class="live-search__section">`;
          html += `<div class="live-search__heading">${msg.products}</div>`;
          html += response.data.join("");

          const totalCount = response.total || response.data.length;
          html += `<div class="live-search__show-all">`;
          html += `<a href="/?s=${encodeURIComponent(query)}" class="action action-underlined">`;
          html += `${msg.showAll} <span>(${totalCount})</span>`;
          html += `</a></div>`;
          html += `</div>`;
        }

        html += "</div>";
        this.$results.html(html);
      },
      error: () => {
        this.$results.removeClass(this.props.settings.loadingClass);
        this.$results.html(
          `<div class="live-search__results-inner"><p class="live-search__empty">${msg.error}</p></div>`,
        );
      },
    });
  }
}
