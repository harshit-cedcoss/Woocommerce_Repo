<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<!-- <div><h2>Taxonomy Page</h2></div> -->
		<header class="page-header">
			<?php
			the_archive_title( '<h3 class="page-title">', '</h3>' );
			the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
		</header><!-- .page-header -->
		<?php
			$args = array(
				'numberposts' => -1, // -1 is for all
				'post_type'   => 'storefront_product',
				'post_status' => 'publish',
				'orderby'     => 'title',
				'order'       => 'ASC',
			);
			$sf_product = new WP_Query( $args );
			if ( $sf_product->have_posts() ) {
				while ( $sf_product->have_posts() ) {
					$sf_product->the_post();
					?>
				<div class="col-12 col-md-6">
					<div><?php echo get_the_post_thumbnail(); ?></div>
					<div class="sf_title"><a href="<?php echo get_the_permalink(); ?>"><b><?php echo get_the_title(); ?></b></a></div>
					<div>
						<?php //echo get_the_excerpt( $sf_product->ID ); ?>
					</div>
					<div class="">
						<?php
						$cats = get_terms( array( 'taxonomy' => 'storefront_product_category' ) );
						foreach ( $cats as $cat1 ) {
							echo '<a href="' . get_term_link( $cat1 ) . '">' . $cat1->name . '</a> | ';
						}
						?>
						<a href="#" class=""><?php echo get_the_time(); ?></a></p>
					</div>
				</div>
					<?php
					//get_template_part( 'loop' );
				}
			} else {

				get_template_part( 'content', 'none' );

			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();
