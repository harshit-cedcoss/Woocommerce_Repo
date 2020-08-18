<?php
/**
 * Theme Name  : Storefront Child
 * Version     : 1.0
 * Description : Child theme for Storefront.
 * Author      : Woo
 * Author URI  : http://woocommerce.com
 * Template    : storefront
 *
 * @package      Storefront
 */

/**
 * Enqueue child theme and parent theme
 *
 * @return void
 */
function my_theme_enqueue_styles() {

	$parent_style = 'storefront-style';

	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array(), '2.5.9' );
	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/**
 * Custom-Post Type
 *
 * @return void
 */
function storefront_custom_post_type() {
	$labels = array(
		'name'                  => _x( 'SF Products', 'Post type general name', 'storefront' ),
		'singular_name'         => _x( 'SF Product', 'Post type singular name', 'storefront' ),
		'menu_name'             => _x( 'SF Products', 'Admin Menu text', 'storefront' ),
		'add_new'               => _x( 'Add New', 'Storefront Product', 'storefront' ),
		'add_new_item '         => __( 'Add New SF Product', 'storefront' ),
		'edit_item'             => __( 'Edit SF Product', 'storefront' ),
		'new_item'              => __( 'New SF Product', 'storefront' ),
		'name_admin_bar'        => _x( 'SF Product', 'Add new on toolbar', 'storefront' ),
		'view_item'             => __( 'View SF Product', 'storefront' ),
		'all_items'             => __( 'All SF Products', 'storefront' ),
		'search_items'          => __( 'Search SF Product', 'storefront' ),
		'not_found'             => __( 'No storefront product found', 'storefront' ),
		'not_found_in_trash'    => __( 'No storefront products found in trash', 'storefront' ),
	//	'parent_items_colon'    => __( 'Parent Product', 'storefront' ),
		'archives'              => __( 'Archives', 'storefront' ),
		'attributes'            => __( 'Attributes', 'storefront' ),
		'insert_into_item'      => __( 'Insert into Product', 'storefront' ),
		'uploaded_to_this_item' => __( 'Upload to this Product', 'storefront' ),
		'featured_image'        => _x( 'SF Product Cover Image', 'Overrides the featured image phrase for this post type', 'storefront' ),
	);
	$args   = array(
		'labels'             => $labels,
		'public'             => true,
		'description'        => __( 'Storefront Products are described here', 'storefront' ),
		'has_archive'        => true,
		'rewrite'            => array( 'slug' => 'storefront-product' ), // my custom slug.
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menu'   => true,
		'show_in_admin_bar'  => true,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'show_in_rest'       => false,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		'taxonomies'         => array( 'storefront_product_category', 'storefront_product_tag' ),
	//	'menu_icon'          => 'product',
		'map_meta_cap'       => true,
		'query-var'          => true,
	// 'register_meta_box_cb' => 'wporg_dashboard_widget_render',
	);
	register_post_type( 'storefront_product', $args );
}
add_action( 'init', 'storefront_custom_post_type' );

/**
 * SF Product Category Custom Taxonomy.
 *
 * @return void
 */
function storefront_product_register_taxonomy_category() {
	$labels = array(
		'name'              => _x( 'SF Product Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'SF Product Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search category' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ),
		'view_item'         => __( 'View Category' ),
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'menu_name'         => __( 'SF Product Categories' ),
	);
	$args   = array(
		'hierarchical'      => true, // make it hierarchical (like categories).
		'labels'            => $labels,
		'description'       => 'this is the custom taxonomy description',
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => false,
		'rewrite'           => array( 'slug' => 'sf-product-category' ),
	);
	register_taxonomy( 'storefront_product_category', array( 'storefront_product' ), $args );
}
add_action( 'init', 'storefront_product_register_taxonomy_category' );

/**
 * Our Custom Taxonomy.
 *
 * @return void
 */
function storefront_product_register_taxonomy_tags() {
	$labels = array(
		'name'              => _x( 'SF Product Tags', 'taxonomy general name' ),
		'singular_name'     => _x( 'SF Product Tag', 'taxonomy singular name' ),
		'search_items'      => __( 'Search tag' ),
		'all_items'         => __( 'All Tags' ),
		'parent_item'       => __( 'Parent Tag' ),
		'parent_item_colon' => __( 'Parent Tag:' ),
		'edit_item'         => __( 'Edit Tag' ),
		'view_item'         => __( 'View Tag' ),
		'update_item'       => __( 'Update Tag' ),
		'add_new_item'      => __( 'Add New Tag' ),
		'new_item_name'     => __( 'New Tag Name' ),
		'menu_name'         => __( 'SF Product Tags' ),
	);
	$args   = array(
		'hierarchical'      => false, // make it hierarchical (like categories).
		'labels'            => $labels,
		'description'       => 'this is the custom taxonomy description',
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'sf-product-tag' ),
	);
	register_taxonomy( 'storefront_product_tag', array( 'storefront_product' ), $args );
}
add_action( 'init', 'storefront_product_register_taxonomy_tags' );
