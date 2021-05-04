<?php
/**
 * Add new Invoiced status for woocommerce
 */
add_action( 'init', 'register_blankpay_order_statuses' );

function register_blankpay_order_statuses() {
    register_post_status( 'wc-invoiced', array(
        'label'                     => _x( 'Invoiced', 'Order status', 'blankpay-pay-woo' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Invoiced <span class="count">(%s)</span>', 'Invoiced<span class="count">(%s)</span>', 'blankpay-pay-woo' )
    ) );
}

add_filter( 'wc_order_statuses', 'blankpay_wc_order_statuses' );

// Register in wc_order_statuses.
function blankpay_wc_order_statuses( $order_statuses ) {
    $order_statuses['wc-invoiced'] = _x( 'Invoiced', 'Order status', 'blankpay-pay-woo' );
    return $order_statuses;
}

function blankpay_add_bulk_invoice_order_status() {
    global $post_type;

    if ( $post_type == 'shop_order' ) {
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('<option>').val('mark_invoiced').text('<?php _e( 'Mark invoiced', 'textdomain' ); ?>').appendTo("select[name='action']");
                    jQuery('<option>').val('mark_invoiced').text('<?php _e( 'Mark invoiced', 'textdomain' ); ?>').appendTo("select[name='action2']");   
                });
            </script>
        <?php
    }
}

add_action( 'admin_footer', 'blankpay_add_bulk_invoice_order_status' );
