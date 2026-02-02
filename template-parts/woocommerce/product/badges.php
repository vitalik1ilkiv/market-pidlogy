<?php
/**
 * Product badges template
 *
 * Usage: get_template_part('./template-parts/woocommerce/product/badges', null, ['product_id' => $product_id]);
 *
 * @var array $args Template arguments with 'product_id' key
 */

if (!defined('ABSPATH')) {
    exit;
}

$product_id = $args['product_id'] ?? get_the_ID();
?>

<div class="badges">
  <?php if (get_field('акція_товар', $product_id)) : ?>
    <span class="badge badge--sale">
      <?php _e('Акція', 'market-pidlogy'); ?>
    </span>
  <?php endif; ?>

  <?php if (get_field('новинка_товар', $product_id)) : ?>
    <span class="badge badge--new">
      <?php _e('Новинка', 'market-pidlogy'); ?>
    </span>
  <?php endif; ?>

  <?php if (get_field('бесплатная_доставка_по_украине', $product_id)) : ?>
    <span class="badge badge--delivery">
      <?php _e('Безкоштовна доставка', 'market-pidlogy'); ?>
    </span>
  <?php endif; ?>

  <?php if (get_field('rasprodazha', $product_id)) : ?>
    <span class="badge badge--clearance">
      <?php _e('Розпродаж', 'market-pidlogy'); ?>
    </span>
  <?php endif; ?>
</div>
