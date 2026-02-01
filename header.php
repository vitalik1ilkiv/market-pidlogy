<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Theme -->
	<meta name="theme-color" content="#3960ff">

	<?php wp_head(); ?>
</head>
<script>
  if (window.navigator.userAgent.includes("Firefox")) {
    document.documentElement.className = document.documentElement.className + ' firefox';
  } else if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
    document.documentElement.className = document.documentElement.className + ' ios';
  }

  (function () {
    let currentWidth = window.innerWidth;

    function appHeight() {
      let vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--app-height', `${vh}px`);
    }

    function changeWidth() {
      if (window.innerWidth !== currentWidth) {
        appHeight();
        currentWidth = window.innerWidth;
      }
    }

    window.addEventListener('resize', changeWidth);
    appHeight();
  })();
</script>

<?php get_template_part('./inc/svg-sprite'); ?>
<body <?php body_class(); ?>>

	<!-- Site Header -->
  <header id="header" class="header">
    <div class="header__top">
      <div class="container">
        <div class="header__top-wrap">
          <p>
            <?php if(get_field('location_text', 'option')): ?>
              <a class="action action-color" href="<?php the_field('location_link', 'option'); ?>" style="text-wrap: wrap;">
                <?php the_field('location_text', 'option'); ?>
              </a>
            <?php endif; ?>
          </p>
          <p class="hidden-mobile">
            Маркет підлоги
          </p>
        </div>
      </div>
    </div>
    <div class="header-main">
      <div class="container">
        <div class="header__wrap">
           <button class="header__burger j-burger-action hidden-tablet-l" aria-label="Відкрити меню">
            <span></span>
          </button>
          <div class="header__logo">
            <a href="">
              <img src="<?php echo THEME_ASSETS ?>/img/logo.png" alt="Маркет підлоги" width="250" height="47">
            </a>
          </div>

          <nav class="header__menu hidden-mobile-to-tablet-p">
            <ul>
              <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-24515">
                <a href="">Магазин</a>
                <ul class="sub-menu">
                  <li id="menu-item-5275" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-has-children menu-item-251">
                    <a href="https://market-pidlogy.com.ua/uk/laminat/">Ламінат</a>
                    <ul class="sub-menu">
                      <li id="menu-item-252" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-252"><a href="https://protec.org.ua/product-category/plate/avs-mod-2/">AVS Mod.2</a></li>
                      <li id="menu-item-253" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-253"><a href="https://protec.org.ua/product-category/plate/frankenstein/">Frankenstein</a></li>
                    </ul>
                  </li>
                  <li id="menu-item-24917" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-24917"><a href="https://market-pidlogy.com.ua/uk/vinilovyj-pol/">Вінілова підлога</a></li>
                  <li id="menu-item-10309" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-10309"><a href="https://market-pidlogy.com.ua/uk/doska/">Терасна дошка</a></li>
                  <li id="menu-item-32068" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-32068"><a href="https://market-pidlogy.com.ua/uk/fasadna-doshka/">Фасадна дошка</a></li>
                  <li id="menu-item-25120" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-25120"><a href="https://market-pidlogy.com.ua/uk/stenovye-paneli/">Стінові панелі</a></li>
                  <li id="menu-item-10395" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-10395"><a href="https://market-pidlogy.com.ua/uk/aksesuari/">Плінтус та підкладка</a></li>
                  <li id="menu-item-22179" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-22179"><a href="https://market-pidlogy.com.ua/uk/parketnaya-himiya/">Клей для ПВХ</a></li>
                  <li id="menu-item-27630" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-27630"><a href="https://market-pidlogy.com.ua/uk/akczijni-tovary/">Акційні товари</a></li>
                </ul>
              </li>
              <li>
                <a href="">Магазин</a>
              </li>
              <li>
                <a href="">Магазин</a>
              </li>
              <li>
                <a href="">Магазин</a>
              </li>
            </ul>
          </nav>

          <div class="header__social hidden-mobile-to-tablet-p">
            <?php get_template_part('./template-parts/social'); ?>
          </div>
          <div class="header__info hidden-mobile-to-tablet-p">
            <?php if (have_rows('phones', 'option')) : ?>
              <?php while (have_rows('phones', 'option')) : the_row();
                $phone = get_sub_field('phone');
              ?>
                <p class="mb-0">
                  <a class="action action-color" href="tel:<?php echo sanitize_phone($phone); ?>">
                    <?php echo esc_html($phone); ?>
                  </a>
                </p>
              <?php endwhile; ?>
            <?php endif; ?>
            <?php if (get_field('schedule_work', 'option')) : ?>
              <div class="schedule-work ">
                <?php the_field('schedule_work', 'option'); ?>
              </div>
            <?php endif; ?>
          </div>
          <div class ="header__action-icon">
            <div class="header__action-icon hidden-mobile-to-tablet-p">
              <a class="action action-icon" href="">
                <svg class="icon icon--search" width="24" height="24">
                  <use xlink:href="#icon-search"></use>
                </svg>
              </a>
              <a class="action action-icon" href="">
                <svg class="icon icon--heart" width="24" height="24">
                  <use xlink:href="#icon-heart"></use>
                </svg>
              </a>
              <a class="action action-icon" href="">
                <svg class="icon icon--chart" width="24" height="24">
                  <use xlink:href="#icon-chart"></use>
                </svg>
              </a>
            </div>
            <a class="action action-icon" href="">
              <svg class="icon icon--cart" width="24" height="24">
                <use xlink:href="#icon-cart"></use>
              </svg>
            </a>
          </div>
        </div>
        <div class="header__bottom hidden-tablet-l">
          <div class="header__bottom-icon">
            <?php get_template_part('./template-parts/social'); ?>
             <div class="header__action-icon">
              <a class="action action-icon" href="">
                <svg class="icon icon--heart" width="24" height="24">
                  <use xlink:href="#icon-heart"></use>
                </svg>
              </a>
               <a class="action action-icon" href="">
                <svg class="icon icon--chart" width="24" height="24">
                  <use xlink:href="#icon-chart"></use>
                </svg>
              </a>
            </div>
          </div>

          <div class="header__info hidden-tablet-l text-center mt-1">
            <?php if (have_rows('phones', 'option')) : ?>
              <?php while (have_rows('phones', 'option')) : the_row();
                $phone = get_sub_field('phone');
              ?>
                <p class="mb-0">
                  <a class="action action-color" href="tel:<?php echo sanitize_phone($phone); ?>">
                    <?php echo esc_html($phone); ?>
                  </a>
                </p>
              <?php endwhile; ?>
            <?php endif; ?>
            <?php if (get_field('schedule_work', 'option')) : ?>
              <div class="schedule-work ">
                <?php the_field('schedule_work', 'option'); ?>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="header__bottom-content j-nav-mobile " style="display: none;">
            <nav class="header__menu">
              <ul>
              <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-24515">
                <a href="">Магазин</a>
                <ul class="sub-menu">
                  <li id="menu-item-5275" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-has-children menu-item-251">
                    <a href="https://market-pidlogy.com.ua/uk/laminat/">Ламінат</a>
                    <ul class="sub-menu">
                      <li id="menu-item-252" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-252"><a href="https://protec.org.ua/product-category/plate/avs-mod-2/">AVS Mod.2</a></li>
                      <li id="menu-item-253" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-253"><a href="https://protec.org.ua/product-category/plate/frankenstein/">Frankenstein</a></li>
                    </ul>
                  </li>
                  <li id="menu-item-24917" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-24917"><a href="https://market-pidlogy.com.ua/uk/vinilovyj-pol/">Вінілова підлога</a></li>
                  <li id="menu-item-10309" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-10309"><a href="https://market-pidlogy.com.ua/uk/doska/">Терасна дошка</a></li>
                  <li id="menu-item-32068" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-32068"><a href="https://market-pidlogy.com.ua/uk/fasadna-doshka/">Фасадна дошка</a></li>
                  <li id="menu-item-25120" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-25120"><a href="https://market-pidlogy.com.ua/uk/stenovye-paneli/">Стінові панелі</a></li>
                  <li id="menu-item-10395" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-10395"><a href="https://market-pidlogy.com.ua/uk/aksesuari/">Плінтус та підкладка</a></li>
                  <li id="menu-item-22179" class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-22179"><a href="https://market-pidlogy.com.ua/uk/parketnaya-himiya/">Клей для ПВХ</a></li>
                  <li id="menu-item-27630" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-27630"><a href="https://market-pidlogy.com.ua/uk/akczijni-tovary/">Акційні товари</a></li>
                </ul>
              </li>
              <li>
                <a href="">Магазин</a>
              </li>
              <li>
                <a href="">Магазин</a>
              </li>
              <li>
                <a href="">Магазин</a>
              </li>
            </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </header>

	<!-- Main wrapper -->
	<div class="main-wrapper">
