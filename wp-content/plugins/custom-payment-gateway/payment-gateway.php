<?php
/**
 * Plugin Name: Custom Payment Gateway
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Extention for woocommerce to add Reward points for the customers.
 * Version: 1.0.0
 * Author: Harshit
 * Author URI: http://yourdomain.com/
 * Developer: Harshit
 * Developer URI: http://yourdomain.com/
 * Text Domain: custom-payment-gateway
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

	add_action( 'plugins_loaded', 'woocommerce_gateway_custom_init', 0 );
	/**
	 * Custom payment Gateway.
	 */
	function woocommerce_gateway_custom_init() {
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
		class WC_Gateway_Custom extends WC_Payment_Gateway {

			// Go wild in here.
			/**
			 * Custructor function for custom test payment gateway
			 */
			public function __construct() {
				$this->id                 = 'custom_payment_gateway';
				$this->icon               = '';
				$this->has_fields         = false;
				$this->method_title       = __( 'Custom Payment Gateway', 'woo-gateway' );
				$this->method_description = __( 'Description for my custom payment method', 'woo-gateway' );

				$this->init_form_fields();
				$this->init_settings();

				$this->title           = $this->get_option( 'title' );
				$this->description     = $this->get_option( 'description' );
				$this->enabled         = $this->get_option( 'enabled' );
				$this->testmode        = 'yes' === $this->get_option( 'testmode' );
				$this->private_key     = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
				$this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );

				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			/**
			 * Initialize gateway settings form fields.
			 */
			public function init_form_fields() {
				$this->form_fields = apply_filters(
					'wc_test_gateway_form_fields',
					array(
						'enabled'              => array(
							'title'   => __( 'Enabled/Disabled', 'woo-gateway' ),
							'type'    => 'checkbox',
							'label'   => __( 'Enable Custom Payment Gateway', 'woo-gateway' ),
							'default' => 'yes',
						),
						'title'                => array(
							'title'       => __( 'Payment gateway title', 'woo-gateway' ),
							'type'        => 'text',
							'description' => __( 'Name the title for payment gateway which is to be shown on the frontend', 'woo-gateway' ),
							'desc_tip'    => true,
							'default'     => __( 'MyPay', 'woo-gateway' ),
						),
						'description'          => array(
							'title'       => __( 'Gateway Description', 'woo-gateway' ),
							'type'        => 'textarea',
							'description' => __( 'Payent method description that customer will see at the checkout', 'woo-gateway' ),
							'desc_tip'    => true,
							'default'     => __( 'This is the custom test payment gateway', 'woo-gateway' ),
						),
						'testmode'             => array(
							'title'       => 'Test mode',
							'label'       => 'Enable Test Mode',
							'type'        => 'checkbox',
							'description' => 'Place the payment gateway in test mode using test API keys.',
							'default'     => 'yes',
							'desc_tip'    => true,
						),
						'test_publishable_key' => array(
							'title' => 'Test Publishable Key',
							'type'  => 'text',
						),
						'test_private_key'     => array(
							'title' => 'Test Private Key',
							'type'  => 'password',
						),
						'publishable_key'      => array(
							'title' => 'Live Publishable Key',
							'type'  => 'text',
						),
						'private_key'          => array(
							'title' => 'Live Private Key',
							'type'  => 'password',
						),
					)
				);
			}
			/**
			 * You will need it if you want your custom Payment card form
			 */
			public function payment_fields() {

				// ok, let's display some description before the payment form.
				if ( $this->description ) {
					// you can instructions for test mode, I mean test card numbers etc.
					if ( $this->testmode ) {
						$this->description .= 'TEST MODE ENABLED.';
						$this->description  = trim( $this->description );
					}
					// display the description with <p> tags etc.
					echo wpautop( wp_kses_post( $this->description ) );
				}

				// I will echo() the form, but you can close PHP tags and print it directly in HTML.
				echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

				// Add this action hook if you want your custom payment gateway to support it.
				do_action( 'woocommerce_credit_card_form_start', $this->id );

				// I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc.
				echo '<div class="form-row form-row-wide"><label>Card Number <span class="required">*</span></label>
					<input id="CPG_ccNo" name="CPG_ccNo" type="text" autocomplete="off">
					</div>
					<div class="form-row form-row-first">
						<label>Expiry Date <span class="required">*</span></label>
						<input id="CPG_expdate" name="CPG_expdate" type="text" autocomplete="off" placeholder="MM / YY">
					</div>
					<div class="form-row form-row-last">
						<label>Card Code (CVC) <span class="required">*</span></label>
						<input id="CPG_cvv" name="CPG_cvv" type="password" autocomplete="off" placeholder="CVC">
					</div>
					<div class="clear"></div>';

				do_action( 'woocommerce_credit_card_form_end', $this->id );

				echo '<div class="clear"></div></fieldset>';

			}

			/**
			 * Fields validation, more in Step 5
			 */
			public function validate_fields() {

				if ( empty( $_POST['CPG_ccNo'] ) ) {
					wc_add_notice( 'Card number is required!', 'error' );
					return false;
				} elseif ( empty( $_POST['CPG_expdate'] ) ) {
					wc_add_notice( 'Expiry date is required!', 'error' );
					return false;
				} elseif ( empty( $_POST['CPG_cvv'] ) ) {
					wc_add_notice( 'CVV number is required!', 'error' );
					return false;
				}
				return true;

			}
			/**
			 * Function after Payment is completed.
			 *
			 * @param [int] $order_id To fetch the order ID.
			 */
			// public function process_payment( $order_id ) {
			// 	global $woocommerce;
			// 	$order = new WC_Order( $order_id );

			// 	// make the payment status.
			// 	$order->payment_complete();

			// 	// Reduce stock levels.
			// 	$order->reduce_order_stock();

			// 	// remove cart items.
			// 	$woocommerce->cart->empty_cart();

			// 	// Return Thankyou page redirect.
			// 	return array(
			// 		'result'   => 'success',
			// 		'redirect' => $this->get_return_url( $order ),
			// 	);
			// }
			/**
			 * Function after Payment is completed.
			 *
			 * @param [int] $order_id To fetch the order ID.
			 */
			public function process_payment( $order_id ) {

				global $woocommerce;

				// we need it to get any order details.
				$order = wc_get_order( $order_id );

				/*
				 * Array with parameters for API interaction
				 */
				$args = array();

				/*
				 * Your API interaction could be built with wp_remote_post()
				  */
				$response = wp_remote_post( '{payment processor endpoint}', $args );

				if ( ! is_wp_error( $response ) ) {

					$body = json_decode( $response['body'], true );

					// it could be different depending on your payment processor.
					if ( $body['response']['responseCode'] == 'APPROVED' ) {

						// we received the payment.
						$order->payment_complete();
						$order->reduce_order_stock();

						// some notes to customer (replace true with false to make it private).
						$order->add_order_note( 'Hey, your order is paid! Thank you!', true );

						// Empty cart.
						$woocommerce->cart->empty_cart();

						// Redirect to the thank you page.
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);

					} else {
						wc_add_notice( 'Please try again.', 'error' );
						return;
					}
				} else {
					wc_add_notice( 'Connection error.', 'error' );
					return;
				}

			}
		}

		/**
		 * Add the Gateway to WooCommerce
		 * This action hook registers our PHP class as a WooCommerce payment gateway.
		 *
		 * @param [array] $methods fro including the gateway to woocommerce.
		 * @return $methods
		 */
		function woocommerce_add_gateway_custom_gateway( $methods ) {
			$methods[] = 'WC_Gateway_Custom';
			return $methods;
		}

		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_custom_gateway' );
	}
}
