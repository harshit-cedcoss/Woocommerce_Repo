<?php
/**
 * Class for creating a new setting demo tab
 *
 * @package Woo Commerce
 */
class WC_Feedback_Settings_Tab {
	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_feedback_tab', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_feedback_tab', __CLASS__ . '::update_settings' );
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['feedback_tab'] = __( 'Feedback Tab', 'woocommerce-feedback-tab' );
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
			'section_title' => array(
				'name' => __( 'Feedback', 'woocommerce-feedback-tab' ),
				'type' => 'title',
				'desc' => 'Shopping Experience Feedback',
				'id'   => 'wc_feedback_tab_section_title',
			),
			'enable'         => array(
				'name' => __( 'Enable/Disable', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Tick to enable the Feedback form', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_enable',
			),
			'name'         => array(
				'name' => __( 'Name', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the Name field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_name',
			),
			'email'         => array(
				'name' => __( 'E-mail', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the E-mail field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_email',
			),
			'phone'   => array(
				'name' => __( 'Phone Number', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the Phone Number field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_phone',
			),
			'section_end'   => array(
				'type' => 'sectionend',
				'id'   => 'wc_feedback_tab_section_end',
			),
		);
		return apply_filters( 'wc_feedback_tab_settings', $settings );
	}

}
WC_Feedback_Settings_Tab::init();
