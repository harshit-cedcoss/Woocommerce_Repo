<?php
/**
 * Plugin Name: WooCommerce Extension
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Your extension's description text.
 * Version: 1.0.0
 * Author: Harshit
 * Author URI: http://yourdomain.com/
 * Developer: Harshit
 * Developer URI: http://yourdomain.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
 * WC requires at least: 2.2
 * WC tested up to: 2.3
 *
 * @package WordPress
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	// Put your plugin code here.
	/**
	 * Define constants
	 */
	if ( ! defined( 'TPWCP_PLUGIN_VERSION' ) ) {
		define( 'TPWCP_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'TPWCP_PLUGIN_DIR_PATH' ) ) {
		define( 'TPWCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-tpwcp-admin.php';

	/**
	 * Class for adding a new setting demo tab
	 */
	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-settings-tab-demo.php';

	/**
	 * Start the plugin.
	 */
	function tpwcp_init() {
		if ( is_admin() ) {
			$TPWCP = new TPWCP_Admin();
			$TPWCP->init();
		}
	}
	add_action( 'plugins_loaded', 'tpwcp_init' );

	add_action( 'woocommerce_product_options_advanced', 'misha_adv_product_options' );
	function misha_adv_product_options() {

		echo '<div class="options_group">';

		woocommerce_wp_checkbox(
			array(
				'id'          => 'super_product',
				'value'       => get_post_meta( get_the_ID(), 'super_product', true ),
				'label'       => 'This is a super product',
				'desc_tip'    => true,
				'description' => 'If it is not a regular WooCommerce product',
			)
		);

		echo '</div>';

	}


	add_action( 'woocommerce_process_product_meta', 'misha_save_fields', 10, 2 );
	function misha_save_fields( $id, $post ) {

		//if( !empty( $_POST['super_product'] ) ) {
			update_post_meta( $id, 'super_product', $_POST['super_product'] );
		//} else {
		//	delete_post_meta( $id, 'super_product' );
		//}

	}


	/**
	 * Exclude products from a particular category on the shop page
	 */
	function custom_pre_get_posts_query( $q ) {

		$tax_query = (array) $q->get( 'tax_query' );

		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( 'women' ), // Don't display products in the clothing category on the shop page.
			'operator' => 'NOT IN',
		);

		$q->set( 'tax_query', $tax_query );

	}
	//add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

	/**
	 * Add custom tracking code to the thank-you page
	 */
	//add_action( 'woocommerce_thankyou', 'my_custom_tracking' );

	function my_custom_tracking( $order_id ) {

		// Lets grab the order.
		$order = wc_get_order( $order_id );

		/**
		 * Put your tracking code here
		 * You can get the order total etc e.g. $order->get_total();
		 */

		// This is the order total.
		$order->get_total();

		// This is how to grab line items from the order .
		$line_items = $order->get_items();

		// This loops over line items.
		foreach ( $line_items as $item ) {
			// This will be a product.
			$product = $order->get_product_from_item( $item );

			// This is the products SKU.
			$sku = $product->get_sku();

			// This is the qty purchased.
			$qty = $item['qty'];

			// Line item total cost including taxes and rounded.
			$total = $order->get_line_total( $item, true, true );

			// Line item subtotal (before discounts).
			$subtotal = $order->get_line_subtotal( $item, true, true );
		}
	}

	/**
	 * Allow HTML in term (category, tag) descriptions
	 */
	function html_terms_tax() {
		foreach ( array( 'pre_term_description' ) as $filter ) {
			remove_filter( $filter, 'wp_filter_kses' );
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				add_filter( $filter, 'wp_filter_post_kses' );
			}
		}

		foreach ( array( 'term_description' ) as $filter ) {
			remove_filter( $filter, 'wp_kses_data' );
		}
	}
	//add_action( 'init', 'html_terms_tax' );

	/**
	 * Override loop template and show quantities next to add to cart buttons
	 */
	add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
	function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
			$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
			$html .= woocommerce_quantity_input( array(), $product, false );
			$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
			$html .= '</form>';
		}
		return $html;
	}

	/**
	 * Change the default state and country on the checkout page
	 */
	add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
	add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );

	function change_default_checkout_country() {
		return 'IN'; // country code.
	}

	function change_default_checkout_state() {
		return 'UP'; // state code.
	}


	/**
	 * Change the default country on the checkout for non-existing users only
	 */
	//add_filter( 'default_checkout_billing_country', 'change_default_checkout_country1', 10, 1 );

	function change_default_checkout_country1( $country ) {
		// If the user already exists, don't override country
		if ( WC()->customer->get_is_paying_customer() ) {
			return $country;
		}

		return 'DE'; // Override default to Germany (an example)
	}

	/**
	 * Add custom sorting options (asc/desc)
	 */
	add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );
	function custom_woocommerce_get_catalog_ordering_args( $args ) {
		$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		if ( 'random_list' == $orderby_value ) {
			$args['orderby']  = 'rand';
			$args['order']    = '';
			$args['meta_key'] = '';
		}
		return $args;
	}
	add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
	add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );
	function custom_woocommerce_catalog_orderby( $sortby ) {
		$sortby['random_list'] = 'Random';
		return $sortby;
	}

	/**
	 * Remove product data tabs
	 */
	//add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

	function woo_remove_product_tabs( $tabs ) {

		unset( $tabs['description'] );            // Remove the description tab.
		unset( $tabs['reviews'] );                // Remove the reviews tab.
		unset( $tabs['additional_information'] ); // Remove the additional information tab.

		return $tabs;
	}

	/**
	 * Rename product data tabs
	 */
	add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
	function woo_rename_tabs( $tabs ) {

		$tabs['description']['title']            = __( 'More Information' ); // Rename the description tab.
		$tabs['reviews']['title']                = __( 'Ratings' );          // Rename the reviews tab.
		$tabs['additional_information']['title'] = __( 'Product Data' );     // Rename the additional information tab.

		return $tabs;

	}

	/**
	 * Reorder product data tabs
	 */
	add_filter( 'woocommerce_product_tabs', 'woo_reorder_tabs', 98 );
	function woo_reorder_tabs( $tabs ) {

		$tabs['reviews']['priority']                = 5;  // Reviews first.
		$tabs['description']['priority']            = 10; // Description second.
		$tabs['additional_information']['priority'] = 15; // Additional information third.

		return $tabs;
	}

	/**
	 * Customize product data tabs
	 */
	add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );
	function woo_custom_description_tab( $tabs ) {

		$tabs['description']['callback'] = 'woo_custom_description_tab_content'; // Custom description callback.

		return $tabs;
	}

	function woo_custom_description_tab_content() {
		echo '<h2>Custom Description</h2>';
		echo '<p>Here\'s a custom description</p>';
	}

	/**
	 * Add a custom product data tab
	 *
	 * @param array $tabs Tabs array.
	 */
	function woo_new_product_tab( $tabs ) {

		// Adds the new tab.

		$tabs['test_tab'] = array(
			'title'    => __( 'New Product Tab', 'woocommerce' ),
			'priority' => 50,
			'callback' => 'woo_new_product_tab_content',
		);

		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
	/**
	 * Callback function for New product tab
	 *
	 * @return void
	 */
	function woo_new_product_tab_content() {

		// The new tab content.

		echo '<h2>New Product Tab</h2>';
		echo '<p>Here\'s your new product tab.</p>';

	}

	/**
	 * Check if product has attributes, dimensions or weight to override the call_user_func() expects parameter 1 to be a valid callback error when changing the additional tab
	 */
	add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs1', 98 );

	function woo_rename_tabs1( $tabs ) {

		global $product;

		if( $product->has_attributes() || $product->has_dimensions() || $product->has_weight() ) { // Check if product has attributes, dimensions or weight.
			$tabs['additional_information']['title'] = __( 'Product Data new' );                       // Rename the additional information tab.
		}

		return $tabs;
	}

	/**
	 * Hide category product count in product archives
	 */
	//add_filter( 'woocommerce_subcategory_count_html', '__return_false' );

	/**
	 * Remove product content based on category
	 */
	//add_action( 'wp', 'remove_product_content' );
	function remove_product_content() {
		// If a product in the 'Cookware' category is being viewed.
		if ( is_product() && has_term( 'Men', 'product_cat' ) ) {
			// ... Remove the images.
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			// For a full list of what can be removed please see woocommerce-hooks.php.
		}
	}

	/**
	 * Auto Complete all WooCommerce orders.
	 */
	add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
	function custom_woocommerce_auto_complete_order( $order_id ) { 
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		$order->update_status( 'completed' );
	}

	/**
	 * Add a 1% surcharge to your cart / checkout
	 * change the $percentage to set the surcharge to a value to suit
	 */
	// add_action( 'woocommerce_cart_calculate_fees', 'woocommerce_custom_surcharge' );
	function woocommerce_custom_surcharge() {
		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		$percentage = 0.01;
		$surcharge  = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;	
		$woocommerce->cart->add_fee( 'Surcharge', $surcharge, true, '' );
	}

	/**
	 * Add a standard $ value surcharge to all transactions in cart / checkout
	 */
	// add_action( 'woocommerce_cart_calculate_fees', 'wc_add_surcharge' );
	function wc_add_surcharge() {
		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		$county = array( 'IN' );
		// change the $fee to set the surcharge to a value to suit.
		$fee = 1.00;

		if ( in_array( WC()->customer->get_shipping_country(), $county ) ) :
			$woocommerce->cart->add_fee( 'Surcharge', $fee, true, 'standard' );
		endif;
	}

	/**
	 * Add a 1% surcharge to your cart / checkout based on delivery country
	 * Taxes, shipping costs and order subtotal are all included in the surcharge amount
	 */
	add_action( 'woocommerce_cart_calculate_fees', 'woocommerce_custom_surcharge1' );
	function woocommerce_custom_surcharge1() {
		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		$county     = array( 'IN' );
		$percentage = 0.01;

		if ( in_array( $woocommerce->customer->get_shipping_country(), $county ) ) :
			$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;
			$woocommerce->cart->add_fee( 'Surcharge', $surcharge, true, '' );
		endif;
	}

	/**
	 * Rename "home" in breadcrumb
	 */
	// add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text', 20 );
	function wcc_change_breadcrumb_home_text( $defaults ) {
		// Change the breadcrumb home text from 'Home' to 'Apartment'.
		$defaults['home'] = 'Apartment';
		return $defaults;
	}

	/**
	 * Change the breadcrumb separator
	 */
	//add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_delimiter', 20 );
	function wcc_change_breadcrumb_delimiter( $defaults ) {
		// Change the breadcrumb delimeter from '/' to '>'.
		$defaults['delimiter'] = ' &gt; ';
		return $defaults;
	}

	/**
	 * Change several of the breadcrumb defaults
	 */
	//add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs', 19 );
	function jk_woocommerce_breadcrumbs() {
		return array(
			'delimiter'   => ' &#47; ',
			'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
			'wrap_after'  => '</nav>',
			'before'      => '',
			'after'       => '',
			'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
		);
	}

	/**
	 * Replace the home link URL
	 */
	// add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );
	function woo_custom_breadrumb_home_url() {
		return 'http://woocommerce.com';
	}

	/**
	 * Remove breadcrumbs for Storefront theme
	 */
	// add_action( 'init', 'wc_remove_storefront_breadcrumbs' );

	function wc_remove_storefront_breadcrumbs() {
		remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
	}

	/**
	 * Add a message above the login / register form on my-account page
	 */
	add_action( 'woocommerce_before_customer_login_form', 'jk_login_message' );
	function jk_login_message() {
		if ( get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' ) {
			?>
			<div class="woocommerce-info">
				<p><?php _e( 'Returning customers login. New users register for next time so you can:' ); ?></p>
				<ul>
					<li><?php _e( 'View your order history' ); ?></li>
					<li><?php _e( 'Check on your orders' ); ?></li>
					<li><?php _e( 'Edit your addresses' ); ?></li>
					<li><?php _e( 'Change your password' ); ?></li>
				</ul>
			</div>
			<?php
		}
	}

	/**
	* Apply a coupon for minimum cart total
	*/

	add_action( 'woocommerce_before_cart', 'add_coupon_notice' );
	add_action( 'woocommerce_before_checkout_form', 'add_coupon_notice' );

	function add_coupon_notice() {

			$cart_total     = WC()->cart->get_subtotal();
			$minimum_amount = 7000;
			$currency_code  = get_woocommerce_currency();
			wc_clear_notices();

		if ( $cart_total < $minimum_amount ) {
				WC()->cart->remove_coupon( 'COUPON' );
				wc_print_notice( "Get 50% off if you spend more than $minimum_amount $currency_code!", 'notice' );
		} else {
				WC()->cart->apply_coupon( 'COUPON' );
				wc_print_notice( 'You just got 50% off your order!', 'notice' );
		}
			wc_clear_notices();
	}



											// GENERAL SNIPPETS.


	/**
	 * Custom currency and currency symbol
	 */
	add_filter( 'woocommerce_currencies', 'add_my_currency' );

	function add_my_currency( $currencies ) {
		$currencies['ABC'] = __( 'My Custom Currency', 'woocommerce' );
		return $currencies;
	}

	add_filter( 'woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2 );

	function add_my_currency_symbol( $currency_symbol, $currency ) {
		switch ( $currency ) {
			case 'ABC':
				$currency_symbol = '$$';
				break;
		}
		return $currency_symbol;
	}

	/**
	 * Allow shortcodes in product excerpts
	 */
	if ( ! function_exists( 'woocommerce_template_single_excerpt' ) ) {

		function woocommerce_template_single_excerpt( $post ) {
			global $post;
			if ( $post->post_excerpt ) :
				echo '<div itemprop="description">' . do_shortcode( wpautop( wptexturize( $post->post_excerpt ) ) ) . '</div>';
			endif;
		}
	}


	/**
	 * Send an email each time an order with coupon(s) is completed
	 * The email contains coupon(s) used during checkout process
	 *
	 */ 
	function woo_email_order_coupons( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( $order->get_used_coupons() ) {

			$to      = 'youremail@yourcompany.com';
			$subject = 'New Order Completed';
			$headers = 'From: My Name <youremail@yourcompany.com>' . "\r\n";

			$message  = 'A new order has been completed.\n';
			$message .= 'Order ID: ' . $order_id . '\n';
			$message .= 'Coupons used:\n';

			foreach ( $order->get_used_coupons() as $coupon ) {
				$message .= $coupon . '\n';
			}
			@wp_mail( $to, $subject, $message, $headers );
		}
	}
	add_action( 'woocommerce_thankyou', 'woo_email_order_coupons' );

	/*
	* goes in theme functions.php or a custom plugin
	*
	* Subject filters:
	*   woocommerce_email_subject_new_order
	*   woocommerce_email_subject_customer_processing_order
	*   woocommerce_email_subject_customer_completed_order
	*   woocommerce_email_subject_customer_invoice
	*   woocommerce_email_subject_customer_note
	*   woocommerce_email_subject_low_stock
	*   woocommerce_email_subject_no_stock
	*   woocommerce_email_subject_backorder
	*   woocommerce_email_subject_customer_new_account
	*   woocommerce_email_subject_customer_invoice_paid
	**/
	add_filter( 'woocommerce_email_subject_new_order', 'change_admin_email_subject', 1, 2 );

	function change_admin_email_subject( $subject, $order ) {
		global $woocommerce;

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$subject = sprintf( '[%s] New Customer Order (# %s) from Name %s %s', $blogname, $order->id, $order->billing_first_name, $order->billing_last_name );

		return $subject;
	}

	/**
	 * Adjust the quantity input values
	 */
	add_filter( 'woocommerce_quantity_input_args', 'jk_woocommerce_quantity_input_args', 10, 2 ); // Simple products.

	function jk_woocommerce_quantity_input_args( $args, $product ) {
		if ( is_singular( 'product' ) ) {
			$args['input_value'] = 2; // Starting value (we only want to affect product pages, not cart).
		}
		$args['max_value'] = 80; // Maximum value.
		$args['min_value'] = 1;  // Minimum value.
		$args['step']      = 1;  // Quantity steps.
		return $args;
	}

	add_filter( 'woocommerce_available_variation', 'jk_woocommerce_available_variation' ); // Variations.

	function jk_woocommerce_available_variation( $args ) {
		$args['max_qty'] = 80; // Maximum value (variations).
		$args['min_qty'] = 1;  // Minimum value (variations).
		return $args;
	}


	/**
	 * Custom payment Gateway.
	 */
	add_action( 'plugins_loaded', 'woocommerce_gateway_test_init', 0 );
	function woocommerce_gateway_test_init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) :
			return;
		endif;
		/**
		 * Localisation
		*/
		load_plugin_textdomain( 'wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		/**
		 * Gateway class
		 */
		class WC_Gateway_Test extends WC_Payment_Gateway {

			// Go wild in here.
			/**
			 * Custructor function for custom test payment gateway
			 */
			public function __construct() {
				$this->id                 = 'test_payment_gateway';
				$this->icon               = apply_filters( 'test_gateway_payment_icon', plugins_url( '/assets/images/icons/eway-logo.png', __FILE__ ) );
				$this->has_fields         = false;
				$this->method_title       = __( 'Test Payment Gateway', 'woo-gateway' );
				$this->method_description = __( 'Description for my custom payment method', 'woo-gateway' );

				$this->init_form_fields();
				$this->init_settings();

				$this->title       = $this->get_option( 'title' );
				$this->description = $this->get_option( 'description' );

				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			/**
			 * Initialize gateway settings form fields
			 */
			public function init_form_fields() {
				$this->form_fields = apply_filters(
					'wc_test_gateway_form_fields',
					array(
						'enabled'     => array(
							'title'   => __( 'Enabled/Disabled', 'woo-gateway' ),
							'type'    => 'checkbox',
							'label'   => __( 'Enable Payment Gateway', 'woo-gateway' ),
							'default' => 'yes',
						),
						'title'       => array(
							'title'       => __( 'Payment gateway title', 'woo-gateway' ),
							'type'        => 'text',
							'description' => __( 'Name the title for payment gateway which is to be shown on the frontend', 'woo-gateway' ),
							'desc_tip'    => true,
							'default'     => __( 'MyPay', 'woo-gateway' ),
						),
						'description' => array(
							'title'       => __( 'Gateway Description', 'woo-gateway' ),
							'type'        => 'textarea',
							'description' => __( 'Payent method description that customer will see at the checkout', 'woo-gateway' ),
							'desc_tip'    => true,
							'default'     => __( 'This is the custom test payment gateway', 'woo-gateway' ),
						),
					)
				);
			}

			public function process_payment( $order_id ) {
				global $woocommerce;
				$order = new WC_Order( $order_id );

				// make the payment status.
				$order->payment_complete();

				// Reduce stock levels.
				$order->reduce_order_stock();

				// remove cart items.
				$woocommerce->cart->empty_cart();

				// Return Thankyou page redirect.
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}
		}

		/**
		 * Add the Gateway to WooCommerce
		 */
		function woocommerce_add_gateway_name_gateway( $methods ) {
			$methods[] = 'WC_Gateway_Test';
			return $methods;
		}

		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_name_gateway' );
	}

	/**
	 * Add a new country to countries list
	 */
	add_filter( 'woocommerce_countries', 'handsome_bearded_guy_add_my_country' );
	function handsome_bearded_guy_add_my_country( $countries ) {
		$new_countries = array(
			'NIRE' => __( 'Northern Ireland', 'woocommerce' ),
		);

		return array_merge( $countries, $new_countries );
	}

	add_filter( 'woocommerce_continents', 'handsome_bearded_guy_add_my_country_to_continents' );
	function handsome_bearded_guy_add_my_country_to_continents( $continents ) {
		$continents['EU']['countries'][] = 'NIRE';
		return $continents;
	}

	/**
	 * Add a custom field (in an order) to the emails
	 */
	add_filter( 'woocommerce_email_order_meta_fields', 'custom_woocommerce_email_order_meta_fields', 10, 3 );

	function custom_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
		$fields['meta_key'] = array(
			'label' => __( 'Label' ),
			'value' => get_post_meta( $order->id, 'meta_key', true ),
		);
		return $fields;
	}

	/**
	 * Allow customers to access wp-admin
	 */
	add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
	add_filter( 'woocommerce_disable_admin_bar', '__return_false' );


	/**
	 * Add another product depending on the cart total
	 */
	//add_action( 'template_redirect', 'add_product_to_cart' );
	function add_product_to_cart() {
		if ( ! is_admin() ) {
			global $woocommerce;
			$product_id = 26; // replace with your product id.
			$found      = false;
			$cart_total = 30; // replace with your cart total needed to add above item.

			if ( $woocommerce->cart->total >= $cart_total ) {
				// check if product already in cart.
				if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
					foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
						$_product = $values['data'];
						if ( $_product->get_id() == $product_id ) {
							$found = true;
						}
					}
					// if product not found, add it.
					if ( ! $found ) {
						$woocommerce->cart->add_to_cart( $product_id );
					}
				} else {
					// if no products in cart, add it.
					$woocommerce->cart->add_to_cart( $product_id );
				}
			}
		}
	}

	/**
	 * Show product weight on archive pages
	 */
	add_action( 'woocommerce_after_shop_loop_item', 'rs_show_weights', 9 );

	function rs_show_weights() {

		global $product;
		$weight = $product->get_weight();

		if ( $product->has_weight() ) {
			echo '<div class="product-meta"><span class="product-meta-label">Weight: </span>' . $weight . get_option( 'woocommerce_weight_unit' ) . '</div></br>';
		}
	}




	/**
	 * Notify admin when a new customer account is created
	 */
	add_action( 'woocommerce_created_customer', 'woocommerce_created_customer_admin_notification' );
	function woocommerce_created_customer_admin_notification( $customer_id ) {
		wp_send_new_user_notifications( $customer_id, 'admin' );
	}

	/**
	 * Display product attribute archive links
	 */
	add_action( 'woocommerce_product_meta_end', 'wc_show_attribute_links' );
	// if you'd like to show it on archive page, replace "woocommerce_product_meta_end" with "woocommerce_shop_loop_item_title".

	function wc_show_attribute_links() {
		global $post;
		$attribute_names = array( 'pa_color', 'pa_size' ); // Add attribute names here and remember to add the pa_ prefix to the attribute name.

		foreach ( $attribute_names as $attribute_name ) {
			$taxonomy = get_taxonomy( $attribute_name );

			if ( $taxonomy && ! is_wp_error( $taxonomy ) ) {
				$terms       = wp_get_post_terms( $post->ID, $attribute_name );
				$terms_array = array();

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$archive_link = get_term_link( $term->slug, $attribute_name );
						$full_line    = '<a href="' . $archive_link . '">' . $term->name . '</a>';
						array_push( $terms_array, $full_line );
					}
					echo $taxonomy->labels->name . ' ' . implode( $terms_array, ', ' ) . '<br>';
				}
			}
		}
	}

	/**
	 * Trim zeros in price decimals
	 */
	// add_filter( 'woocommerce_price_trim_zeros', '__return_true' );


	/**
	 * Show product dimensions on archive pages for WC 3+
	 */
	add_action( 'woocommerce_after_shop_loop_item', 'rs_show_dimensions', 9 );

	function rs_show_dimensions() {
		global $product;
		$dimensions = wc_format_dimensions( $product->get_dimensions( false ) );

		if ( $product->has_dimensions() ) {
			echo '<div class="product-meta"><span class="product-meta-label">Dimensions: </span>' . $dimensions . '</div>';
		}
	}

	/**
	 * Add or modify States
	 */
	add_filter( 'woocommerce_states', 'custom_woocommerce_states' );

	function custom_woocommerce_states( $states ) {

		$states['IN']['IN1'] = 'State 1';

		return $states;
	}

	/**
	 * Unhook and remove WooCommerce default emails.
	 */
	add_action( 'woocommerce_email', 'unhook_those_pesky_emails' );

	function unhook_those_pesky_emails( $email_class ) {

		/**
		 * Hooks for sending emails during store events
		 */
		remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
		remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
		remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );

		// New order emails.
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

		// Processing order emails.
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

		// Completed order emails.
		remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );

		// Note emails.
		remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
	}

	/**
	 * Hide shipping rates when free shipping is available.
	 * Updated to support WooCommerce 2.6 Shipping Zones.
	 *
	 * @param array $rates Array of rates found for the package.
	 * @return array
	 */
	function my_hide_shipping_when_free_is_available( $rates ) {
		$free = array();
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'free_shipping' === $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}
		}
		return ! empty( $free ) ? $free : $rates;
	}
	//add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );

	/**
	 * Hide shipping rates when free shipping is available, but keep "Local pickup"
	 * Updated to support WooCommerce 2.6 Shipping Zones
	 */
	function hide_shipping_when_free_is_available( $rates, $package ) {
		$new_rates = array();
		foreach ( $rates as $rate_id => $rate ) {
			// Only modify rates if free_shipping is present.
			if ( 'free_shipping' === $rate->method_id ) {
				$new_rates[ $rate_id ] = $rate;
				break;
			}
		}

		if ( ! empty( $new_rates ) ) {
			//Save local pickup if it's present.
			foreach ( $rates as $rate_id => $rate ) {
				if ('local_pickup' === $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
					break;
				}
			}
			return $new_rates;
		}

		return $rates;
	}

	add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );



	/**
	 * Rename a country
	 */
	add_filter( 'woocommerce_countries', 'rename_ireland' );

	function rename_ireland( $countries ) {
		$countries['IE'] = 'Ireland';
		return $countries;
	}

	/**
	 * Change a currency symbol
	 */
	add_filter( 'woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2 );

	function change_existing_currency_symbol( $currency_symbol, $currency ) {
		switch ( $currency ) {
			case 'AUD':
				$currency_symbol = 'AUD$';
				break;
		}
		return $currency_symbol;
	}

	/**
	 * Set a minimum order amount for checkout
	 */
	add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
	add_action( 'woocommerce_before_cart', 'wc_minimum_order_amount' );

	function wc_minimum_order_amount() {
		// Set this variable to specify a minimum order value.
		$minimum = 60;

		if ( WC()->cart->total < $minimum ) {

			if ( is_cart() ) {

				wc_print_notice(
					sprintf(
						'Your current order total is %s — you must have an order with a minimum of %s to place your order ',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);

			} else {

				wc_add_notice(
					sprintf(
						'Your current order total is %s — you must have an order with a minimum of %s to place your order',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);

			}
		}
	}

													// Theming Snippets.

	/**
	 * Change number of products that are displayed per page (shop page)
	 */
	add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

	function new_loop_shop_per_page( $cols ) {
		// $cols contains the current number of products per page based on the value stored on Options -> Reading
		// Return the number of products you wanna show per page.
		$cols = 9;
		return $cols;
	}

	/**
	 * Change number or products per row to 3
	 */
	add_filter( 'loop_shop_columns', 'loop_columns', 999 );
	if ( ! function_exists( 'loop_columns' ) ) {
		function loop_columns() {
			return 3; // 3 products per row
		}
	}

	/**
	 * Override theme default specification for product # per row
	 */
	function loop_columns2() {
		return 5; // 5 products per row
	}
	//add_filter( 'loop_shop_columns', 'loop_columns2', 999 );

	/**
	 * Set WooCommerce image dimensions upon theme activation
	 */
	// Remove each style one by one.
	add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );
	function jk_dequeue_styles( $enqueue_styles ) {
		unset( $enqueue_styles['woocommerce-general'] );     // Remove the gloss.
		unset( $enqueue_styles['woocommerce-layout'] );      // Remove the layout.
		unset( $enqueue_styles['woocommerce-smallscreen'] ); // Remove the smallscreen optimisation.
		return $enqueue_styles;
	}

	// Or just remove them all in one line.
	add_filter( 'woocommerce_enqueue_styles', '__return_false' );

	add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

	/**
	 * Show cart contents / total Ajax
	 */
	add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;

		ob_start();

		?>
		<a class="cart-customlocation" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'woothemes' ); ?>"><?php echo sprintf( _n( '%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes' ), $woocommerce->cart->cart_contents_count ); ?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
		<?php
		$fragments['a.cart-customlocation'] = ob_get_clean();
		return $fragments;
	}

	/**
	 * Hide loop read more buttons for out of stock items 
	 */
	if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
		function woocommerce_template_loop_add_to_cart() {
			global $product;
			if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
				return;
			}
			wc_get_template( 'loop/add-to-cart.php' );
		}
	}

	/**
	 * Change number of related products output
	 */
	function woo_related_products_limit() {
		global $product;

		$args['posts_per_page'] = 6;
		return $args;
	}
	//add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
	function jk_related_products_args( $args ) {
		$args['posts_per_page'] = 4; // 4 related products.
		$args['columns']        = 2; // arranged in 2 columns.
		return $args;
	}

	/**
	 * Change number of upsells output
	 */
	add_filter( 'woocommerce_upsell_display_args', 'wc_change_number_related_products', 20 );

	function wc_change_number_related_products( $args ) {

		$args['posts_per_page'] = 3;
		$args['columns']        = 4; // change number of upsells here.
		return $args;
	}

	/**
	 * Change the placeholder image
	 */
	add_filter('woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src');

	function custom_woocommerce_placeholder_img_src( $src ) {
		$upload_dir = wp_upload_dir();
		$uploads    = untrailingslashit( $upload_dir['baseurl'] );
		// replace with path to your image.
		$src = $uploads . '/woocommerce-placeholder-300x300.png';

		return $src;
	}

	/**
	 * Show product categories in Wooframework breadcrumbs
	 */
	// Get breadcrumbs on product pages that read: Home > Shop > Product category > Product Name
	//add_filter( 'woo_breadcrumbs_trail', 'woo_custom_breadcrumbs_trail_add_product_categories', 20 );

	function woo_custom_breadcrumbs_trail_add_product_categories ( $trail ) {
		if ( ( get_post_type() == 'product' ) && is_singular() ) {
			global $post;

			$taxonomy = 'product_cat';

			$terms = get_the_terms( $post->ID, $taxonomy );
			$links = array();

			if ( $terms && ! is_wp_error( $terms ) ) {
			$count = 0;
				foreach ( $terms as $c ) {
					$count++;
					if ( $count > 1 ) { continue; }
					$parents = woo_get_term_parents( $c->term_id, $taxonomy, true, ', ', $c->name, array() );

					if ( $parents != '' && ! is_wp_error( $parents ) ) {
						$parents_arr = explode( ', ', $parents );

						foreach ( $parents_arr as $p ) {
							if ( $p != '' ) { $links[] = $p; }
						}
					}
				}

				// Add the trail back on to the end.
				// $links[] = $trail['trail_end'];.
				$trail_end = get_the_title( $post->ID );

				// Add the new links, and the original trail's end, back into the trail.
				array_splice( $trail, 2, count( $trail ) - 1, $links );

				$trail['trail_end'] = $trail_end;
			}
		}

		return $trail;
	}

	/**
	 * Retrieve term parents with separator.
	 *
	 * @param int $id Term ID.
	 * @param string $taxonomy.
	 * @param bool $link Optional, default is false. Whether to format with link.
	 * @param string $separator Optional, default is '/'. How to separate terms.
	 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
	 * @param array $visited Optional. Already linked to terms to prevent duplicates.
	 * @return string
	 */

	if ( ! function_exists( 'woo_get_term_parents' ) ) {
		function woo_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
			$chain  = '';
			$parent = &get_term( $id, $taxonomy );
			if ( is_wp_error( $parent ) ) {
				return $parent;
			}
			if ( $nicename ) {
				$name = $parent->slug;
			} else {
				$name = $parent->name;
			}

			if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
				$visited[] = $parent->parent;
				$chain .= woo_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
			}

			if ( $link ) {
				$chain .= '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$parent->name.'</a>' . $separator;
			} else {
				$chain .= $name . $separator;
			}
			return $chain;
		}
	}

	/**
	 * Remove related products output
	 */
	function related_poducts_output(){
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}
	//add_action( 'after_setup_theme', 'related_poducts_output' );

	/**
	 * Prevent PO box shipping
	 */
	add_action( 'woocommerce_after_checkout_validation', 'deny_pobox_postcode' );

	function deny_pobox_postcode( $posted ) {
		global $woocommerce;

		$address  = ( isset( $posted['shipping_address_1'] ) ) ? $posted['shipping_address_1'] : $posted['billing_address_1'];
		$postcode = ( isset( $posted['shipping_postcode'] ) ) ? $posted['shipping_postcode'] : $posted['billing_postcode'];

		$replace  = array( ' ', '.', ',' );
		$address  = strtolower( str_replace( $replace, '', $address ) );
		$postcode = strtolower( str_replace( $replace, '', $postcode ) );

		if ( strstr( $address, 'pobox' ) || strstr( $postcode, 'pobox' ) ) {
			wc_add_notice( sprintf( __( 'Sorry, we cannot ship to PO BOX addresses.' ) ), 'error' );
		}
	}
	//add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

	// Our hooked in function - $fields is passed via the filter!
	function custom_override_checkout_fields( $fields ) {
		$fields['order']['order_comments']['placeholder'] = 'My new placeholder';
		$fields['order']['order_comments']['label']       = 'My new label';
		return $fields;
	}

	//add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields1' );

	// Our hooked in function - $fields is passed via the filter!
	function custom_override_checkout_fields1( $fields ) {
		unset( $fields['order']['order_comments'] );
		$fields['billing']['your_field']['options'] = array(
			'option_1' => 'Option 1 text',
			'option_2' => 'Option 2 text',
		);

		return $fields;
	}

	//add_filter( 'woocommerce_default_address_fields', 'custom_override_default_address_fields' );

	// Our hooked in function - $address_fields is passed via the filter!
	function custom_override_default_address_fields( $address_fields ) {
		$address_fields['address_1']['required'] = false;

		return $address_fields;
	}


	// Hook in.
	add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields2' );

	// Our hooked in function - $fields is passed via the filter!
	function custom_override_checkout_fields2( $fields ) {
		$fields['shipping']['shipping_phone'] = array(
			'label'       => __( 'Phone', 'woocommerce' ),
			'placeholder' => _x( 'Phone', 'placeholder', 'woocommerce' ),
			'required'    => false,
			'class'       => array( 'form-row-wide' ),
			'clear'       => true,
		);

		return $fields;
	}

	/**
	 * Display field value on the order edit page
	 */

	add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

	function my_custom_checkout_field_display_admin_order_meta( $order ) {
		echo '<p><strong>' . __( 'Phone From Checkout Form' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_shipping_phone', true ) . '</p>';
	}

	/**
	 * Add the field to the checkout
	 */
	add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );

	function my_custom_checkout_field( $checkout ) {

		echo '<div id="my_custom_checkout_field"><h2>' . __( 'My Field' ) . '</h2>';

		woocommerce_form_field(
			'my_field_name',
			array(
				'type'        => 'text',
				'class'       => array( 'my-field-class form-row-wide' ),
				'label'       => __( 'Fill in this field' ),
				'placeholder' => __( 'Enter something' ),
			),
			$checkout->get_value( 'my_field_name' )
		);

		echo '</div>';

	}

	/**
	 * Process the checkout
	 */
	add_action( 'woocommerce_checkout_process', 'my_custom_checkout_field_process' );

	function my_custom_checkout_field_process() {
		// Check if set, if its not set add an error.
		if ( ! $_POST['my_field_name'] )
			wc_add_notice( __( 'Please enter something into this new shiny field.' ), 'error' );
	}

	/**
	 * Update the order meta with field value
	 */
	add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

	function my_custom_checkout_field_update_order_meta( $order_id ) {
		if ( ! empty( $_POST['my_field_name'] ) ) {
			update_post_meta( $order_id, 'My Field', sanitize_text_field( $_POST['my_field_name'] ) );
		}
	}

	/**
	 * Display field value on the order edit page
	 */
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta2', 10, 1 );

	function my_custom_checkout_field_display_admin_order_meta2( $order ) {
		echo '<p><strong>' . __( 'My Field' ) . ':</strong> ' . get_post_meta( $order->id, 'My Field', true ) . '</p>';
	}

	//add_filter( 'woocommerce_billing_fields', 'wc_npr_filter_phone', 10, 1 );
	function wc_npr_filter_phone( $address_fields ) {
		$address_fields['billing_phone']['required'] = false;
		return $address_fields;
	}

	/*
	* To use:
	1. Add this snippet to your theme's functions.php file
	2. Change the meta key names in the snippet
	3. Create a custom field in the order post - e.g. key = "Tracking Code" value = abcdefg
	4. When next updating the status, or during any other event which emails the user, they will see this field in their email
	*/
	add_filter( 'woocommerce_email_order_meta_keys', 'my_custom_order_meta_keys' );

	function my_custom_order_meta_keys( $keys ) {
		$keys[] = 'Tracking Code'; // This will look for a custom field called 'Tracking Code' and add it to emails.
		return $keys;
	}

	/**
	 * Custom Shipping Method
	 *
	 * @return void
	 */
	function your_shipping_method_init() {
		if ( ! class_exists( 'WC_Your_Shipping_Method' ) ) {
			class WC_Your_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct( $instance_id = 0 ) {
					$this->id                 = 'your_shipping_method'; // Id for your shipping method. Should be uunique.
					$this->method_title       = __( 'Your Shipping Method' );  // Title shown in admin.
					$this->method_description = __( 'Description of your shipping method' ); // Description shown in admin.
					$this->instance_id        = absint( $instance_id );
					$this->supports           = array(
						'shipping-zones',
						'instance-settings',
						'instance-settings-modal',
					);

				//	$this->enabled = 'yes'; // This can be added as an setting but for this example its forced enabled.
				//	$this->title   = 'My Shipping Method'; // This can be added as an setting but for this example its forced.

					$this->init();
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				public function init() {
					// Load the settings API.
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings.
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

					// Define user set variables.
					$this->title = $this->get_option( 'title' );
					$this->cost  = $this->get_option( 'cost' );

					// Save settings in admin if you have any defined.
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}

				/**
				 * Init form fields.
				 */
				public function init_form_fields() {

					$this->instance_form_fields = array(

						'enabled' => array(
							'title'       => __( 'Enable', 'shipping' ),
							'type'        => 'checkbox',
							'description' => __( 'Enable this shipping method.', 'shipping' ),
							'default'     => 'yes',
						),

						'title'   => array(
							'title'       => __( 'Title', 'shipping' ),
							'type'        => 'text',
							'description' => __( 'Title to be displayed on site', 'shipping' ),
							'default'     => __( 'Request a Quote', 'shipping' ),
						),

						'cost'    => array(
							'title'       => __( 'Cost', 'shipping' ),
							'type'        => 'text',
							'description' => __( 'Title to be displayed on site', 'shipping' ),
							'default'     => __( 'Cost', 'shipping' ),
						),

					);

				}

				/**
				 * Calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package = array() ) {
					$rate = array(
						'id'       => $this->id,
						'label'    => $this->title,
						'cost'     => $this->cost,
						'calc_tax' => 'per_item',
					);

					// Register the rate.
					$this->add_rate( $rate );
				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );

	function add_your_shipping_method( $methods ) {
		$methods['your_shipping_method'] = 'WC_Your_Shipping_Method';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );
}
