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
            <?php
              wp_nav_menu([
                  'theme_location' => 'header_menu',
              ]);
            ?>
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
              <?php
                wp_nav_menu([
                    'theme_location' => 'header_menu',
                ]);
              ?>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </header>

	<!-- Main wrapper -->
	<div class="main-wrapper">
