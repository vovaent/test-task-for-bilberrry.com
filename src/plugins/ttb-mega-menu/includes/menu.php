<?php
/**
 * Menu functions.
 *
 * @package Ttb_Mega_Menu
 */

/**
 * Add a dropdown arrow in the navigation menu, for menu items that
 * have a dropdown sub-menu. Returns all other menu item titles unchanged.
 *
 * @param array    $atts Attributes.
 * @param WP_Post  $item Menu item data.
 * @param stdClass $args Menu arguments data.
 * @param int      $depth Menu item depth.
 *
 * @return array
 */
function ttb_mm_nav_menu_add_dropdown_arrow( array $atts, WP_Post $item, stdClass $args, int $depth ): array {
	if ( 'menu-1' !== $args->theme_location ) {
		return $atts;
	}

	if ( 0 !== $depth ) {
		return $atts;
	}

	$item_classes = $item->classes;

	if ( empty( $item_classes ) || ! in_array( 'menu-item-has-children', $item_classes, true ) ) {
		return $atts;
	}

	$atts['class'] = 'dropdown-toggle';

	return $atts;
}

add_filter( 'nav_menu_link_attributes', 'ttb_mm_nav_menu_add_dropdown_arrow', 10, 4 );

/**
 * Add submenu custom dropdown class.
 *
 * @param array    $classes Classes array.
 * @param stdClass $args Submenu arguments data.
 * @param int      $depth Submenu depth.
 *
 * @return mixed
 */
function ttb_mm_add_submenu_custom_class( array $classes, stdClass $args, int $depth ): mixed {
	if ( 0 !== $depth ) {
		return $classes;
	}

	$classes[] = 'dropdown-menu';

	return $classes;
}

add_filter( 'nav_menu_submenu_css_class', 'ttb_mm_add_submenu_custom_class', 10, 3 );

/**
 * Add image to child menu item title.
 *
 * @param string   $title The menu item's title.
 * @param WP_Post  $menu_item The current menu item object.
 * @param stdClass $args An object of wp_nav_menu() arguments.
 * @param int      $depth Depth of menu item. Used for padding.
 *
 * @return string
 */
function ttb_mm_add_image_to_nav_menu_title( string $title, WP_Post $menu_item, stdClass $args, int $depth ): string {
	if ( 1 !== $depth ) {
		return $title;
	}

	$output_title = $title;

	$image_id = get_field( 'image', $menu_item->ID );

	if ( $image_id ) {
		$output_title  = '<span class="link-image">';
		$output_title .= wp_get_attachment_image( $image_id );
		$output_title .= '</span>';
		$output_title .= $title;
	}

	return $output_title;
}

add_filter( 'nav_menu_item_title', 'ttb_mm_add_image_to_nav_menu_title', 10, 4 );

/**
 * Add class columns number menu items.
 *
 * @param array    $menu_objects The menu items, sorted by each menu item's menu order.
 * @param stdClass $args An object containing wp_nav_menu() arguments.
 *
 * @return mixed
 */
function ttb_mm_add_class_columns_number_menu_items( array $menu_objects, stdClass $args ): mixed {
	if ( 'menu-1' !== $args->theme_location ) {
		return $menu_objects;
	}

	if ( 0 !== $args->depth ) {
		return $menu_objects;
	}

	foreach ( $menu_objects as $menu_object ) {
		$item_classes = $menu_object->classes;

		if ( empty( $item_classes ) || ! in_array( 'menu-item-has-children', $item_classes, true ) ) {
			return $menu_objects;
		}

		$cols_number = get_field( 'columns_number', $menu_object->ID );

		$item_classes[]       = "ttb-col-{$cols_number}";
		$menu_object->classes = $item_classes;
	}

	return $menu_objects;
}

add_filter( 'wp_nav_menu_objects', 'ttb_mm_add_class_columns_number_menu_items', 10, 2 );
