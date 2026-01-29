import lazyHelper from "../helper/lazy-init.js";
import { getResize } from "../helper/breakpoints.js";
import { Component } from "../component.js";

// чекаємо, поки Swiper з'явиться у window
function waitForSwiper(callback) {
  if (window.Swiper) {
    callback(window.Swiper);
  } else {
    const check = setInterval(() => {
      if (window.Swiper) {
        clearInterval(check);
        callback(window.Swiper);
      }
    }, 50);
  }
}

Component("slider", ($el, props) => {
  waitForSwiper(() => new Slider($el, props));
});

class Slider {
  static defaultProps = {
    settings: {
      loop: false,
      lazy: false,
      lazyPreloadPrevNext: 1,
      roundLengths: true,
      resizable: false,
      resizeBreakpoint: "",
    },
  };

  constructor($el, props) {
    this.props = {
      settings: {
        ...Slider.defaultProps.settings,
        ...props.settings,
      },
    };

    if (this.props.settings?.pagination?.type === "fraction") {
      this.props.settings.pagination.renderFraction = function (
        currentClass,
        totalClass
      ) {
        return (
          '<span class="' +
          currentClass +
          '"></span>' +
          " of " +
          '<span class="' +
          totalClass +
          '"></span>'
        );
      };
    }

    this.el = $el;
    this.init();
  }

  init() {
    const self = this;

    if (self.props.settings.resizable) {
      if (self.props.settings.lazy) {
        lazyHelper.onLazyload(self.el[0], function () {
          self.initSliderResize();
        });
      } else {
        self.initSliderResize();
      }
    } else {
      if (self.props.settings.lazy) {
        lazyHelper.onLazyload(self.el[0], function () {
          self.initSlider();
        });
      } else {
        self.initSlider();
      }
    }
  }

  initSliderResize() {
    const breakpoint = getResize(this.props.settings.resizeBreakpoint);
    breakpoint.addListener(this.mediaSize.bind(this, breakpoint));
    this.mediaSize(breakpoint);
  }

  mediaSize(breakpoint) {
    if (breakpoint.matches) {
      if (this.slider) {
        this.slider.destroy(true, true);
      }
    } else {
      this.initSlider();
    }
  }

  initSlider() {
    const Swiper = window.Swiper;
    this.slider = new Swiper(this.el[0], this.props.settings);

    // Зберігаємо інстанс в DOM елемент для доступу ззовні
    if (!this.el[0].wpInstance) {
      this.el[0].wpInstance = {};
    }
    this.el[0].wpInstance.slider = this.slider;

    this.slider.on("click", () => {
      const clickedSlide = this.slider.clickedSlide;

      if (clickedSlide) {
        const zoomContainer = clickedSlide.querySelector(
          ".swiper-zoom-container"
        );

        if (zoomContainer) {
          const firstElement = zoomContainer.firstElementChild;

          if (firstElement && firstElement.classList.contains("bss_pl_img")) {
            clickedSlide.appendChild(firstElement);
          }
        }
      }
    });
  }
}
