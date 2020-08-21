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
		add_action( 'woocommerce_admin_fields_feedback_form_list', __CLASS__ . '::feedback_list_settings' );
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
			array(
				'name' => __( 'Feedback List', 'woocommerce-feedback-tab' ),
				'type' => 'title',
				'desc' => 'Feedback List',
				'id'   => 'wc_feedback_list_tab_section_title',
			),
			array( 'type' => 'feedback_form_list' ),
			array(
				'type' => 'sectionend',
				'id'   => 'wc_feedback_list_tab_section_end',
			),
			array(
				'name' => __( 'Feedback', 'woocommerce-feedback-tab' ),
				'type' => 'title',
				'desc' => 'Shopping Experience Feedback',
				'id'   => 'wc_feedback_tab_section_title',
			),
			array(
				'name' => __( 'Enable/Disable', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Tick to enable the Feedback form', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_enable',
			),
			array(
				'name' => __( 'Field-1 Label', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the label you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field1_label',
			),
			array(
				'name' => __( 'Field-1 Type', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the type you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field1_type',
			),
			array(
				'name' => __( 'Field-1 Enable', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the above field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field1_enable',
			),
			array(
				'name' => __( 'Field-2 Label', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the label you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field2_label',
			),
			array(
				'name' => __( 'Field-2 Type', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the type you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field2_type',
			),
			array(
				'name' => __( 'Field-2 Enable', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the above field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field2_enable',
			),
			array(
				'name' => __( 'Field-3 Label', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the label you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field3_label',
			),
			array(
				'name' => __( 'Field-3 Type', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the type you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field3_type',
			),
			array(
				'name' => __( 'Field-3 Enable', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the above field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field3_enable',
			),
			array(
				'name' => __( 'Field-4 Label', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the label you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field4_label',
			),
			array(
				'name' => __( 'Field-4 Type', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the type you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field4_type',
			),
			array(
				'name' => __( 'Field-4 Enable', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the above field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field4_enable',
			),
			array(
				'name' => __( 'Field-5 Label', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the label you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field5_label',
			),
			array(
				'name' => __( 'Field-5 Type', 'woocommerce-feedback-tab' ),
				'type' => 'text',
				'desc' => __( 'Enter the type you want to create', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field5_type',
			),
			array(
				'name' => __( 'Field-5 Enable', 'woocommerce-feedback-tab' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the above field', 'woocommerce-feedback-tab' ),
				'id'   => 'wc_feedback_tab_field5_enable',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc_feedback_tab_section_end',
			),
		);
		return apply_filters( 'wc_feedback_tab_settings', $settings );
	}

	public static function feedback_list_settings() {
		?>
		<h2>this is the heading</h2>
		<?php
		die( 'slhs' );
	}

}
WC_Feedback_Settings_Tab::init();
