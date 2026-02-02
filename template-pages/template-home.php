<?php
/**
 * The template for displaying custom page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

/**
 * Template Name: Home
 */

  get_header();
?>

	<!-- Main content -->
	<main class="main-content">
		<!-- Page content -->
		<div class="page-content">

      <?php
        get_template_part('template-parts/blocks/main-slider');
      ?>

      <?php get_template_part('./template-parts/blocks/sale-products'); ?>

      <?php get_template_part('template-parts/blocks/category-products', null, [
        'category'      => 'laminat',
        'title'         => 'Ламінат',
        'acf_title'     => 'laminate_title',
        'acf_link_text' => 'laminate_link_text',
        'acf_link'      => 'laminate_link',
      ]); ?>

      <?php get_template_part('template-parts/blocks/category-products', null, [
        'category'      => 'vinilovyj-pol',
        'title'         => 'Вінілова підлога',
        'acf_title'     => 'floor_title',
        'acf_link_text' => 'floor_link_text',
        'acf_link'      => 'floor_link',
        'with_bg'  => true
      ]); ?>

      <?php
        get_template_part('template-parts/blocks/brands-slider');
      ?>

      <article class="article">
        <?php
          the_post();
          the_content();
        ?>
      </article>

		</div>
		<!-- END: Page content -->

	</main>
	<!-- END: Main content -->

<?php get_footer(); ?>