<?php

add_action('wp_ajax_live_search', 'ajax_live_search');
add_action('wp_ajax_nopriv_live_search', 'ajax_live_search');

function ajax_live_search() {
    $query = sanitize_text_field($_POST['query'] ?? '');
    if (empty($query)) {
        wp_send_json_error('Empty query');
    }

    global $wpdb;

    // --- Категорії ---
    $categories = get_terms([
        'taxonomy'   => 'product_cat',
        'name__like' => $query,
        'hide_empty' => true,
        'number'     => 5,
    ]);

    $cat_results = [];
    if (!is_wp_error($categories) && !empty($categories)) {
        foreach ($categories as $cat) {
            $cat_name = apply_filters('the_title', $cat->name);
            $cat_link = get_term_link($cat);
            if (!is_wp_error($cat_link)) {
                $cat_results[] = '<a href="' . esc_url($cat_link) . '" class="live-search__cat-item">' . esc_html($cat_name) . '</a>';
            }
        }
    }

    // --- Товари ---
    $filter = function($search, $wp_query) use ($query, $wpdb) {
        if (empty($search)) return $search;

        $search_term = $wpdb->esc_like($query);
        $search = " AND (
            ({$wpdb->posts}.post_title LIKE '%{$search_term}%')
            OR ({$wpdb->posts}.post_excerpt LIKE '%{$search_term}%')
            OR ({$wpdb->posts}.post_content LIKE '%{$search_term}%')
            OR EXISTS (
                SELECT 1 FROM {$wpdb->postmeta}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND {$wpdb->postmeta}.meta_key = '_sku'
                AND {$wpdb->postmeta}.meta_value LIKE '%{$search_term}%'
            )
        )";

        return $search;
    };

    add_filter('posts_search', $filter, 10, 2);

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 5,
        'post_status'    => 'publish',
        's'              => $query,
    ];

    $search_query = new WP_Query($args);

    remove_filter('posts_search', $filter, 10, 2);

    $total_found = $search_query->found_posts;
    $product_results = [];

    while ($search_query->have_posts()) {
        $search_query->the_post();
        $product = wc_get_product(get_the_ID());

        ob_start();
        get_template_part('template-parts/woocommerce/product/product-small-item', null, [
            'product' => $product,
        ]);
        $html = ob_get_clean();

        $product_results[] = $html;
    }

    wp_reset_postdata();

    wp_send_json([
        'success'    => true,
        'categories' => $cat_results,
        'data'       => $product_results,
        'total'      => $total_found,
    ]);
}
