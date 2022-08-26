<?php

/**
 * Class for creating recurring billing requests to HitPay.
 */
class HitPay_Recurring_Billing_Request {

    /**
     * var @HitPay_Gateway_API
     */
    public $gateway_api;

    /**
     * Subscription plan ID.
     *
     * @var string
     */
    public $plan_id;

    /**
     * Recurring billing ID.
     *
     * @var string
     */
    public $recurring_billing_id;

    /**
     * Customer email.
     *
     * @var string
     */
    public $customer_email;

    /**
     * Customer name.
     *
     * @var string
     */
    public $customer_name;

    /**
     * Billing start date (YYYY-MM-DD) in SGT.
     *
     * @var string
     */
    public $start_date;

    /**
     * Redirect URL after a payment.
     *
     * URL where HitPay redirects the user after the users enters the card details and
     * the subscription is active. Query arguments `reference` (subscription id)
     * and `status` are sent along
     *
     * @var string
     */
    public $redirect_url;

    /**
     * Reference.
     *
     * Arbitrary reference number that you can map to your internal reference number.
     *
     * @var string
     */
    public $reference;

    /**
     * Amount related to the payment.
     *
     * @var float
     */
    public $amount;

    /**
     * Currency related to the payment.
     *
     * @var string
     */
    public $currency;

    /**
     * Webhook URL.
     *
     * URL where our server do POST request after a payment is done.
     *
     * @var string
     */
    public $webhook;

    /**
     * Saving the card.
     *
     * @var bool
     */
    public $save_card;

    /**
     * Number of times to charge the card.
     *
     * @var integer
     */
    public $times_to_be_charge;

    /**
     * HitPay to send email receipts to the customer.
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
     * Create and send the request.
     *
     * @return false|mixed
     */
    public function create() {

        $endpoint = $this->gateway_api->get_endpoint_prefix() . 'recurring-billing';

        $this->last_response = wp_remote_post( $endpoint, array_merge( $this->gateway_api->get_options(), [
            'body' => [
                'plan_id'               => $this->plan_id,
                'customer_email'        => $this->customer_email,
                'customer_name'         => $this->customer_name,
                'start_date'            => $this->start_date,
                'redirect_url'          => $this->redirect_url,
                'reference'             => $this->reference,
                'amount'                => $this->amount,
                'currency'              => $this->currency,
                'webhook'               => $this->webhook,
                'save_card'             => $this->save_card             ? 'true' : '',
                'times_to_be_charge'    => $this->times_to_be_charge,
                'send_email'            => $this->send_email            ? 'true' : '',

            ],
        ] ) );

        return $this->fetch_response_data();
    }

    /**
     * Charge the saved card.
     *
     * @return false|mixed
     */
    public function charge() {

        $endpoint = $this->gateway_api->get_endpoint_prefix()
            . 'charge/recurring-billing/' . $this->recurring_billing_id;

        $this->last_response = wp_remote_post( $endpoint, array_merge( $this->gateway_api->get_options(), [
            'body' => [
                'amount'    => $this->amount,
                'currency'  => $this->currency,
            ],
        ] ) );

        return $this->fetch_response_data();
    }

    /**
     * @param string $plan_id
     */
    public function set_plan_id($plan_id)
    {
        $this->plan_id = $plan_id;

        return $this;
    }

    /**
     * @param string $recurring_billing_id
     */
    public function set_recurring_billing_id( $recurring_billing_id )
    {
        $this->recurring_billing_id = $recurring_billing_id;

        return $this;
    }

    /**
     * @param string $customer_email
     */
    public function set_customer_email($customer_email)
    {
        $this->customer_email = $customer_email;

        return $this;
    }

    /**
     * @param string $customer_name
     */
    public function set_customer_name($customer_name)
    {
        $this->customer_name = $customer_name;

        return $this;
    }

    /**
     * @param string $start_date
     */
    public function set_start_date($start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    /**
     * @param string $redirect_url
     */
    public function set_redirect_url($redirect_url)
    {
        $this->redirect_url = $redirect_url;

        return $this;
    }

    /**
     * @param string $reference
     */
    public function set_reference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @param float $amount
     */
    public function set_amount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param string $currency
     */
    public function set_currency($currency)
    {
        $this->currency = strtolower( $currency );

        return $this;
    }

    /**
     * @param string $webhook
     */
    public function set_webhook($webhook)
    {
        $this->webhook = $webhook;

        return $this;
    }

    /**
     * @param bool $save_card
     */
    public function set_save_card($save_card)
    {
        $this->save_card = $save_card;

        return $this;
    }

    /**
     * @param int $times_to_be_charge
     */
    public function set_times_to_be_charge($times_to_be_charge)
    {
        $this->times_to_be_charge = $times_to_be_charge;

        return $this;
    }

    /**
     * @param bool $send_email
     */
    public function set_send_email($send_email)
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

        $response_code = wp_remote_retrieve_response_code( $this->last_response );

        if ( ! in_array( $response_code, [
            HitPay_Gateway_API::RESPONSE_CODE_OK,
            HitPay_Gateway_API::RESPONSE_CODE_CREATED,
        ] ) ) {
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
