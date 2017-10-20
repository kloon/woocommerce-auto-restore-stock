<?php
/**
 * Plugin Name: WooCommerce Auto Restore Stock
 * Plugin URI: http://gerhardpotgieter.com/tag/woocommerce-auto-restore-stock
 * Description: Auto restore stock when orders are refunded or cancelled
 * Version: 1.1
 * Author: Gerhard Potgieter
 * Author URI: http://gerhardpotgieter.cim
 * License: GPL2+
 * Requires at least: 4.4
 * Tested up to: 4.7
 * WC requires at least: 3.0
 * WC tested up to: 3.2
 *
 * @package WooCommerce Auto Restore Stock
 * @author Gerhard Potgieter
 */

/**
 *    Copyright 2017 Gerhard Potgieter  (email : potgieterg@gmail.com)
 *
 *    This program is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License, version 2, as
 *    published by the Free Software Foundation.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program; if not, write to the Free Software
 *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once dirname( __FILE__ ) . '/includes/class-wc-auto-restore-stock.php';

/**
 * Main instance of WC_Auto_Stock_Restore
 */
function wc_auto_restore_stock() {
	return WC_Auto_Restore_Stock::instance();
}
