<?php

/**
 * Template: Product Filters
 * Динамічні фільтри для категорій товарів (без AJAX)
 */

$category_id = is_product_category() ? get_queried_object_id() : 0;
$price_range = get_category_price_range($category_id);
$attributes = get_category_attributes($category_id);
$active_filters = get_active_filters();

// Поточні значення ціни
$current_min = $active_filters['min_price'] ?? $price_range['min'];
$current_max = $active_filters['max_price'] ?? $price_range['max'];

// Базовий URL для форми
$form_action = is_product_category() ? get_term_link(get_queried_object()) : get_permalink(wc_get_page_id('shop'));

// Перевіряємо чи є активні фільтри
$has_active_filters = !empty($active_filters['min_price']) ||
                      !empty($active_filters['max_price']) ||
                      !empty($active_filters['attributes']);
?>

<aside class="product-filters js-product-filters">
  <button class="product-filters__close js-filter-close" type="button" aria-label="<?php esc_attr_e('Close filters', 'market-pidlogy'); ?>">
    <svg class="icon icon--close" width="24" height="24">
      <use xlink:href="#icon-close"></use>
    </svg>
  </button>
  <div class="product-filters__header">
    <h3 class="product-filters__title"><?php _e('Selection of parameters', 'market-pidlogy'); ?></h3>
    <?php if ($has_active_filters) : ?>
      <a href="<?php echo esc_url($form_action); ?>" class="product-filters__reset">
        <?php _e('Cast', 'market-pidlogy'); ?>
      </a>
    <?php endif; ?>
  </div>

  <form class="product-filters__form js-filters-form" method="get" action="<?php echo esc_url($form_action); ?>">

    <?php
    // Зберігаємо поточне сортування
    if (isset($_GET['orderby'])) : ?>
      <input type="hidden" name="orderby" value="<?php echo esc_attr($_GET['orderby']); ?>">
    <?php endif; ?>

    <!-- Фільтр по ціні -->
    <div class="product-filters__section">
      <div class="product-filters__section-header js-filter-toggle _open" data-target="price">
        <h4 class="product-filters__section-title"><?php _e('Price', 'market-pidlogy'); ?></h4>
        <span class="product-filters__toggle-icon">
          <svg width="12" height="12" viewBox="0 0 12 12">
            <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
          </svg>
        </span>
      </div>
      <div class="product-filters__section-content" id="filter-price" style="display: block;">
        <div class="price-filter">
          <div class="price-filter__inputs">
            <div class="price-filter__field">
              <label for="min-price"><?php _e('From', 'market-pidlogy'); ?></label>
              <input
                type="number"
                id="min-price"
                name="min_price"
                value="<?php echo esc_attr($current_min); ?>"
                min="<?php echo esc_attr($price_range['min']); ?>"
                max="<?php echo esc_attr($price_range['max']); ?>"
                class="js-price-input"
              >
              <span class="price-filter__currency"><?php echo get_woocommerce_currency_symbol(); ?></span>
            </div>
            <span class="price-filter__separator">—</span>
            <div class="price-filter__field">
              <label for="max-price"><?php _e('To', 'market-pidlogy'); ?></label>
              <input
                type="number"
                id="max-price"
                name="max_price"
                value="<?php echo esc_attr($current_max); ?>"
                min="<?php echo esc_attr($price_range['min']); ?>"
                max="<?php echo esc_attr($price_range['max']); ?>"
                class="js-price-input"
              >
              <span class="price-filter__currency"><?php echo get_woocommerce_currency_symbol(); ?></span>
            </div>
          </div>
          <div class="price-filter__slider">
            <div class="price-filter__track">
              <div class="price-filter__range js-price-range"></div>
            </div>
            <input
              type="range"
              class="price-filter__slider-input js-price-slider"
              data-type="min"
              aria-label="<?php esc_attr_e('Minimum price', 'market-pidlogy'); ?>"
              min="<?php echo esc_attr($price_range['min']); ?>"
              max="<?php echo esc_attr($price_range['max']); ?>"
              value="<?php echo esc_attr($current_min); ?>"
            >
            <input
              type="range"
              class="price-filter__slider-input js-price-slider"
              data-type="max"
              aria-label="<?php esc_attr_e('Maximum price', 'market-pidlogy'); ?>"
              min="<?php echo esc_attr($price_range['min']); ?>"
              max="<?php echo esc_attr($price_range['max']); ?>"
              value="<?php echo esc_attr($current_max); ?>"
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Фільтри по атрибутах -->
    <?php foreach ($attributes as $attribute) :
      $is_active = isset($active_filters['attributes'][$attribute['taxonomy']]);
      $is_open = $is_active ? '_open' : '';
      $display = $is_active ? 'block' : 'none';
    ?>
      <div class="product-filters__section">
        <div class="product-filters__section-header js-filter-toggle <?php echo $is_open; ?>" data-target="<?php echo esc_attr($attribute['name']); ?>">
          <h4 class="product-filters__section-title">
            <?php echo esc_html($attribute['label']); ?>
            <?php if ($is_active) : ?>
              <span class="product-filters__active-count">(<?php echo count($active_filters['attributes'][$attribute['taxonomy']]); ?>)</span>
            <?php endif; ?>
          </h4>
          <span class="product-filters__toggle-icon">
            <svg width="12" height="12" viewBox="0 0 12 12">
              <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
            </svg>
          </span>
        </div>
        <div class="product-filters__section-content" id="filter-<?php echo esc_attr($attribute['name']); ?>" style="display: <?php echo $display; ?>;">
          <ul class="attribute-filter">
            <?php foreach ($attribute['terms'] as $term) :
              $is_checked = $is_active && in_array($term->slug, $active_filters['attributes'][$attribute['taxonomy']]);
            ?>
              <li class="attribute-filter__item">
                <label class="attribute-filter__label">
                  <input
                    type="checkbox"
                    data-taxonomy="<?php echo esc_attr($attribute['taxonomy']); ?>"
                    value="<?php echo esc_attr($term->slug); ?>"
                    class="attribute-filter__input js-attribute-filter"
                    <?php checked($is_checked); ?>
                  >
                  <span class="attribute-filter__checkbox"></span>
                  <span class="attribute-filter__name"><?php echo esc_html($term->name); ?></span>
                  <!-- <span class="attribute-filter__count">(<?php echo esc_html($term->count); ?>)</span> -->
                </label>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- Кнопки -->
    <div class="product-filters__actions">
      <button type="submit" class="product-filters__apply action action-primary">
        <?php _e('Apply', 'market-pidlogy'); ?>
      </button>
      <?php if ($has_active_filters) : ?>
        <a href="<?php echo esc_url($form_action); ?>" class="product-filters__reset-btn action action-primary-transparent">
          <?php _e('Cast', 'market-pidlogy'); ?>
        </a>
      <?php endif; ?>
    </div>
  </form>
</aside>
