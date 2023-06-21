<?php

if (! defined('ABSPATH')) {
    exit;
}

return apply_filters(
    'wc_m360_payments_settings',
    array(
        'account_details'          => array(
            'title'       => __('Connect to Marketing 360®', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'api_connection',
            'default'     => '',
        ),
        'enabled'                       => array(
            'title'       => __('Enable/Disable', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Enable Marketing 360® Payments', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no',
        ),
        'title'                         => array(
            'title'       => __('Title', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'text',
            'description' => __('This controls the title which the user sees during checkout.', 'marketing-360-payments-for-woocommerce'),
            'default'     => __('Credit Card (Marketing 360® Payments)', 'marketing-360-payments-for-woocommerce'),
            'desc_tip'    => true,
        ),
        'description'                   => array(
            'title'       => __('Description', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'text',
            'description' => __('This controls the description which the user sees during checkout.', 'marketing-360-payments-for-woocommerce'),
            'default'     => __('Pay with your credit card via Marketing 360® Payments.', 'marketing-360-payments-for-woocommerce'),
            'desc_tip'    => true,
        ),
        'inline_cc_form'                => array(
            'title'       => __('Inline Credit Card Form', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'checkbox',
            'description' => __('Choose the style you want to show for your credit card form. When unchecked, the credit card form will display separate credit card number field, expiry date field and cvc field.', 'marketing-360-payments-for-woocommerce'),
            'default'     => 'no',
            'desc_tip'    => true,
        ),
        'capture'                       => array(
            'title'       => __('Capture', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Capture charge immediately', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'checkbox',
            'description' => __('Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later. Uncaptured charges expire in 7 days.', 'marketing-360-payments-for-woocommerce'),
            'default'     => 'yes',
            'desc_tip'    => true,
        ),
        'payment_request'                     => [
			'title'       => __( 'Payment Request Buttons', 'marketing-360-payments-for-woocommerce' ),
			'label'       => sprintf(
				/* translators: 1) br tag 2) Stripe anchor tag 3) Apple anchor tag 4) Stripe dashboard opening anchor tag 5) Stripe dashboard closing anchor tag */
				__( 'Enable Apple Pay/Google Pay Buttons', 'woocommerce-gateway-stripe' ),
			),
			'type'        => 'checkbox',
			'description' => __( 'If enabled, users will be able to pay using Apple Pay or Chrome Payment Request if supported by the browser.', 'marketing-360-payments-for-woocommerce' ),
			'default'     => 'yes',
			'desc_tip'    => true,
		],

        'payment_request_button_type'   => array(
            'title'       => __('Payment Request Button Type', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Button Type', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'select',
            'description' => __('Select the button type you would like to show.', 'marketing-360-payments-for-woocommerce'),
            'default'     => 'buy',
            'desc_tip'    => true,
            'options'     => array(
                'default' => __('Default', 'marketing-360-payments-for-woocommerce'),
                'buy'     => __('Buy', 'marketing-360-payments-for-woocommerce'),
                'donate'  => __('Donate', 'marketing-360-payments-for-woocommerce'),
                'branded' => __('Branded', 'marketing-360-payments-for-woocommerce'),
                'custom'  => __('Custom', 'marketing-360-payments-for-woocommerce'),
            ),
        ),
        'payment_request_button_theme'  => array(
            'title'       => __('Payment Request Button Theme', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Button Theme', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'select',
            'description' => __('Select the button theme you would like to show.', 'marketing-360-payments-for-woocommerce'),
            'default'     => 'dark',
            'desc_tip'    => true,
            'options'     => array(
                'dark'          => __('Dark', 'marketing-360-payments-for-woocommerce'),
                'light'         => __('Light', 'marketing-360-payments-for-woocommerce'),
                'light-outline' => __('Light-Outline', 'marketing-360-payments-for-woocommerce'),
            ),
        ),
        'payment_request_button_height' => array(
            'title'       => __('Payment Request Button Height', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Button Height', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'text',
            'description' => __('Enter the height you would like the button to be in pixels. Width will always be 100%.', 'marketing-360-payments-for-woocommerce'),
            'default'     => '44',
            'desc_tip'    => true,
        ),
        'payment_request_button_label' => array(
            'title'       => __('Payment Request Button Label', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Button Label', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'text',
            'description' => __('Enter the custom text you would like the button to have.', 'marketing-360-payments-for-woocommerce'),
            'default'     => __('Buy now', 'marketing-360-payments-for-woocommerce'),
            'desc_tip'    => true,
        ),
        'payment_request_button_branded_type' => array(
            'title'       => __('Payment Request Branded Button Label Format', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Branded Button Label Format', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'select',
            'description' => __('Select the branded button label format.', 'marketing-360-payments-for-woocommerce'),
            'default'     => 'long',
            'desc_tip'    => true,
            'options'     => array(
                'short' => __('Logo only', 'marketing-360-payments-for-woocommerce'),
                'long'  => __('Text and logo', 'marketing-360-payments-for-woocommerce'),
            ),
        ),
        'saved_cards'                   => array(
            'title'       => __('Saved Cards', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Enable Payment via Saved Cards', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'checkbox',
            'description' => __('If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Marketing 360® Payments servers, not on your store.', 'marketing-360-payments-for-woocommerce'),
            'default'     => 'yes',
            'desc_tip'    => true,
        ),
        'logging'                       => array(
            'title'       => __('Logging', 'marketing-360-payments-for-woocommerce'),
            'label'       => __('Log debug messages', 'marketing-360-payments-for-woocommerce'),
            'type'        => 'checkbox',
            'description' => __('Save debug messages to the WooCommerce System Status log.', 'marketing-360-payments-for-woocommerce'),
            'default'     => 'no',
            'desc_tip'    => true,
        ),
    )
);
