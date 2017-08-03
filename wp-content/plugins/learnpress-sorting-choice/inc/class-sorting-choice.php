<?php
die();

class LPR_Sorting_Choice {
	function __construct() {
		require_once( 'class-lpr-question-type-sorting-choice.php' );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_filter( 'lpr_question_types', array( $this, 'add_new_question_type' ) );
		add_action( 'admin_print_scripts', array( $this, 'print_js_template' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_assets' ) );
	}

	function load_frontend_assets() {
		wp_enqueue_style( 'sorting-choice', plugins_url( 'assets/style.css', LPR_SORTING_CHOICE_FILE ) );
		wp_enqueue_script( 'sorting-choice', plugins_url( 'assets/frontend-sorting-choice.js', LPR_SORTING_CHOICE_FILE ), array( 'jquery-ui-sortable' ) );
	}

	function load_assets() {
		wp_enqueue_script( 'sorting-choice', plugins_url( 'assets/sorting-choice.js', LPR_SORTING_CHOICE_FILE ) );
	}

	function add_new_question_type( $types ) {
		$types[] = 'sorting_choice';
		return $types;
	}

	function print_js_template() {
		?>
		<script type="text/html" id="tmpl-sorting-choice-question-answer">
			<tr class="lpr-disabled">
				<td class="lpr-sortable-handle">
					<i class="dashicons dashicons-sort"></i>
				</td>
				<td>
					<input class="lpr-answer-text" type="text" name="lpr_question[{{data.question_id}}][answer][text][__INDEX__]" value="" />
				</td>
				<td align="center" class="lpr-remove-answer">
					<span class=""><i class="dashicons dashicons-trash"></i></span></td>
			</tr>

		</script>
		<?php
	}
}

new LPR_Sorting_Choice();