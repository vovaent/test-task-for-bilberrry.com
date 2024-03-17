<?php
/**
 * Plugin Name:       TTB Gravity Forms Field Test Product
 * Description:       The plugin adds a custom Test Product Field for Gravity Form.
 * Requires at least: 6.4
 * Requires PHP:      8.2
 * Version:           0.1.0
 * Author:            Volodymyr Voitovych
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package           Ttb_GF_Test_Product_Field
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-ttb-gf-field-test-product.php';
