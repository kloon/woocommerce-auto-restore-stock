<?php
/**
 * WC_Auto_Stock_Restore class file, main class handling functionality.
 *
 * @package WooCommerce Auto Restore Stock
 * @author Gerhard Potgieter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Auto_Stock_Restore' ) ) {

	/**
	 * Main stock restore class
	 */
	class WC_Auto_Stock_Restore {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'restore_order_stock' ), 10, 1 );
			add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'restore_order_stock' ), 10, 1 );
			add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'restore_order_stock' ), 10, 1 );
			add_action( 'woocommerce_order_status_processing_to_refunded', array( $this, 'restore_order_stock' ), 10, 1 );
			add_action( 'woocommerce_order_status_completed_to_refunded', array( $this, 'restore_order_stock' ), 10, 1 );
			add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'restore_order_stock' ), 10, 1 );
		} // End __construct()

		/**
		 * Restore order stock
		 * Restore stock of an order that has been refunded or cancelled.
		 *
		 * @param int $order_id Order ID.
		 */
		public function restore_order_stock( $order_id ) {
			$order = new WC_Order( $order_id );

			if ( ! 'yes' === get_option( 'woocommerce_manage_stock' ) && ! count( $order->get_items() ) > 0 ) {
				return;
			}

			foreach ( $order->get_items() as $item ) {

				if ( $item['product_id'] > 0 ) {
					$_product = $order->get_product_from_item( $item );

					if ( $_product && $_product->exists() && $_product->managing_stock() ) {

						$old_stock = $_product->stock;

						$qty = apply_filters( 'woocommerce_order_item_quantity', $item['qty'], $this, $item );

						$new_quantity = $_product->increase_stock( $qty );

						do_action( 'woocommerce_auto_stock_restored', $_product, $item );

						$order->add_order_note( sprintf( __( 'Item #%1$s stock incremented from %2$s to %3$s.', 'woocommerce' ), $item['product_id'], $old_stock, $new_quantity ) );

						$order->send_stock_notifications( $_product, $new_quantity, $item['qty'] );
					}
				}
			}
		} // End restore_order_stock()
	}
	$GLOBALS['wc_auto_stock_restore'] = new WC_Auto_Stock_Restore();
}
