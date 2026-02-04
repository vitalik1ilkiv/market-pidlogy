<?php
/**
 * Block: Category Products (grid, no slider)
 *
 * Args:
 *   category  (string) — slug категорії
 *   title     (string) — заголовок за замовчуванням
 *   limit     (int)    — кількість товарів (default 4)
 *   acf_title (string) — назва ACF поля для заголовка
 *   acf_link_text (string) — назва ACF поля для тексту посилання
 *   acf_link  (string) — назва ACF поля для URL посилання
 *   with_bg   (bool)   — додати клас with-bg до секції
 */

defined('ABSPATH') || exit;

$category      = $args['category'] ?? '';
$default_title = $args['title'] ?? '';
$limit         = $args['limit'] ?? 4;
$acf_title     = $args['acf_title'] ?? '';
$acf_link_text = $args['acf_link_text'] ?? '';
$acf_link      = $args['acf_link'] ?? '';
$with_bg       = !empty($args['with_bg']);

if (!$category) {
    return;
}

$products = wc_get_products([
    'category' => [$category],
    'limit'    => $limit,
    'status'   => 'publish',
    'orderby'  => 'date',
    'order'    => 'DESC',
]);

if (empty($products)) {
    return;
}

$title     = ($acf_title && get_field($acf_title)) ? get_field($acf_title) : $default_title;
$link_text = ($acf_link_text && get_field($acf_link_text)) ? get_field($acf_link_text) : __('Watch all', 'market-pidlogy');
$link      = ($acf_link && get_field($acf_link)) ? get_field($acf_link) : '';

if (!$link) {
    $term = get_term_by('slug', $category, 'product_cat');
    if ($term) {
        $link = get_term_link($term);
    }
}
?>

<section class="section section-product section-<?php echo esc_attr($category); ?><?php echo $with_bg ? ' with-bg' : ''; ?>">
  <div class="container">
    <h2 class="section-product__title"><?php echo esc_html($title); ?></h2>
    <?php if ($link) : ?>
      <div class="section-product__link">
        <a class="action action-color" href="<?php echo esc_url($link); ?>">
          <?php echo esc_html($link_text); ?>
        </a>
      </div>
    <?php endif; ?>
    <div class="products-grid">
      <?php foreach ($products as $product) :
          get_template_part('template-parts/woocommerce/product/product-item', null, ['product' => $product]);
      endforeach; ?>
    </div>
  </div>
</section>
