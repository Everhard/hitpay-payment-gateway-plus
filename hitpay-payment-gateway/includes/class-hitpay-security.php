<?php

/**
 * HitPay Security class.
 */
class HitPay_Security {

    /**
     * Get signature for some data.
     *
     * @param $salt
     * @param array $data
     * @return false|string
     */
    public static function get_signature( $salt, array $data ) {

        $source = [];

        foreach ( $data as $key => $value ) {
            $source[ $key ] = "{$key}{$value}";
        }

        ksort( $source );

        $formatted = implode( '', array_values( $source ) );

        return hash_hmac( 'sha256', $formatted, $salt );
    }
}
