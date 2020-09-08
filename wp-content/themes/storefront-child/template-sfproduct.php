<?php
/**
 * Template Name: Store Front Product
 *
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div><h2>Store-Front Products</h2></div>
			<?php
			$args = array( 
				'numberposts'	=> -1, // -1 is for all
				'post_type'		=> 'storefront_product', // or 'post', 'page'
				'post_status'   => 'publish',
				'orderby' 		=> 'title', // or 'date', 'rand'
				'order' 		  => 'ASC', // or 'DESC'
			  //  'poca_podcast_category' => 'tech',
			  //   'tax_query' => array(
			  //     array(
			  //         'taxonomy' => 'poca_podcast_category',
			  //         'field'    => 'slug',
			  //         'terms'    => 'tech'
			  //     )
			  // )
				//'category' 		=> $category_id,
				//'exclude'		=> get_the_ID()
				// ...
				// http://codex.wordpress.org/Template_Tags/get_posts#Usage
				);
		
			// Get the posts support@mytachyon.in
			$sf_products = get_posts( $args );

			if ( $sf_products ) {
				foreach ( $sf_products as $sf_product ) {
					?>
					<div class="col-12 col-md-6">
						<div><?php echo get_the_post_thumbnail( $sf_product->ID, 'post-thumbnail' ); ?></div>
						<div class="sf_title"><a href="<?php echo get_the_permalink( $sf_product->ID ) ?>"><b><?php echo get_the_title( $sf_product->ID ); ?></b></a></div>
						<div>
							<?php //echo get_the_excerpt( $sf_product->ID ); ?>
						</div>
						<div class="">
							<?php $cats = wp_get_post_terms( $sf_product->ID, 'storefront_product_category' );
							foreach ( $cats as $cat1 ) {
								echo '<a href="' . get_term_link( $cat1 ) . '">' . $cat1->name . '</a> | ';
							}
							?>
							<a href="#" class=""><?php echo get_the_time( '', $sf_product->ID ); ?></a></p>
						</div>
					</div>
					<?php
				}
			}
			// while ( have_posts() ) :
			// 	the_post();

			// 	do_action( 'storefront_page_before' );

			// 	get_template_part( 'content', 'page' );

			// 	/**
			// 	 * Functions hooked in to storefront_page_after action
			// 	 *
			// 	 * @hooked storefront_display_comments - 10
			// 	 */
			// 	do_action( 'storefront_page_after' );

			// endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();
