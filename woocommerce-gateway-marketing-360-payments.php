<?php
/**
 * Plugin Name: Marketing 360® Payments for WooCommerce
 * Plugin URI: https://marketing360.com/marketing-360-payments-for-woocommerce
 * Description: Accept all major debit and credit cards securely on your site.
 * Author: Marketing 360®
 * Author URI: https://marketing360.com
 * Version: 1.0.3
 * Requires at least: 5.4
 * Tested up to: 6.1
 * Stable tag: 1.0.2
 * WC requires at least: 3.0
 * WC tested up to: 4.2
 * Text Domain: marketing-360-payments-for-woocommerce
 * Domain Path: /languages
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required minimums and constants
 */
define( 'WC_M360_PAYMENTS_VERSION', '4.5.0' );
define( 'WC_M360_PAYMENTS_MIN_PHP_VER', '5.6.0' );
define( 'WC_M360_PAYMENTS_MIN_WC_VER', '3.0' );
define( 'WC_M360_PAYMENTS_FUTURE_MIN_WC_VER', '3.0' );
define( 'WC_M360_PAYMENTS_MAIN_FILE', __FILE__ );
define( 'WC_M360_PAYMENTS_PLUGIN_URL', 	untrailingslashit(plugin_dir_url	(__FILE__)));
define( 'WC_M360_PAYMENTS_PLUGIN_PATH', untrailingslashit(plugin_dir_path	(__FILE__)));

// phpcs:disable WordPress.Files.FileName

/**
 * WooCommerce fallback notice.
 *
 * @since 4.1.2
 * @return string
 */
function woocommerce_m360_payments_missing_wc_notice() {
	/* translators: 1. URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Marketing 360® Payments requires WooCommerce to be installed and active. You can download %s here.', 'marketing-360-payments-for-woocommerce' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * WooCommerce not supported fallback notice.
 *
 * @since 4.4.0
 * @return string
 */
function woocommerce_m360_payments_wc_not_supported() {
	/* translators: $1. Minimum WooCommerce version. $2. Current WooCommerce version. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Marketing 360® Payments requires WooCommerce %1$s or greater to be installed and active. WooCommerce %2$s is no longer supported.', 'marketing-360-payments-for-woocommerce' ), WC_M360_PAYMENTS_MIN_WC_VER, WC_VERSION ) . '</strong></p></div>';
}

function woocommerce_gateway_m360_payments_stripe_installed_notice() {
	ob_start(); ?>
		<div class="notice notice-error">
			<p><?php echo __('Head’s up! You need to deactivate the Stripe plugin to take advantage of Marketing 360 Payments. Leaving both plugins active will cause issues processing transactions in your store, and no one wants that.'); ?></p>
		</div>
	<?php echo ob_get_clean();
}

add_action( 'plugins_loaded', 'woocommerce_gateway_m360_payments_init', 9999 );

function woocommerce_gateway_m360_payments_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_m360_payments_missing_wc_notice' );
		return;
	}

	if ( version_compare( WC_VERSION, WC_M360_PAYMENTS_MIN_WC_VER, '<' ) ) {
		add_action( 'admin_notices', 'woocommerce_m360_payments_wc_not_supported' );
		return;
	}

	if (class_exists('WC_Stripe')):

		add_action('admin_notices', 'woocommerce_gateway_m360_payments_stripe_installed_notice');

	else:

		class WC_Stripe {

			/**
			 * @var Singleton The reference the *Singleton* instance of this class
			 */
			private static $instance;

			/**
			 * Returns the *Singleton* instance of this class.
			 *
			 * @return Singleton The *Singleton* instance.
			 */
			public static function get_instance() {
				if ( null === self::$instance ) {
					self::$instance = new self();
				}
				return self::$instance;
			}

			/**
			 * Private clone method to prevent cloning of the instance of the
			 * *Singleton* instance.
			 *
			 * @return void
			 */
			public function __clone() {}

			/**
			 * Private unserialize method to prevent unserializing of the *Singleton*
			 * instance.
			 *
			 * @return void
			 */
			public function __wakeup() {}

			/**
			 * Protected constructor to prevent creating a new instance of the
			 * *Singleton* via the `new` operator from outside of this class.
			 */
			public function __construct() {
				add_action( 'admin_init', array( $this, 'install' ) );
				$this->init();
			}

			/**
			 * Init the plugin after plugins_loaded so environment variables are set.
			 *
			 * @since 1.0.0
			 * @version 4.0.0
			 */
			public function init() {
				if ( is_admin() ) {
					require_once dirname( __FILE__ ) . '/includes/admin/class-wc-stripe-privacy.php';
				}

				require_once dirname( __FILE__ ) . '/marketing-360-payments.php';

				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-exception.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-logger.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-helper.php';
				include_once dirname( __FILE__ ) . '/includes/class-wc-stripe-api.php';
				require_once dirname( __FILE__ ) . '/includes/abstracts/abstract-wc-stripe-payment-gateway.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-webhook-handler.php';
				require_once dirname( __FILE__ ) . '/includes/compat/class-wc-stripe-pre-orders-compat.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-gateway-stripe.php';
				require_once dirname( __FILE__ ) . '/includes/payment-methods/class-wc-stripe-payment-request.php';
				require_once dirname( __FILE__ ) . '/includes/compat/class-wc-stripe-subs-compat.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-order-handler.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-payment-tokens.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-customer.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-stripe-intent-controller.php';

				if ( is_admin() ) {
					require_once dirname( __FILE__ ) . '/includes/admin/class-wc-stripe-admin-notices.php';
				}

				add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
				add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

				// Modify emails emails.
				add_filter( 'woocommerce_email_classes', array( $this, 'add_emails' ), 20 );

				if ( version_compare( WC_VERSION, '3.4', '<' ) ) {
					add_filter( 'woocommerce_get_sections_checkout', array( $this, 'filter_gateway_order_admin' ) );
				}

				// Register the REST endpoint for testing the authorization credentials and returning a list of M360 Accounts.
				add_action('rest_api_init', function() {

					require_once('marketing-360-payments.php');
					
					register_rest_route('wc_marketing_360_payments/' . Marketing_360_Payments::VER, '/sign_in', array(
						'methods' => 'POST',
						'callback' => 'Marketing_360_Payments::rest_list_m360_accounts',
						'permission_callback' => function() {
							return current_user_can('manage_options');
						}
					));
				}, 10);
			}

			/**
			 * Updates the plugin version in db
			 *
			 * @since 3.1.0
			 * @version 4.0.0
			 */
			public function update_plugin_version() {
				delete_option( 'wc_m360_payments_version' );
				update_option( 'wc_m360_payments_version', WC_M360_PAYMENTS_VERSION );
			}

			/**
			 * Handles upgrade routines.
			 *
			 * @since 3.1.0
			 * @version 3.1.0
			 */
			public function install() {
				if ( ! is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					return;
				}

				if ( ! defined( 'IFRAME_REQUEST' ) && ( WC_M360_PAYMENTS_VERSION !== get_option( 'wc_m360_payments_version' ) ) ) {
					do_action( 'woocommerce_stripe_updated' );

					if ( ! defined( 'WC_STRIPE_INSTALLING' ) ) {
						define( 'WC_STRIPE_INSTALLING', true );
					}

					$this->update_plugin_version();
				}
			}

			/**
			 * Add plugin action links.
			 *
			 * @since 1.0.0
			 * @version 4.0.0
			 */
			public function plugin_action_links( $links ) {
				$plugin_links = array(
					'<a href="admin.php?page=wc-settings&tab=checkout&section=stripe">' . esc_html__( 'Settings', 'marketing-360-payments-for-woocommerce' ) . '</a>',
				);
				return array_merge( $plugin_links, $links );
			}

			/**
			 * Add plugin action links.
			 *
			 * @since 4.3.4
			 * @param  array  $links Original list of plugin links.
			 * @param  string $file  Name of current file.
			 * @return array  $links Update list of plugin links.
			 */
			public function plugin_row_meta( $links, $file ) {
				if ( plugin_basename( __FILE__ ) === $file ) {
					$row_meta = array(
						//'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_gateway_stripe_docs_url', 'https://docs.woocommerce.com/document/stripe/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'marketing-360-payments-for-woocommerce' ) ) . '">' . __( 'Docs', 'marketing-360-payments-for-woocommerce' ) . '</a>',
						//'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_gateway_stripe_support_url', 'https://woocommerce.com/my-account/create-a-ticket?select=18627' ) ) . '" title="' . esc_attr( __( 'Open a support request at Marketing360.com', 'marketing-360-payments-for-woocommerce' ) ) . '">' . __( 'Support', 'marketing-360-payments-for-woocommerce' ) . '</a>',
					);
					return array_merge( $links, $row_meta );
				}
				return (array) $links;
			}

			/**
			 * Add the gateways to WooCommerce.
			 *
			 * @since 1.0.0
			 * @version 4.0.0
			 */
			public function add_gateways( $methods ) {
				if ( class_exists( 'WC_Subscriptions_Order' ) && function_exists( 'wcs_create_renewal_order' ) ) {
					$methods[] = 'WC_Stripe_Subs_Compat';
				} else {
					$methods[] = 'WC_Gateway_Stripe';
				}

				return $methods;
			}

			/**
			 * Modifies the order of the gateways displayed in admin.
			 *
			 * @since 4.0.0
			 * @version 4.0.0
			 */
			public function filter_gateway_order_admin( $sections ) {
				unset( $sections['stripe'] );
				$sections['stripe']            = 'Stripe';

				return $sections;
			}

			/**
			 * Adds the failed SCA auth email to WooCommerce.
			 *
			 * @param WC_Email[] $email_classes All existing emails.
			 * @return WC_Email[]
			 */
			public function add_emails( $email_classes ) {
				require_once WC_M360_PAYMENTS_PLUGIN_PATH . '/includes/compat/class-wc-stripe-email-failed-authentication.php';
				require_once WC_M360_PAYMENTS_PLUGIN_PATH . '/includes/compat/class-wc-stripe-email-failed-renewal-authentication.php';
				require_once WC_M360_PAYMENTS_PLUGIN_PATH . '/includes/compat/class-wc-stripe-email-failed-preorder-authentication.php';
				require_once WC_M360_PAYMENTS_PLUGIN_PATH . '/includes/compat/class-wc-stripe-email-failed-authentication-retry.php';

				// Add all emails, generated by the gateway.
				$email_classes['WC_Stripe_Email_Failed_Renewal_Authentication']  = new WC_Stripe_Email_Failed_Renewal_Authentication( $email_classes );
				$email_classes['WC_Stripe_Email_Failed_Preorder_Authentication'] = new WC_Stripe_Email_Failed_Preorder_Authentication( $email_classes );
				$email_classes['WC_Stripe_Email_Failed_Authentication_Retry'] = new WC_Stripe_Email_Failed_Authentication_Retry( $email_classes );

				return $email_classes;
			}
		}

		WC_Stripe::get_instance();
	endif;
}