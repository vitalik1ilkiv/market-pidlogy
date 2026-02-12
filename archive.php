<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_header();
?>

  <main class="main-content">
    <div class="container">
      <div class="page-content">
        <div class="breadcrumbs">
          <?php
            protec_breadcrumbs([
              'separator' => '/',
              'container_class' => 'breadcrumbs'
            ]);
          ?>
        </div>

        <h1 class="page-title"><?php the_archive_title(); ?></h1>

        <?php if (have_posts()) : ?>
          <div class="news-grid">
            <?php while (have_posts()) : the_post(); ?>
              <?php get_template_part('template-parts/news-item'); ?>
            <?php endwhile; ?>
          </div>

          <?php
            the_posts_pagination([
              'prev_text' => '&laquo;',
              'next_text' => '&raquo;',
              'mid_size'  => 2,
            ]);
          ?>
        <?php else : ?>
          <p><?php esc_html_e('Записів не знайдено.', 'market-pidlogy'); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </main>

<?php get_footer(); ?>
