<?php
/**
 * Template Name: Улюблені товари
 */

defined('ABSPATH') || exit;

get_header();

$favorite_ids = [];
if (!empty($_COOKIE['mp_favorites'])) {
    $favorite_ids = array_filter(array_map('absint', explode(',', $_COOKIE['mp_favorites'])));
}

$products = [];
if (!empty($favorite_ids)) {
    $products = wc_get_products([
        'include' => $favorite_ids,
        'limit'   => count($favorite_ids),
        'status'  => 'publish',
        'orderby' => 'post__in',
    ]);
}
?>

<div class="woocommerce">
  <div class="woocommerce-products-header">
    <div class="container">
      <?php woocommerce_breadcrumb(); ?>
      <h1 class="woocommerce-products-header__title page-title">
        <?php _e('Улюблені товари', 'market-pidlogy'); ?>
      </h1>
    </div>
  </div>

  <main id="main" class="site-main container" role="main">
    <?php if (!empty($products)) : ?>
      <div class="products-grid">
        <?php foreach ($products as $product) :
            get_template_part(
                'template-parts/woocommerce/product/product-item',
                null,
                ['product' => $product]
            );
        endforeach; ?>
      </div>
    <?php else : ?>
      <p class="favorites-page__empty">
        <?php _e('У вас поки немає улюблених товарів.', 'market-pidlogy'); ?>
      </p>
    <?php endif; ?>
  </main>
</div>

<?php get_footer(); ?>
