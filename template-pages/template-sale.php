<?php
/**
 * Template Name: Акційні товари
 * Description: Сторінка з акційними товарами з фільтрами
 */

defined('ABSPATH') || exit;

get_header('shop');

?>
<div class="woocommerce">
  <div class="woocommerce-products-header">
    <div class="container">
      <?php woocommerce_breadcrumb(); ?>
      <h1 class="woocommerce-products-header__title page-title">Акційні товари</h1>
    </div>
  </div>

  <main id="main" class="site-main container" role="main">
    <div class="archive-products-wrapper" style="grid: unset">
      <div class="archive-products-content">
        <?php echo do_shortcode('[products on_sale="true" limit="15" paginate="true" columns="5"]'); ?>
      </div>
    </div>
  </main>
</div>
<?php
get_footer('shop');
