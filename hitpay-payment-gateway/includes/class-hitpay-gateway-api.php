<?php

/**
 * HitPay API for processing payments.
 */
class HitPay_Gateway_API {

    const ENDPOINT_PREFIX_LIVE     = 'https://api.hit-pay.com/v1/';
    const ENDPOINT_PREFIX_SANDBOX  = 'https://api.sandbox.hit-pay.com/v1/';

    const TIMEZONE = 'Asia/Singapore';

    const RESPONSE_CODE_OK      = 200;
    const RESPONSE_CODE_CREATED = 201;

    const HTTP_REQUEST_TIMEOUT  = 15; // seconds

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
     * HTTP options to be sent with each request.
     *
     * @var array
     */
    public $options;

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

        $this->options = [
            'headers' => [
                'Content-Type'          => 'application/x-www-form-urlencoded',
                'X-BUSINESS-API-KEY'    => $this->api_key,
                'X-Requested-With'      => 'XMLHttpRequest',
            ],
            'timeout' => self::HTTP_REQUEST_TIMEOUT,
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
     * Getting options for the HTTP requests.
     *
     * @return array
     */
    public function get_options() {

        return $this->options;
    }
}
