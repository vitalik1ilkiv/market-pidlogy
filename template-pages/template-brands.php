<?php
/**
 * Template Name: Бренди
 */

defined('ABSPATH') || exit;

get_header();

$brands = get_terms([
    'taxonomy'   => 'pa_brend',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

// Group brands by first letter (only letters and digits)
$grouped_brands = [];
$letters = [];

if (!is_wp_error($brands) && !empty($brands)) {
    foreach ($brands as $brand) {
        $first_letter = mb_strtoupper(mb_substr($brand->name, 0, 1, 'UTF-8'), 'UTF-8');
  
        if (!preg_match('/[\p{L}0-9]/u', $first_letter)) {
            continue;
        }

        if (is_numeric($first_letter)) {
            $first_letter = '0-9';
        }

        if (!isset($grouped_brands[$first_letter])) {
            $grouped_brands[$first_letter] = [];
            $letters[] = $first_letter;
        }

        $grouped_brands[$first_letter][] = $brand;
    }
}
?>

<div class="brands-page">
  <div class="container">
    <?php protec_breadcrumbs(['separator' => '/', 'container_class' => 'breadcrumbs']); ?>

    <h1 class="brands-page__title page-title">
      <?php the_title(); ?>
    </h1>

    <?php if (!empty($letters)) : ?>
      <div class="brands-page__alphabet" id="brands-alphabet">
        <?php foreach ($letters as $letter) : ?>
          <a class="brands-page__letter" href="#brand-<?php echo esc_attr($letter); ?>">
            <?php echo esc_html($letter); ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="brands-page__grid">
        <?php
          $current_letter = '';
          foreach ($grouped_brands as $letter => $group) :
            foreach ($group as $brand) :
              $image = get_field('brend_kartinka', $brand);
              $link  = get_term_link($brand);
              $id    = ($letter !== $current_letter) ? 'brand-' . esc_attr($letter) : '';
              $current_letter = $letter;
        ?>
            <a class="brands-page__item" <?php if ($id) echo 'id="' . $id . '"'; ?> href="<?php echo esc_url($link); ?>">
              <div class="brands-page__item-image">
                <picture>
                  <?php if ($image) : ?>
                    <img
                      decoding="async"
                      class="lazyload"
                      data-expand="1"
                      src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                      data-src="<?php echo esc_url($image['url']); ?>"
                      alt="<?php echo esc_attr($brand->name); ?>"
                      width="<?php echo esc_attr($image['width']); ?>"
                      height="<?php echo esc_attr($image['height']); ?>"
                    >
                  <?php endif; ?>
                </picture>
              </div>
              <h5 class="brands-page__item-name"><?php echo esc_html($brand->name); ?></h5>
            </a>
        <?php
            endforeach;
          endforeach;
        ?>
      </div>
    <?php else : ?>
      <p><?php _e('No brands found.', 'market-pidlogy'); ?></p>
    <?php endif; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var alphabet = document.getElementById('brands-alphabet');
    if (!alphabet) return;

    alphabet.addEventListener('click', function (e) {
      var link = e.target.closest('.brands-page__letter');
      if (!link) return;

      e.preventDefault();
      var targetId = link.getAttribute('href').substring(1);
      var target = document.getElementById(targetId);
      if (!target) return;

      var headerOffset = 100;
      var top = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;

      window.scrollTo({ top: top, behavior: 'smooth' });
    });
  });
</script>

<?php get_footer(); ?>
