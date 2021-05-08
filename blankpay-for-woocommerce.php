<?php
/**
 * 
 * Plugin Name: Blankpay WooCommerce
 * Plugin URI: https://developers.blankpay.com
 * Author: blankpay
 * Author URI: https://blankpay.com
 * Description: blankpay enables businesses to receive Mobile Money Payments from their customers. Supports MTN Mobile Money and Airtel Money.
 * Version: 0.1.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text-Domain: blankpay-pay-woo
 * 
 * WC requires at least: 3.0
 * WC tested up to: 4.3.0
 */ 

// Basic Security to avoid brute access to file.
defined( 'ABSPATH' ) or exit;

// Define constants to be used.
if( ! defined( 'BLANK_BASENAME' ) ) {
	define( 'BLANK_BASENAME', plugin_basename( __FILE__ ) );
}

if( ! defined( 'BLANK_PLUGIN_NAME' ) ) {
	define( 'BLANK_PLUGIN_NAME', 'UCatch for WooCommerce' );
}

if( ! defined( 'BLANK_DIR_PATH' ) ) {
	define( 'BLANK_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

// Check if WooCommerce is installed when Plugins loaded.
add_action( 'plugins_loaded', 'blank_wc_init', 0 );

function blank_wc_init() {

    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {

		function woocommerce_not_installed_notice() {
			$message = sprintf(
				/* translators: URL of WooCommerce plugin */
				__( 'requires <a href="%s">WooCommerce</a> 3.0 or greater to be installed and active.', 'cashleo-sms-notifications-for-woo' ),
				'https://wordpress.org/plugins/woocommerce/'
			);
		
			printf( '<div class="error notice notice-error is-dismissible"><p>%s</p></div>', BLANK_PLUGIN_NAME . ' ' . $message ); 
		}

		// Check if WooCommerce is active
		add_action( 'admin_notices', 'woocommerce_not_installed_notice' );
		return;
	}

	add_filter( 'woocommerce_payment_gateways', 'blank_payment_gateway_add_to_woo');

	/**
	 * Add the gateway class.
	 * Add function helpers.
	 */
	require_once BLANK_DIR_PATH . 'includes/blank-initial-setup.php';
	require_once BLANK_DIR_PATH . 'includes/class-blank-gateway.php';
	require_once BLANK_DIR_PATH . 'includes/blank-order-statuses.php';
	require_once BLANK_DIR_PATH . 'includes/blank-checkout-page.php';
	require_once BLANK_DIR_PATH . 'includes/blank-payments-cpt.php';
	require_once BLANK_DIR_PATH . 'includes/blank-rest-api-endpoint.php';
	
}

/**
 * Add Payment gateway to Woocommerce.
 *
 * @param array $gateways Existing Gateways in WC.
 * @return array $gateways Existing Gateways in WC + blankpay.
 */
function blankpay_payment_gateway_add_to_woo( $gateways ) {
    $gateways[] = 'WC_BlankPay_Gateway';
    return $gateways;
}
