<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Log all things!
 *
 * @since 4.0.0
 * @version 4.0.0
 */
class WC_Stripe_Logger {

	public static $logger;
	const WC_LOG_FILENAME = 'marketing-360-payments-for-woocommerce';

	/**
	 * Utilize WC logger class
	 *
	 * @since 4.0.0
	 * @version 4.0.0
	 */
	public static function log( $message, $start_time = null, $end_time = null ) {
		if ( ! class_exists( 'WC_Logger' ) ) {
			return;
		}

		if ( apply_filters( 'wc_stripe_logging', true, $message ) ) {
			if ( empty( self::$logger ) ) {
				self::$logger = wc_get_logger();
			}

			$settings = get_option( 'woocommerce_stripe_settings' );

			if ( empty( $settings ) || isset( $settings['logging'] ) && 'yes' !== $settings['logging'] ) {
				return;
			}

			if ( ! is_null( $start_time ) ) {

				$formatted_start_time = date_i18n( get_option( 'date_format' ) . ' g:ia', $start_time );
				$end_time             = is_null( $end_time ) ? current_time( 'timestamp' ) : $end_time;
				$formatted_end_time   = date_i18n( get_option( 'date_format' ) . ' g:ia', $end_time );
				$elapsed_time         = round( abs( $end_time - $start_time ) / 60, 2 );

				$log_entry  = "\n" . '====Stripe Version: ' . WC_M360_PAYMENTS_VERSION . '====' . "\n";
				$log_entry .= '====Start Log ' . $formatted_start_time . '====' . "\n" . $message . "\n";
				$log_entry .= '====End Log ' . $formatted_end_time . ' (' . $elapsed_time . ')====' . "\n\n";

			} else {
				$log_entry  = "\n" . '====Stripe Version: ' . WC_M360_PAYMENTS_VERSION . '====' . "\n";
				$log_entry .= '====Start Log====' . "\n" . $message . "\n" . '====End Log====' . "\n\n";

			}

			self::$logger->debug( $log_entry, array( 'source' => self::WC_LOG_FILENAME ) );
		}
	}

		/**
	 * Creates a log entry of type error.
	 *
	 * @param string $message To send to the log file.
	 * @return void
	 */
	// public static function error( $message ) {
	// 	if ( ! self::can_log() ) {
	// 		return;
	// 	}

	// 	if ( empty( self::$logger ) ) {
	// 		self::$logger = wc_get_logger();
	// 	}

	// 	self::$logger->error( $message, [ 'source' => self::WC_LOG_FILENAME ] );
	// }

	/**
	 * Creates a log entry of type debug.
	 *
	 * @param string $message To send to the log file.
	 * @return void
	 */
	// public static function debug( $message ) {
	// 	if ( ! self::can_log() ) {
	// 		return;
	// 	}

	// 	if ( empty( self::$logger ) ) {
	// 		self::$logger = wc_get_logger();
	// 	}

	// 	self::$logger->debug( $message, [ 'source' => self::WC_LOG_FILENAME ] );
	// }

	/**
	 * Whether we can log based on settings and filters.
	 *
	 * @return boolean
	 */
	// private static function can_log(): bool {
	// 	if ( ! class_exists( 'WC_Logger' ) ) {
	// 		return false;
	// 	}

	// 	$settings = WC_Stripe_Helper::get_stripe_settings();

	// 	if ( empty( $settings ) || isset( $settings['logging'] ) && 'yes' !== $settings['logging'] ) {
	// 		return false;
	// 	}

	// 	return true;
	// }
}