<?php

/**
 * Get commission value global.
 *
 * @return int|mixed|void
 */
function lp_commission_get_commission_global() {
	return LPC()->get_commission_global();
}

/**
 * Get instructors by course id.
 *
 * @param null $course_id
 *
 * @TODO Need change
 *
 * @return array
 */
function lp_commission_get_instructors_by_course_id( $course_id = null ) {
	if ( empty( $course_id ) ) {
		$course_id = get_the_ID();
	}

	if ( ! $course_id ) {
		return array();
	}

	$lp_courses = new LP_Course( $course_id );
	$instructor = get_userdata( $lp_courses->post->post_author );

	$instructors = array(
		$instructor,
	);

	return $instructors;
}

function lp_commission_get_main_instructor_by_course_id( $course_id ) {
	$lp_courses = new LP_Course( $course_id );
	$instructor = get_userdata( $lp_courses->post->post_author );

	return $instructor;
}

function lp_commission_is_active( $course_id ) {
	$is_active = get_post_meta( $course_id, LPC()->key_active, true );

	if ( ! isset( $is_active ) || $is_active == '' ) {
		return true;
	}

	return (bool) $is_active;

}

/**
 * Get Query get all courses
 *
 * @return WP_Query
 */
function lp_commission_query_all_course() {
	$post_type = LP_COURSE_CPT;

	$args = array(
		'post_type'      => array( $post_type ),
		'post_status'    => array( 'publish' ),
		'posts_per_page' => - 1,
	);

	$the_query = new WP_Query( $args );

	return $the_query;
}