jQuery( document ).ready( function( $ ) {
    /**
     * Update the settings page.
     */
    let updateSettingsPage = function () {

        let paymentLinkExpires = $( '#woocommerce_hit_pay_payment_link_expires' );

        let ttlTableRow = $('#woocommerce_hit_pay_payment_link_ttl').closest( 'tr' );

        paymentLinkExpires.is( ':checked' ) ? ttlTableRow.show() : ttlTableRow.hide();
    };

    /**
     * Click on the checkbox is a trigger.
     */
    $( '#woocommerce_hit_pay_payment_link_expires' ).click( updateSettingsPage );

    /**
     * Show or hide the row depending on the value:
     */
    updateSettingsPage();
} );
