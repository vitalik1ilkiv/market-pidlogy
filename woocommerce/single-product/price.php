<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$s_pack = $product->get_attribute( 'v-pachtsi' );
if ( empty( $s_pack ) ) {
	$s_pack = 1;
}

$vymir = $product->get_attribute( 'vymir-tovaru' );
if ( empty( $vymir ) ) {
	$vymir = 'м²';
}
?>

<p class="single-product__price single-product__row">
	<span class="el_title td">
		<?php echo esc_html__( 'Price per', 'market-pidlogy' ) . ' ' . esc_html( $vymir ); ?>
	</span>
	<?php echo $product->get_price_html(); ?>
</p>
<p class="s_pack single-product__row">
	<span class="el_title td">
		<?php esc_html_e( 'In a pack', 'market-pidlogy' ); ?>
	</span>
	<?php echo esc_html( $s_pack ) . ' ' . esc_html( $vymir ); ?>
</p>
