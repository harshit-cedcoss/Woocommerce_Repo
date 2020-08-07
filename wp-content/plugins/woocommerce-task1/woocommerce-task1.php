<?php
/**
 * Plugin Name: Woocommerce Product Settings Extension
 * Description: Adding new tabs in Product Settings.
 * Version:     1.0.0
 * Author:      Harshit
 * Developer:   Harshit
 * Text Domain: woocommerce-task1
 * Domain Path: /languages
 *
 * @package Woocommerce
 */

/**
 * Check if woocommerce is active.
 */
if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {

	/**
	 * Function to add a field of Rating in the general tab (Product-data Settings).
	 */
	function add_rating_field() {
		echo '<div class="option_group" >';
		woocommerce_wp_text_input(
			array(
				'id'          => 'product_rating',
				'label'       => __( 'Ratings', '' ),
				'description' => __( 'Rate this product from 1 to 5', '' ),
				'desc_tip'    => true,
				'type'        => 'number',
			)
		);
		echo '</div>';
	}
	add_action( 'woocommerce_product_options_general_product_data', 'add_rating_field' );

	/**
	 * Save the Rating field.
	 *
	 * @param int $post_id ID of the product.
	 */
	function save_rating_field( $post_id ) {

		update_post_meta( $post_id, 'product_rating', isset( $_POST['product_rating'] ) ? sanitize_text_field( $_POST['product_rating'] ) : '' );

	}
	add_action( 'woocommerce_process_product_meta', 'save_rating_field', 10, 1 );

	/**
	 * Showing rating in form of stars on frontend.
	 *
	 * @return void
	 */
	function add_star_rating() {

		global $product;
		$id     = $product->get_id();
		$rating = (float) get_post_meta( $id, 'product_rating', true );
		// echo( gettype( $average ) );
		echo '<div class="star-rating"><span style="width:' . ( ( esc_html( $rating ) / 5 ) * 100 ) . '%"></span></div>';
	}
	add_action( 'woocommerce_after_shop_loop_item', 'add_star_rating' );

	/**
	 * Function to load the setting when plugin is loaded.
	 *
	 * @return void
	 */
	function custom_init() {

		add_filter( 'woocommerce_product_data_tabs', 'create_custom_settings_tab' );
		add_action( 'woocommerce_product_data_panels', 'display_custom_settings_fields' );
	}
	add_action( 'plugins_loaded', 'custom_init' );

	/**
	 * Add the new tab to the $tabs array
	 *
	 * @param array $tabs To alter the default tabs.
	 */
	function create_custom_settings_tab( $tabs ) {
		$tabs['custom_setting'] = array(
			'label'    => __( 'Custom Settings', '' ),
			'target'   => 'custom_settings_panel',
			'class'    => array( 'custom_tab', 'show_if_simple' ),
			'priority' => 100,
		);
		return $tabs;
	}

	/**
	 * Display fields for new Custom Settings Panel
	 */
	function display_custom_settings_fields() { ?>

		<div id="custom_settings_panel" class="panel woocommerce_options_panel">
			<div class="options_group">
				<h4>Choose one of the <i>Product Remark Input<i> field.</h4>
				<?php
				woocommerce_wp_checkbox(
					array(
						'id'          => 'activate_input_field',
						'label'       => __( 'Activate Input Field', '' ),
						'description' => __( 'Select the checkbox to Activate the Input field on detailed product page', '' ),
						'desc_tip'    => true,
					)
				);
				woocommerce_wp_checkbox(
					array(
						'id'          => 'activate_select_field',
						'label'       => __( 'Activate Select Field', '' ),
						'description' => __( 'Select the checkbox to Activate the Select field on detailed product page', '' ),
						'desc_tip'    => true,
					)
				);
				woocommerce_wp_checkbox(
					array(
						'id'          => 'activate_radio_field',
						'label'       => __( 'Activate Radio Field', '' ),
						'description' => __( 'Select the checkbox to Activate the Radio field on detailed product page', '' ),
						'desc_tip'    => true,
					)
				);
				?>
			</div>
		</div>	
		<?php
	}

	/**
	 * Function to save the Custom Settings Tab.
	 */
	function save_custom_settings_fields( $post_id ) {

		update_post_meta( $post_id, 'activate_input_field', isset( $_POST['activate_input_field'] ) ? 'yes' : 'no' );
		update_post_meta( $post_id, 'activate_select_field', isset( $_POST['activate_select_field'] ) ? 'yes' : 'no' );
		update_post_meta( $post_id, 'activate_radio_field', isset( $_POST['activate_radio_field'] ) ? 'yes' : 'no' );
	}

	add_action( 'woocommerce_process_product_meta', 'save_custom_settings_fields' );

	/**
	 * Function to display the Active Custom fields.
	 */
	function display_active_fields() {
		global $product;
		if ( get_post_meta( $product->id, 'activate_input_field', true ) === 'yes' ) {
			//die( "remark" );
			?>
			<br/><br/>
			<label for="custom_active_input_field" ><b>Remark:</b></label>
			<input id="custom_active_input_field" type="text" name="remark" />
			<?php

		}
		if ( get_post_meta( $product->id, 'activate_select_field', true ) === 'yes' ) {

			?>
			<br/><br/>
			<label for="custom_active_select_field" ><b>Remark:</b></label>
			<select id="custom_active_select_field" name="remark" >
				<option value="good">Good</option>
				<option value="fair">Fair</option>
				<option value="bad">Bad</option>
			</select>
			<?php

		}
		if ( get_post_meta( $product->id, 'activate_radio_field', true ) === 'yes' ) {

			?>
			<br/><br/>
			<label><b>Remark:</b></label>
			<input class="custom_active_input_field" id="good" type="radio" name="remark" value="good" />
			<lable for="good">Good</lable>
			<input class="custom_active_input_field" id="fair" type="radio" name="remark" value="fair" />
			<lable for="good">Fair</lable>
			<input class="custom_active_input_field" id="bad" type="radio" name="remark" value="bad" />
			<lable for="good">Bad</lable>
			<?php

		}
	}
	add_action( 'woocommerce_after_add_to_cart_button', 'display_active_fields', 10 );

	/**
	 * Getting the Product Remark on cart page.
	 */
	function get_product_remark( $cart_item_data ) {

		$remark = filter_input( INPUT_POST, 'remark' );
		if ( empty( $remark ) ) {

			return $cart_item_data;

		}
		$cart_item_data['remark'] = $remark;
		return $cart_item_data;

	}
	add_filter( 'woocommerce_add_cart_item_data', 'get_product_remark', 10, 3 );

	/**
	 * Display the Product Remark on cart page.
	 *
	 * @param [type] $item_data
	 * @param [type] $cart_item
	 * @return $item_data
	 */
	function display_product_remark( $item_data, $cart_item ) {

		if ( empty( $cart_item['remark'] ) ) {

			return $item_data;

		}
		$item_data[] = array(
			'key'     => __( 'Remark', '' ),
			'value'   => wc_clean( $cart_item['remark'] ),
			'display' => '',
		);

		return $item_data;
	}
	add_filter( 'woocommerce_get_item_data', 'display_product_remark', 10, 2 );

}
