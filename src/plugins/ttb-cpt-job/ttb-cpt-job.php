<?php
/**
 * Plugin Name:       TTB CPT Job
 * Description:       The plugin adds a Job CPT and creates a function that returns a sorted array of Jobs arrays.
 * Requires at least: 6.4
 * Requires PHP:      8.2
 * Version:           0.1.0
 * Author:            Volodymyr Voitovych
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package           Ttb_Cpt_Job
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register CPT Job.
 *
 * @return void
 */
function ttb_register_cpt_job(): void {
	$args = array(
		'label'  => 'Jobs',
		'public' => true,
	);
	register_post_type( 'job', $args );
}

add_action( 'init', 'ttb_register_cpt_job' );

/**
 * Returns sorted jobs.
 *
 * @return array
 */
function ttb_get_sorted_jobs(): array {
	global $wpdb;

	$jobs        = array();
	$sorted_jobs = array();

	$query = $wpdb->prepare( "SELECT ID, post_title FROM $wpdb->posts WHERE post_type=%s AND post_status=%s ORDER BY post_title", 'job', 'publish' );

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
	$jobs = $wpdb->get_results( $query, 'ARRAY_A' );

	foreach ( $jobs as $job ) {
		if ( ! function_exists( 'get_field' ) ) {
			break;
		}

		$custom_url = get_field( 'custom_url', $job['ID'] );

		$letter = $job['post_title'][0];

		$sorted_jobs[ $letter ][0][ $job['post_title'] ] = $custom_url;
	}

	return $sorted_jobs;
}
