<?php
/**
 * HitPay Recurring Payments
 *
 * @package                 HitPayRecurringPayments
 * @author                  HitPay Payment Solutions Pte Ltd
 * @link                    https://github.com/hit-pay/woocommerce-recurring
 * @copyright               2022 HitPay Payment Solutions Pte Ltd
 *
 * @wordpress-plugin
 * Plugin Name:             HitPay Recurring Payments
 * Plugin URI:              https://wordpress.org/plugins/hitpay-recurring-payments/
 * Description:             HitPay Recurring Payments plugin allows your WooCommerce store to accept PayNow QR, Cards, Apple Pay, Google Pay, WeChatPay, AliPay and GrabPay Payments.
 * Version:                 1.0.0
 * Requires at least:       4.0
 * Tested up to:            5.8.2
 * WC requires at least:    2.4
 * WC tested up to:         5.8.1
 * Requires PHP:            5.5
 * Author:                  HitPay Payment Solutions Pte Ltd
 * Author URI:              https://www.hitpayapp.com
 * Developer:               Andrew Dorokhov
 * Developer URI:           https://dorokhov.dev
 * Text Domain:             hitpay-payment-gateway
 * GitHub Plugin URI:       https://github.com/hit-pay/woocommerce-recurring
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

            /**
             * Load all the necessary dependencies (libraries, classes, files).
             */
            $this->load_dependencies();

            /**
             * Register the gateway in WooCommerce.
             */
            add_filter( 'woocommerce_payment_gateways', [ $this, 'filter_woocommerce_payment_gateways' ] );

            /**
             * Adds the action links displayed for the plugin in the Plugins list table.
             */
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'filter_plugin_action_links' ] );

            /**
             * Validate the admin options.
             */
            add_action( 'woocommerce_settings_api_sanitized_fields_' . $this->gateway->id,
                [ $this->gateway, 'validate_options' ] );

            /**
             * Save the admin options.
             */
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->gateway->id,
                [ $this->gateway, 'process_admin_options' ] ) ;

            /**
             * Show custom payment logos.
             */
            add_filter( 'woocommerce_gateway_icon', [ $this->gateway, 'show_custom_payment_logos' ], 10, 2 );

            /**
             * Add a handler for scheduled subscription payments.
             */
            add_action( 'woocommerce_scheduled_subscription_payment_' . $this->gateway->id,
            [ $this->gateway, 'process_scheduled_subscription_payment' ], 10, 2 );

            /**
             * Add a handler for webhook requests from HitPay API (regular payments).
             */
            add_action( 'woocommerce_api_hitpay-regular-payments',
                [ $this->gateway, 'handle_webhook_regular_payment' ] );

            /**
             * Add a handler for webhook requests from HitPay API (recurring payments).
             */
            add_action( 'woocommerce_api_hitpay-recurring-payments',
                [ $this->gateway, 'handle_webhook_recurring_payment' ] );

			/**
			 * Add a handler for the first recurring payment (after adding card information).
			 */
			add_action('woocommerce_thankyou_' . $this->gateway->id,
				[ $this->gateway, 'handle_first_recurring_payment' ] );
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

            require_once plugin_dir_path( __FILE__ ) . 'includes/class-hitpay-gateway-api.php';

            require_once plugin_dir_path( __FILE__ ) . 'includes/class-hitpay-payment-request.php';

            require_once plugin_dir_path( __FILE__ ) . 'includes/class-hitpay-refund-request.php';

            require_once plugin_dir_path( __FILE__ ) . 'includes/class-hitpay-recurring-billing-request.php';

            require_once plugin_dir_path( __FILE__ ) . 'includes/class-hitpay-security.php';

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

        /**
         * Adds the action links displayed for the plugin in the Plugins list table.
         *
         * @param $links
         * @return array
         */
        public function filter_plugin_action_links( $links ) {

            $setting_page_url = add_query_arg( [
                'page'      => 'wc-settings',
                'tab'       => 'checkout',
                'section'   => $this->gateway->id,
            ], admin_url( 'admin.php' ) );

            $action_links = [
                'settings' => "<a href='$setting_page_url' aria-label='"
                    . esc_attr__( 'View HitPay Payment Gateway settings', 'hitpay-payment-gateway' ) . "'>"
                    . esc_html__( 'Settings', 'hitpay-payment-gateway' ) . "</a>",
            ];

            return array_merge( $action_links, $links );
        }
    }

    HitPay_Payment_Gateway::run();
}

add_action( 'plugins_loaded', 'initiate_hitpay_payment_gateway' );
