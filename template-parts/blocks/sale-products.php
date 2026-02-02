<?php
/**
 * Block: Sale Products
 * Виводить 12 останніх товарів з ACF полем "акція_товар"
 */

defined('ABSPATH') || exit;

$sale_products = new WP_Query([
    'post_type'      => 'product',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'   => 'акція_товар',
            'value' => '1',
        ],
    ],
]);

if (!$sale_products->have_posts()) {
    return;
}

$sale_title     = get_field('sale_title') ?: __('Акційні товари', 'market-pidlogy');
$sale_link_text = get_field('sale_link_text') ?: __('Дивитись всі', 'market-pidlogy');
$sale_link      = get_field('sale_link') ?: '';
?>

<section class="section section-product section-sale">
  <div class="container">
    <h2 class="section-product__title"><?php echo esc_html($sale_title); ?></h2>
    <?php if ($sale_link) : ?>
      <div class="section-product__link">
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
          "slidesPerView": 4,
          "spaceBetween": 15,
          "loop": false,
          "speed": 1000,
          "autoHeight": false,
          "navigation": {
            "nextEl": ".section-product .swiper-button-next",
            "prevEl": ".section-product .swiper-button-prev"
          },
          "pagination": {
            "el": ".section-product .swiper-pagination",
            "clickable": true
          },
          "breakpoints": {
            "320": { "slidesPerView": 2, "spaceBetween": 5 },
            "1024": { "slidesPerView": 3, "spaceBetween": 15 },
            "1280": { "slidesPerView": 4 }
          }
        }
      }'
    >
      <div class="swiper-wrapper">
        <?php 
          while ($sale_products->have_posts()) : $sale_products->the_post();
        ?>
          <div class="swiper-slide">
            <?php
              $product = wc_get_product(get_the_ID());
              if (!$product) continue;
              get_template_part('./template-parts/woocommerce/product/product-item', null, ['product' => $product]);
            ?>
          </div>
        <?php
          endwhile; 
        ?>
      </div>
       <div class="swiper-pagination"></div>
        <button class="swiper-button swiper-button-prev" aria-label="<?php esc_attr_e('Previous slide', 'protecstore'); ?>">
          <svg class="icon" width="12" height="12">
            <use xlink:href="#icon-prev"></use>
          </svg>
        </button>
        <button class="swiper-button swiper-button-next" aria-label="<?php esc_attr_e('Next slide', 'protecstore'); ?>">
          <svg class="icon" width="12" height="12">
            <use xlink:href="#icon-next"></use>
          </svg>
        </button>
      </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>
