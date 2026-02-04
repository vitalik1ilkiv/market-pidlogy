<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) :
	/**
	 * Ensure all images of related products are lazy loaded by increasing the
	 * current media count to WordPress's lazy loading threshold if needed.
	 * Because wp_increase_content_media_count() is a private function, we
	 * check for its existence before use.
	 */
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
	?>

	<section class="related products">

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2 class="mb-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
      <div 
        class="section-related-products__slider swiper"
        data-wp="slider"
        data-wp-config='{
          "settings": {
            "slidesPerView": 5,
            "spaceBetween": 6,
            "loop": false,
            "speed": 1000,
            "navigation": {
              "nextEl": ".section-related-products__slider .section-related-products__next",
              "prevEl": ".section-related-products__slider .section-related-products__prev"
            },
            "pagination": {
              "el": ".section-related-products__slider .swiper-pagination",
              "clickable": true
            },
            "breakpoints": {
              "320": { "slidesPerView": 2 },
              "768": { "slidesPerView": 3 },
              "1024": { "slidesPerView": 4 },
              "1280": { "slidesPerView": 5 }
            }
          }
        }'
      >
        <div class="swiper-wrapper">
          <?php foreach ( $related_products as $related_product ) : ?>
            <?php
              $post_object = get_post( $related_product->get_id() );
              setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
              global $product;

              // Виводимо через власний шаблон product-item.php
              $product_item_template = get_template_directory() . '/template-parts/woocommerce/product/product-item.php';
            ?>
            <div class="swiper-slide">
              <?php
                if (file_exists($product_item_template)) {
                    include $product_item_template;
                }
              ?>
            </div>
          <?php endforeach; ?>
          <?php wp_reset_postdata(); ?>
        </div>
        <button class="swiper-button swiper-button-prev section-related-products__button section-related-products__prev" aria-label="<?php esc_attr_e('Previous slide', 'protecstore'); ?>">
          <svg class="icon" width="12" height="12">
            <use xlink:href="#icon-prev"></use>
          </svg>
        </button>
        <button class="swiper-button swiper-button-next section-related-products__button section-related-products__next" aria-label="<?php esc_attr_e('Next slide', 'protecstore'); ?>">
          <svg class="icon" width="12" height="12">
            <use xlink:href="#icon-next"></use>
          </svg>
        </button>
        <div class="section-related-products__pagination swiper-pagination"></div>
      </div>

	</section>
	<?php
endif;

wp_reset_postdata();
