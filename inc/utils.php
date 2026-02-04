<?php

/**
 * Очищає номер телефону: залишає тільки цифри і знак плюс.
 *
 * @param string $phone Номер телефону.
 * @return string Очищений номер телефону для використання в tel:
 */
function sanitize_phone($phone) {
    return preg_replace('/[^\d+]/', '', $phone);
}

/**
 * Виводить хлібні крихти (breadcrumbs) для сторінок категорій записів.
 *
 * @param array $args Масив параметрів для налаштування breadcrumbs.
 * @return void
 */
function protec_breadcrumbs($args = []) {
    // Параметри за замовчуванням
    $defaults = [
        'home_label'       => __( 'Home', 'market-pidlogy' ),
        'separator'        => '/',
        'container_class'  => 'breadcrumbs',
        'list_class'       => 'breadcrumbs__list',
        'item_class'       => 'breadcrumbs__item',
        'link_class'       => 'breadcrumbs__link',
        'active_class'     => 'breadcrumbs__item--active',
        'show_on_home'     => false,
    ];

    $args = wp_parse_args($args, $defaults);

    // Не показувати на головній сторінці, якщо вказано
    if (is_front_page() && !$args['show_on_home']) {
        return;
    }

    $breadcrumbs = [];

    // Головна сторінка
    $breadcrumbs[] = [
        'url'   => home_url('/'),
        'title' => $args['home_label'],
    ];

    // Тег записів
    if (is_tag()) {
        $tag = get_queried_object();

        // Поточний тег (без посилання)
        $breadcrumbs[] = [
            'title' => $tag->name,
        ];
    }

    // Категорія записів (archive)
    elseif (is_category()) {
        $category = get_queried_object();

        // Якщо є батьківські категорії, додаємо їх
        if ($category->parent) {
            $parent_categories = [];
            $parent_id = $category->parent;

            while ($parent_id) {
                $parent = get_category($parent_id);
                $parent_categories[] = [
                    'url'   => get_category_link($parent->term_id),
                    'title' => $parent->name,
                ];
                $parent_id = $parent->parent;
            }

            // Додаємо батьківські категорії у правильному порядку
            $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_categories));
        }

        // Поточна категорія (без посилання)
        $breadcrumbs[] = [
            'title' => $category->name,
        ];
    }

    // Таксономія (для кастомних типів записів)
    elseif (is_tax()) {
        $term = get_queried_object();
        $taxonomy = get_taxonomy($term->taxonomy);

        // Якщо є батьківські терміни, додаємо їх
        if ($term->parent) {
            $parent_terms = [];
            $parent_id = $term->parent;

            while ($parent_id) {
                $parent = get_term($parent_id, $term->taxonomy);
                $parent_terms[] = [
                    'url'   => get_term_link($parent),
                    'title' => $parent->name,
                ];
                $parent_id = $parent->parent;
            }

            // Додаємо батьківські терміни у правильному порядку
            $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_terms));
        }

        // Поточний термін (без посилання)
        $breadcrumbs[] = [
            'title' => $term->name,
        ];
    }

    // Архів типу запису
    elseif (is_post_type_archive()) {
        $post_type = get_post_type_object(get_post_type());

        $breadcrumbs[] = [
            'title' => $post_type->labels->name,
        ];
    }

    // Одиночний запис
    elseif (is_single()) {
        $post_type = get_post_type();

        // Якщо це звичайний пост
        if ($post_type === 'post') {
            $categories = get_the_category();

            if ($categories) {
                $category = $categories[0];

                // Додаємо батьківські категорії
                if ($category->parent) {
                    $parent_categories = [];
                    $parent_id = $category->parent;

                    while ($parent_id) {
                        $parent = get_category($parent_id);
                        $parent_categories[] = [
                            'url'   => get_category_link($parent->term_id),
                            'title' => $parent->name,
                        ];
                        $parent_id = $parent->parent;
                    }

                    $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_categories));
                }

                // Поточна категорія
                $breadcrumbs[] = [
                    'url'   => get_category_link($category->term_id),
                    'title' => $category->name,
                ];
            }
        }
        // Якщо це кастомний тип запису
        else {
            $post_type_obj = get_post_type_object($post_type);

            // Посилання на архів типу запису
            if ($post_type_obj->has_archive) {
                $breadcrumbs[] = [
                    'url'   => get_post_type_archive_link($post_type),
                    'title' => $post_type_obj->labels->name,
                ];
            }
        }

        // Поточний запис (без посилання)
        $breadcrumbs[] = [
            'title' => get_the_title(),
        ];
    }

    // Сторінка
    elseif (is_page()) {
        // Якщо є батьківські сторінки
        if (wp_get_post_parent_id(get_the_ID())) {
            $parent_pages = [];
            $parent_id = wp_get_post_parent_id(get_the_ID());

            while ($parent_id) {
                $parent_pages[] = [
                    'url'   => get_permalink($parent_id),
                    'title' => get_the_title($parent_id),
                ];
                $parent_id = wp_get_post_parent_id($parent_id);
            }

            $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_pages));
        }

        // Поточна сторінка (без посилання)
        $breadcrumbs[] = [
            'title' => get_the_title(),
        ];
    }

    // Пошук
    elseif (is_search()) {
        $breadcrumbs[] = [
            'title' => sprintf(__('Search results: %s', 'market-pidlogy'), get_search_query()),
        ];
    }

    // 404
    elseif (is_404()) {
        $breadcrumbs[] = [
            'title' => __('Page not found', 'market-pidlogy'),
        ];
    }

    // Виводимо breadcrumbs
    if (count($breadcrumbs) > 0) {
        echo '<div class="' . esc_attr($args['container_class']) . '">';
        echo '<ul class="' . esc_attr($args['list_class']) . '">';

        $total = count($breadcrumbs);
        foreach ($breadcrumbs as $index => $crumb) {
            $is_last = ($index === $total - 1);
            $item_class = $args['item_class'];

            if ($is_last) {
                $item_class .= ' ' . $args['active_class'];
            }

            echo '<li class="' . esc_attr($item_class) . '">';

            if (!empty($crumb['url']) && !$is_last) {
                echo '<a href="' . esc_url($crumb['url']) . '" class="' . esc_attr($args['link_class']) . '">';
                echo esc_html($crumb['title']);
                echo '</a>';
            } else {
                echo '<span>' . esc_html($crumb['title']) . '</span>';
            }

            if (!$is_last) {
                echo '<span class="breadcrumbs__separator">' . esc_html($args['separator']) . '</span>';
            }

            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
    }
}
