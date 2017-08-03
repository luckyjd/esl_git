<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * @param $order_id
 */
function lp_commission_calculate( $order_id ) {
	$order = learn_press_get_order( $order_id );

	$courses = $order->get_items();
	if ( count( $courses ) ) {
		foreach ( $courses as $index => $course ) {
			$course_id          = $course['course_id'];
			$quantity           = intval( $course['quantity'] );
			$total_one_course   = floatval( $course['total'] );
			$total              = $quantity * $total_one_course;
			$percent_commission = LPC()->get_commission_main_instructor( $course_id );
			$commission_value   = $total * $percent_commission / 100;

			$instructor = lp_commission_get_main_instructor_by_course_id( $course_id );
			if ( ! empty( $instructor ) ) {
				lp_commission_add_commission( $instructor->ID, $commission_value );
			}
		}
	}
}

//lp_commission_calculate( 305 );
add_action( 'learn_press_order_status_completed', 'lp_commission_calculate', 10, 1 );