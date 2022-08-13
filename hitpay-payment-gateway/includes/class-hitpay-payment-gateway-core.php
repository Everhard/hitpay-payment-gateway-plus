<?php
/**
 * HitPay Payment Gateway Core class
 *
 * This is the main class of the HitPay Payment Gateway plugin for WooCommerce.
 * The class extends the WooCommerce's WC_Payment_Gateway class
 * and provides necessary functionality for payments and subscriptions.
 */
class HitPay_Payment_Gateway_Core extends WC_Payment_Gateway {

    public function __construct() {

        /**
         * ID of the class extending the settings API. Used in option names.
         */
        $this->id = 'hit_pay';

        /**
         * Supported features such as 'default_credit_card_form', 'refunds'.
         *
         * @var array
         */
        $this->supports = ['products'];

        /**
         * Icon for the gateway.
         */
        $this->icon = plugin_dir_url( __DIR__ ) . 'assets/images/logo.png';

        /**
         * True if the gateway shows fields on the checkout.
         */
        $this->has_fields = false;

        /**
         * Gateway title.
         */
        $this->method_title = __('HitPay Payment Gateway', 'hitpay-payment-gateway');

        /**
         * Gateway description.
         */
        $this->method_description = '';

        /**
         * Initialise settings form fields.
         *
         * Add an array of fields to be displayed on the gateway's settings screen.
         */
        $this->init_form_fields();

        /**
         * Init settings for gateways.
         */
        $this->init_settings();

        /**
         * Applying all the options from the Settings page to the instance.
         */
        $this->enabled              = $this->get_option( 'enabled' );
        $this->title                = $this->get_option( 'title' );
        $this->description          = $this->get_option( 'description' );
        $this->live_mode            = $this->get_option( 'live_mode' );
        $this->api_key              = $this->get_option( 'api_key' );
        $this->api_salt             = $this->get_option( 'api_salt' );
        $this->payment_logos        = $this->get_option( 'payment_logos' );
        $this->status_after_payment = $this->get_option( 'status_after_payment' );
        $this->payment_link_expires = $this->get_option( 'payment_link_expires' );
        $this->payment_link_ttl     = $this->get_option( 'payment_link_ttl' );
    }

    /**
     * Process Payment.
     *
     * Process the payment. This should return the success
     * and redirect in an array. e.g:
     *
     *        return array(
     *            'result'   => 'success',
     *            'redirect' => $this->get_return_url( $order )
     *        );
     *
     * @param int $order_id Order ID.
     * @return array
     */
    public function process_payment( $order_id ) {

        $order = wc_get_order( $order_id );

        WC()->cart->empty_cart();

        return [
            'result'    => 'success',
            'redirect'  => $this->get_return_url( $order ),
        ];
    }

    /**
     * Initialise settings form fields.
     *
     * Add an array of fields to be displayed on the gateway's settings screen.
     */
    public function init_form_fields() {

        $this->form_fields =[
            'enabled' => [
                'title'   => __( 'Enable / Disable', 'hitpay-payment-gateway' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable HitPay Payment Gateway', 'hitpay-payment-gateway' ),
                'default' => 'yes'
            ],
            'title' => [
                'title'         => __( 'Title', 'hitpay-payment-gateway' ),
                'type'          => 'text',
                'description'   => __( 'This controls the title which the user sees during checkout.', 'hitpay-payment-gateway' ),
                'default'       => $this->method_title,
                'desc_tip'      => true,
            ],
            'description' => [
                'title'         => __( 'Description', 'hitpay-payment-gateway' ),
                'type'          => 'textarea',
                'description'   => __( 'Instructions that the customer will see on your checkout.', 'hitpay-payment-gateway' ),
                'default'       => $this->method_description,
                'desc_tip'      => true,
            ],
            'live_mode' => [
                'title'         => __( 'Live Mode', 'hitpay-payment-gateway' ),
                'type'          => 'checkbox',
                'label'         => __( 'Enable Live Mode', 'hitpay-payment-gateway' ),
                'default'       => 'no',
                'description'   => __( 'Enable checkbox to enable payments in live mode.', 'hitpay-payment-gateway' ),
                'desc_tip'      => true,
            ],
            'api_key' => [
                'title'         => __( 'API Key', 'hitpay-payment-gateway' ),
                'type'          => 'text',
                'description'   => __( 'Copy and paste values from the HitPay Dashboard under Payment Gateway > API Keys.', 'hitpay-payment-gateway' ),
                'desc_tip'      => true,
            ],
            'api_salt' => [
                'title'         => __( 'Salt', 'hitpay-payment-gateway' ),
                'type'          => 'text',
                'description'   => __( 'Copy and paste values from the HitPay Dashboard under Payment Gateway > API Keys.', 'hitpay-payment-gateway' ),
                'desc_tip'      => true,
            ],
            'payment_logos' => [
                'title'         => __( 'Payment Logos', 'hitpay-payment-gateway' ),
                'type'          => 'multiselect',
                'class'         => 'wc-enhanced-select',
                'description'   => __( 'Activate payment methods in the HitPay dashboard under Settings > Payment Gateway > Integrations.', 'hitpay-payment-gateway' ),
                'css'           => 'height: 10rem',
                'options'       => [
                    'paynow-qr'         => 'PayNow QR',
                    'visa'              => 'Visa',
                    'mastercard'        => 'Mastercard',
                    'american-express'  => 'American Express',
                    'grabpay'           => 'GrabPay',
                    'wechatpay'         => 'WeChatPay',
                    'alipay'            => 'AliPay',
                    'shopeepay'         => 'ShopeePay',
                    'hoolahpay'         => 'HoolahPay',
                ],
                'desc_tip'      => true,
            ],
            'status_after_payment' => [
                'title'         => __( 'Status After Payment', 'hitpay-payment-gateway' ),
                'type'          => 'select',
                'class'         => 'wc-enhanced-select',
                'description'   => __( 'Set your desired order status upon successful payment.', 'hitpay-payment-gateway' ),
                'options'       => $this->get_order_statuses(),
                'default'       => 'wc-processing',
                'desc_tip'      => true,
            ],
            'payment_link_expires' => [
                'title'         => __( 'Expire the payment link?', 'hitpay-payment-gateway' ),
                'type'          => 'checkbox',
                'label'         => __( 'Yes', 'hitpay-payment-gateway' ),
                'default'       => 'no',
            ],
            'payment_link_ttl' => [
                'title'         => __( 'Expire after [x] min', 'hitpay-payment-gateway' ),
                'type'          => 'text',
                'description'   => __( 'Minimum value is 5. Maximum is 1000.', 'hitpay-payment-gateway' ),
            ],
        ];
    }

    /**
     * Load JS for the Settings page.
     *
     * We have special behaviour with the form on the Settings page:
     * we hide one field and show it when a user clicks a checkbox.
     * Here we are loading JS for this functionality.
     *
     * @return void
     */
    public function admin_options()
    {
        /**
         * Output the gateway settings screen.
         */
        parent::admin_options();

        /**
         * Loading "settings-page.js".
         */
        wp_enqueue_script(
            'hitpay-settings-page',
            plugin_dir_url( __DIR__ ) . 'admin/js/settings-page.js',
            ['jquery']
        );

        wp_localize_script('hitpay-settings-page', 'app', [
            'paymentLinkExpires'  => (bool) $this->get_option('payment_link_expires'),
        ]);
    }

    /**
     * Validate admin options.
     *
     * @param $settings
     * @return array
     */
    public function validate_options( $settings ) {

        $errors = [];

        if ( ! $settings[ 'api_key' ] || ! $settings[ 'api_salt' ] ) {
            $errors[] = __( 'Please enter HitPay API Key and Salt.', 'hitpay-payment-gateway' );
        }

        if ( 'yes' == $settings[ 'payment_link_expires' ]) {

            if ( ! $settings[ 'payment_link_ttl' ] ) {
                $errors[] = __( 'Please enter "Expire after [x] min" value.', 'hitpay-payment-gateway' );
            }

            if ( $settings[ 'payment_link_ttl' ] &&  $settings[ 'payment_link_ttl' ] < 5 ) {
                $errors[] = __( 'Value for "Expire after [x] min" should not be less 5.', 'hitpay-payment-gateway' );
            }

            if ( $settings[ 'payment_link_ttl' ] &&  $settings[ 'payment_link_ttl' ] > 1000 ) {
                $errors[] = __( 'Value for "Expire after [x] min" should not be more 1000.', 'hitpay-payment-gateway' );
            }
        }

        foreach ( $errors as $error ) {
            WC_Admin_Settings::add_error( $error );
        }

        return $settings;
    }

    /**
     * Getting the list of available order statuses.
     *
     * There's an option in the settings allowing us to choose which order status
     * will be set after a successful payment. Here we are forming the list of
     * available statuses.
     *
     * @return array
     */
    private function get_order_statuses() {

        $order_statuses = [];

        $skipped_order_statuses = [ 'wc-cancelled', 'wc-refunded', 'wc-failed', 'wc-on-hold' ];

        foreach ( wc_get_order_statuses() as $slug => $text ) {
            if ( ! in_array( $slug, $skipped_order_statuses) ) {
                $order_statuses[$slug] = $text;
            }
        }

        return $order_statuses;
    }
}
