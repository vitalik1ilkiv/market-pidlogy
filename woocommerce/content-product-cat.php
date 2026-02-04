<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div <?php wc_product_cat_class( 'category-item', $category ); ?>>
	<a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
    <div class="category-item__image">
      <picture>
        <?php
        $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
        if ($thumbnail_id) {
            $image_url = wp_get_attachment_image_url($thumbnail_id, 'woocommerce_thumbnail');
            $image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) ?: $category->name;
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
            echo wc_placeholder_img('woocommerce_thumbnail');
        } ?>
      </picture>
    </div>
		<div class="category-item__info">
      <h3 class="h4 text-center mt-2"><?php echo esc_html( $category->name ); ?></h3>
    </div>
	</a>
</div>
