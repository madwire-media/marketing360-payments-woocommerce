<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo esc_html($email_heading) . "\n\n";

printf(
	// translators: %s is a link to the payment re-authentication URL.
	esc_html_x( 'Your pre-order is now available, but payment cannot be completed automatically. Please complete the payment now: %s', 'In failed SCA authentication for a pre-order.', 'marketing-360-payments-for-woocommerce' ),
	esc_url($authorization_url)
);

if ( $email->get_custom_message() ) :

	echo "----------\n\n";
	echo esc_html( wptexturize( $email->get_custom_message() ) ) . "\n\n";
	echo "----------\n\n";

endif;


echo "\n\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";

do_action( 'woocommerce_subscriptions_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
