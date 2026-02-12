<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * @param int
 */
function send_woocommerce_order_to_telegram($order_id) {

    $telegram_bot_token = ID_TELEGRAM_BOT;
    $telegram_chat_id  = ID_TELEGRAM_CHAT;

    if (empty($telegram_bot_token) || empty($telegram_chat_id)) {
        error_log('Telegram: Токен бота або ID каналу не налаштовані');
        return;
    }

    $order = wc_get_order($order_id);

    if (!$order) {
        return;
    }

    // Перевіряємо, чи вже було відправлено повідомлення для цього замовлення
    $telegram_sent = $order->get_meta('_telegram_notification_sent', true);
    if ($telegram_sent) {
        return; // Вже відправлено, не дублюємо
    }

    $message = "🛒 *Нове замовлення #{$order_id}*\n\n";

    $message .= "👤 *Клієнт:*\n";
    $message .= "Ім'я: " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "\n";
    $message .= "Email: " . $order->get_billing_email() . "\n";
    $message .= "Телефон: " . $order->get_billing_phone() . "\n\n";

    // Адреса доставки
    if ($order->needs_shipping_address()) {
        $message .= "📍 *Адреса доставки:*\n";

        // Перевіряємо Нову Пошту
        $np_city = $order->get_meta('mrkv_ua_shipping_nova-poshta_city');
        $np_warehouse = $order->get_meta('mrkv_ua_shipping_nova-poshta_warehouse');

        // Перевіряємо Укрпошту (міжнародна)
        $up_city = $order->get_meta('mrkv_ua_shipping_ukr-poshta_international_city');
        $up_region = $order->get_meta('mrkv_ua_shipping_ukr-poshta_international_region');
        $up_street = $order->get_meta('mrkv_ua_shipping_ukr-poshta_international_street');
        $up_house = $order->get_meta('mrkv_ua_shipping_ukr-poshta_international_house');
        $up_flat = $order->get_meta('mrkv_ua_shipping_ukr-poshta_international_flat');
        $up_postcode = $order->get_meta('mrkv_ua_shipping_ukr-poshta_international_postcode');

        // Якщо є дані Нової Пошти
        if (!empty($np_city) && !empty($np_warehouse)) {
            $message .= "Нова Пошта\n";
            $message .= "Місто: " . $np_city . "\n";
            $message .= "Відділення: " . $np_warehouse . "\n";
        }
        // Якщо є дані Укрпошти
        elseif (!empty($up_city)) {
            $message .= "Укрпошта\n";
            if (!empty($up_region)) {
                $message .= "Область: " . $up_region . "\n";
            }
            $message .= "Місто: " . $up_city . "\n";
            if (!empty($up_postcode)) {
                $message .= "Індекс: " . $up_postcode . "\n";
            }
            if (!empty($up_street)) {
                $message .= "Вулиця: " . $up_street;
                if (!empty($up_house)) {
                    $message .= ", буд. " . $up_house;
                }
                if (!empty($up_flat)) {
                    $message .= ", кв. " . $up_flat;
                }
                $message .= "\n";
            }
        }
        // Якщо немає кастомних полів, використовуємо стандартні
        else {
            $message .= $order->get_shipping_address_1() . "\n";
            if ($order->get_shipping_address_2()) {
                $message .= $order->get_shipping_address_2() . "\n";
            }
            $message .= $order->get_shipping_city() . ", " . $order->get_shipping_postcode() . "\n";
            $message .= $order->get_shipping_state() . ", " . $order->get_shipping_country() . "\n";
        }

        $message .= "\n";
    }

    // Товари в замовленні
    $message .= "📦 *Товари:*\n";
    $items = $order->get_items();

    // Логування для діагностики
    if (empty($items)) {
        error_log("Telegram: Замовлення #{$order_id} не має товарів");
    }

    foreach ($items as $item) {
        $product = $item->get_product();

        // Отримуємо назву товару
        $product_name = $item->get_name();

        // Якщо є продукт, спробуємо отримати українську назву
        if ($product) {
            // Спроба отримати назву з поточної мови (WPML/Polylang)
            if (function_exists('pll_get_post')) {
                $uk_product_id = pll_get_post($product->get_id(), 'uk');
                if ($uk_product_id) {
                    $product_name = get_the_title($uk_product_id);
                }
            }
            // Для WPML
            elseif (function_exists('icl_object_id')) {
                $uk_product_id = icl_object_id($product->get_id(), 'product', false, 'uk');
                if ($uk_product_id) {
                    $product_name = get_the_title($uk_product_id);
                }
            }
        }

        $message .= "• " . $product_name . " x " . $item->get_quantity();

        // Додаємо варіації, якщо є
        $variation_text = '';
        if ($product && $product->is_type('variation')) {
            $attributes = $product->get_attributes();
            if (!empty($attributes)) {
                $variation_parts = array();
                foreach ($attributes as $attr_name => $attr_value) {
                    $variation_parts[] = $attr_value;
                }
                if (!empty($variation_parts)) {
                    $variation_text = ' (' . implode(', ', $variation_parts) . ')';
                }
            }
        }

        $message .= $variation_text . " - " . number_format($item->get_total(), 2, ',', ' ') . " ₴\n";
    }
    $message .= "\n";

    // Підсумки
    $message .= "💰 *Підсумок:*\n";
    $message .= "Сума товарів: " . number_format($order->get_subtotal(), 2, ',', ' ') . " ₴\n";

    if ($order->get_shipping_total() > 0) {
        $message .= "Доставка: " . number_format($order->get_shipping_total(), 2, ',', ' ') . " ₴\n";
    }

    if ($order->get_total_tax() > 0) {
        $message .= "Податок: " . number_format($order->get_total_tax(), 2, ',', ' ') . " ₴\n";
    }

    $message .= "*Всього: " . number_format($order->get_total(), 2, ',', ' ') . " ₴*\n\n";

    // Спосіб оплати
    $message .= "💳 Спосіб оплати: " . $order->get_payment_method_title() . "\n";

    // Спосіб доставки
    if ($order->get_shipping_method()) {
        $message .= "🚚 Спосіб доставки: " . $order->get_shipping_method() . "\n";
    }

    // Коментар клієнта
    if ($order->get_customer_note()) {
        $message .= "\n💬 *Коментар:*\n" . $order->get_customer_note();
    }

    // Надсилаємо повідомлення в Telegram
    $api_url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";

    $data = array(
        'chat_id' => $telegram_chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    );

    $response = wp_remote_post($api_url, array(
        'body' => $data,
        'timeout' => 15
    ));

    if (is_wp_error($response)) {
        error_log('Telegram error: ' . $response->get_error_message());
    } else {
        // Позначаємо, що повідомлення успішно відправлено
        $order->update_meta_data('_telegram_notification_sent', true);
        $order->save();
    }
}

// Підключаємо функцію до хука створення замовлення
// Використовуємо woocommerce_thankyou, щоб товари вже були збережені
add_action('woocommerce_thankyou', 'send_woocommerce_order_to_telegram', 10, 1);

// Також можна надсилати при зміні статусу на "Обробляється"
// add_action('woocommerce_order_status_processing', 'send_woocommerce_order_to_telegram', 10, 1);
