<?php
/**
 * Plugin Name: Credit points WooExtension
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Extention for woocommerce to add Reward points for the customers.
 * Version: 1.0.0
 * Author: Harshit
 * Author URI: http://yourdomain.com/
 * Developer: Harshit
 * Developer URI: http://yourdomain.com/
 * Text Domain: reward-points-extention
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
	if ( ! defined( 'PLUGIN_VERSION' ) ) {
		define( 'PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'PLUGIN_DIR_PATH' ) ) {
		define( 'PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Class for adding a new setting demo tab
	 */
	require PLUGIN_DIR_PATH . '/classes/class-wc-reward-points-settings-tab.php';

	add_filter( 'manage_users_columns', 'adding_reward_points_column', 20 );
	/**
	 * Adding Reward Points Column in the User's Admin panel Table
	 *
	 * @param array $user_columns
	 */
	function adding_reward_points_column( $user_columns ) {

		return array_slice( $user_columns, 0, 5, true ) // 4 columns before
		+ array( 'reward_points' => 'Reward Points' ) // our column is 5th.
		+ array_slice( $user_columns, 1, null, true );

	}

	add_filter( 'manage_users_custom_column', 'filling_reward_points_column', 10, 3 );
	/**
	 * Updating the User's reward points info in User's Admin Panel
	 *
	 * @param array $row_output
	 * @param string $user_column_name
	 * @param int $user_id
	 */
	function filling_reward_points_column( $row_output, $user_column_name, $user_id ) {

		if ( empty( get_user_meta( $user_id, 'user_reward_points', true ) ) ) {

			add_user_meta( $user_id, 'user_reward_points', '', true );
		}

		if ( 'reward_points' === $user_column_name ) {

			$reward_points = isset( $reward_points ) ? $reward_points : 0;
			$reward_points = get_user_meta( $user_id, 'user_reward_points', true );
			return $reward_points; // here we replace and return our custom output.

		}
	}

	/**
	 * Updating the Reward points in the user's meta table in database.
	 *
	 * @return void
	 */
	function update_user_reward_points_info() {

		global $woocommerce;
		$cart_subtotal = $woocommerce->cart->subtotal;
		// $cart_items = $woocommerce->cart->cart_contents_count;
		$user_id              = get_current_user_id();
		$checkout_reward      = isset( $checkout_reward ) ? $checkout_reward : 0;
		$reward_points        = isset( $reward_points ) ? $reward_points : 0;
		$product_visit_reward = isset( $product_visit_reward ) ? $product_visit_reward : 0;
		$cart_price_reward    = isset( $cart_price_reward ) ? $cart_price_reward : 0;

		if ( is_product() ) {

			$product_visit_reward = get_option( 'wc_visiting_product_reward_points_settings_tab', 0 );
		}

		if ( is_checkout() ) {
			if ( $cart_subtotal > 0 ) {

				$cart_price_reward = floor( $cart_subtotal * get_option( 'wc_reward_points_conversion_rate_settings_tab', 0 ) );

			}
		}

		if ( is_wc_endpoint_url( 'order-received' ) ) {

			$checkout_reward = get_option( 'wc_checkout_reward_points_settings_tab', 0 );

		}

		$reward_points       = get_user_meta( $user_id, 'user_reward_points', true );
		$total_reward_points = $reward_points + $product_visit_reward + $checkout_reward + $cart_price_reward;
		update_user_meta( $user_id, 'user_reward_points', $total_reward_points, false );
	}

	add_action( 'wp_head', 'update_user_reward_points_info' );

	/**
	 * Reward Points Discount for a customer on the frontend.
	 */
	function woocommerce_reward_points_discount() {

		global $woocommerce;
		$min_points    = get_option( 'wc_min_reward_points_settings_tab' );
		$max_points    = get_option( 'wc_max_reward_points_settings_tab' );
		$redemption    = get_option( 'wc_redemption_conversion_rate_settings_tab' );
		$reward_points = get_user_meta( get_current_user_id(), 'user_reward_points', true );

		if ( empty( $min_points ) || empty( $max_points ) || empty( $redemption ) ) {
			return;
		}
		if ( $reward_points > $min_points ) {
			if ( $reward_points < $max_points ) {
				$reward_points_discount = floor( $reward_points / $redemption );
				$points_left            = 0;
			//	wc_print_notice( "Discount Applied for $reward_points Reward Points!", 'success' );

			} else {
				$reward_points_discount = floor( $max_points / $redemption );
				$points_left            = $reward_points - $max_points;
			//	wc_print_notice( "Discount Applied for $max_points Reward Points!", 'success' );
			}
		}
		$woocommerce->cart->add_fee( 'Reward Points Discount', -$reward_points_discount, true, '' );
		// update_user_meta( get_current_user_id(), 'user_reward_points', $points_left, false );
	}
	add_action( 'woocommerce_cart_calculate_fees', 'woocommerce_reward_points_discount' );
}
