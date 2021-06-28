<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters(
	'wc_m360_payments_settings',
	array(
		'account_details'          => array(
		    'title'       => __( 'Connect to Marketing 360®', 'woocommerce-gateway-marketing-360-payments' ),
		    'type'        => 'api_connection',
		    'default'     => '',
		),
		'enabled'                       => array(
			'title'       => __( 'Enable/Disable', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Enable Marketing 360® Payments', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'no',
		),
		'title'                         => array(
			'title'       => __( 'Title', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => __( 'Credit Card (Marketing 360® Payments)', 'woocommerce-gateway-marketing-360-payments' ),
			'desc_tip'    => true,
		),
		'description'                   => array(
			'title'       => __( 'Description', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'text',
			'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => __( 'Pay with your credit card via Marketing 360® Payments.', 'woocommerce-gateway-marketing-360-payments' ),
			'desc_tip'    => true,
		),
		'inline_cc_form'                => array(
			'title'       => __( 'Inline Credit Card Form', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'checkbox',
			'description' => __( 'Choose the style you want to show for your credit card form. When unchecked, the credit card form will display separate credit card number field, expiry date field and cvc field.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => 'no',
			'desc_tip'    => true,
		),
		'capture'                       => array(
			'title'       => __( 'Capture', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Capture charge immediately', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'checkbox',
			'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later. Uncaptured charges expire in 7 days.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => 'yes',
			'desc_tip'    => true,
		),
		'payment_request_button_type'   => array(
			'title'       => __( 'Payment Request Button Type', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Button Type', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'select',
			'description' => __( 'Select the button type you would like to show.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => 'buy',
			'desc_tip'    => true,
			'options'     => array(
				'default' => __( 'Default', 'woocommerce-gateway-marketing-360-payments' ),
				'buy'     => __( 'Buy', 'woocommerce-gateway-marketing-360-payments' ),
				'donate'  => __( 'Donate', 'woocommerce-gateway-marketing-360-payments' ),
				'branded' => __( 'Branded', 'woocommerce-gateway-marketing-360-payments' ),
				'custom'  => __( 'Custom', 'woocommerce-gateway-marketing-360-payments' ),
			),
		),
		'payment_request_button_theme'  => array(
			'title'       => __( 'Payment Request Button Theme', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Button Theme', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'select',
			'description' => __( 'Select the button theme you would like to show.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => 'dark',
			'desc_tip'    => true,
			'options'     => array(
				'dark'          => __( 'Dark', 'woocommerce-gateway-marketing-360-payments' ),
				'light'         => __( 'Light', 'woocommerce-gateway-marketing-360-payments' ),
				'light-outline' => __( 'Light-Outline', 'woocommerce-gateway-marketing-360-payments' ),
			),
		),
		'payment_request_button_height' => array(
			'title'       => __( 'Payment Request Button Height', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Button Height', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'text',
			'description' => __( 'Enter the height you would like the button to be in pixels. Width will always be 100%.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => '44',
			'desc_tip'    => true,
		),
		'payment_request_button_label' => array(
			'title'       => __( 'Payment Request Button Label', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Button Label', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'text',
			'description' => __( 'Enter the custom text you would like the button to have.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => __( 'Buy now', 'woocommerce-gateway-marketing-360-payments' ),
			'desc_tip'    => true,
		),
		'payment_request_button_branded_type' => array(
			'title'       => __( 'Payment Request Branded Button Label Format', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Branded Button Label Format', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'select',
			'description' => __( 'Select the branded button label format.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => 'long',
			'desc_tip'    => true,
			'options'     => array(
				'short' => __( 'Logo only', 'woocommerce-gateway-marketing-360-payments' ),
				'long'  => __( 'Text and logo', 'woocommerce-gateway-marketing-360-payments' ),
			),
		),
		'saved_cards'                   => array(
			'title'       => __( 'Saved Cards', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Enable Payment via Saved Cards', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'checkbox',
			'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Marketing 360® Payments servers, not on your store.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => 'yes',
			'desc_tip'    => true,
		),
		'logging'                       => array(
			'title'       => __( 'Logging', 'woocommerce-gateway-marketing-360-payments' ),
			'label'       => __( 'Log debug messages', 'woocommerce-gateway-marketing-360-payments' ),
			'type'        => 'checkbox',
			'description' => __( 'Save debug messages to the WooCommerce System Status log.', 'woocommerce-gateway-marketing-360-payments' ),
			'default'     => 'no',
			'desc_tip'    => true,
		),
	)
);
