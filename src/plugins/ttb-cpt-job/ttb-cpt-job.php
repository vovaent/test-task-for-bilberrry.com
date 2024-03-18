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

const TTB_CPT_SLUG = 'job';

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
	register_post_type( TTB_CPT_SLUG, $args );
}

add_action( 'init', 'ttb_register_cpt_job' );

/**
 * Returns sorted jobs.
 *
 * @return array
 */
function ttb_get_jobs_sorted_and_alphabetized(): array {
	global $wpdb;

	$result = array();

	$jobs = get_transient( 'jobs_sorted_and_alphabetized' );

	if ( false === $jobs ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$jobs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title FROM $wpdb->posts
                      	WHERE post_type = %s AND post_status = %s
                      	ORDER BY post_title",
				TTB_CPT_SLUG,
				'publish'
			),
			'ARRAY_A'
		);

		set_transient(
			'jobs_sorted_and_alphabetized',
			$jobs,
			DAY_IN_SECONDS
		);
	}

	foreach ( $jobs as $job ) {
		$custom_url = get_post_meta( (int) $job['ID'], 'custom_url', true );

		$letter = $job['post_title'][0];

		$result[ $letter ][0][ $job['post_title'] ] = $custom_url;
	}

	return $result;
}

/**
 * Removes cached jobs while saving a job
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post Post object.
 *
 * @return void
 */
function ttb_remove_cached_jobs_on_update( int $post_id, WP_Post $post ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'job' !== $post->post_type ) {
		return;
	}

	delete_transient( 'jobs_sorted_and_alphabetized' );
}

add_action( 'save_post', 'ttb_remove_cached_jobs_on_update', 10, 2 );
