<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! class_exists( 'Marketing_360_Payments' ) ) {
	require_once dirname( __FILE__ ) . '/marketing-360-payments.php';
}


/**
 * Remove webhooks from Marketing 360 Payments
 */
$token = Marketing_360_Payments::get_authorization();
$m360_options = get_option('woocommerce_stripe_settings');
if( !empty( $token ) && isset( $m360_options['m360_webhook_id'] ) ) {
	Marketing_360_Payments::remove_webhooks( $token, $m360_options['m360_webhook_id'] );
}

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
	// Delete options.
	delete_option( 'woocommerce_stripe_settings' );
	delete_option( 'wc_stripe_show_styles_notice' );
	delete_option( 'wc_stripe_show_request_api_notice' );
	delete_option( 'wc_stripe_show_apple_pay_notice' );
	delete_option( 'wc_stripe_show_ssl_notice' );
	delete_option( 'wc_stripe_show_keys_notice' );
	delete_option( 'wc_stripe_version' );
}