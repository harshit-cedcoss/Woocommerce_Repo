<?php
/**
 * Class for creating a new setting demo tab
 *
 * @package Woo Commerce
 */
class WC_Reward_Points_Settings_Tab {
	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_points_settings_tab', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_points_settings_tab', __CLASS__ . '::update_settings' );
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['points_settings_tab'] = __( 'Reward Points Settings', 'woocommerce-reward-points-tab' );
		return $settings_tabs;
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {
		$settings = array(
			'section_title'           => array(
				'name' => __( 'Points Settings', 'woocommerce-reward-points-tab' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wc_points_settings_tab_section_title',
			),
			'title'                   => array(
				'name'     => __( 'Earn Points Conversion Rates', 'woocommerce-reward-points-tab' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded based on the product price.', 'woocommerce-reward-points-tab' ),
				'id'       => 'wc_reward_points_conversion_rate_settings_tab',
			),
			'adding_product_reward'   => array(
				'name'     => __( 'Reward Points for add to cart a product', 'woocommerce-reward-points-tab' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded for adding a product to the cart.', 'woocommerce-reward-points-tab' ),
				'id'       => 'wc_adding_product_reward_points_settings_tab',
			),
			'visiting_product_reward' => array(
				'name'     => __( 'Reward Points for visiting a product page', 'woocommerce-reward-points-tab' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded for visiting a product detailed page.', 'woocommerce-reward-points-tab' ),
				'id'       => 'wc_visiting_product_reward_points_settings_tab',
			),
			'checkout_reward'         => array(
				'name'     => __( 'Reward Points for Checkout', 'woocommerce-reward-points-tab' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded for every Checkout.', 'woocommerce-reward-points-tab' ),
				'id'       => 'wc_checkout_reward_points_settings_tab',
			),
			'redemption'              => array(
				'name'     => __( 'Redemption Conversion Rate', 'woocommerce-reward-points-tab' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the value of points redeemed for a discount.', 'woocommerce-reward-points-tab' ),
				'id'       => 'wc_redemption_conversion_rate_settings_tab',
			),
			'min_points'              => array(
				'name'     => __( 'Minimum Reward Points', 'woocommerce-reward-points-tab' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the minimum Reward Points which are to be collected by user to provide a Discount.', 'woocommerce-reward-points-tab' ),
				'id'       => 'wc_min_reward_points_settings_tab',
			),
			'max_points'              => array(
				'name'     => __( 'Maximum Reward Points', 'woocommerce-reward-points-tab' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the Maximum Reward Points which can be redeemed for a Discount.', 'woocommerce-reward-points-tab' ),
				'id'       => 'wc_max_reward_points_settings_tab',
			),
			'section'                 => array(
				'type' => 'sectionend',
				'id'   => 'wc_points_settings_tab_section_end',
			),
		);
		return apply_filters( 'wc_points_settings_tab_settings', $settings );
	}

}
WC_Reward_Points_Settings_Tab::init();
