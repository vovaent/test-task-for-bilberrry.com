<?php
/**
 * ACF functions
 *
 * @package Ttb_Mega_Menu
 */

/**
 * Add new location rules types.
 *
 * @param array $choices The location rule types.
 *
 * @return array
 */
function ttb_mm_add_acf_location_rules_types( array $choices ): array {
	$choices['Menu']['menu_level'] = 'Menu Depth';

	return $choices;
}

add_filter( 'acf/location/rule_types', 'ttb_mm_add_acf_location_rules_types' );

/**
 * Add location rule values level.
 *
 * @param array $choices The location rule values.
 *
 * @return array
 */
function ttb_mm_add_acf_location_rule_values_level( array $choices ): array {
	$choices[0] = '0';
	$choices[1] = '1';

	return $choices;
}

add_filter( 'acf/location/rule_values/menu_level', 'ttb_mm_add_acf_location_rule_values_level' );

/**
 * Add location rule match level.
 *
 * @param bool  $result The match result.
 * @param array $rule The location rule.
 * @param array $options The screen args.
 *
 * @return bool
 */
function ttb_mm_add_acf_location_rule_match_level( bool $result, array $rule, array $options ): bool {
	$current_screen = get_current_screen();
	if ( 'nav-menus' === $current_screen->base ) {
		if ( '==' === $rule['operator'] ) {
			$result = ( $options['nav_menu_item_depth'] === (int) $rule['value'] );
		}
	}

	return $result;
}

add_filter( 'acf/location/rule_match/menu_level', 'ttb_mm_add_acf_location_rule_match_level', 10, 3 );
