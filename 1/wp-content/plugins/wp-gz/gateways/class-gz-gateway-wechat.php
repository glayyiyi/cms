<?php
/**
 * WooCommerce Payment Gateway
 *
 * Custom Payment Gateway for WooCommerce.
 * @see http://docs.woothemes.com/document/payment-gateway-api/
 * @since 0.1
 * @version 1.2.2
 */
if ( !function_exists( 'wechat_init_woo_gateway' ) ) {
    /**
     * Construct Gateway
     * @since 0.1
     * @version 1.0
     */
    add_action( 'after_setup_theme', 'wechat_init_woo_gateway' );
    function wechat_init_woo_gateway()
    {
        if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
        class WC_Gateway_weCHAT extends WC_Payment_Gateway {

            var $notify_url;
            var $we_chat_url;
            var $config_id;
            var $local_url;
            /**
             * Constructor
             */
            public function __construct() {
                $this->id				  = 'wechat';
                $this->icon 	          = '';
                $this->has_fields 		  = false;
                $this->method_title       = __( 'weCHAT', 'wechat' );
                $this->method_description = __( '使用微信支付订单', 'wechat' );
                $this->notify_url        = $_SERVER['HTTP_HOST'].'/api/register/notify';

                // Load the settings.
                $this->init_form_fields();
                $this->init_settings();

                // Define user set variables
                $this->title 		 = $this->get_option( 'title' );
                $this->description   = $this->get_option( 'description' );

                $this->log_template  = $this->get_option( 'log_template' );
                $this->config_id     = $this->get_option( 'config_id' );
                $this->we_chat_url     = $this->get_option( 'we_chat_url' );
                $this->local_url     = $this->get_option( 'local_url' );
//                $this->profit_sharing_percent = $this->get_option( 'profit_sharing_percent' );
//                $this->profit_sharing_log = $this->get_option( 'profit_sharing_log' );

                // Actions
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
                add_action( 'woocommerce_thankyou_wechat',                              array( $this, 'thankyou_page' ) );

//                add_action( 'woocommerce_api_wc_gateway_wechat', array( $this, 'check_ipn_response' ) );
//                add_action( 'valid-we-chat-ipn-request', array( $this, 'successful_request' ) );
            }

            function successful_request( $posted ) {

                $posted = stripslashes_deep( $posted );

                // Custom holds post ID
                if ( ! empty( $posted['outTradeNo'] )) {

                    $order = $this->get_wechat_order( $posted['outTradeNo']);

                    if ( 'yes' == $this->debug ) {
                        $this->log->add( 'paypal', 'Found order #' . $order->id );
                    }

                    // Lowercase returned variables
                    $posted['payment_status'] 	= strtolower( $posted['payment_status'] );
                    $posted['txn_type'] 		= strtolower( $posted['txn_type'] );

                    // Sandbox fix
                    if ( 1 == $posted['test_ipn'] && 'pending' == $posted['payment_status'] ) {
                        $posted['payment_status'] = 'completed';
                    }

                    if ( 'yes' == $this->debug ) {
                        $this->log->add( 'paypal', 'Payment status: ' . $posted['payment_status'] );
                    }

                    // We are here so lets check status and do actions
                    switch ( $posted['payment_status'] ) {
                        case 'completed' :
                        case 'pending' :

                            // Check order not already completed
                            if ( $order->status == 'completed' ) {
                                if ( 'yes' == $this->debug ) {
                                    $this->log->add( 'paypal', 'Aborting, Order #' . $order->id . ' is already complete.' );
                                }
                                exit;
                            }

                            // Check valid txn_type
                            $accepted_types = array( 'cart', 'instant', 'express_checkout', 'web_accept', 'masspay', 'send_money' );

                            if ( ! in_array( $posted['txn_type'], $accepted_types ) ) {
                                if ( 'yes' == $this->debug ) {
                                    $this->log->add( 'paypal', 'Aborting, Invalid type:' . $posted['txn_type'] );
                                }
                                exit;
                            }

                            // Validate currency
                            if ( $order->get_order_currency() != $posted['mc_currency'] ) {
                                if ( 'yes' == $this->debug ) {
                                    $this->log->add( 'paypal', 'Payment error: Currencies do not match (code ' . $posted['mc_currency'] . ')' );
                                }

                                // Put this order on-hold for manual checking
                                $order->update_status( 'on-hold', sprintf( __( 'Validation error: PayPal currencies do not match (code %s).', 'woocommerce' ), $posted['mc_currency'] ) );
                                exit;
                            }

                            // Validate amount
                            if ( $order->get_total() != $posted['mc_gross'] ) {
                                if ( 'yes' == $this->debug ) {
                                    $this->log->add( 'paypal', 'Payment error: Amounts do not match (gross ' . $posted['mc_gross'] . ')' );
                                }

                                // Put this order on-hold for manual checking
                                $order->update_status( 'on-hold', sprintf( __( 'Validation error: PayPal amounts do not match (gross %s).', 'woocommerce' ), $posted['mc_gross'] ) );
                                exit;
                            }

                            // Validate Email Address
//                            if ( strcasecmp( trim( $posted['receiver_email'] ), trim( $this->receiver_email ) ) != 0 ) {
//                                if ( 'yes' == $this->debug ) {
//                                    $this->log->add( 'paypal', "IPN Response is for another one: {$posted['receiver_email']} our email is {$this->receiver_email}" );
//                                }
//
//                                // Put this order on-hold for manual checking
//                                $order->update_status( 'on-hold', sprintf( __( 'Validation error: PayPal IPN response from a different email address (%s).', 'woocommerce' ), $posted['receiver_email'] ) );
//
//                                exit;
//                            }

                            // Store PP Details
                            if ( ! empty( $posted['payer_email'] ) ) {
                                update_post_meta( $order->id, 'Payer PayPal address', wc_clean( $posted['payer_email'] ) );
                            }
                            if ( ! empty( $posted['txn_id'] ) ) {
                                update_post_meta( $order->id, 'Transaction ID', wc_clean( $posted['txn_id'] ) );
                            }
                            if ( ! empty( $posted['first_name'] ) ) {
                                update_post_meta( $order->id, 'Payer first name', wc_clean( $posted['first_name'] ) );
                            }
                            if ( ! empty( $posted['last_name'] ) ) {
                                update_post_meta( $order->id, 'Payer last name', wc_clean( $posted['last_name'] ) );
                            }
                            if ( ! empty( $posted['payment_type'] ) ) {
                                update_post_meta( $order->id, 'Payment type', wc_clean( $posted['payment_type'] ) );
                            }

                            if ( $posted['payment_status'] == 'completed' ) {
                                $order->add_order_note( __( 'IPN payment completed', 'woocommerce' ) );
                                $order->payment_complete();
                            } else {
                                $order->update_status( 'on-hold', sprintf( __( 'Payment pending: %s', 'woocommerce' ), $posted['pending_reason'] ) );
                            }

                            if ( 'yes' == $this->debug ) {
                                $this->log->add( 'paypal', 'Payment complete.' );
                            }

                            break;
                        case 'denied' :
                        case 'expired' :
                        case 'failed' :
                        case 'voided' :
                            // Order failed
                            $order->update_status( 'failed', sprintf( __( 'Payment %s via IPN.', 'woocommerce' ), strtolower( $posted['payment_status'] ) ) );
                            break;
                        case 'refunded' :

                            // Only handle full refunds, not partial
                            if ( $order->get_total() == ( $posted['mc_gross'] * -1 ) ) {

                                // Mark order as refunded
                                $order->update_status( 'refunded', sprintf( __( 'Payment %s via IPN.', 'woocommerce' ), strtolower( $posted['payment_status'] ) ) );
                            }

                            break;
                        case 'reversed' :

                            // Mark order as refunded
                            $order->update_status( 'on-hold', sprintf( __( 'Payment %s via IPN.', 'woocommerce' ), strtolower( $posted['payment_status'] ) ) );

                            break;
                        case 'canceled_reversal' :
                            break;
                        default :
                            // No action
                            break;
                    }

                    exit;
                }

            }

            private function get_wechat_order( $custom) {
                $custom = maybe_unserialize( $custom );

                $order = new WC_Order( $custom );

                return $order;
            }

            /**
             * Initialise Gateway Settings Form Fields
             * @since 0.1
             * @version 1.1
             */
            function init_form_fields() {
                // Fields
                $fields['enabled'] = array(
                    'title'   => __( 'Enable/Disable', 'gz' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable weCHAT Payment', 'gz' ),
                    'default' => 'no',
                    'description' => __( 'Users who are not logged in or excluded from using myCRED will not have access to this gateway!', 'gz' )
                );
                $fields['title'] = array(
                    'title'       => __( 'Title', 'gz' ),
                    'type'        => 'text',
                    'description' => __( 'Title to show for this payment option.', 'mycred' ),
                    'default'     => __( 'Pay with weCHAT', 'gz' ),
                    'desc_tip'    => true
                );
                $fields['log_template'] = array(
                    'title'       => __( 'Log Template', 'mycred' ),
                    'type'        => 'text',
                    'description' => __( 'Log entry template for successful payments. Available template tags: %order_id%, %order_link%', 'mycred' ),
                    'default'     => __( 'Payment for Order: #%order_id%', 'mycred' )
                );

                $fields['we_chat_url'] = array(
                    'title'       => __( '服务器微信支付链接', 'mycred' ),
                    'type'        => 'text',
                    'description' => __( '生成微信支付请求内容', 'mycred' ),
                    'default'     => __( '3333', 'mycred' )
                );

                $fields['config_id'] = array(
                    'title'       => __( '微信支付配置标识', 'mycred' ),
                    'type'        => 'text',
                    'description' => __( '微信支付的config_id', 'mycred' ),
                    'default'     => __( '123', 'mycred' )
                );

                $fields['local_url'] = array(
                    'title'       => __( '本地微信支付页面', 'mycred' ),
                    'type'        => 'text',
                    'description' => __( '本地嵌入微信支付的html页面', 'mycred' ),
                    'default'     => __( 'http://localhost/html/wp-gz-we-chat-pay-index.php', 'mycred' )
                );

                $this->form_fields = apply_filters( 'mycred_woo_fields', $fields, $this );
            }

            /**
             * Admin Panel Options
             * @since 0.1
             * @version 1.0
             */
            public function admin_options() { ?>

                <h3><?php _e( 'weCHAT Payment', 'mycred' ); ?></h3>
                <p><?php echo 'admin_options'; ?></p>
                <table class="form-table">
                    <?php
                    // Generate the HTML For the settings form.
                    $this->generate_settings_html(); ?>

                </table><!--/.form-table-->
            <?php
            }


            function get_IP(){
                if (getenv('HTTP_CLIENT_IP')) {
                    $ip = getenv('HTTP_CLIENT_IP');
                }
                elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                    $ip = getenv('HTTP_X_FORWARDED_FOR');
                }
                elseif (getenv('HTTP_X_FORWARDED')) {
                    $ip = getenv('HTTP_X_FORWARDED');
                }
                elseif (getenv('HTTP_FORWARDED_FOR')) {
                    $ip = getenv('HTTP_FORWARDED_FOR');

                }
                elseif (getenv('HTTP_FORWARDED')) {
                    $ip = getenv('HTTP_FORWARDED');
                }
                else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                return $ip;
            }

            /**
             * Process Payment
             * @since 0.1
             * @version 1.1.1
             */
            function process_payment( $order_id ) {
                $order = new WC_Order( $order_id );

                $http = new WP_Http();
                $form = array('body'=>$order->get_order_number(),
                'outTradeNo'=>$order_id, 'totalFee'=>$order->get_total(),
                'spbillCreateIp'=> $this->get_IP());
                $body = array('configId'=>$this->config_id, 'requestType'=>'WX',
                    'notifyUrl'=>$this->notify_url, 'packageForm'=>$form);
                $json = array('body'=>$body);
                $post_body = array('json'=>json_encode($json));
                $result = $http->post($this->we_chat_url, $post_body);

                $result_code = $result['response'];
                $response = json_decode($result_code['response']);
                if($response['code'] === 0){
                    $paypal_args = array('requestJson'=>$result_code);
                    $paypal_args = http_build_query( $paypal_args, '', '&' );

                    $paypal_adr = $this->local_url . '?';

                    return array(
                        'result' 	=> 'success',
                        'redirect'	=> $paypal_adr . $paypal_args
                    );
                } else {
                    wp_die( "请求微信支付失败", "微信支付", array( 'response' => 200 ) );
                }
            }

            /**
             * Thank You Page
             * @since 0.1
             * @version 1.0
             */
            function thankyou_page() {
                echo __( '您已成功付款。', 'mycred' );
            }
        }
    }
}

/**
 * Log Entry
 * @since 0.1
 * @version 1.0
 */
//add_filter( 'mycred_parse_log_entry_woocommerce_payment', 'mycred_woo_log_entry', 90, 2 );
//function mycred_woo_log_entry( $content, $log_entry )
//{
//    // Prep
//    $mycred = mycred_get_settings();
//    $order = new WC_Order( $log_entry->ref_id );
//    $cui = get_current_user_id();
//
//    // Order ID
//    $content = str_replace( '%order_id%', $order->id, $content );
//
//    // Link to order if we can edit plugin or are the user who made the order
//    if ( $cui == $order->user_id || $mycred->can_edit_plugin( $cui ) ) {
//        $url = esc_url( add_query_arg( 'order', $order->id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) );
//        $content = str_replace( '%order_link%', '<a href="' . $url . '">#' . $order->id . '</a>', $content );
//    }
//    else {
//        $content = str_replace( '%order_link%', '#' . $order->id, $content );
//    }
//
//    return $content;
//}

/**
 * Register Gateway
 * @since 0.1
 * @version 1.0
 */
add_filter( 'woocommerce_payment_gateways', 'wechat_register_woo_gateway' );
function wechat_register_woo_gateway( $methods )
{
    $methods[] = 'WC_Gateway_weCHAT';
    return $methods;
}

/**
 * Available Gateways
 * "Removes" this gateway as a payment option if:
 * - User is not logged in
 * - User is excluded
 * - Users balance is too low
 *
 * @since 0.1
 * @version 1.0
 */
add_filter( 'woocommerce_available_payment_gateways', 'wechat_woo_available_gateways' );
function wechat_woo_available_gateways( $gateways )
{
    if ( !isset( $gateways['wechat'] ) ) return $gateways;

//    // Check if we are logged in
//    if ( !is_user_logged_in() ) {
//        unset( $gateways['wechat'] );
//        return $gateways;
//    }
//
//    // Get myCRED
//    $mycred = mycred_get_settings();
//    $cui = get_current_user_id();
//
//    // Check if we are excluded from myCRED usage
//    if ( $mycred->exclude_user( $cui ) ) {
//        unset( $gateways['wechat'] );
//        unset( $mycred );
//        return $gateways;
//    }
//
//    global $woocommerce;
//
//    // Calculate cost in CREDs
//    $cost = $mycred->apply_exchange_rate( $mycred->number( $woocommerce->cart->total ), $gateways['wechat']->get_option( 'exchange_rate' ) );
//
//    // Check if we have enough points
//    if ( $mycred->get_users_cred( $cui ) < $cost ) {
//        $gateways['mycred']->enabled = 'no';
//    }

    // Clean up and return
//    unset( $mycred );
    return $gateways;
}

/**
 * Add Currency
 * Adds myCRED as one form of currency.
 * @since 0.1
 * @version 1.0
 */
add_filter( 'woocommerce_currencies', 'wechat_woo_add_currency' );
function wechat_woo_add_currency( $currencies )
{
    $mycred = mycred_get_settings();
    $currencies['MYC'] = $mycred->plural();
    unset( $mycred );
    return $currencies;
}

/**
 * Currency Symbol
 * Appends the myCRED prefix or suffix to the amount.
 * @since 0.1
 * @version 1.0
 */
add_filter( 'woocommerce_currency_symbol', 'wechat_woo_currency', 10, 2 );
function wechat_woo_currency( $currency_symbol, $currency )
{
    switch ( $currency ) {
        case 'MYC':
            $mycred = mycred_get_settings();
            if ( !empty( $mycred->before ) )
                $currency_symbol = $mycred->before;
            elseif ( !empty( $mycred->after ) )
                $currency_symbol = $mycred->after;
            break;
    }
    return $currency_symbol;
}

/**
 * Add CRED Cost
 * Appends the cost in myCRED format.
 * @since 0.1
 * @version 1.0
 */
add_action( 'woocommerce_review_order_after_order_total', 'wechat_woo_after_order_total' );
add_action( 'woocommerce_cart_totals_after_order_total', 'wechat_woo_after_order_total' );
function wechat_woo_after_order_total()
{
    if ( !is_user_logged_in() ) return;

    $mycred = mycred_get_settings();
    $cui = get_current_user_id();

    if ( $mycred->exclude_user( $cui ) ) return;

    // Only available for logged in non-excluded users
    global $woocommerce;

    $available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
    if ( !isset( $available_gateways['wechat'] ) ) return;

    // Check if enabled
    $show = $available_gateways['mycred']->get_option( 'show_total' );
    if ( empty( $show ) ) return;
    elseif ( $show === 'cart' && !is_cart() ) return;
    elseif ( $show === 'checkout' && !is_checkout() ) return;

    // Make sure myCRED is not the currency used
    $currency = get_woocommerce_currency();
    if ( $currency != 'MYC' ) {

        // Apply Exchange Rate
        $rate = $available_gateways['mycred']->get_option( 'exchange_rate' );
        $mycred_cost = $mycred->apply_exchange_rate( $woocommerce->cart->total, $rate ); ?>

        <tr class="total">
            <th><strong><?php echo $mycred->template_tags_general( $available_gateways['mycred']->get_option( 'total_label' ) ); ?></strong></th>
            <td>
                <?php

                // Balance
                $balance = $mycred->get_users_cred( $cui );
                $balance = $mycred->number( $balance );

                // Insufficient Funds
                if ( $balance < $mycred_cost ) { ?>

                    <strong class="mycred-low-funds" style="color:red;"><?php echo $mycred->format_creds( $mycred_cost ); ?></strong>
                <?php
                }
                // Funds exist
                else { ?>

                    <strong class="mycred-funds"><?php echo $mycred->format_creds( $mycred_cost ); ?></strong>
                <?php
                } ?>

                <small class="mycred-current-balance"><?php

                    // Current balance
                    echo sprintf( '( %1s: %2s )', __( 'Your current balance', 'mycred' ), $mycred->format_creds( $balance ) ); ?></small>
            </td>
        </tr>
        <?php
        unset( $available_gateways );
    }

    unset( $mycred );
}
?>