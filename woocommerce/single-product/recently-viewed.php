<?php
/**
 * Recently Viewed Products
 *
 * @package suspended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] )
	? array_filter( array_map( 'absint', explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ) )
	: array();

if ( empty( $viewed_products ) ) {
	return;
}

// Exclude current product
$current_product_id = get_the_ID();
$viewed_products = array_diff( $viewed_products, array( $current_product_id ) );

if ( empty( $viewed_products ) ) {
	return;
}

// Show most recent first, limit to 10
$viewed_products = array_reverse( $viewed_products );
$viewed_products = array_slice( $viewed_products, 0, 10 );

$products = wc_get_products( array(
	'include' => $viewed_products,
	'limit'   => 10,
	'status'  => 'publish',
	'orderby' => 'post__in',
) );

if ( empty( $products ) ) {
	return;
}
?>

<section class="recently-viewed products mt-2">
	<h2 class="mb-2"><?php esc_html_e( 'Recently viewed', 'market-pidlogy' ); ?></h2>

	<div
		class="section-recently-viewed__slider swiper"
		data-wp="slider"
		data-wp-config='{
			"settings": {
				"slidesPerView": 5,
				"spaceBetween": 6,
				"loop": false,
				"speed": 1000,
				"navigation": {
					"nextEl": ".section-recently-viewed__slider .section-recently-viewed__next",
					"prevEl": ".section-recently-viewed__slider .section-recently-viewed__prev"
				},
				"pagination": {
					"el": ".section-recently-viewed__slider .swiper-pagination",
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
			<?php foreach ( $products as $viewed_product ) : ?>
				<?php
					$post_object = get_post( $viewed_product->get_id() );
					setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					global $product;

					$product_item_template = get_template_directory() . '/template-parts/woocommerce/product/product-item.php';
				?>
				<div class="swiper-slide">
					<?php
						if ( file_exists( $product_item_template ) ) {
							include $product_item_template;
						}
					?>
				</div>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>
		<button class="swiper-button swiper-button-prev section-recently-viewed__button section-recently-viewed__prev" aria-label="<?php esc_attr_e( 'Previous slide', 'woocommerce' ); ?>">
			<svg class="icon" width="12" height="12">
				<use xlink:href="#icon-prev"></use>
			</svg>
		</button>
		<button class="swiper-button swiper-button-next section-recently-viewed__button section-recently-viewed__next" aria-label="<?php esc_attr_e( 'Next slide', 'woocommerce' ); ?>">
			<svg class="icon" width="12" height="12">
				<use xlink:href="#icon-next"></use>
			</svg>
		</button>
		<div class="section-recently-viewed__pagination swiper-pagination"></div>
	</div>
</section>
