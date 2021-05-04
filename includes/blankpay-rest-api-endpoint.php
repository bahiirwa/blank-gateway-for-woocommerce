<?php
/**
 * Handle all the Webhook payments coming from Payments.
 */

// POST All Posts using API
add_action( 'rest_api_init', 'blankpay_add_webhook_endpoint' );

function blankpay_add_webhook_endpoint() {

  	register_rest_route( 'blankpay/v1', 'payments', array(
		'methods'  => 'POST',
		'callback' => 'blankpay_add_webhook_endpoint_callback',
	));

}

function blankpay_add_webhook_endpoint_callback( $request_data ) {
    
	// Fetching values from API
	$parameters = $request_data->get_params();
	$headers    = $request_data->get_headers();

	// custom meta values
	$transactionStatus         = $parameters['data']['transactionStatus'];
	$ExternalReference         = $parameters['data']['transactionExternalReference'];
	$transactionReference      = $parameters['data']['transactionReference'];
	$MNOTransactionReferenceId = $parameters['data']['MNOTransactionReferenceId'];
	$amount                    = $parameters['data']['amount'];
	$msisdn                    = $parameters['data']['msisdn'];
	$transactionInitiationDate = $parameters['data']['transactionInitiationDate'];
	$transactionCompletionDate = $parameters['data']['transactionCompletionDate'];
	
	// Verify Ping to contain data needed.
	if( empty( $ExternalReference ) && empty( $transactionStatus ) ) {
        return;
	}
    
    // Clear the order with the particular External ID.
    $order_id     = blankpay_get_order_with_external_ref_metakey( $ExternalReference );
    $order        = new WC_Order( $order_id );
    $order_amount = intval( $order->get_total() );
    $header_hash  = $headers['x_blankpay_signature'][0];
    $secret       = get_option( 'woocommerce_blankpay_payment_settings' )['secretKey'];
    
    // Verify the header X-ZENGAPAY-SIGNATURE.
    $security = hash_hmac( 'sha256', $transactionReference . $msisdn . $order_amount , $secret );
	
	if ( $security !== $header_hash ) {
	    return;
	}

    // Change the status of the order post type.
    if ( 'SUCCEEDED' === $transactionStatus ){
    
        if ( $order->has_downloadable_item() ) {
            $order->payment_complete();
            $order->update_status( 'completed' );
            $order->add_order_note('Payment was successful!');
        } else {
            $order->payment_complete();
            $order->update_status('processing');
            $order->add_order_note('Payment was successful!');
        }
    } else {
        $order->update_status('failed');
        $order->add_order_note('Payment Failed. Order is Cancelled!');
    }

	// Create Transaction Post Object.
	$blankpay_payments_post = array(
		'post_title'   => $ExternalReference,
		'post_status'  => 'publish',
		'post_type'    => 'blankpayments',
	);

	$blankpay_new_post_id = wp_insert_post( $blankpay_payments_post );

	// Set Custom Metabox
	update_post_meta( $blankpay_new_post_id, 'transactionStatus', $transactionStatus );
	update_post_meta( $blankpay_new_post_id, 'externalReference', $ExternalReference );
	update_post_meta( $blankpay_new_post_id, 'transactionReference', $transactionReference );
	update_post_meta( $blankpay_new_post_id, 'MNOTransactionReferenceId', $MNOTransactionReferenceId );
	update_post_meta( $blankpay_new_post_id, 'amount', $amount );
	update_post_meta( $blankpay_new_post_id, 'msisdn', $msisdn );
	update_post_meta( $blankpay_new_post_id, 'transactionInitiationDate', $transactionInitiationDate );
	update_post_meta( $blankpay_new_post_id, 'transactionCompletionDate', $transactionCompletionDate );
	update_post_meta( $blankpay_new_post_id, 'headers', $headers );
    
	$data = new WP_REST_Response(
		array(
			'status' => 'Received'
		)
	);

	$data->set_status(200);
		
	return $data;
}

/**
 * Get the order ID for the Paid Shop Order.
 */ 
function blankpay_get_order_with_external_ref_metakey( $ExternalReference ) {
    
    global $wpdb;
    
    $meta_query = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = 'blankpay_external_reference' AND  meta_value = $ExternalReference LIMIT 1", ARRAY_A );
    
    return $meta_query[0]['post_id'];
}