<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		
  <div class="variations variations-container">
    <?php foreach ( $attributes as $attribute_name => $options ) : ?>
        <div class="variation-block">
            <?php  $select_id = sanitize_title( $attribute_name ); ?>
            <!-- Label атрибута -->
            <label class="variation-label" for="<?php echo esc_attr( $select_id ); ?>">
              <?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>
            </label>

            <!-- Кастомний дропдаун -->
            <div 
              class="variation-dropdown dropdown" 
              data-wp="dropdown" 
              data-wp-config='{
                "settings": {
                  "select": "#<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"
                }
              }'
            >
                <div data-head>
                    <span data-head-name><?php echo esc_html__( 'Виберіть значення', 'woocommerce' ); ?></span>
                </div>
                <div data-content>
                    <?php
                      $available_variations = $product->get_available_variations();

                      foreach ( $options as $option ) {
                          // перевіряємо, чи є доступна варіація з цим атрибутом
                          $is_available = false;
                          foreach ( $available_variations as $variation ) {
                              if ( isset( $variation['attributes']['attribute_' . $attribute_name] ) && $variation['attributes']['attribute_' . $attribute_name] === $option ) {
                                  $is_available = true;
                                  break;
                              }
                          }
                          if ( ! $is_available ) continue; // пропускаємо недоступні

                          $term = get_term_by( 'slug', $option, $attribute_name );
                          $label = $term ? $term->name : $option;
                          ?>
                          <div data-item data-value="<?php echo esc_attr( $option ); ?>">
                              <?php echo esc_html( $label ); ?>
                          </div>
                          <?php
                      }       
                    ?>
                </div>

                <!-- WooCommerce select (прихований) -->
                <?php
               
                wc_dropdown_variation_attribute_options( array(
                    'options'   => $options,
                    'attribute' => $attribute_name,
                    'product'   => $product,
                    'class'     => 'hidden-wc-select',
                ) );
                ?>
               
            </div>

        </div>
    <?php endforeach; ?>
  </div>
    
		<div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
