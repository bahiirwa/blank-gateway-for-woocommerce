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

// Check if WooCommerce is installed.
// if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

// Define constants to be used.
if( ! defined( 'BASENAME' ) ) {
	define( 'BASENAME', plugin_basename( __FILE__ ) );
}

if( ! defined( 'DIR_PATH' ) ) {
	define( 'DIR_PATH', plugin_dir_path( __FILE__ ) );
}

// When plugin is loaded. Call init functions.
add_action( 'plugins_loaded', 'blankpay_payment_init' );
add_filter( 'woocommerce_payment_gateways', 'blankpay_payment_gateway_add_to_woo');

/**
 * Add the gateway class.
 * Add function helpers.
 * 
 * @return void
 */
function blankpay_payment_init() {
	require_once DIR_PATH . 'includes/blankpay-initial-setup.php';
	require_once DIR_PATH . 'includes/class-blankpay-gateway.php';
	require_once DIR_PATH . 'includes/blankpay-order-statuses.php';
	require_once DIR_PATH . 'includes/blankpay-checkout-page.php';
	require_once DIR_PATH . 'includes/blankpay-payments-cpt.php';
	require_once DIR_PATH . 'includes/blankpay-rest-api-endpoint.php';
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
