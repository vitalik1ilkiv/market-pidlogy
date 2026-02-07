<?php

// Підключення основних стилів і скриптів
function theme_enqueue_assets() {
    // Основні стилі
    wp_enqueue_style(
        'theme-styles',
        get_template_directory_uri() . '/dist/styles.css',
        [],
        filemtime(get_template_directory() . '/dist/styles.css')
    );

    // Скрипти
    wp_enqueue_script(
        'swiper-js',
        get_template_directory_uri() . '/assets/js/lib/swiper.min.js',
        ['jquery'],
        null,
        true
    );

    wp_enqueue_script(
        'lazysizes',
        get_template_directory_uri() . '/assets/js/lib/lazysizes.min.js',
        [],
        filemtime(get_template_directory() . '/assets/js/lib/lazysizes.min.js'),
        true
    );

    wp_enqueue_script(
        'fancybox-js',
        get_stylesheet_directory_uri() . '/assets/js/lib/fancybox.umd.js',
        [],
        filemtime(get_template_directory() . '/assets/js/lib/fancybox.umd.js'),
        true
    );

    wp_enqueue_script(
        'arcticmodal-js',
        get_stylesheet_directory_uri() . '/assets/js/lib/arcticmodal.js',
        [],
        filemtime(get_template_directory() . '/assets/js/lib/arcticmodal.js'),
        true
    );

    // wp_enqueue_script(
    //     'intlTelInput-js',
    //     get_stylesheet_directory_uri() . '/assets/js/lib/intlTelInput.min.js',
    //     [],
    //     filemtime(get_template_directory() . '/assets/js/lib/intlTelInput.min.js'),
    //     true
    // );

    wp_enqueue_script(
        'utils-js',
        get_stylesheet_directory_uri() . '/assets/js/lib/utils.js',
        [],
        filemtime(get_template_directory() . '/assets/js/lib/utils.js'),
        true
    );

    wp_enqueue_script(
        'theme-scripts',
        get_template_directory_uri() . '/dist/scripts.js',
        ['jquery'],
        filemtime(get_template_directory() . '/dist/scripts.js'),
        true
    );

    $fancybox_css_url = get_stylesheet_directory_uri() . '/assets/scss/lib/fancybox.css';
    echo '<link rel="preload" href="' . esc_url($fancybox_css_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    echo '<noscript><link rel="stylesheet" href="' . esc_url($fancybox_css_url) . '"></noscript>';

    // $intlTelInput_css_url = get_stylesheet_directory_uri() . '/assets/scss/lib/intlTelInput.css';
    // echo '<link rel="preload" href="' . esc_url($intlTelInput_css_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    // echo '<noscript><link rel="stylesheet" href="' . esc_url($intlTelInput_css_url) . '"></noscript>';

    // Cart fragments for AJAX cart count update
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }

    wp_localize_script('theme-scripts', 'marketData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('market-nonce'),
        'themeUrl' => get_template_directory_uri(),
    ));

    wp_localize_script('theme-scripts', 'favoritesMessages', [
      'added'   => __('added to wish list', 'market-pidlogy'),
      'removed' => __('removed from wish list', 'market-pidlogy'),
    ]);

    wp_localize_script('theme-scripts', 'formValidateMessages', [
      'required'        => __('This field is required.', 'market-pidlogy'),
      'email'           => __('Please enter a valid email address.', 'market-pidlogy'),
      'tel'             => __('Please enter a valid phone number.', 'market-pidlogy'),
      'password'        => __('The password must contain at least 5 characters, uppercase letters and numbers.', 'market-pidlogy'),
      'passwordConfirm' => __('Please enter the same value.', 'market-pidlogy'),
      'telUa' => __('Please enter a valid phone number.', 'market-pidlogy'),
    ]);

    wp_localize_script('theme-scripts', 'liveSearchMessages', [
      'categories' => __('Categories', 'market-pidlogy'),
      'products'   => __('Products', 'market-pidlogy'),
      'showAll'    => __('Show all found products', 'market-pidlogy'),
      'notFound'   => __('Nothing found', 'market-pidlogy'),
      'error'      => __('Request error', 'market-pidlogy'),
      'placeholder' => __('Search products...', 'market-pidlogy'),
    ]);

    wp_localize_script('theme-scripts', 'addToCartMessages', [
      'added' => __('added to cart', 'market-pidlogy'),
    ]);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

// Переносимо jQuery у футер
function move_jquery_to_footer() {
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), [], null, true);
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'move_jquery_to_footer', 0);

// Додаємо defer до скриптів
function add_defer_attribute($tag, $handle) {
    $defer_scripts = ['theme-scripts', 'swiper-js', 'lazysizes'];

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);

function remove_block_library_css() {
  wp_dequeue_style('wp-block-library');
  wp_dequeue_style('wp-block-library-theme');
}

add_action( 'wp_enqueue_scripts', 'remove_block_library_css', 999 );


/**
 * Setup woocommerce.
 */
function mytheme_add_woocommerce_support() {
	add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'mytheme_add_woocommerce_support');

require get_template_directory() . '/inc/woocommerce/woocommerce.php';
require get_template_directory() . '/inc/woocommerce/live-search.php';

add_theme_support( 'title-tag' );


/**
 * utils
 */

require_once get_template_directory() . '/inc/utils.php';
require_once get_template_directory() . '/inc/constants.php';

/**
 * page theme settings
 */

if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title'    => 'Налаштування теми',
        'menu_title'    => 'Налаштування теми',
        'menu_slug'     => 'theme-settings',
        'capability'    => 'edit_theme_options',
        'redirect'      => false,
        'position'      => 60,
        'icon_url'      => 'dashicons-admin-generic',
    ]);
}

/**
 * support menu
 */

function market_register_menus() {
    register_nav_menus([
        'header_menu' => __('Menu in header', 'market-pidlogy'),
        'footer_menu' => __('Menu in footer', 'market-pidlogy'),
        'footer_menu2' => __('Menu in footer 2', 'market-pidlogy'),
    ]);
}
add_action('after_setup_theme', 'market_register_menus');

/**
 * Support thumbnail
 */

add_theme_support('post-thumbnails');

// custom image-size

add_image_size( 'product-card', 350, 350, true );

// Дозволити SVG у медіабібліотеці
add_filter( 'upload_mimes', function ( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_filter( 'wp_prepare_attachment_for_js', function ( $response ) {
    if ( $response['mime'] === 'image/svg+xml' ) {
        $response['sizes'] = [
            'full' => [
                'url' => $response['url'],
            ],
        ];
    }
    return $response;
});


function my_theme_load_textdomain() {
    load_theme_textdomain( 'market-pidlogy', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'my_theme_load_textdomain' );


add_action('init', function() {
    if (function_exists('qtranxf_getLanguage')) {
        $lang = qtranxf_getLanguage();
        switch ($lang) {
            case 'ru':
                load_textdomain('woocommerce', WP_LANG_DIR . '/plugins/woocommerce-ru_RU.mo');
                break;
            case 'uk':
                load_textdomain('woocommerce', WP_LANG_DIR . '/plugins/woocommerce-uk.mo');
                break;
        }
    }
});

