<?php
/**
 * Things that run before the default plugin functions.
 * 
 * Add UGX as a currency in WC
 * Enable the Ugandan Shillings currency symbol in WC
 */
if( ! function_exists( 'blankpay_add_ugx_currency' ) ) {
	add_filter( 'woocommerce_currencies', 'blankpay_add_ugx_currency' );
}

if( ! function_exists( 'blankpay_add_ugx_currency_symbol' ) ) {
	add_filter('woocommerce_currency_symbol', 'blankpay_add_ugx_currency_symbol', 10, 2);
}

/**
 * Add UGX Currency if that does not exist.
 *
 * @param array $currencies All old currencies.
 * @return array $currencies All new currencies + UGX.
 */
function blankpay_add_ugx_currency( $currencies ) {
	$currencies['UGX'] = __( 'Ugandan Shillings', 'blankpay-pay-woo' );
	return $currencies;
}

/**
 * Add Currency symbol for UGX if that does not exist.
 *
 * @param array $currencies All old Currency symbol.
 * @return array $currencies All new Currency symbol + UGX.
 */
function blankpay_add_ugx_currency_symbol( $currency_symbol, $currency ) {
	switch( $currency ) {
		case 'UGX': $currency_symbol = 'UGX '; break;
	}
	return $currency_symbol;
}

/**
 * Adds plugin page links
 * 
 * @since 0.1.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_blankpay_gateway_plugin_links( $links ) {

    // TODO: change the docs link for the plugin
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=blankpay_payment' ) . '">' . __( 'Configure Gateway', 'blankpay-pay-woo' ) . '</a>',
		'<a "target="_blank" rel="noopener noreferrer" href="https://github.com/bahiirwa/blankpay-for-woocommerce/blob/master/docs.md">' . __( 'Docs', 'blankpay-pay-woo' ) . '</a>'
	);

	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . BASENAME, 'wc_blankpay_gateway_plugin_links' );
