<?php
/**
 * The template for displaying custom page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

  get_header();
?>

	<!-- Main content -->
	<main class="main-content">
    <div class="container">
      <!-- Page content -->
      <div class="page-content">
        <div class="breadcrumbs">
          <?php 
            protec_breadcrumbs([
              'separator' => '/',
              'container_class' => 'breadcrumbs'
            ]); 
          ?>
        </div>
        <article class="article">
          <?php
            the_post();
            the_content();
          ?>
        </article>
      </div>
      <!-- END: Page content -->
    </div>
	</main>
	<!-- END: Main content -->

<?php get_footer(); ?>