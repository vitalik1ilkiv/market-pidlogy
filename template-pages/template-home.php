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