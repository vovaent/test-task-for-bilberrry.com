<?php
/**
 * Plugin Name:       TTB Mega Menu
 * Description:       The plugin creates a Mega Menu.
 * Requires at least: 6.4
 * Requires PHP:      8.2
 * Version:           0.1.0
 * Author:            Volodymyr Voitovych
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package           Ttb_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! class_exists( 'ACF' ) ) {
	return;
}

/**
 * Add styles in enqueue.
 *
 * @return void
 */
function ttb_mm_enqueue_styles(): void {
	// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' );

	$inline_styles = '
		.menu-item-has-children > .sub-menu.dropdown-menu {
			flex-wrap: wrap;
			align-items: center;
			min-width: 600px;
		}
		.menu-item-has-children.ttb-col-2 > .sub-menu.dropdown-menu > .menu-item {
			width: 50%;
		}
		.menu-item-has-children.ttb-col-3 > .sub-menu.dropdown-menu > .menu-item {
			width: 33.33%;
		}
		.menu-item-has-children.ttb-col-4 > .sub-menu.dropdown-menu > .menu-item {
			width: 25%;
		}
	';

	wp_add_inline_style( 'bootstrap', $inline_styles );
}

add_action( 'wp_enqueue_scripts', 'ttb_mm_enqueue_styles' );

require_once plugin_dir_path( __FILE__ ) . 'includes/menu.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/acf.php';
