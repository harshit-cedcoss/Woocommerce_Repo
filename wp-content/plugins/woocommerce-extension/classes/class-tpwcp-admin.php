<?php
/**
 * Class to create additional product panel in admin
 *
 * @package TPWCP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TPWCP_Admin' ) ) {
	/**
	 * Class to create additional product panel in admin
	 */
	class TPWCP_Admin {

		/**
		 * Custructor Function
		 */
		public function __construct() {
		}
		/**
		 * Function init() for hooking the other functions in the class
		 *
		 * @return void
		 */
		public function init() {
			// Create the custom tab.
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'create_giftwrap_tab' ) );
			// Add the custom fields.
			add_action( 'woocommerce_product_data_panels', array( $this, 'display_giftwrap_fields' ) );
			// Save the custom fields.
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
		}

		/**
		 * Add the new tab to the $tabs array
		 *
		 * @see     https://github.com/woocommerce/woocommerce/blob/e1a82a412773c932e76b855a97bd5ce9dedf9c44/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
		 *
		 * @param array $tabs To alter the default tabs.
		 * @since   1.0.0
		 */
		public function create_giftwrap_tab( $tabs ) {
			$tabs['giftwrap'] = array(
				'label'    => __( 'Giftwrap', 'tpwcp' ), // The name of your panel.
				'target'   => 'gifwrap_panel', // Will be used to create an anchor link so needs to be unique.
				'class'    => array( 'giftwrap_tab', 'show_if_simple', 'show_if_variable' ), // Class for your panel tab - helps hide/show depending on product type.
				'priority' => 80, // Where your panel will appear. By default, 70 is last item.
			);
			return $tabs;
		}

		/**
		 * Display fields for the new panel
		 *
		 * @see https://docs.woocommerce.com/wc-apidocs/source-function-woocommerce_wp_checkbox.html
		 * @since   1.0.0
		 */
		public function display_giftwrap_fields() { ?>

			<div id='gifwrap_panel' class='panel woocommerce_options_panel'>
				<div class="options_group">
					<?php
					woocommerce_wp_checkbox(
						array(
							'id'                => 'include_giftwrap_option',
							'label'             => __( 'Include giftwrap option', 'tpwcp' ),
							'desc_tip'          => __( 'Select this option to show giftwrapping options for this product', 'tpwcp' ),
							'custom_attributes' => array( 'data-custom' => 'honaka' ),
						)
					);
					woocommerce_wp_checkbox(
						array(
							'id'       => 'include_custom_message',
							'label'    => __( 'Include custom message', 'tpwcp' ),
							'desc_tip' => __( 'Select this option to allow customers to include a custom message', 'tpwcp' ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'       => 'giftwrap_cost',
							'label'    => __( 'Giftwrap cost', 'tpwcp' ),
							'type'     => 'number',
							'desc_tip' => __( 'Enter the cost of giftwrapping this product', 'tpwcp' ),
						)
					);
					?>
			</div>
		</div>

			<?php
		}

		/**
		 * Save the custom fields using CRUD method
		 *
		 * @param $post_id
		 * @since 1.0.0
		 */
		public function save_fields( $post_id ) {

			$product = wc_get_product( $post_id );

			// Save the include_giftwrap_option setting.
			$include_giftwrap_option = isset( $_POST['include_giftwrap_option'] ) ? 'yes' : 'no';
			// update_post_meta( $post_id, 'include_giftwrap_option', sanitize_text_field( $include_giftwrap_option ) );.
			$product->update_meta_data( 'include_giftwrap_option', sanitize_text_field( $include_giftwrap_option ) );

			// Save the include_giftwrap_option setting.
			$include_custom_message = isset( $_POST['include_custom_message'] ) ? 'yes' : 'no';
			$product->update_meta_data( 'include_custom_message', sanitize_text_field( $include_custom_message ) );

			// Save the giftwrap_cost setting.
			$giftwrap_cost = isset( $_POST['giftwrap_cost'] ) ? $_POST['giftwrap_cost'] : '';
			$product->update_meta_data( 'giftwrap_cost', sanitize_text_field( $giftwrap_cost ) );

			$product->save();

		}

	}
}
