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
    public function process_payment( $order_id )
    {
        $order = wc_get_order( $order_id );

        WC()->cart->empty_cart();

        return [
            'result'    => 'success',
            'redirect'  => $this->get_return_url( $order ),
        ];
    }

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
        ];
    }
}
