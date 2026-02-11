<?php
  remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
  remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

  // remove review
  add_filter( 'woocommerce_product_tabs', function( $tabs ) {
    unset( $tabs['reviews'] );
    return $tabs;
  }, 98 );
  remove_action( 'woocommerce_after_single_product_summary', 'comments_template', 50 );

  remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

  // 🔥 Прибрати вкладки повністю
  add_filter( 'woocommerce_product_tabs', '__return_empty_array', 20 );

  remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

  add_action( 'woocommerce_single_product_summary', function() {
    global $product;
    if ( $product && $product->get_short_description() ) {
        echo '<div class="single-product__short-description">';
        echo apply_filters( 'the_content', $product->get_short_description() );
        echo '</div>';
    }
  }, 6 );

  add_action( 'woocommerce_after_single_product_summary', function() {
    global $product;
    if ( $product && $product->get_description() ) {
        $description = $product->get_description();

        // // Видаляємо залишки структури табів WooCommerce
        // $description = preg_replace( '/<div[^>]*class="[^"]*woocommerce-tabs[^"]*"[^>]*>/i', '', $description );
        // $description = preg_replace( '/<div[^>]*class="[^"]*wc-tabs[^"]*"[^>]*>/i', '', $description );
        // $description = preg_replace( '/<div[^>]*id="tab-description"[^>]*>/i', '', $description );
        // $description = preg_replace( '/<div[^>]*class="[^"]*woocommerce-Tabs-panel[^"]*"[^>]*>/i', '', $description );

        // // Видаляємо зайві закриваючі </div> в кінці
        // $description = preg_replace( '/(<\/div>\s*){2,}$/i', '', $description );

        echo '<div class="single-product__full-description">';
        echo '<h2 class="h4 mb-2">' . esc_html__( 'Description', 'market-pidlogy' ) . '</h2>';
        echo apply_filters( 'the_content', $description );
        echo '</div>';
    }
  }, 10 );

  // Відновлюємо кошик при скасуванні/невдалій оплаті
  add_action( 'woocommerce_order_status_cancelled', 'restore_cart_on_cancelled_order' );
  add_action( 'woocommerce_order_status_failed', 'restore_cart_on_cancelled_order' );
  function restore_cart_on_cancelled_order( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    if ( WC()->cart->is_empty() ) {
      foreach ( $order->get_items() as $item ) {
        $product_id   = $item->get_variation_id() ?: $item->get_product_id();
        $quantity     = $item->get_quantity();
        WC()->cart->add_to_cart( $product_id, $quantity );
      }
    }
  }

  // Редірект на кошик при поверненні зі скасованої оплати
  add_action( 'template_redirect', 'redirect_cancelled_order_to_cart' );
  function redirect_cancelled_order_to_cart() {
    if ( ! is_wc_endpoint_url( 'order-pay' ) && ! is_wc_endpoint_url( 'order-received' ) ) return;

    if ( isset( $_GET['cancel_order'] ) || ( isset( $_GET['key'] ) && isset( $_GET['order'] ) ) ) {
      $order_id = isset( $_GET['order'] ) ? absint( $_GET['order'] ) : 0;
      if ( ! $order_id && is_wc_endpoint_url( 'order-received' ) ) {
        global $wp;
        $order_id = absint( $wp->query_vars['order-received'] );
      }

      if ( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( $order && in_array( $order->get_status(), array( 'cancelled', 'failed', 'pending' ) ) ) {
          // Відновлюємо кошик якщо пустий
          if ( WC()->cart->is_empty() ) {
            foreach ( $order->get_items() as $item ) {
              $product_id = $item->get_variation_id() ?: $item->get_product_id();
              $quantity   = $item->get_quantity();
              WC()->cart->add_to_cart( $product_id, $quantity );
            }
          }

          // Скасовуємо замовлення якщо pending
          if ( $order->get_status() === 'pending' ) {
            $order->update_status( 'cancelled', __( 'Оплату скасовано клієнтом.', 'market-pidlogy' ) );
          }

          wp_safe_redirect( wc_get_cart_url() );
          exit;
        }
      }
    }
  }

  add_filter( 'woocommerce_checkout_fields', 'custom_checkout_fields_priority' );
  function custom_checkout_fields_priority( $fields ) {

    // Робимо email необов'язковим
    $fields['billing']['billing_email']['required'] = false;

    // Робимо телефон обов'язковим
    $fields['billing']['billing_phone']['required'] = true;

    return $fields;
  }

  remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );

  add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    // Імʼя — обовʼязкове
    $fields['billing']['billing_first_name']['custom_attributes'] = array(
        'data-validate' => '{"required":true}'
    );

    // Телефон — обовʼязковий + валідація формату
    $fields['billing']['billing_phone']['custom_attributes'] = array(
        'data-validate' => '{"required":true, "telUa": true}'
    );

    $fields['billing']['billing_phone']['wrapper_class'][] = 'field';
    $fields['billing']['billing_phone']['validate'] = array('required', 'tel');

    add_filter( 'woocommerce_form_field_billing_phone', function( $field_html, $key, $args, $value ) {
        $field_html = str_replace(
            '<p class="form-row ',
            '<p class="form-row field" data-validate=\'{"required":true, "telUa": true}\' ',
            $field_html
        );
        return $field_html;
    }, 10, 4 );

    return $fields;
  });

  // 1️⃣ Відкриваємо wrapper
  add_action( 'woocommerce_single_product_summary', function() {
      echo '<div class="single-product__summary-wrapper">';
  }, 1 );

  // 2️⃣ Закриваємо wrapper В КІНЦІ summary (але ще до after_single_product_summary)
  add_action( 'woocommerce_single_product_summary', function() {
      echo '</div>'; // закриваємо single-product__summary-wrapper
  }, 51 ); // високий пріоритет — в кінці summary

  // 3️⃣ Додаємо таблицю атрибутів ПІСЛЯ summary (вже за межами wrapper)
  add_action( 'woocommerce_single_product_summary', function() {

      if ( ! is_product() ) return;

      $product = wc_get_product( get_the_ID() );
      if ( ! $product ) return;

      if ( ! $product->has_attributes() ) return;

      echo '<div class="single-product__attributes mt-4">';

      wc_get_template(
          'single-product/tabs/additional-information.php',
          array( 'product' => $product )
      );

      echo '</div>';

  }, 52 ); // пріоритет 1 — на самому початку after_single_product_summary

  add_action('woocommerce_after_edit_attribute_fields', function () {
    // Отримуємо ID атрибута з URL
    $attribute_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;
    if (!$attribute_id) return;

    // беремо збережене значення
    $value = get_option('wc_attr_show_in_filters_' . $attribute_id);

    // якщо значення ще не збережене → включено за замовчуванням
    $is_checked = ($value === false || $value === '1' || $value === 1);
    ?>
    <tr class="form-field">
      <th scope="row" valign="top">
        <label for="show_in_filters">Показувати у фільтрах</label>
      </th>
      <td>
        <input
          type="checkbox"
          name="show_in_filters"
          id="show_in_filters"
          value="1"
          <?php checked($is_checked); ?>
        />
        <p class="description">
          Якщо вимкнено — атрибут не буде відображатися у фільтрах.
        </p>
      </td>
    </tr>
    <?php
  });

  add_action('woocommerce_attribute_updated', function ($attribute_id) {
    // Дебаг - можна видалити після перевірки
    error_log('woocommerce_attribute_updated: ' . $attribute_id);
    error_log('show_in_filters POST: ' . (isset($_POST['show_in_filters']) ? $_POST['show_in_filters'] : 'NOT SET'));

    if (isset($_POST['show_in_filters'])) {
      update_option('wc_attr_show_in_filters_' . $attribute_id, '1');
    } else {
      update_option('wc_attr_show_in_filters_' . $attribute_id, '0');
    }

  });

  // Також для додавання нового атрибута
  add_action('woocommerce_attribute_added', function ($attribute_id) {
    if (isset($_POST['show_in_filters'])) {
      update_option('wc_attr_show_in_filters_' . $attribute_id, '1');
    } else {
      update_option('wc_attr_show_in_filters_' . $attribute_id, '0');
    }
  });

  // Track recently viewed products in cookie
  add_action( 'template_redirect', function() {
    if ( ! is_singular( 'product' ) ) {
      return;
    }

    $product_id = get_the_ID();
    $viewed = ! empty( $_COOKIE['woocommerce_recently_viewed'] )
      ? array_filter( array_map( 'absint', explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ) )
      : array();

    // Remove current product if already in list
    $viewed = array_diff( $viewed, array( $product_id ) );
    // Add to end
    $viewed[] = $product_id;
    // Keep last 15
    $viewed = array_slice( $viewed, -15 );

    wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed ) );
  });

  // Display recently viewed products after related products
  add_action( 'woocommerce_after_single_product_summary', function() {
    wc_get_template( 'single-product/recently-viewed.php' );
  }, 25 );

  add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );
  function woocommerce_header_add_to_cart_fragment( $fragments ) {
      $count = WC()->cart->cart_contents_count;
      ob_start();
      ?>
        <span class="icon-count icon-cart-count"<?php if ( $count === 0 ) echo ' style="display:none;"'; ?>><?php echo $count; ?></span>
      <?php

      $fragments['span.icon-cart-count'] = ob_get_clean();

      return $fragments;
  }

