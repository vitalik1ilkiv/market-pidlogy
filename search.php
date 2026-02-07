<?php
/**
 * The template for displaying search results
 * Displays products in WooCommerce catalog style
 *
 * @package Badminton
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

// Отримуємо пошуковий запит
$search_query = get_search_query();

// Налаштовуємо WooCommerce loop properties для пагінації
global $wp_query;
wc_set_loop_prop( 'total', $wp_query->found_posts );
wc_set_loop_prop( 'total_pages', $wp_query->max_num_pages );
wc_set_loop_prop( 'current_page', max( 1, get_query_var( 'paged', 1 ) ) );
wc_set_loop_prop( 'per_page', get_query_var( 'posts_per_page' ) );
?>

<main class="site-main woocommerce">
	<div class="container">

		<?php woocommerce_breadcrumb(); ?>

		<header class="page-header">
			<h1 class="page-title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Search results: %s', 'market-pidlogy' ),
					'<span>' . esc_html( $search_query ) . '</span>'
				);
				?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>

			<?php
			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );
			?>

			<?php woocommerce_product_loop_start(); ?>

			<?php
			while ( have_posts() ) :
				the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 */
				do_action( 'woocommerce_shop_loop' );

				wc_get_template_part( 'content', 'product' );
			endwhile;
			?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php else : ?>

			<div class="woocommerce-no-products-found">
				<p class="woocommerce-info">
					<?php esc_html_e( 'На жаль, за вашим запитом нічого не знайдено.', 'badminton' ); ?>
				</p>
			</div>

		<?php endif; ?>

	</div>
</main>

<?php
get_footer( 'shop' );
