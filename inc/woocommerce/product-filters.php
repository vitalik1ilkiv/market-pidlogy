<?php

/**
 * Product Filters - логіка отримання фільтрів для категорій
 */

/**
 * Отримати діапазон цін для акційних товарів
 */
function get_sale_products_price_range()
{
    global $wpdb;

    $query = "
        SELECT MIN(CAST(pm_price.meta_value AS DECIMAL(10,2))) as min_price,
               MAX(CAST(pm_price.meta_value AS DECIMAL(10,2))) as max_price
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm_price ON p.ID = pm_price.post_id
        INNER JOIN {$wpdb->postmeta} pm_sale ON p.ID = pm_sale.post_id
        WHERE p.post_type = 'product'
        AND p.post_status = 'publish'
        AND pm_price.meta_key = '_price'
        AND pm_price.meta_value > 0
        AND pm_sale.meta_key = '_sale_price'
        AND pm_sale.meta_value != ''
        AND pm_sale.meta_value > 0
    ";

    $result = $wpdb->get_row($query);

    $min_price = ($result && isset($result->min_price)) ? $result->min_price : 0;
    $max_price = ($result && isset($result->max_price)) ? $result->max_price : 1000;

    return [
        'min' => floor($min_price),
        'max' => ceil($max_price),
    ];
}

/**
 * Отримати атрибути які використовуються в акційних товарах
 */
function get_sale_products_attributes()
{
    $attributes = [];

    // Отримуємо всі атрибути WooCommerce
    $attribute_taxonomies = wc_get_attribute_taxonomies();

    if (empty($attribute_taxonomies)) {
        return $attributes;
    }

    // Аргументи для запиту товарів на акції
    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_sale_price',
                'value' => '',
                'compare' => '!=',
            ],
            [
                'key' => '_sale_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC',
            ],
        ],
    ];

    $product_ids = get_posts($args);

    if (empty($product_ids)) {
        return $attributes;
    }

    // Для кожного атрибута перевіряємо чи є терми в товарах на акції
    foreach ($attribute_taxonomies as $attribute) {
        // Перевіряємо чи атрибут дозволено показувати у фільтрах
        $show_in_filters = get_option('wc_attr_show_in_filters_' . $attribute->attribute_id, '1');
        if ($show_in_filters === '0' || $show_in_filters === 0) {
            continue;
        }

        $taxonomy = 'pa_' . $attribute->attribute_name;

        // Отримуємо терми які використовуються в товарах на акції
        $terms = wp_get_object_terms($product_ids, $taxonomy, [
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            // Видаляємо дублікати
            $unique_terms = [];
            foreach ($terms as $term) {
                if (!isset($unique_terms[$term->term_id])) {
                    $unique_terms[$term->term_id] = $term;
                }
            }

            if (!empty($unique_terms)) {
                $attributes[] = [
                    'name' => $attribute->attribute_name,
                    'label' => $attribute->attribute_label,
                    'taxonomy' => $taxonomy,
                    'terms' => array_values($unique_terms),
                ];
            }
        }
    }

    return $attributes;
}

/**
 * Отримати діапазон цін для поточної категорії
 */
function get_category_price_range($category_id = null)
{
    global $wpdb;

    $tax_query = "";
    if ($category_id) {
        $tax_query = $wpdb->prepare(
            "INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
             INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
             WHERE tt.term_id = %d AND tt.taxonomy = 'product_cat'",
            $category_id
        );
    } else {
        $tax_query = "WHERE 1=1";
    }

    $query = "
        SELECT MIN(CAST(pm.meta_value AS DECIMAL(10,2))) as min_price,
               MAX(CAST(pm.meta_value AS DECIMAL(10,2))) as max_price
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        {$tax_query}
        AND p.post_type = 'product'
        AND p.post_status = 'publish'
        AND pm.meta_key = '_price'
        AND pm.meta_value > 0
    ";

    $result = $wpdb->get_row($query);

    return [
        'min' => floor($result->min_price ?? 0),
        'max' => ceil($result->max_price ?? 1000),
    ];
}

/**
 * Отримати атрибути які використовуються в товарах поточної категорії
 */
function get_category_attributes($category_id = null)
{
    $attributes = [];

    // Отримуємо всі атрибути WooCommerce
    $attribute_taxonomies = wc_get_attribute_taxonomies();

    if (empty($attribute_taxonomies)) {
        return $attributes;
    }

    // Аргументи для запиту товарів
    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
    ];

    // Якщо є категорія - додаємо фільтр
    if ($category_id) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category_id,
                'include_children' => true,
            ],
        ];
    }

    $product_ids = get_posts($args);

    if (empty($product_ids)) {
        return $attributes;
    }

    // Для кожного атрибута перевіряємо чи є терми в товарах категорії
    foreach ($attribute_taxonomies as $attribute) {
        // Перевіряємо чи атрибут дозволено показувати у фільтрах
        // get_option повертає false якщо опції не існує, або рядок "0"/"1"
        $show_in_filters = get_option('wc_attr_show_in_filters_' . $attribute->attribute_id, '1');
        if ($show_in_filters === '0' || $show_in_filters === 0) {
            continue;
        }

        $taxonomy = 'pa_' . $attribute->attribute_name;

        // Отримуємо терми які використовуються в товарах категорії
        $terms = wp_get_object_terms($product_ids, $taxonomy, [
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            // Видаляємо дублікати
            $unique_terms = [];
            foreach ($terms as $term) {
                if (!isset($unique_terms[$term->term_id])) {
                    $unique_terms[$term->term_id] = $term;
                }
            }

            if (!empty($unique_terms)) {
                $attributes[] = [
                    'name' => $attribute->attribute_name,
                    'label' => $attribute->attribute_label,
                    'taxonomy' => $taxonomy,
                    'terms' => array_values($unique_terms),
                ];
            }
        }
    }

    return $attributes;
}

/**
 * Отримати поточні активні фільтри з URL
 */
function get_active_filters()
{
    $active = [
        'min_price' => isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null,
        'max_price' => isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null,
        'attributes' => [],
    ];

    // Перевіряємо атрибути в URL (filter_pa_color, filter_pa_size, etc.)
    foreach ($_GET as $key => $value) {
        if (strpos($key, 'filter_') === 0 && !empty($value)) {
            $taxonomy = str_replace('filter_', '', $key);
            $active['attributes'][$taxonomy] = array_map('sanitize_text_field', explode(',', $value));
        }
    }

    return $active;
}

/**
 * Нормалізуємо GET-параметри фільтрів: масиви -> рядки через кому
 * WooCommerce QueryClauses очікує рядки, а HTML checkboxes відправляють масиви
 */
add_action('wp_loaded', 'normalize_filter_query_params');

function normalize_filter_query_params()
{
    foreach ($_GET as $key => $value) {
        if (strpos($key, 'filter_') === 0 && is_array($value)) {
            $_GET[$key] = implode(',', array_map('sanitize_text_field', $value));
            $_REQUEST[$key] = $_GET[$key];
        }
    }
}

/**
 * Застосовуємо фільтри до основного запиту
 */
add_action('pre_get_posts', 'apply_product_filters');

function apply_product_filters($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (!is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
        return;
    }

    $active_filters = get_active_filters();

    // Фільтр по ціні
    if (!empty($active_filters['min_price']) || !empty($active_filters['max_price'])) {
        $meta_query = $query->get('meta_query') ?: [];

        if (!empty($active_filters['min_price'])) {
            $meta_query[] = [
                'key' => '_price',
                'value' => $active_filters['min_price'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (!empty($active_filters['max_price'])) {
            $meta_query[] = [
                'key' => '_price',
                'value' => $active_filters['max_price'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        $query->set('meta_query', $meta_query);
    }

    // Фільтр по атрибутах
    if (!empty($active_filters['attributes'])) {
        $tax_query = $query->get('tax_query') ?: [];

        foreach ($active_filters['attributes'] as $taxonomy => $terms) {
            if (!empty($terms)) {
                $tax_query[] = [
                    'taxonomy' => sanitize_text_field($taxonomy),
                    'field' => 'slug',
                    'terms' => $terms,
                    'operator' => 'IN',
                ];
            }
        }

        if (!empty($tax_query)) {
            $tax_query['relation'] = 'AND';
            $query->set('tax_query', $tax_query);
        }
    }
}

/**
 * Зберігаємо параметри фільтрів в URL пагінації та сортування
 */
add_filter('woocommerce_get_catalog_ordering_args', 'preserve_filter_params_in_ordering', 10, 1);

function preserve_filter_params_in_ordering($args)
{
    return $args;
}

/**
 * Додаємо параметри фільтрів до посилань пагінації
 */
add_filter('paginate_links', 'add_filter_params_to_pagination');

function add_filter_params_to_pagination($link)
{
    $filter_params = [];

    // Зберігаємо параметри фільтрів
    foreach ($_GET as $key => $value) {
        if (strpos($key, 'filter_') === 0 || $key === 'min_price' || $key === 'max_price' || $key === 'orderby') {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $filter_params[] = $key . '[]=' . urlencode($v);
                }
            } else {
                $filter_params[] = $key . '=' . urlencode($value);
            }
        }
    }

    if (!empty($filter_params)) {
        $separator = strpos($link, '?') !== false ? '&' : '?';
        $link .= $separator . implode('&', $filter_params);
    }

    return $link;
}

/**
 * Додаємо параметри фільтрів до форми сортування
 */
add_action('woocommerce_before_shop_loop', 'add_filter_hidden_inputs_to_ordering', 25);

function add_filter_hidden_inputs_to_ordering()
{
    $active_filters = get_active_filters();

    if (empty($active_filters['min_price']) && empty($active_filters['max_price']) && empty($active_filters['attributes'])) {
        return;
    }

    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var orderingForm = document.querySelector(".woocommerce-ordering");
        if (orderingForm) {';

    if (!empty($active_filters['min_price'])) {
        echo 'var minPrice = document.createElement("input");
              minPrice.type = "hidden";
              minPrice.name = "min_price";
              minPrice.value = "' . esc_js($active_filters['min_price']) . '";
              orderingForm.appendChild(minPrice);';
    }

    if (!empty($active_filters['max_price'])) {
        echo 'var maxPrice = document.createElement("input");
              maxPrice.type = "hidden";
              maxPrice.name = "max_price";
              maxPrice.value = "' . esc_js($active_filters['max_price']) . '";
              orderingForm.appendChild(maxPrice);';
    }

    foreach ($active_filters['attributes'] as $taxonomy => $terms) {
        foreach ($terms as $term) {
            echo 'var attr = document.createElement("input");
                  attr.type = "hidden";
                  attr.name = "filter_' . esc_js($taxonomy) . '[]";
                  attr.value = "' . esc_js($term) . '";
                  orderingForm.appendChild(attr);';
        }
    }

    echo '}
    });
    </script>';
}
