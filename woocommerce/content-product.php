<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

// Get product images
$product_id = $product->get_id();
$image_id = $product->get_image_id();
$gallery_image_ids = $product->get_gallery_image_ids();
$main_image = wp_get_attachment_image_url( $image_id, 'woocommerce_single' );
$classes = 'product-item';
if ( ! $product->is_in_stock() ) {
    $classes .= ' outofstock';
}
?>
<div <?php wc_product_class( $classes, $product ); ?> data-product-id="<?php echo esc_attr($product_id); ?>">
	<a class="product-item__body" href="<?php echo esc_url( $product->get_permalink() ); ?>" aria-label="Перейти до товару">
		<div class="product-item__image-box">
			<div class="product-item__image">
        <picture>
          <?php
          $image_id = $product->get_image_id();
          if ($image_id) {
              $image_src = wp_get_attachment_image_src($image_id, 'woocommerce_single');
              $image_url = $image_src[0];
              $image_w   = $image_src[1];
              $image_h   = $image_src[2];
              $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $product->get_name();
          ?>
            <img
              decoding="sync"
              class="lazyload"
              data-expand="1"
              src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
              data-src="<?= esc_url($image_url); ?>"
              alt="<?= esc_attr($image_alt); ?>"
              width="<?= esc_attr($image_w); ?>"
              height="<?= esc_attr($image_h); ?>"
            />
          <?php } else {
              echo wc_placeholder_img('woocommerce_single');
          } ?>
        </picture>
      </div>
      <?php if ($product->is_in_stock()) : ?>
        <div class="product-item__action-wrap">
          <?php if (!$product->is_type('variable')) : ?>
            <a class="action action-primary j-ajax-add-to-cart"
               href="<?php echo esc_url(add_query_arg('add-to-cart', $product->get_id(), get_permalink($product->get_id()))); ?>"
               data-product-id="<?php echo esc_attr($product->get_id()); ?>"
               aria-label="<?php esc_attr_e('Buy', 'market-pidlogy'); ?>">
              <?php _e('Buy', 'market-pidlogy'); ?>
            </a>
          <?php else : ?>
            <a class="action action-primary"
               href="<?php echo esc_url(get_permalink($product->get_id())); ?>"
               aria-label="<?php esc_attr_e('Choose options', 'market-pidlogy'); ?>">
              <?php _e('Buy', 'market-pidlogy'); ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="product-item__action-icons">
        <button class="button-favorite" data-wp="favorites" aria-label="Додати до списку бажань">
          <svg class="icon icon--favorite" width="16" height="16">
            <use xlink:href="#icon-favorite"></use>
          </svg>
        </button>

        <!-- <button>
          <svg class="icon icon--compare" width="16" height="16" aria-label="Додати до порівняння">
            <use xlink:href="#icon-compare"></use>
          </svg>
        </button> -->
      </div>
      <?php get_template_part('./template-parts/woocommerce/product/badges', null, ['product_id' => $product->get_id()]); ?>
		</div>
		<div class="product-item__info">
      <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="product-item__body">
        <h4 class="h5 product-item__name"><?php echo esc_html($product->get_name()); ?></h4>
        <div class="product-item__price">
        <?php
          if ( $product->is_type( 'variable' ) ) {

              $regular_prices = [];
              $sale_prices    = [];

              foreach ( $product->get_children() as $variation_id ) {
                  $variation = wc_get_product( $variation_id );
                  if ( ! $variation ) {
                      continue;
                  }

                  $regular = (float) $variation->get_regular_price();
                  $sale    = (float) $variation->get_sale_price();

                  if ( $regular ) {
                      $regular_prices[] = $regular;
                  }

                  if ( $sale && $sale < $regular ) {
                      $sale_prices[] = $sale;
                  }
              }

              // Якщо є знижки у варіаціях
              if ( ! empty( $sale_prices ) ) {

                  echo '<span class="price-sale">' . wc_price( min( $sale_prices ) ) . '</span>';
                  echo '<span class="price-regular">' . wc_price( max( $regular_prices ) ) . '</span>';

              } else {
                  // Без знижок → мінімальна ціна
                  echo '<span class="price-normal">' . wc_price( min( $regular_prices ) ) . '</span>';
              }

          } else {

              // ПРОСТИЙ ТОВАР
              if ( $product->is_on_sale() ) {
                  echo '<span class="price-sale">' . wc_price( $product->get_sale_price() ) . '</span>';
                  echo '<span class="price-regular">' . wc_price( $product->get_regular_price() ) . '</span>';
              } else {
                  echo '<span class="price-normal">' . wc_price( $product->get_price() ) . '</span>';
              }

          }
        ?>
        </div>
      </a>
    </div>
	</a>
</div>
