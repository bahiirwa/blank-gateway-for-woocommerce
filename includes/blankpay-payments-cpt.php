<?php
/**
 * BlankPay Payments Custom Post Type for the client follow up.
 */
function blankpay_setup_post_type() {
    $args = array(
        'public'    => true,
        'label'     => __( 'ZENGAPAY', 'textdomain' ),
        'menu_icon' => 'dashicons-analytics',
        'supports'  => array( 'title' ),
        'capabilities' => array(
            'create_posts' => 'false', // false < WP 4.5, credit @Ewout
          ),
        'map_meta_cap' => 'false', // Set to `false`, if users are not allowed to edit/delete existing posts
    );
    register_post_type( 'blankpayments', $args );
}

add_action( 'init', 'blankpay_setup_post_type' );

// Disable Add new Posts.
function disable_new_posts() {
    // Hide sidebar link
    global $submenu;
    unset($submenu['edit.php?post_type=blankpayments'][10]);

    // Hide link on listing page
    if (isset($_GET['post_type']) && $_GET['post_type'] == 'blankpayments') {
        echo '<style type="text/css">
        #favorite-actions, .page-title-action, .add-new-h2, .tablenav { display:none; }
        </style>';
    }
}

// add_action('admin_menu', 'disable_new_posts');

// Add metabox
function blankpay_add_custom_box()
{
    $screens = ['blankpayments'];
    foreach ($screens as $screen) {
        add_meta_box(
            'blankpay_payments_box',           // Unique ID
            'Payment Information',  // Box title
            'blankpay_show_admin_boxes',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'blankpay_add_custom_box');

// Show admin side boxes
function blankpay_show_admin_boxes( $post )
{
    ?>

    <label for="externalReference">External Reference</label><br>
    <input readonly type="text" name="externalReference" id="externalReference" class="widefat" value="<?php echo get_post_meta($post->ID,'externalReference', true); ?>"><br><br>
    
    <label for="transactionStatus">Transaction Status</label><br>
    <input readonly type="text" name="transactionStatus" id="transactionStatus" class="widefat" value="<?php echo get_post_meta($post->ID,'transactionStatus', true); ?>"><br><br>

    <label for="transactionReference">Transaction Reference</label><br>
    <input readonly type="text" name="transactionReference" id="transactionReference" class="widefat" value="<?php echo get_post_meta($post->ID,'transactionReference', true); ?>"><br><br>

    <label for="MNOTransactionReferenceId">MNO Transaction Reference Id</label><br>
    <input readonly type="text" name="MNOTransactionReferenceId" id="MNOTransactionReferenceId" class="widefat" value="<?php echo get_post_meta($post->ID,'MNOTransactionReferenceId', true); ?>"><br><br>

    <label for="amount">Amount</label><br>
    <input readonly type="text" name="amount" id="amount" class="widefat" value="<?php echo get_post_meta($post->ID,'amount', true); ?>"><br><br>

    <label for="msisdn">Phone</label><br>
    <input readonly type="text" name="msisdn" id="msisdn" class="widefat" value="<?php echo get_post_meta($post->ID,'msisdn', true); ?>"><br><br>
    
    <label for="transactionInitiationDate">Transaction Initiation Date</label><br>
    <input readonly type="text" name="transactionInitiationDate" id="transactionInitiationDate" class="widefat" value="<?php echo get_post_meta($post->ID,'transactionInitiationDate', true); ?>"><br><br>
    
    <label for="transactionCompletionDate">Transaction Completion Date</label><br>
    <input readonly type="text" name="transactionCompletionDate" id="transactionCompletionDate" class="widefat" value="<?php echo get_post_meta($post->ID,'transactionCompletionDate', true); ?>"><br><br>
    
    <?php
}

function blankpay_custom_columns_list($columns) {
     
    unset( $columns['title']  );
    unset( $columns['author'] );
    unset( $columns['date']   );
     
    $columns['externalReference'] = 'External Reference';
    $columns['MNOTransactionReferenceId'] = 'MNO Reference Id';
    $columns['transactionReference']      = 'Reference';
	$columns['transactionStatus']         = 'Status';
    $columns['amount']                    = 'Amount';
    $columns['msisdn']                    = 'Phone';
    $columns['transactionInitiationDate'] = 'Initiation Date';
    $columns['transactionCompletionDate'] = 'Completion Date';
     
    return $columns;
}

add_filter( 'manage_blankpayments_posts_columns', 'blankpay_custom_columns_list' );
add_filter( 'manage_blankpayments_posts_custom_column', 'blankpay_add_custom_column_data',10 ,2 );

function blankpay_add_custom_column_data($column, $post_id) {

    switch ( $column ) {
        case 'externalReference' :
            echo get_post_meta($post_id,'externalReference', true);
            break;
        case 'MNOTransactionReferenceId' :
            echo get_post_meta($post_id,'MNOTransactionReferenceId', true);
            break;
        case 'transactionReference' :
            echo get_post_meta($post_id,'transactionReference', true);
            break;
        case 'transactionStatus' :
            echo get_post_meta($post_id,'transactionStatus', true);
            break;
        case 'amount' :
            echo get_post_meta($post_id,'amount', true);
            break;
        case 'msisdn' :
            echo get_post_meta($post_id,'msisdn', true);
            break;
        case 'transactionInitiationDate' :
            echo get_post_meta($post_id,'transactionInitiationDate', true);
            break;
        case 'transactionCompletionDate' :
            echo get_post_meta($post_id,'transactionCompletionDate', true);
            break;
    }

}
