<?php

/**
 * Payment Request class.
 */
class HitPay_Payment_Request {

    /**
     * var @HitPay_Gateway_API
     */
    public $gateway_api;

    /**
     * Amount related to the payment.
     *
     * @var float
     */
    public $amount;

    /**
     * Choice of payment methods we want to offer the customer.
     *
     * @var array
     */
    public $payment_methods = [];

    /**
     * Currency related to the payment.
     *
     * @var string
     */
    public $currency;

    /**
     * Buyer’s email.
     *
     * @var string
     */
    public $email;

    /**
     * Purpose of the payment request.
     *
     * @var string
     */
    public $purpose;

    /**
     * Buyer’s name.
     *
     * @var string
     */
    public $name;

    /**
     * Reference number.
     *
     * Arbitrary reference number that you can map to your internal reference number.
     * This value cannot be edited by the customer.
     *
     * @var string
     */
    public $reference_number;

    /**
     * URL where we redirect the user after a payment.
     *
     * @var string
     */
    public $redirect_url;

    /**
     * Webhook URL.
     *
     * URL where our server does a POST request after a payment.
     *
     * @var string
     */
    public $webhook;

    /**
     * Allow repeated payments.
     *
     * If set to true, multiple payments can be paid on a payment request link.
     *
     * @var bool
     */
    public $allow_repeated_payments;

    /**
     * Expiry date.
     *
     * Time after which the payment link will be expired(time in SGT).
     * Applicable for repeated payments. Default is null.
     *
     * @var string
     */
    public $expiry_date;

    /**
     * Send email.
     *
     * If set to true, an email receipt will be sent to the customer
     * after the payment is completed. Default is false.
     *
     * @var bool
     */
    public $send_email;

    /**
     * Last response from the API.
     *
     * @var array|WP_Error
     */
    private $last_response;

    /**
     * @param HitPay_Gateway_API $gateway_api
     */
    public function __construct( HitPay_Gateway_API $gateway_api ) {

        $this->gateway_api = $gateway_api;
    }

    /**
     * Create and sent a payment request.
     *
     * @return false|mixed
     */
    public function create() {

        $endpoint = $this->gateway_api->get_endpoint_prefix() . 'payment-requests';

        $this->last_response = wp_remote_post( $endpoint, array_merge( $this->gateway_api->get_options(), [
            'body' => [
                'amount'                    => $this->amount,
                'payment_methods'           => $this->payment_methods,
                'currency'                  => $this->currency,
                'email'                     => $this->email,
                'purpose'                   => $this->purpose,
                'name'                      => $this->name,
                'reference_number'          => $this->reference_number,
                'redirect_url'              => $this->redirect_url,
                'webhook'                   => $this->webhook,
                'allow_repeated_payments'   => $this->allow_repeated_payments   ? 'true' : '',
                'expiry_date'               => $this->expiry_date,
                'send_email'                => $this->send_email                ? 'true' : '',
            ],
        ] ) );

        return $this->fetch_response_data();
    }

    public function delete() {

    }

    public function get_status() {

    }

    /**
     * @param mixed $amount
     */
    public function set_amount( $amount )
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param array $payment_methods
     */
    public function set_payment_methods( $payment_methods )
    {
        $this->payment_methods = $payment_methods;

        return $this;
    }

    /**
     * @param mixed $currency
     */
    public function set_currency( $currency )
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @param string $email
     */
    public function set_email( $email )
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param mixed $purpose
     */
    public function set_purpose( $purpose )
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * @param mixed $name
     */
    public function set_name( $name )
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param mixed $reference_number
     */
    public function set_reference_number( $reference_number )
    {
        $this->reference_number = $reference_number;

        return $this;
    }

    /**
     * @param mixed $redirect_url
     */
    public function set_redirect_url( $redirect_url )
    {
        $this->redirect_url = $redirect_url;

        return $this;
    }

    /**
     * @param mixed $webhook
     */
    public function set_webhook( $webhook )
    {
        $this->webhook = $webhook;

        return $this;
    }

    /**
     * @param mixed $allow_repeated_payments
     */
    public function set_allow_repeated_payments( $allow_repeated_payments )
    {
        $this->allow_repeated_payments = $allow_repeated_payments;

        return $this;
    }

    /**
     * @param mixed $expiry_date
     */
    public function set_expiry_date( $expiry_date )
    {
        $this->expiry_date = $expiry_date;

        return $this;
    }

    /**
     * @param mixed $send_email
     */
    public function set_send_email( $send_email )
    {
        $this->send_email = $send_email;

        return $this;
    }

    /**
     * Fetch the response data.
     *
     * @return false|mixed
     */
    private function fetch_response_data() {

        if ( HitPay_Gateway_API::RESPONSE_CODE_CREATED
            != wp_remote_retrieve_response_code( $this->last_response ) ) {
            return false;
        }

        if ( $json_body = wp_remote_retrieve_body( $this->last_response ) ) {
            if ( $body = json_decode( $json_body ) ) {
                return $body;
            }
        }

        return false;
    }
}
