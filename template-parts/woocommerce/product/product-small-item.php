<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($args['product'])) {
    $product = $args['product'];
}

if (!isset($product) || !is_a($product, 'WC_Product')) {
    return;
}

$permalink = get_permalink($product->get_id());
?>

<a href="<?php echo esc_url($permalink); ?>" class="product-small <?php echo ! $product->is_in_stock() ? 'outofstock' : ''; ?>">
  <div class="product-small__image">
    <?php echo $product->get_image('woocommerce_gallery_thumbnail'); ?>
  </div>
  <div class="product-small__info">
    <span class="product-small__name"><?php echo esc_html(apply_filters('the_title', $product->get_name())); ?></span>
    <span class="product-small__price">
      <?php
        if ($product->is_type('variable')) {
            $prices = [];
            $sale_prices = [];
            foreach ($product->get_children() as $vid) {
                $v = wc_get_product($vid);
                if (!$v) continue;
                $r = (float) $v->get_regular_price();
                $s = (float) $v->get_sale_price();
                if ($r) $prices[] = $r;
                if ($s && $s < $r) $sale_prices[] = $s;
            }
            if (!empty($sale_prices)) {
                echo '<span class="price-sale">' . wc_price(min($sale_prices)) . '</span>';
                echo '<span class="price-regular">' . wc_price(max($prices)) . '</span>';
            } else {
                echo '<span class="price-normal">' . wc_price(min($prices)) . '</span>';
            }
        } else {
            if ($product->is_on_sale()) {
                echo '<span class="price-sale">' . wc_price($product->get_sale_price()) . '</span>';
                echo '<span class="price-regular">' . wc_price($product->get_regular_price()) . '</span>';
            } else {
                echo '<span class="price-normal">' . wc_price($product->get_price()) . '</span>';
            }
        }
      ?>
    </span>
  </div>
</a>
