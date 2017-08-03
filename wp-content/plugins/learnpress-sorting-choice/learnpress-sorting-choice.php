<?php
/*
Plugin Name: LearnPress - Sorting Choice Question
Plugin URI: http://thimpress.com/learnpress
Description: Sorting Choice provide ability to sorting the options of a question to the right order
Author: ThimPress
Version: 2.1.0
Author URI: http://thimpress.com
Tags: learnpress
Text Domain: learnpress
Domain Path: /languages/
*/
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !defined( 'LP_QUESTION_SORTING_CHOICE_PATH' ) ) {
	define( 'LP_QUESTION_SORTING_CHOICE_FILE', __FILE__ );
	define( 'LP_QUESTION_SORTING_CHOICE_PATH', dirname( __FILE__ ) );
	define( 'LP_QUESTION_SORTING_CHOICE_VER', '2.1.0' );
	define( 'LP_QUESTION_SORTING_CHOICE_REQUIRE_VER', '2.0' );

}

/**
 * Class LP_Addon_Question_Sorting_Choice
 */
class LP_Addon_Question_Sorting_Choice {

	/**
	 * Initialize
	 */
	static function init() {
		if ( !defined( 'LEARNPRESS_VERSION' ) || ( version_compare( LEARNPRESS_VERSION, LP_QUESTION_SORTING_CHOICE_REQUIRE_VER, '<' ) ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
			return false;
		}
		add_action( 'plugins_loaded', array( __CLASS__, 'load_text_domain' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'learn_press_question_types', array( __CLASS__, 'register_question' ) );

		require_once LP_QUESTION_SORTING_CHOICE_PATH . '/inc/class-lp-question-sorting-choice.php';
		LP_Question_Factory::add_template( 'sorting-choice-option', self::admin_js_template() );
	}

	public static function admin_notice() {
		?>
		<div class="error">
			<p><?php printf( __( '<strong>Sorting Choice</strong> addon version %s requires <strong>LearnPress</strong> version %s or higher', 'learnpress' ), LP_QUESTION_SORTING_CHOICE_VER, LP_QUESTION_SORTING_CHOICE_REQUIRE_VER ); ?></p>
		</div>
		<?php
	}

	/**
	 * @return mixed|void
	 */
	static function admin_js_template() {
		ob_start();
		?>
		<tr class="lp-list-option lp-list-option-new lp-list-option-empty <# if(data.value){ #>lp-list-option-{{data.value}}<# } #>" data-id="{{data.value}}">
			<td>
				<input class="lp-answer-text no-submit key-nav" type="text" name="learn_press_question[{{data.question_id}}][answer][text][]" value="{{data.text}}" />
				<input type="hidden" name="learn_press_question[{{data.question_id}}][answer][value][]" value="{{data.value}}" />
			</td>
			<!--
			<td class="display-position display-position-{{data.question_id}} display-position-{{data.value}}">
						<span class="lp-question-sorting-choice-display-position lp-question-sorting-choice-display-position-{{data.question_id}} lp-question-sorting-choice-display-position-{{data.value}}">
							<input type="hidden" name="learn_press_question[{{data.question_id}}][answer][position][]" value="{{data.value}}" />
							<span>{{data.text}}</span>
						</span>
			</td>-->
			<td class="lp-list-option-actions lp-remove-list-option">
				<i class="dashicons dashicons-trash"></i>
			</td>
			<td class="lp-list-option-actions lp-move-list-option open-hand">
				<i class="dashicons dashicons-sort"></i>
			</td>
		</tr>
		<?php
		return apply_filters( 'learn_press_question_sorting_choice_answer_option_template', ob_get_clean(), __CLASS__ );
	}

	static function enqueue_assets() {
		wp_enqueue_script( 'jquery-ui-touch-punch-js', plugins_url( '/', LP_QUESTION_SORTING_CHOICE_FILE ) . 'assets/jquery.ui.touch-punch.min.js', array( 'jquery', 'jquery-ui-sortable' ) );
		wp_enqueue_script( 'question-sorting-choice-js', plugins_url( '/', LP_QUESTION_SORTING_CHOICE_FILE ) . 'assets/script.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ) );
		wp_enqueue_style( 'question-sorting-choice-css', plugins_url( '/', LP_QUESTION_SORTING_CHOICE_FILE ) . 'assets/style.css' );
	}

	/**
	 *
	 */
	static function ready() {

	}

	/**
	 * @param $types
	 *
	 * @return mixed
	 */
	static function register_question( $types ) {
		$types['sorting_choice'] = __( 'Sorting Choice', 'learn_press' );
		return $types;
	}

	/**
	 *
	 */
	static function load_text_domain() {
		if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
			learn_press_load_plugin_text_domain( LP_QUESTION_SORTING_CHOICE_PATH );
		}
		learn_press_add_question_type_support( 'sorting_choice', array( 'check-answer' ) );
	}

	/**
	 * @param      $name
	 * @param null $args
	 */
	static function get_template( $name, $args = null ) {
		learn_press_get_template( $name, $args, get_template_directory() . '/addons/sorting-choice/', LP_QUESTION_SORTING_CHOICE_PATH . '/templates/' );
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	static function locate_template( $name ) {
		$template = learn_press_locate_template( $name, get_template_directory() . '/addons/sorting-choice/', LP_QUESTION_SORTING_CHOICE_PATH . '/templates/' );
		return $template;
	}
}

// That's all, run...
add_action( 'learn_press_ready', array( 'LP_Addon_Question_Sorting_Choice', 'init' ) );