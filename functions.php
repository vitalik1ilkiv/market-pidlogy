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

    $intlTelInput_css_url = get_stylesheet_directory_uri() . '/assets/scss/lib/intlTelInput.css';
    echo '<link rel="preload" href="' . esc_url($intlTelInput_css_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    echo '<noscript><link rel="stylesheet" href="' . esc_url($intlTelInput_css_url) . '"></noscript>';

    wp_localize_script('theme-scripts', 'marketData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('market-nonce'),
        'themeUrl' => get_template_directory_uri(),
    ));

    wp_localize_script('theme-scripts', 'formValidateMessages', [
      'required'        => __('Це поле обов\'язкове.', 'protec'),
      'email'           => __('Будь ласка, введіть дійсну адресу електронної пошти.', 'protec'),
      'tel'             => __('Будь ласка, введіть дійсний номер телефону.', 'protec'),
      'password'        => __('Пароль повинен містити щонайменше 5 символів, великі літери та цифри.', 'protec'),
      'passwordConfirm' => __('Будь ласка, введіть те саме значення.', 'protec'),
      'telUa' => __('Введіть коректний номер телефону.', 'protec'),
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

function protec_register_menus() {
    register_nav_menus([
        'header_menu' => __('Меню в шапці', 'protec'),
        'footer_menu' => __('Меню в підвалі', 'protec'),
        'footer_menu2' => __('Меню в підвалі 2', 'protec'),
    ]);
}
add_action('after_setup_theme', 'protec_register_menus');

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

