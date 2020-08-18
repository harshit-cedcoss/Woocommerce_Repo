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
	 * Define constants
	 */
	if ( ! defined( 'PLUGIN_VERSION' ) ) {
		define( 'PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'PLUGIN_DIR_PATH' ) ) {
		define( 'PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}
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
				'type'        => 'float',
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
	 * Getting the Product's Remark on product page.
	 */
	function get_product_remark( $cart_item_data ) {

	//	$remark = filter_input( INPUT_POST, 'remark' );
		$remark = isset( $_POST['remark'] ) ? $_POST['remark'] : '';
		if ( empty( $remark ) ) {

			return $cart_item_data;

		}
		$cart_item_data['remark'] = $remark;
		//print_r( $cart_item );die;
		return $cart_item_data;

	}
	add_filter( 'woocommerce_add_cart_item_data', 'get_product_remark', 10, 1 );

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
		//print_r( $item_data );
		$item_data[] = array(
			'key'     => __( 'Remark', '' ),
			'value'   => wc_clean( $cart_item['remark'] ),
			'display' => '',
		);
		//print_r( $item_data );
		//print_r( $cart_item );die;
		return $item_data;
	}
	add_filter( 'woocommerce_get_item_data', 'display_product_remark', 10, 2 );



	// echo '<pre>';
	// var_dump(curl_version());
	// echo '</pre>';

	/**
	 * Woocommere Mailchimp Integration
	 */
	function mailchimp_integration() {

		$api_key        = 'b407f3bd7139bc14ff72eaa1ab5a257a-us17';
		$list_id        = 'abe3b053be';
		$subsciber_hash = md5( strtolower( $_POST['email'] ) );
		$data_center    = 'us17';
		// print_r( $_POST );die;
		$url  = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members'; // endpoint.
		$data = array(
			'email_address' => $_POST['email'],
			'status'        => 'subscribed', // subscribed, unsubscribed, cleaned, pending.
			'merge_fields'  => array(
				'FNAME' => $_POST['username'],
				'LNAME' => '',
			),
			// 'email_address' => 'shantanu12@gmail.com',
			// 'status'        => 'subscribed',
			// 'merge_fields'  => array(
			// 'FNAME' => 'Shantanu',
			// 'LNAME' => 'Kumar',
			// ),
		);
		$json = json_encode( $data );
																		// Integration using PHP cURL.
		/*
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_USERPWD, 'user:' . $api_key );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );

		$result = curl_exec( $ch );
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		if ( false === $result ) {
			echo 'Error Number:' . curl_errno( $ch ) . '<br>';
			echo 'Error String:' . curl_error( $ch );
		}

		curl_close( $ch );

		return $http_code . $result;
		*/
																	// Integration using wp_remote_post().
		$basic_auth = 'Basic ' . base64_encode( 'user:' . $api_key );
		$options    = array(
			'method'      => 'POST',
			'timeout'     => 10,
			'redirection' => 5,
			'blocking'    => true,
			'body'        => $json,
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'Authorization' => $basic_auth,
			),
			'cookies'     => array(),
			'sslverify'   => false,
		);

		$response = wp_remote_post( $url, $options );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo 'Something went wrong:' . $error_message;
		} else {
			echo 'Response:<pre>';
		//	print_r( $_POST );
		//	print_r( $response );
			echo '</pre>';
		}
	}
	//echo mailchimp_integration();
	add_action( 'user_register', 'mailchimp_integration' );

	$response1 = wp_remote_get( 'https://api.github.com/users/wordpress' );
	//echo 'Response:<pre>';
	//print_r( $response1 );
	$last_modified = wp_remote_retrieve_header( $response1, 'last-modified' );
	//echo "<br>";
	//echo $last_modified;
	//echo '</pre>';

	/**
	 * Step 1. Add Link (Tab) to My Account menu
	 */
	function last_order_link( $items ) {

		$items = array_slice( $items, 0, 4, true ) 
		+ array( 'last-order' => 'Last Order' )
		+ array_slice( $items, 4, 2, true );
		//$menu_links['last-order'] = __( 'Last Order', '' );

		return $items;

	}
	add_filter( 'woocommerce_account_menu_items', 'last_order_link', 40 );
	/**
	 * Step 2. Register Permalink Endpoint
	 */
	function add_endpoint_last_order() {

		add_rewrite_endpoint( 'last-order', EP_PAGES );

	}
	add_action( 'init', 'add_endpoint_last_order' );
	/**
	 * Step 3. Content for the new page in My Account
	 */
	function my_account_endpoint_content() {

		if ( is_user_logged_in() ) {

			$user_id  = get_current_user_id();
			$customer = new WC_Customer( $user_id );

			$last_order = $customer->get_last_order();
			$item_count = $last_order->get_item_count() - $last_order->get_item_count_refunded();
			//print_r( $last_order );
			if ( $last_order ) {
				//die("die");
				?>
				<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Last Order details', 'woocommerce' ); ?></h2><br/>
				<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table" >
					<thead>
						<tr>
							<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) { ?>
								<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
							<?php } ?>
						</tr>
					</thead>

					<tbody>
						<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $last_order->get_status() ); ?> order">
							<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) { ?>
								<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>" >
									<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>

									<?php elseif ( 'order-number' === $column_id ) : ?>
										<a href="<?php echo esc_url( $last_order->get_view_order_url() ); ?>">
											<?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $last_order->get_order_number() ); ?>
										</a>

									<?php elseif ( 'order-date' === $column_id ) : ?>
										<time datetime="<?php echo esc_attr( $last_order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $last_order->get_date_created() ) ); ?></time>

									<?php elseif ( 'order-status' === $column_id ) : ?>
										<?php echo esc_html( wc_get_order_status_name( $last_order->get_status() ) ); ?>

									<?php elseif ( 'order-total' === $column_id ) : ?>
										<?php
										/* translators: 1: formatted order total 2: total order items */
										echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $last_order->get_formatted_order_total(), $item_count ) );
										?>

									<?php elseif ( 'order-actions' === $column_id ) : ?>
										<?php
										$actions = wc_get_account_orders_actions( $last_order );

										if ( ! empty( $actions ) ) {
											foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
												echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
											}
										}
										?>
									<?php endif; ?>
								</td>
							<?php } ?>
						</tr>
					</tbody>
				</table>
				<?php
			}
		}
	}
	add_action( 'woocommerce_account_last-order_endpoint', 'my_account_endpoint_content' );

	/**
	 * Class for adding a new Feedback Settings tab
	 */
	require PLUGIN_DIR_PATH . '/classes/class-wc-feedback-settings-tab.php';

	/**
	 * Adding feedback form in frontend.
	 *
	 * @param [type] $order_id gives the order id.
	 * @return void
	 */
	function feedback_form( $order_id ) {

		if ( isset( $_POST['feedback_comment'] ) && isset( $_POST['submit_feedback'] ) ) {
			//die;
			wc_add_notice( 'Thanyou! Your Feedback haas been submitted', 'success' );
			$feedback = sanitize_textarea_field( wp_unslash( $_POST['feedback_comment'] ) );
			if ( is_user_logged_in() ) {

				$current_user = wp_get_current_user();
				$email        = $current_user->user_email;
				update_user_meta( get_current_user_id(), 'feedback', $feedback );
				update_user_meta( get_current_user_id(), 'email', $email );
				wp_mail( $email, 'Feedback', $feedback );

			} else {
				//die("kjdv");
				$email = isset( $_POST['email'] ) ? sanitize_textarea_field( wp_unslash( $_POST['email'] ) ) : '';
				$name  = isset( $_POST['name'] ) ? sanitize_textarea_field( wp_unslash( $_POST['name'] ) ) : '';
				$phone = isset( $_POST['phone'] ) ? sanitize_textarea_field( wp_unslash( $_POST['phone'] ) ) : '';
				// update_user_meta( get_current_user_id(), 'feedback', $feedback );
				// update_user_meta( get_current_user_id(), 'name', $name );
				// update_user_meta( get_current_user_id(), 'phone', $phone );
				wp_mail( $email, 'Feedback', $feedback );
			}
		}
		if ( 'yes' === get_option( 'wc_feedback_tab_enable' ) ) {

			$form = '<h2>What do you think about Your Shopping Experience</h2>
			<form id="thankyou_feedback_form" action="" method="POST">';

			if ( is_user_logged_in() ) {

				$form .= '<textarea name="feedback_comment" placeholder="Give your feedback here."></textarea></br></br>
				<input type="hidden" name="action" value="collect_feedback" />
				<input type="hidden" name="order_id" value="' . esc_html( $order_id ) . '" />
				<input type="submit" name="submit_feedback" value="Submit" />
				</form>';

			} else {
				if ( 'yes' === get_option( 'wc_feedback_tab_name' ) ) {

					$form .= '<input type="text" name="name" placeholder="Name"/></br></br>';
				}
				if ( 'yes' === get_option( 'wc_feedback_tab_email' ) ) {

					$form .= '<input type="text" name="email" placeholder="E-mail"/></br></br>';
				}
				if ( 'yes' === get_option( 'wc_feedback_tab_phone' ) ) {

					$form .= '<input type="text" name="phone" placeholder="Phone Number"/></br></br>';
				}
				$form .= '<textarea name="feedback_comment" placeholder="Give your feedback here."></textarea></br></br>
					<input type="hidden" name="action" value="collect_feedback" />
					<input type="hidden" name="order_id" value="' . esc_html( $order_id ) . '" />
					<input type="submit" name="submit_feedback" value="Submit" />
					</form>';
			}
			echo $form;
		}
	}
	add_action( 'woocommerce_thankyou', 'feedback_form', 10 );
}
