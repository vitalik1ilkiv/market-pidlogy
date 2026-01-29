import $ from "../helper/jquery.js";
import GetBodyScroll from "../helper/bodyscroll.js";
import { getResize } from "../helper/breakpoints.js";

export const Header = () => {
  const header = $("header#header");
  const navDrop = $(".j-nav-mobile");
  const navDesktop = $(".header__wrap .header__menu");
  const mobileBreakpoint = getResize("LG-1"); // max-width: 1023px

  // Створюємо backdrop елемент
  const backdrop = $('<div class="mobile-menu-backdrop"></div>');
  $("body").append(backdrop);

  var bind = function () {
    header.on("click", headerActions.bind(this));

    navDrop.find(".menu-item-has-children").each(function () {
      const $li = $(this);
      const $submenu = $li.children(".sub-menu"); // підменю безпосередньо всередині li
      const $link = $li.children("a");

      // Додаємо кнопку тільки якщо підменю є і кнопки ще немає
      if ($submenu.length && !$link.children(".sub-menu-toggle").length) {
        $link.append(
          '<button class="sub-menu-toggle" aria-label="Відкрити під меню"></button>'
        );
      }
    });

    navDesktop.find(".menu-item-has-children").each(function () {
      const $li = $(this);
      const $submenu = $li.children(".sub-menu"); // підменю безпосередньо всередині li
      const $link = $li.children("a");

      // Додаємо кнопку тільки якщо підменю є і кнопки ще немає
      if ($submenu.length && !$link.children(".sub-menu-toggle").length) {
        $link.append('<span class="sub-menu-toggle"></span>');
      }
    });

    navDrop
      .find(".current-menu-ancestor, .current-menu-parent")
      .each(function () {
        const $li = $(this);
        const $submenu = $li.children(".sub-menu");
        const $button = $li.find("> a > .sub-menu-toggle");

        if ($submenu.length) {
          $submenu.show(); // або .slideDown(0)
          $button.addClass("sub-menu-open");
        }
      });

    // Слухач зміни breakpoint
    mobileBreakpoint.addEventListener("change", handleBreakpointChange);

    // Виклик при ініціалізації
    handleBreakpointChange(mobileBreakpoint);
  };

  var handleBreakpointChange = function (e) {
    if (e.matches) {
      navDrop.on("click", toggleSubmenu.bind(this));
    }
  };

  var toggleSubmenu = function (e) {
    if ($(e.target).is(".sub-menu-toggle")) {
      e.preventDefault();

      const $button = $(e.target);
      const $li = $button.closest("li");
      const $submenu = $li.children(".sub-menu");

      // Анімація відкриття/закриття підменю
      $submenu.stop(true, true).slideToggle(300); // 300ms для плавності

      // Додаємо/знімаємо клас на кнопці (стрілка)
      $button.toggleClass("sub-menu-open");
    }
  };

  var openMobileMenu = function () {
    navDrop.addClass("nav-open");
    backdrop.addClass("active");
    GetBodyScroll.disable();
  };

  var closeMobileMenu = function () {
    navDrop.removeClass("nav-open");
    backdrop.removeClass("active");
    GetBodyScroll.enable();
  };

  var headerActions = function (e) {
    // mobile burger menu
    if ($(e.target).is(".j-burger-action")) {
      openMobileMenu();
    }

    if ($(e.target).is(".j-close-nav")) {
      closeMobileMenu();
    }
  };

  // Закриття меню при кліку на backdrop
  backdrop.on("click", function () {
    closeMobileMenu();
  });

  bind();
};
