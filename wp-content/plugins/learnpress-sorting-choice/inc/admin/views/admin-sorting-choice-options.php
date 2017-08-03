<div class="learn-press-question" id="learn-press-question-<?php echo $this->id; ?>" data-type="<?php echo str_replace( '_', '-', $this->type ); ?>" data-id="<?php echo $this->id; ?>">

	<table class="lp-sortable lp-list-options" id="learn-press-list-options-<?php echo $this->id; ?>">
		<thead>
		<th><?php _e( 'Answer Text', 'learn_press' ); ?></th>
		<!-- <th width="50%"><?php _e( 'Displaying Position', 'learn_press' ); ?></th> -->
		<th width="100"></th>
		<th width="100"></th>
		</thead>
		<tbody>

		<?php
		$answers  = $this->answers;
		$sorted   = $this->get_option_position();
		$position = - 1;
		if ( $answers ): ?>
			<?php foreach ( $answers as $answer ): ?>
				<?php
				$value = $this->_get_option_value( $answer['value'] );
				$id    = !empty( $answer['id'] ) ? $answer['id'] : '';
				$position ++;
				?>

				<?php do_action( 'learn_press_before_question_answer_option', $this ); ?>

				<tr class="lp-list-option lp-list-option-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
					<td>
						<input type="hidden" name="learn_press_question[<?php echo $this->id; ?>][answer][value][]" value="<?php echo $value; ?>" />
						<input class="lp-answer-text no-submit key-nav" type="text" name="learn_press_question[<?php echo $this->id; ?>][answer][text][]" value="<?php echo esc_attr( $answer['text'] ); ?>" />
					</td>
					<td class="lp-list-option-actions lp-remove-list-option">
						<i class="dashicons dashicons-trash"></i>
					</td>
					<td class="lp-list-option-actions lp-move-list-option open-hand">
						<i class="dashicons dashicons-sort"></i>
					</td>
				</tr>

				<?php do_action( 'learn_press_after_question_answer_option', $this ); ?>

			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
	<p class="question-bottom-actions">
		<?php
		$buttons = array(
			'change_type' => learn_press_dropdown_question_types( array( 'echo' => false, 'id' => 'learn-press-dropdown-question-types-' . $this->id, 'selected' => $this->type ) )
		);
		if ( $this->type != 'true_or_false' ) {
			array_splice( $buttons, 0, 0, sprintf(
					__( '<button class="button add-question-option-button add-question-option-button-%1$d" data-id="%1$d" type="button">%2$s</button>', 'learn_press' ),
					$this->id,
					__( 'Add Option', 'learn_press' )
				)
			);
		}
		$buttons = apply_filters(
			'learn_press_question_bottom_buttons',
			$buttons,
			$this
		);
		echo join( "\n", $buttons );
		?>
	</p>
</div>
<script type="text/javascript">
	jQuery(function ($) {
		if (typeof LP == 'undefined' && typeof LearnPress != 'undefined') {
			window.LP = LearnPress;
		}
		LP.sortableQuestionAnswers($('#learn-press-question-<?php echo $this->id;?>'), {
			stop: function (e, ui) {
				$(this).find('tr').each(function (i) {
					$(this).find('td.correct-position').html('#' + (i + 1));
				})
			}
		});
		var sortable = {
			connectWith: '.display-position-<?php echo $this->id;?>',
			start      : function (e, ui) {
				ui.item.data('parent', this);
			},
			sort       : function (e, ui) {

			},
			change     : function (e, ui) {
				var $container = $('.display-position-<?php echo $this->id;?>'),
					$item = $('.display-position-<?php echo $this->id;?> .ui-sortable-placeholder').siblings();
				$container.each(function () {
					var $child = $(this).find('.lp-question-sorting-choice-display-position:not(.ui-sortable-helper)');
					if ($child.length == 0) {
						$item.appendTo($(this));
					}
				})
			}
		};
		$('.display-position-<?php echo $this->id;?>').sortable(sortable);
		$(document).on('change', '.lp-answer-text', function () {
			var $this = $(this),
				$tr = $this.closest('tr'),
				id = $tr.attr('data-id');
			$tr.closest('tbody').find('.lp-question-sorting-choice-display-position-' + id).html($this.val())
			console.log(id)
		}).on('mouseenter', '.display-position-<?php echo $this->id;?>', function () {
			$(this).sortable(sortable);
		})
	})
</script>