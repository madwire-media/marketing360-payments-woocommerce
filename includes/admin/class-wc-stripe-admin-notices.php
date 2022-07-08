<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that represents admin notices.
 *
 * @since 4.1.0
 */
class WC_Stripe_Admin_Notices {
	/**
	 * Notices (array)
	 * @var array
	 */
	public $notices = array();

	/**
	 * Constructor
	 *
	 * @since 4.1.0
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		add_action( 'woocommerce_stripe_updated', array( $this, 'stripe_updated' ) );
	}

	/**
	 * Allow this class and other classes to add slug keyed notices (to avoid duplication).
	 *
	 * @since 1.0.0
	 * @version 4.0.0
	 */
	public function add_admin_notice( $slug, $class, $message, $dismissible = false ) {
		$this->notices[ $slug ] = array(
			'class'       => $class,
			'message'     => $message,
			'dismissible' => $dismissible,
		);
	}

	/**
	 * Display any notices we've collected thus far.
	 *
	 * @since 1.0.0
	 * @version 4.0.0
	 */
	public function admin_notices() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Main Stripe payment method.
		$this->stripe_check_environment();

		// All other payment methods.
		$this->payment_methods_check_environment();

		foreach ( (array) $this->notices as $notice_key => $notice ) {
			echo '<div class="' . esc_attr( $notice['class'] ) . '" style="position:relative;">';

			if ( $notice['dismissible'] ) {
				?>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-stripe-hide-notice', $notice_key ), 'wc_stripe_hide_notices_nonce', '_wc_stripe_notice_nonce' ) ); ?>" class="woocommerce-message-close notice-dismiss" style="position:relative;float:right;padding:9px 0px 9px 9px 9px;text-decoration:none;"></a>
				<?php
			}

			echo '<p>';
			echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array(), 'target' => array() ) ) );
			echo '</p></div>';
		}
	}

	/**
	 * List of available payment methods.
	 *
	 * @since 4.1.0
	 * @return array
	 */
	public function get_payment_methods() {
		return array();
	}

	/**
	 * The backup sanity check, in case the plugin is activated in a weird way,
	 * or the environment changes after activation. Also handles upgrade routines.
	 *
	 * @since 1.0.0
	 * @version 4.0.0
	 */
	public function stripe_check_environment() {
		$show_ssl_notice     = get_option( 'wc_stripe_show_ssl_notice' );
		$show_keys_notice    = get_option( 'wc_stripe_show_keys_notice' );
		$show_3ds_notice     = get_option( 'wc_stripe_show_3ds_notice' );
		$show_phpver_notice  = get_option( 'wc_stripe_show_phpver_notice' );
		$show_wcver_notice   = get_option( 'wc_stripe_show_wcver_notice' );
		$show_curl_notice    = get_option( 'wc_stripe_show_curl_notice' );
		$show_sca_notice     = get_option( 'wc_stripe_show_sca_notice' );
		$changed_keys_notice = get_option( 'wc_stripe_show_changed_keys_notice' );
		$options             = get_option( 'woocommerce_stripe_settings' );
		$testmode            = ( isset( $options['testmode'] ) && 'yes' === $options['testmode'] ) ? true : false;
		$test_secret_key     = isset( $options['test_secret_key'] ) ? $options['test_secret_key'] : '';
		$live_secret_key     = isset( $options['secret_key'] ) ? $options['secret_key'] : '';
		$three_d_secure      = isset( $options['three_d_secure'] ) && 'yes' === $options['three_d_secure'];

		$live_pub_key        = isset( $options['publishable_key'] ) ? $options['publishable_key'] : '';
		$m360_account		 = isset( $options['m360_account'] ) ? $options['m360_account'] : '';
		$client_id		 	 = isset( $options['client_id'] ) ? $options['client_id'] : '';
		$client_secret		 = isset( $options['client_secret'] ) ? $options['client_secret'] : '';
		$merchant_id		 = isset( $options['merchant_id'] ) ? $options['merchant_id'] : '';
		$token	 			 = isset( $options['token'] ) ? $options['token'] : '';

		if ( isset( $options['enabled'] ) && 'yes' === $options['enabled'] ) {

			if ( empty( $show_phpver_notice ) ) {
				if ( version_compare( phpversion(), WC_M360_PAYMENTS_MIN_PHP_VER, '<' ) ) {
					/* translators: 1) int version 2) int version */
					$message = __( 'WooCommerce Marketing 360® Payments - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'marketing-360-payments-for-woocommerce' );

					$this->add_admin_notice( 'phpver', 'error', sprintf( $message, WC_M360_PAYMENTS_MIN_PHP_VER, phpversion() ), true );

					return;
				}
			}

			if ( empty( $show_wcver_notice ) ) {
				if ( WC_Stripe_Helper::is_wc_lt( WC_M360_PAYMENTS_FUTURE_MIN_WC_VER ) ) {
					/* translators: 1) int version 2) int version */
					$message = __( 'WooCommerce Marketing 360® Payments - This is the last version of the plugin compatible with WooCommerce %1$s. All furture versions of the plugin will require WooCommerce %2$s or greater.', 'marketing-360-payments-for-woocommerce' );
					$this->add_admin_notice( 'wcver', 'notice notice-warning', sprintf( $message, WC_VERSION, WC_M360_PAYMENTS_FUTURE_MIN_WC_VER ), true );
				}
			}

			if ( empty( $show_curl_notice ) ) {
				if ( ! function_exists( 'curl_init' ) ) {
					$this->add_admin_notice( 'curl', 'notice notice-warning', __( 'WooCommerce Marketing 360® Payments - cURL is not installed.', 'marketing-360-payments-for-woocommerce' ), true );
				}
			}

			if ( empty( $show_ssl_notice ) ) {
				// Show message if enabled and FORCE SSL is disabled and WordpressHTTPS plugin is not detected.
				if ( ! wc_checkout_is_https() ) {
					/* translators: 1) link */
					$this->add_admin_notice( 'ssl', 'notice notice-warning', sprintf( __( 'Marketing 360® Payments is enabled, but a SSL certificate is not detected. Your checkout may not be secure! Please ensure your server has a valid <a href="%1$s" target="_blank">SSL certificate</a>', 'marketing-360-payments-for-woocommerce' ), 'https://en.wikipedia.org/wiki/Transport_Layer_Security' ), true );
				}
			}

			if ( empty( $show_sca_notice ) ) {
				$this->add_admin_notice( 'sca', 'notice notice-success', sprintf( __( 'Marketing 360® Payments is now ready for Strong Customer Authentication (SCA) and 3D Secure 2! <a href="%1$s" target="_blank">Read about SCA</a>', 'marketing-360-payments-for-woocommerce' ), 'https://woocommerce.com/posts/introducing-strong-customer-authentication-sca/' ), true );
			}
		}
	}

	/**
	 * Environment check for all other payment methods.
	 *
	 * @since 4.1.0
	 */
	public function payment_methods_check_environment() {
		$payment_methods = $this->get_payment_methods();

		foreach ( $payment_methods as $method => $class ) {
			$show_notice = get_option( 'wc_m360_payments_show_' . strtolower( $method ) . '_notice' );
			$gateway     = new $class();

			if ( 'yes' !== $gateway->enabled || 'no' === $show_notice ) {
				continue;
			}

			if ( ! in_array( get_woocommerce_currency(), $gateway->get_supported_currency() ) ) {
				/* translators: %1$s Payment method, %2$s List of supported currencies */
				$this->add_admin_notice( $method, 'notice notice-error', sprintf( __( '%1$s is enabled - it requires store currency to be set to %2$s', 'marketing-360-payments-for-woocommerce' ), $method, implode( ', ', $gateway->get_supported_currency() ) ), true );
			}
		}
	}

	/**
	 * Hides any admin notices.
	 *
	 * @since 4.0.0
	 * @version 4.0.0
	 */
	public function hide_notices() {
		if ( isset( $_GET['wc-stripe-hide-notice'] ) && isset( $_GET['_wc_stripe_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_wc_stripe_notice_nonce'], 'wc_stripe_hide_notices_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'marketing-360-payments-for-woocommerce' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'marketing-360-payments-for-woocommerce' ) );
			}

			$notice = wc_clean( $_GET['wc-stripe-hide-notice'] );

			switch ( $notice ) {
				case 'style':
					update_option( 'wc_stripe_show_style_notice', 'no' );
					break;
				case 'phpver':
					update_option( 'wc_stripe_show_phpver_notice', 'no' );
					break;
				case 'wcver':
					update_option( 'wc_stripe_show_wcver_notice', 'no' );
					break;
				case 'curl':
					update_option( 'wc_stripe_show_curl_notice', 'no' );
					break;
				case 'ssl':
					update_option( 'wc_stripe_show_ssl_notice', 'no' );
					break;
				case 'keys':
					update_option( 'wc_stripe_show_keys_notice', 'no' );
					break;
				case '3ds':
					update_option( 'wc_stripe_show_3ds_notice', 'no' );
					break;
				case 'sca':
					update_option( 'wc_stripe_show_sca_notice', 'no' );
					break;
				case 'changed_keys':
					update_option( 'wc_stripe_show_changed_keys_notice', 'no' );
			}
		}
	}

	/**
	 * Get setting link.
	 *
	 * @since 1.0.0
	 *
	 * @return string Setting link
	 */
	public function get_setting_link() {
		return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=stripe' );
	}

	/**
	 * Saves options in order to hide notices based on the gateway's version.
	 *
	 * @since 4.3.0
	 */
	public function stripe_updated() {
		$previous_version = get_option( 'wc_m360_payments_version' );

		// Only show the style notice if the plugin was installed and older than 4.1.4.
		if ( empty( $previous_version ) || version_compare( $previous_version, '4.1.4', 'ge' ) ) {
			update_option( 'wc_m360_payments_show_style_notice', 'no' );
		}

		// Only show the SCA notice on pre-4.3.0 installs.
		if ( empty( $previous_version ) || version_compare( $previous_version, '4.3.0', 'ge' ) ) {
			update_option( 'wc_m360_payments_show_sca_notice', 'no' );
		}
	}
}

new WC_Stripe_Admin_Notices();
