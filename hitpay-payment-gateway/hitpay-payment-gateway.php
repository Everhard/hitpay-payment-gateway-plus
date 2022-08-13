<?php
/**
 * HitPay Payment Gateway
 *
 * @package                 HitPayPaymentGateway
 * @author                  HitPay Payment Solutions Pte Ltd
 * @link                    https://github.com/hit-pay/woocommerce
 * @copyright               2022 HitPay Payment Solutions Pte Ltd
 *
 * @wordpress-plugin
 * Plugin Name:             HitPay Payment Gateway
 * Plugin URI:              https://wordpress.org/plugins/hitpay-payment-gateway/
 * Description:             HitPay Payment Gateway Plugin allows your WooCommerce store to accept PayNow QR, Cards, Apple Pay, Google Pay, WeChatPay, AliPay and GrabPay Payments.
 * Version:                 3.3.0
 * Requires at least:       4.0
 * Tested up to:            5.8.2
 * WC requires at least:    2.4
 * WC tested up to:         5.8.1
 * Requires PHP:            5.5
 * Author:                  HitPay Payment Solutions Pte Ltd
 * Author URI:              https://www.hitpayapp.com
 * Text Domain:             hitpay-payment-gateway
 * GitHub Plugin URI:       https://github.com/hit-pay/woocommerce
 * GitHub Branch:           master
 */

/**
 * Exit if the file accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * This function contains all the necessary logic of the plugin.
 * The need for this function is caused by the fact that
 * we should wait until WooCommerce plugin is loaded (we are hooking
 * it on the "plugins_loaded" hook).
 */
function initiate_hitpay_payment_gateway() {

    /**
     * The WooCommerce plugin should be activated for this plugin to work.
     */
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        return;
    }

    /**
     * The main class of the HitPay Payment Gateway Plugin.
     */
    class HitPay_Payment_Gateway {

        /**
         * Instance of a payment gateway class (extended from "WC_Payment_Gateway").
         *
         * @var HitPay_Payment_Gateway_Core
         */
        public $gateway;

        /**
         * The constructor of the main class.
         * Here we place all the necessary actions and filters of the plugin, dependencies, etc.
         */
        public function __construct() {

            // Load all the necessary dependencies (libraries, classes, files).
            $this->load_dependencies();

            /**
             * Registering the gateway.
             * Adding a filter for the "woocommerce_payment_gateways" hook.
             */
            add_filter( 'woocommerce_payment_gateways', [ $this, 'filter_woocommerce_payment_gateways' ] );

            /**
             * Validating admin options.
             * Adding an action for the "woocommerce_settings_api_sanitized_fields_{ID}" hook.
             */
            add_action('woocommerce_settings_api_sanitized_fields_' . $this->gateway->id,
                [$this->gateway, 'validate_options']);

            /**
             * Saving admin options.
             * Adding an action for the "woocommerce_update_options_payment_gateways_{ID}" hook.
             */
            add_action('woocommerce_update_options_payment_gateways_' . $this->gateway->id,
                [$this->gateway, 'process_admin_options']);

        }

        /**
         * This static function starts the plugin and returns its instance.
         *
         * @return HitPay_Payment_Gateway
         */
        public static function run() {
            return new self;
        }

        /**
         * Load dependencies (libraries, classes, files).
         *
         * @return void
         */
        private function load_dependencies() {

            require_once plugin_dir_path( __FILE__ ) . 'includes/class-hitpay-payment-gateway-core.php';

            $this->gateway = new HitPay_Payment_Gateway_Core;
        }

        /**
         * This filter runs on the early stage and allows to completely
         * modify any of WooCommerce default payment gateways.
         *
         * @param $methods
         * @return mixed
         */
        public function filter_woocommerce_payment_gateways( $methods ) {

            $methods[] = 'HitPay_Payment_Gateway_Core';

            return $methods;
        }
    }

    HitPay_Payment_Gateway::run();
}

add_action( 'plugins_loaded', 'initiate_hitpay_payment_gateway' );
