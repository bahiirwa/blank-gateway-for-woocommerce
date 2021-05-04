<?php
/**
 * Defaults for the BLANKPAY checkout page.
 */

add_filter( 'woocommerce_gateway_description', 'blankpay_billing_phone_fields', 20, 2 );
add_action( 'woocommerce_checkout_process', 'blankpay_billing_phone_fields_validation', 20, 1 );
add_action( 'woocommerce_checkout_update_order_meta', 'blankpay_billing_phone_save_field' );
add_action( 'woocommerce_admin_order_data_after_billing_address', 'blankpay_billing_phone_show_field_admin_order', 10, 1 );

/**
 * Check if the phone number for billing is filled.
 *
 * @param object $order Order Object.
 * @return void
 */
function blankpay_billing_phone_fields_validation( $order ) {

    if ( 'blankpay_payment' === $_POST['payment_method'] ) {
    
        $blankpay_payment_phone_number = $_POST['blankpay_payment_phone_number'];
    
        // Error the Phone number
        if( ! isset( $blankpay_payment_phone_number ) || empty( $blankpay_payment_phone_number ) ) {
            wc_add_notice( 'Please enter the Phone Number for Billing (Format: 256772123456 )', 'error' );
            return;
        }
    
        if( '256' !== substr( $blankpay_payment_phone_number, 3 ) && 12 !== strlen( $blankpay_payment_phone_number ) && ! is_numeric( $blankpay_payment_phone_number ) ) {
            wc_add_notice( 'Please enter the Phone Number with correct format e.g 256772123456 )', 'error' );
        }

    }

}

/**
 * Set up billing number for the payment gateway.
 *
 * @param array $description Fields added in the gateway platform.
 * @param int $payment_id    Order Payment ID.
 * @return void
 */
function blankpay_billing_phone_fields( $description, $payment_id ) {

    if ( 'blankpay_payment' !== $payment_id ) {
        return $description;
    }

    ob_start();
    
    // Billing number Field.
    woocommerce_form_field(
        'blankpay_payment_phone_number',
        array(
            'type' => 'text',
            'label' =>__( 'Enter Phone Number e.g 256772123456', 'blankpay-pay-woo' ),
            'class' => array( 'form-row', 'form-row-wide', 'card-number' ),
            'required' => true,
        )
    );

    $description .= ob_get_clean();
    
    return $description;
}
				
function blankpay_billing_phone_save_field( $order_id ) {
    
    $order       = new WC_Order( $order_id );
    $order_total = intval( $order->get_total() );
    
    if ( $_POST['blankpay_payment_phone_number'] ) {
        update_post_meta( $order_id, 'blankpay_payment_phone_number', esc_attr( $_POST['blankpay_payment_phone_number'] ) );
    }
    if ( $_POST['blankpay_payment_phone_number'] ) {
        update_post_meta( $order_id, 'blankpay_external_reference', esc_attr( $_POST['blankpay_payment_phone_number'] . $order_id . $order_total ) );
    }
}
   
function blankpay_billing_phone_show_field_admin_order( $order ) {    
   $order_id = $order->get_id();
   if ( get_post_meta( $order_id, 'blankpay_payment_phone_number', true ) ) {
       echo '<p><strong>BLANKPAY Payment number:</strong> ' . get_post_meta( $order_id, 'blankpay_payment_phone_number', true ) . '</p>';
       echo '<p><strong>BLANKPAY External Reference:</strong> ' . get_post_meta( $order_id, 'blankpay_external_reference', true ) . '</p>';
   }
}