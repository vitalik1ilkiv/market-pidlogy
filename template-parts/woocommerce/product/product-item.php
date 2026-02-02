<?php
/**
 * Template for displaying a single product item
 *
 * @var WC_Product $product The product object
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (isset($args['product'])) {
    $product = $args['product'];
}

if (!isset($product) || !is_a($product, 'WC_Product')) {
    return;
}
?>

<div class="product-item <?php echo ! $product->is_in_stock() ? 'outofstock' : ''; ?>" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
  <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="product-item__body" aria-label="Перейти до товару">

    <div class="product-item__image-box">
      <div class="product-item__image">
        <picture>
          <?php
            $image_id = $product->get_image_id();
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'product-card');
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $product->get_name();
            ?>
              <img
                decoding="sync"
                class="lazyload"
                data-expand="1"
                src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                data-src="<?= esc_url($image_url); ?>"
                alt="<?= esc_attr($image_alt); ?>"
              />
            <?php } else {
                echo wc_placeholder_img('product-card');
            } ?>
          </picture>
      </div>
      <?php if ($product->is_in_stock()) : ?>
        <div class="product-item__action-wrap">
          <a class="action action-primary" href="<?php echo esc_url(add_query_arg('add-to-cart', $product->get_id(), get_permalink($product->get_id()))); ?>" aria-label="Купити товар">
            <?php _e('Купити', 'market-pidlogy'); ?>
          </a>
        </div>
      <?php endif; ?>
      <div class="product-item__action-icons">
        <button class="button-favorite" data-wp="favorites" aria-label="Додати до списку бажань">
          <svg class="icon icon--favorite" width="16" height="16">
            <use xlink:href="#icon-favorite"></use>
          </svg>
        </button>

        <button>
          <svg class="icon icon--compare" width="16" height="16" aria-label="Додати до порівняння">
            <use xlink:href="#icon-compare"></use>
          </svg>
        </button>
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
