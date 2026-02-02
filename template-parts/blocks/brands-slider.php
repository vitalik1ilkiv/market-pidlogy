<?php
/**
 * Block: Brands Slider
 * Виводить слайдер брендів з атрибута "brend" + ACF поле "brend_kartinka"
 */

defined('ABSPATH') || exit;

$brands = get_terms([
    'taxonomy'   => 'pa_brend',
    'hide_empty' => true,
]);

if (is_wp_error($brands) || empty($brands)) {
    return;
}

$brands_title = get_field('brand_title') ?: __('Бренди', 'market-pidlogy');
$sale_link_text = get_field('brand_link_text') ?: __('Дивитись всі', 'market-pidlogy');
$sale_link      = get_field('brand_link') ?: '';
?>

<section class="section section-brands">
  <div class="container">
    <h2 class="section-brands__title"><?php echo esc_html($brands_title); ?></h2>
    <?php if ($sale_link) : ?>
      <div class="section-brands__link">
        <a class="action action-color" href="<?php echo esc_url($sale_link); ?>">
          <?php echo esc_html($sale_link_text); ?>
        </a>
      </div>
    <?php endif; ?>
    <div
      class="swiper"
      data-wp="slider"
      data-wp-config='{
        "settings": {
          "slidesPerView": 5,
          "spaceBetween": 15,
          "loop": false,
          "speed": 1000,
          "autoHeight": false,
          "autoplay": {
            "delay": 2000,
            "disableOnInteraction": false,
            "pauseOnMouseEnter": true
          },
          "breakpoints": {
            "320": { "slidesPerView": 2, "spaceBetween": 10 },
            "768": { "slidesPerView": 3, "spaceBetween": 15 },
            "1024": { "slidesPerView": 4, "spaceBetween": 15 },
            "1280": { "slidesPerView": 5 }
          }
        }
      }'
    >
      <div class="swiper-wrapper">
        <?php foreach ($brands as $brand) :
          $image = get_field('brend_kartinka', $brand);
          $link  = get_term_link($brand);
        ?>
          <div class="swiper-slide">
            <a class="section-brands__item" href="<?php echo esc_url($link); ?>">
              <div class="section-brands__item-image">
                <picture>
                    <?php if ($image) : ?>
                      <img
                        decoding="sync"
                        class="lazyload"
                        data-expand="1"
                        src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                        data-src="<?php echo esc_url($image['url']); ?>"
                        alt="<?php echo esc_attr($brand->name); ?>"
                        width="<?php echo esc_attr($image['width']); ?>"
                        height="<?php echo esc_attr($image['height']); ?>"
                      >
                    <?php endif; ?>
                </picture>
              </div>
              <h5 class="section-brands__name"><?php echo esc_html($brand->name); ?></h5>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
