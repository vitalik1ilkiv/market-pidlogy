<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 *
 * @var bool   $readonly If the input should be set to readonly mode.
 * @var string $type     The input type attribute.
 */

defined( 'ABSPATH' ) || exit;

if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
	</div>
	<?php
} else {
	/* translators: %s: Quantity. */
	$labelledby = ! empty( $args['product_name'] ) ? sprintf( __( '%s quantity', 'woocommerce' ), strip_tags( $args['product_name'] ) ) : '';
	?>

	<?php if ( is_singular( 'product' ) ) :
		global $product;
		$vymir = $product->get_attribute( 'vymir-tovaru' );
		if ( empty( $vymir ) ) {
			$vymir = 'м²';
		}
	?>
		<div class="quantityin_product">
			<div class="quantity single-product__row">
				<span class="el_title td">
					<?php echo esc_html__( 'Specify the quantity', 'market-pidlogy' ) . ' ' . esc_html( $vymir ); ?>
				</span>
				<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>
        <div class="td">
          <input
					type="number"
					id="<?php echo esc_attr( $input_id ); ?>"
					class="input-text qty text"
					step="<?php echo esc_attr( $step ); ?>"
					min="<?php echo esc_attr( $min_value ); ?>"
					max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
					name="<?php echo esc_attr( $input_name ); ?>"
					value="<?php echo esc_attr( $input_value ); ?>"
					title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
					size="4"
					pattern="<?php echo esc_attr( $pattern ); ?>"
					inputmode="<?php echo esc_attr( $inputmode ); ?>"
					aria-labelledby="<?php echo esc_attr( $labelledby ); ?>" />
        </div>
      </div>
			<div class="total_price_in_product single-product__row">
				<span class="el_title td">
					<?php
					$product_price = $product->is_on_sale() ? $product->get_sale_price() : $product->get_regular_price();
					?>
					<?php _e( 'Together', 'market-pidlogy' ); ?>
				</span>
				<div class="tp_num td">
					<span></span> <?php _e( 'грн', 'market-pidlogy' ); ?>
				</div>
				<script>
				document.addEventListener('DOMContentLoaded', function() {
					var price = <?php echo esc_js( $product_price ); ?>;
					var $qty = jQuery('.quantityin_product input.qty');
					var $total = jQuery('.total_price_in_product .tp_num span');
					if (!$qty.length || !$total.length) return;

					function update() {
						$total.text((price * parseFloat($qty.val() || 0)).toFixed(2));
					}

					update();
					$qty.on('input change', update);
				});
				</script>
			</div>
		</div>
	<?php else : ?>
		<div class="quantity">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>
			<input
				type="number"
				id="<?php echo esc_attr( $input_id ); ?>"
				class="input-text qty text"
				step="<?php echo esc_attr( $step ); ?>"
				min="<?php echo esc_attr( $min_value ); ?>"
				max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
				name="<?php echo esc_attr( $input_name ); ?>"
				value="<?php echo esc_attr( $input_value ); ?>"
				title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
				size="1"
				pattern="<?php echo esc_attr( $pattern ); ?>"
				inputmode="<?php echo esc_attr( $inputmode ); ?>"
				aria-labelledby="<?php echo esc_attr( $labelledby ); ?>" />
		</div>
	<?php endif; ?>

	<?php
}
