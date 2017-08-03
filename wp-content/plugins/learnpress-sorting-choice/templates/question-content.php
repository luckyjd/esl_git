<?php
/**
 * Template for displaying the content of multi-choice question
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$quiz = LP()->global['course-item'];
$user = learn_press_get_current_user();

$completed    = $user->get_quiz_status( $quiz->id ) == 'completed';
$show_result  = $quiz->show_result == 'yes';
$checked      = $user->has_checked_answer( $this->id, $quiz->id );
$check_answer = false;

$args = array(
	'quiz' => $quiz
);
if ( $checked || ( $show_result && $completed ) ) {
	$args['classes'] = 'checked';
	$check_answer    = true;
}

$wrap_id = 'learn_press_question_wrap_' . $this->id;
?>
<div id="<?php echo $wrap_id; ?>" <?php learn_press_question_class( $this, $args ); ?> data-id="<?php echo $this->id; ?>" data-type="sorting-choice">

	<?php do_action( 'learn_press_before_question_wrap', $this, $quiz ); ?>

	<h4 class="learn-press-question-title"><?php echo get_the_title( $this->id ); ?></h4>

	<?php do_action( 'learn_press_before_question_options', $this, $quiz ); ?>

	<input type="hidden" name="learn-press-question-permalink" value="<?php echo esc_url( $quiz->get_question_link( $this->id ) ); ?>" />

	<?php do_action( 'learn_press_after_question_wrap', $this, $quiz ); ?>
	<script>
		;
		jQuery(function ($) {
			$('#<?php echo $wrap_id;?>:not(.checked) ul').sortable({
				axis: 'y',
				handle: '.sort-hand'
			});
		});
	</script>
</div>

