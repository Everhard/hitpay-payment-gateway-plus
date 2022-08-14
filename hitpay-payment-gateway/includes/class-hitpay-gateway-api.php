<?php

/**
 * HitPay API for processing payments.
 */
class HitPay_Gateway_API {

    const ENDPOINT_PREFIX_LIVE     = 'https://api.hit-pay.com/v1/';
    const ENDPOINT_PREFIX_SANDBOX  = 'https://api.sandbox.hit-pay.com/v1/';

    const RESPONSE_CODE_CREATED = 201;

    /**
     * Endpoint prefix to differ live mode from sandbox mode.
     *
     * @var string
     */
    public $endpoint_prefix;

    /**
     * API Key.
     *
     * @var string
     */
    public $api_key;

    /**
     * API Salt.
     *
     * @var string
     */
    public $api_salt;

    /**
     * Headers to be sent with each request.
     *
     * @var array
     */
    public $headers;

    /**
     * Creating a gateway API instance.
     *
     * @param $api_key      string  API Key.
     * @param $api_salt     string  API Salt.
     * @param $live_mode    bool    Live mode or sandbox mode.
     */
    public function __construct( $api_key, $api_salt, $live_mode = false ) {

        $this->endpoint_prefix  = $live_mode ?
            self::ENDPOINT_PREFIX_LIVE : self::ENDPOINT_PREFIX_SANDBOX;

        $this->api_key  = $api_key;
        $this->api_salt = $api_salt;

        $this->headers = [
            'Content-Type'          => 'application/x-www-form-urlencoded',
            'X-BUSINESS-API-KEY'    => $this->api_key,
            'X-Requested-With'      => 'XMLHttpRequest',
        ];
    }

    /**
     * Getting an endpoint prefix.
     *
     * @return string
     */
    public function get_endpoint_prefix() {

        return $this->endpoint_prefix;
    }

    /**
     * Getting headers for the API requests.
     *
     * @return array
     */
    public function get_headers() {

        return $this->headers;
    }
}
