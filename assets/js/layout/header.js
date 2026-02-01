import $ from "../helper/jquery.js";
import { getResize } from "../helper/breakpoints.js";

export const Header = () => {
  const header = $("header#header");
  const navDrop = $(".j-nav-mobile");
  const navDesktop = $(".header__wrap .header__menu");
  const burger = header.find(".j-burger-action");
  const mobileBreakpoint = getResize("LG-1"); // max-width: 1023px

  // Ховаємо мобільне меню на старті
  navDrop.hide();

  var bind = function () {
    // Бургер — акордіон toggle
    burger.on("click", function () {
      navDrop.stop(true, true).slideToggle(300);
      burger.toggleClass("is-active");
    });

    navDrop.find(".menu-item-has-children").each(function () {
      const $li = $(this);
      const $submenu = $li.children(".sub-menu");
      const $link = $li.children("a");

      if ($submenu.length && !$link.children(".sub-menu-toggle").length) {
        $link.append(
          '<button class="sub-menu-toggle" aria-label="Відкрити під меню"></button>'
        );
      }
    });

    navDesktop.find(".menu-item-has-children").each(function () {
      const $li = $(this);
      const $submenu = $li.children(".sub-menu");
      const $link = $li.children("a");

      if ($submenu.length && !$link.children(".sub-menu-toggle").length) {
        $link.append('<span class="sub-menu-toggle"></span>');
      }
    });

    navDrop
      .find(".current-menu-ancestor, .current-menu-parent")
      .each(function () {
        const $li = $(this);
        const $submenu = $li.children(".sub-menu");

        if ($submenu.length) {
          $submenu.show();
          $li.addClass("is-open");
        }
      });

    // Слухач зміни breakpoint
    mobileBreakpoint.addEventListener("change", handleBreakpointChange);
    handleBreakpointChange(mobileBreakpoint);
  };

  var handleBreakpointChange = function (e) {
    if (e.matches) {
      navDrop.on("click", toggleSubmenu.bind(this));
    }
  };

  var toggleSubmenu = function (e) {
    const $target = $(e.target);
    const $li = $target.closest("li.menu-item-has-children");

    if (!$li.length) return;

    // Тільки якщо клік по прямому <a> цього li (не по вкладеному підменю)
    const $directLink = $li.children("a");

    if ($target.closest("a").is($directLink) || $target.is($li)) {
      e.preventDefault();

      const $submenu = $li.children(".sub-menu");

      $submenu.stop(true, true).slideToggle(300);
      $li.toggleClass("is-open");
    }
  };

  bind();
};
