<?php

/**
 * Refund Request class.
 */
class HitPay_Refund_Request {

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
     * Payment ID for the successful payment request.
     *
     * @var string
     */
    public $payment_id;

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

    public function create() {

        $endpoint = $this->gateway_api->get_endpoint_prefix() . 'refund';

        $this->last_response = wp_remote_post( $endpoint, array_merge( $this->gateway_api->get_options(), [
            'body' => [
                'amount'        => $this->amount,
                'payment_id'    => $this->payment_id,
            ],
        ] ) );

        return $this->fetch_response_data();
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
     * @param string $payment_id
     */
    public function set_payment_id( $payment_id )
    {
        $this->payment_id = $payment_id;

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
