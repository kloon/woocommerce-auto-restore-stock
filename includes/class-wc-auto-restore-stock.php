<?php
/**
 * WC_Auto_Restore_Stock class file, main class handling functionality.
 *
 * @package WooCommerce Auto Restore Stock
 * @author Gerhard Potgieter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Auto_Restore_Stock' ) ) {

	/**
	 * Main stock restore class
	 */
	class WC_Auto_Restore_Stock {
		/**
		 * Main instance variable
		 *
		 * @var WC_Auto_Restore_Stock|null $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'restore_order_stock' ), 10, 2 );
			add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'restore_order_stock' ), 10, 2 );
			add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'restore_order_stock' ), 10, 2 );
			add_action( 'woocommerce_order_status_processing_to_refunded', array( $this, 'restore_order_stock' ), 10, 2 );
			add_action( 'woocommerce_order_status_completed_to_refunded', array( $this, 'restore_order_stock' ), 10, 2 );
			add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'restore_order_stock' ), 10, 2 );
		} // End __construct()

		/**
		 * Return main class instance
		 *
		 * @return WC_Auto_Restore_Stock
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Restore order stock
		 * Restore stock of an order that has been refunded or cancelled.
		 *
		 * @param int      $order_id Order ID.
		 * @param WC_Order $order Order Object.
		 */
		public function restore_order_stock( $order_id, $order ) {
			if ( is_a( $order, 'WC_Order' ) ) {
				$order_id = $order->get_id();
			} else {
				$order = wc_get_order( $order_id );
			}

			if ( ! 'yes' === get_option( 'woocommerce_manage_stock' ) && ! count( $order->get_items() ) > 0 ) {
				return;
			}

			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				if ( $item->is_type( 'line_item' ) && ( $product ) && $product->managing_stock() ) {
					$qty       = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );
					$item_name = $product->get_formatted_name();
					$new_stock = wc_update_product_stock( $product, $qty, 'increase' );

					if ( ! is_wp_error( $new_stock ) ) {
						/* translators: 1: item name 2: old stock quantity 3: new stock quantity */
						$order->add_order_note( sprintf( __( '%1$s stock increased from %2$s to %3$s.', 'woocommerce' ), $item_name, $new_stock - $qty, $new_stock ) );

						// Get the latest product data.
						$product = wc_get_product( $product->get_id() );

						if ( '' !== get_option( 'woocommerce_notify_no_stock_amount' ) && $new_stock <= get_option( 'woocommerce_notify_no_stock_amount' ) ) {
							do_action( 'woocommerce_no_stock', $product );
						} elseif ( '' !== get_option( 'woocommerce_notify_low_stock_amount' ) && $new_stock <= get_option( 'woocommerce_notify_low_stock_amount' ) ) {
							do_action( 'woocommerce_low_stock', $product );
						}
					}
				}
			}
		} // End restore_order_stock()
	}
}
