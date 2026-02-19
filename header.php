<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Theme -->
	<meta name="theme-color" content="#3960ff">

	<?php wp_head(); ?>
 <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-THZXKTT');</script>
<!-- End Google Tag Manager -->

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
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-THZXKTT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

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
            <?php _e('Floor market', 'market-pidlogy'); ?>
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
            <a href="/">
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
            <?php if (get_field_object('phones', 'option') && have_rows('phones', 'option')) : ?>
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
              <div class="live-search live-search--desktop" data-wp="live-search">
                <a class="action action-icon j-live-search-toggle" href="#" aria-label="<?php esc_attr_e('Search', 'market-pidlogy'); ?>">
                  <svg class="icon icon--search" width="24" height="24">
                    <use xlink:href="#icon-search"></use>
                  </svg>
                </a>
                <div class="live-search__panel">
                  <div class="live-search__form">
                    <input type="search" class="j-live-search-input" placeholder="Пошук товарів..." autocomplete="off">
                    <button class="live-search__btn j-live-search-btn" type="button" aria-label="Пошук">
                      <svg class="icon icon--search" width="20" height="20">
                        <use xlink:href="#icon-search"></use>
                      </svg>
                    </button>
                  </div>
                  <div class="live-search__results j-live-search-results"></div>
                </div>
              </div>
              <a class="action action-icon action-icon-favorite" href="/wish-list/" aria-label="Відкрити список бажань">
                <svg class="icon icon--heart" width="24" height="24">
                  <use xlink:href="#icon-heart"></use>
                </svg>
                <span class="icon-count" style="display: none;"></span>
              </a>
              <!-- <a class="action action-icon" href="" aria-label="Відкрити список порівнянь">
                <svg class="icon icon--chart" width="24" height="24">
                  <use xlink:href="#icon-chart"></use>
                </svg>
              </a> -->
            </div>
            <a class="action action-icon" href="/cart/" aria-label="Відкрити кошик">
              <svg class="icon icon--cart" width="24" height="24" >
                <use xlink:href="#icon-cart"></use>
              </svg>
               <?php $cart_count = WC()->cart->cart_contents_count; ?>
               <span class="icon-count icon-cart-count"<?php if ( $cart_count === 0 ) echo ' style="display:none;"'; ?>><?php echo $cart_count; ?></span>
            </a>
          </div>
        </div>
        <div class="header__bottom hidden-tablet-l">
          <div class="header__bottom-icon">
            <?php get_template_part('./template-parts/social'); ?>
             <div class="header__action-icon">
              <a class="action action-icon action-icon-favorite" href="/wish-list/" aria-label="Відкрити список бажань">
                <svg class="icon icon--heart" width="24" height="24">
                  <use xlink:href="#icon-heart"></use>
                </svg>
                <span class="icon-count" style="display: none;"></span>
              </a>
              <!-- <a class="action action-icon" href="" aria-label="Відкрити список порівнянь">
                <svg class="icon icon--chart" width="24" height="24">
                  <use xlink:href="#icon-chart"></use>
                </svg>
              </a> -->
            </div>
          </div>

          <div class="header__info hidden-tablet-l text-center">
            <?php if (get_field_object('phones', 'option') && have_rows('phones', 'option')) : ?>
              <?php while (have_rows('phones', 'option')) : the_row();
                $phone = get_sub_field('phone');
              ?>
                <p class="mb-1 mt-1">
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
            <div class="live-search live-search--mobile" data-wp="live-search">
              <div class="live-search__form">
                <input type="search" class="j-live-search-input" placeholder="Пошук товарів..." autocomplete="off">
                <button class="live-search__btn j-live-search-btn" type="button" aria-label="Пошук">
                  <svg class="icon icon--search" width="20" height="20">
                    <use xlink:href="#icon-search"></use>
                  </svg>
                </button>
              </div>
              <div class="live-search__results j-live-search-results"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

	<!-- Main wrapper -->
	<div class="main-wrapper  <?php echo ( is_front_page() ? 'pt-0' : '' ); ?>"">
