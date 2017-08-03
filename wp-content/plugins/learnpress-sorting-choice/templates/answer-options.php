<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$course = learn_press_get_the_course();
$quiz   = LP()->global['course-item'];
$user   = learn_press_get_current_user();

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
if ( $origin_answers = $this->answers ) {
	$origin_ids = array_keys( $origin_answers );
} else {
	$origin_answers = array();
	$origin_ids     = array();
}
$answers = $this->get_displaying_answers( $quiz->id, $user->id, $course->id );
$index   = 0;
?>
<ul id="learn-press-answer-options-<?php echo $this->id; ?>" data-type="sorting-choice" class="learn-press-question-options answer-options-<?php echo $this->id; ?><?php echo $checked ? ' checked' : ''; ?>">
	<?php if ( $answers ) foreach ( $answers as $k => $answer ): ?>
		<?php
		$answer_class = array( 'answer-option' );
		if ( $check_answer ) {
			$origin_index = array_search( $k, $origin_ids );

			if ( $origin_index !== $index ) {
				$answer_class[] = 'user-answer-false';
				$is_true        = false;
			} else {
				$answer_class[] = 'answer-true';
				$is_true        = true;
			}
		}
		?>
		<li class="<?php echo join( ' ', $answer_class ); ?>">
			<?php do_action( 'learn_press_before_question_answer_text', $answer, $this, $quiz ); ?>
			<span class="sort-hand"></span>
			<label>
				<?php if ( !$checked ): ?>
					<input type="hidden" name="learn-press-question-<?php echo $this->id; ?>[<?php echo $k; ?>]" value="<?php echo !empty( $answer['value'] ) ? $answer['value'] : $k; ?>" />
				<?php else: ?>
					<input type="checkbox" disabled="disabled" <?php checked( $is_true, true ); ?> />
				<?php endif; ?>
				<p class="auto-check-lines"><?php echo apply_filters( 'learn_press_question_answer_text', $answer['text'], $answer, $this, $quiz ); ?></p>
			</label>
			<?php do_action( 'learn_press_before_question_answer_text', $answer, $this, $quiz ); ?>
		</li>
		<?php if ( $check_answer && !$is_true ) { ?>
			<li class="correct-answer-label">
				<?php printf( __( 'Correct answer: %s', 'learnpress-sorting-choice' ), @$origin_answers[$origin_ids[$index]]['text'] ); ?>
			</li>
		<?php } ?>
		<?php $index ++; ?>
	<?php endforeach; ?>
</ul>
<script type="text/javascript">
	jQuery(function ($) {
		$('#learn-press-answer-options-<?php echo $this->id;?>:not(.checked)').sortable({
			axis  : 'y',
			handle: '.sort-hand'
		});
	});
</script>