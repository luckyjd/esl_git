<?php
/*
Plugin Name: LearnPress - Randomize Quiz Questions
Plugin URI: http://thimpress.com/learnpress
Description: Mix all available questions in a quiz
Author: ThimPress
Version: 2.1.1
Author URI: http://thimpress.com
Tags: learnpress
Text Domain: learnpress
Domain Path: /languages/
*/

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Addon path
define( 'LP_RANDOM_QUIZ_QUESTIONS_FILE', __FILE__ );
define( 'LP_RANDOM_QUIZ_QUESTIONS_PATH', dirname( __FILE__ ) );
define( 'LP_RANDOM_QUIZ_QUESTIONS_VER', '2.0.1' );
define( 'LP_RANDOM_QUIZ_QUESTIONS_REQUIRE_VER', '2.0.3' );

/**
 * Class LP_Addon_Random_Quiz_Questions
 */
class LP_Addon_Random_Quiz_Questions {

	/**
	 * @var null
	 */
	protected static $quiz_id = null;

	/**
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * Constructor
	 */
	function __construct() {
		add_filter( 'learn_press_quiz_general_meta_box', array( $this, 'random_quiz_settings' ) );
		add_action( 'save_post', array( $this, 'learn_press_update_quiz_mode' ) );
		add_action( 'init', array( __CLASS__, 'load_text_domain' ) );
		add_filter( 'learn_press_quiz_questions', array( $this, 'random_questions' ), 10, 3 );
		add_action( 'learn_press_user_retake_quiz', array( $this, 'update_user_questions' ), 100, 4 );
	}

	/**
	 * Filter questions
	 *
	 * @param $q
	 * @param $quiz_id
	 * @param $force
	 *
	 * @return array
	 */
	public function random_questions( $q, $quiz_id, $force ) {
		$random_quiz = get_user_meta( get_current_user_id(), 'random_quiz', true );
		if ( is_admin() || empty( $random_quiz ) || empty( $random_quiz[$quiz_id] ) ) {
			return $q;
		}
		if ( $_questions = $random_quiz[$quiz_id] ) {
			$questions = array();
			foreach ( $_questions as $qq ) {
				$post = get_post( $qq );
				if ( $post && $post->ID ) {
					$questions[$qq] = $post;
				}
			}
			return $questions;
		}
		return $q;
	}

	/**
	 * Mix questions before user starting or retaking a quiz
	 *
	 * @param $data
	 * @param $quiz_id
	 * @param $course_id
	 * @param $user_id
	 */
	public function update_user_questions( $data, $quiz_id, $course_id, $user_id ) {
		global $wpdb;
		$item = null;
		switch ( current_action() ) {
			case 'learn_press_user_retake_quiz':
				$item = $wpdb->get_row(
					$wpdb->prepare( "
						SELECT *
						FROM {$wpdb->prefix}learnpress_user_items
						WHERE item_id = %d
							AND user_id = %d
							AND ref_id = %d
						ORDER BY user_item_id DESC
					", $quiz_id, $user_id, $course_id )
				);
				break;
			case 'learn_press_user_start_quiz':
				if ( !empty( $data->user_item_id ) ) {
					$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}learnpress_user_items WHERE user_item_id = %d", $data->user_item_id ) );
				}
		}
		if ( !$item ) {
			return;
		}
		if ( !$item->status == 'started' ) {
			return;
		}
		$random_quiz = get_user_meta( $user_id, 'random_quiz', true );
		$quiz        = LP_Quiz::get_quiz( $quiz_id );
		if ( $quiz && $questions = $quiz->get_questions() ) {
			$questions = array_keys( $questions );
			shuffle( $questions );
			$question_id = reset( $questions );
			learn_press_update_user_item_meta( $item->user_item_id, 'current_question', $question_id );
			if ( empty( $random_quiz ) ) {
				$random_quiz = array( $quiz_id => $questions );
			} else {
				$random_quiz[$quiz_id] = $questions;
			}
			update_user_meta( $user_id, 'random_quiz', $random_quiz );
		}
	}

	public function update_questions( $object_id, $meta_key, $_meta_value ) {
		if ( $meta_key == 'questions' && $_meta_value ) {
			//learn_press_update_user_item_meta( $object_id, 'user_random_questions', $questions );
			$history = get_user_meta( learn_press_get_current_user_id(), 'lp_current_quiz_history', true );
			if ( !empty( $history ) ) {
				//$history = array($)
			}
			update_user_meta( learn_press_get_current_user_id(), 'lp_current_quiz_history', $history );
		}
	}

	function user_quiz_data( $data, $quiz_id, $user_id ) {
		$user = learn_press_get_user( $user_id );
		if ( $user->get_quiz_status( $quiz_id ) != '' ) {
			$questions = (array) $data['questions'];
			shuffle( $questions );
			$data['current_question'] = reset( $questions );
			$data['questions']        = $questions;
		}
		return $data;
	}

	function save_questions( $quiz_id, $user_id ) {
		if ( learn_press_user_has_started_quiz( null, $quiz_id ) ) {
			$random_questions = (array) get_user_meta( $user_id, '_lp_quiz_random_questions', true );
			if ( !empty( $random_questions[$quiz_id] ) ) {
				unset( $random_questions[$quiz_id] );
				update_user_meta( $user_id, '_lp_quiz_random_questions', $random_questions );
			}
		}
	}

	function clear_session( $quiz_id, $user_id ) {
		$nonce    = wp_create_nonce( 'random-quiz' );
		$_quizzes = LP_Session::get( $nonce );
		if ( !$_quizzes ) {
			return;
		}
		if ( !empty( $_quizzes[$quiz_id] ) ) {
			unset( $_quizzes[$quiz_id] );
		}
		$_quizzes = array_filter( $_quizzes );
		LP_Session::set( $nonce, $_quizzes );
		$quiz = LP_Quiz::get_quiz( $quiz_id );
		if ( !$quiz ) {
			return;
		}

		$user = learn_press_get_user( $user_id );
		if ( $user->get_quiz_status( $quiz->id ) == 'started' ) {
			remove_filter( 'learn_press_quiz_questions', array( $this, 'random_quiz_questions' ), 99 );
			$origin_questions = $quiz->questions;
			$history          = $user->get_quiz_results( $quiz->id );
			if ( $origin_questions ) {
				learn_press_update_user_quiz_meta( $history->history_id, 'original_questions', array_key_exists( $origin_questions ) );
			}
		}
	}

	function param_questions( $questions, $quiz_id ) {
		$user      = learn_press_get_current_user();
		$quiz_mode = get_post_meta( $quiz_id, '_lp_random_mode', true );
		$history   = $user->get_quiz_results( $quiz_id );
		//print_r( $_SESSION['learn_press'] );
		if ( !$history || empty( $history->questions ) ) {
			return $questions;
		}
		if ( !$quiz_mode || $quiz_mode !== 'yes' || is_admin() || !$questions ) {
			return $questions;
		}
		$_questions = array();

		foreach ( $history->questions as $q ) {
			if ( !empty( $questions[$q] ) ) {
				$_questions[$q] = $questions[$q];
			}
		}

		return $_questions;
	}

	/**
	 * Get the questions of a quiz
	 *
	 * @param $questions
	 * @param $quiz_id
	 * @param $force
	 *
	 * @return array
	 */
	function random_quiz_questions( $questions, $quiz_id, $force ) {
		$user      = learn_press_get_current_user();
		$quiz_mode = get_post_meta( $quiz_id, '_lp_random_mode', true );
		$nonce     = wp_create_nonce( 'random-quiz' );

		if ( !$quiz_mode || $quiz_mode !== 'yes' || is_admin() || !$questions ) {
			return $questions;
		}

		$_questions = array();
		if ( $user->get_quiz_status( $quiz_id ) == '' ) {
			$_quizzes = (array) LP_Session::get( $nonce );
			if ( empty( $_quizzes[$quiz_id] ) ) {
				$_quizzes[$quiz_id] = array();
			} else {
				$_questions = $_quizzes[$quiz_id];
			}
		} else {
			$history    = $user->get_quiz_results( $quiz_id );
			$_questions = !empty( $history->questions ) ? $history->questions : array();
		}
		if ( $_questions ) {
			$sorted = array();
			foreach ( $_questions as $q ) {
				if ( !empty( $questions[$q] ) ) {
					$sorted[$q] = $questions[$q];
				}
			}
			$_questions = $sorted;
			return $_questions;
		}

		shuffle( $questions );
		foreach ( $questions as $q ) {
			$_questions[$q->ID] = $q;
		}
		$_quizzes[$quiz_id] = array_keys( $_questions );

		LP_Session::set( $nonce, $_quizzes );
		return $_questions;

		$quiz_questions = array();
		$limit          = get_post_meta( $quiz_id, '_lp_quiz_questions_limit', true );
		if ( is_null( $limit ) || $limit == 0 ) $limit = - 1;
		$query_args = array(
			'posts_per_page' => $limit,
			'post_type'      => 'lp_question',
			'post_status'    => 'publish',
			'orderby'        => 'rand'
		);

		if ( $tags = get_post_meta( $quiz_id, '_lp_quiz_questions_tags' ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'question_tag',
					'field'    => 'slug',
					'terms'    => $tags
				)
			);
		}

		$quiz_params = md5( maybe_serialize( $query_args ) );

		$saved_quiz_params = get_user_meta( $current_user_id, '_lp_quiz_random_questions_params_' . $quiz_id, true );

		if ( $saved_quiz_params == $quiz_params ) {
			$_quiz_questions    = get_user_meta( $current_user_id, '_lp_quiz_questions', true );
			$has_quiz_questions = ( $_quiz_questions && !empty( $_quiz_questions[$quiz_id] ) && sizeof( $_quiz_questions[$quiz_id] ) > 0 );
			if ( learn_press_user_has_started_quiz( $current_user_id, $quiz_id ) && $has_quiz_questions ) {
				$quiz_questions = array();
				foreach ( $_quiz_questions[$quiz_id] as $question_id ) {
					$quiz_questions[$question_id] = $question_id;
				}
				return $quiz_questions;
			} else {
				$quiz_questions = get_user_meta( $current_user_id, '_lp_quiz_random_questions', true );
				if ( $quiz_questions && !empty( $quiz_questions[$quiz_id] ) && sizeof( $quiz_questions[$quiz_id] ) > 0 ) {
					return $quiz_questions[$quiz_id];
				}
			}
		}
		$questions = array();
		$my_query  = new WP_Query( $query_args );
		if ( $my_query->have_posts() ) {
			global $post;
			while ( $my_query->have_posts() ) : $my_query->the_post();
				$questions[$post->ID] = $only_ids ? $post->ID : $post;
			endwhile;
		}
		wp_reset_query();
		$quiz_questions[$quiz_id] = $questions;
		update_user_meta( $current_user_id, '_lp_quiz_random_questions', $quiz_questions );
		update_user_meta( $current_user_id, '_lp_quiz_random_questions_params_' . $quiz_id, $quiz_params );

		$meta = get_user_meta( $current_user_id, '_lp_quiz_current_question', true );
		if ( !is_array( $meta ) ) $meta = array( $quiz_id => reset( $questions ) );
		else $meta[$quiz_id] = reset( $questions );
		update_user_meta( $current_user_id, '_lp_quiz_current_question', $meta );

		return $questions;
	}

	/**
	 * Get the questions of a quiz
	 *
	 * @param $questions
	 * @param $quiz_id
	 * @param $current_user_id
	 *
	 * @return array
	 */
	function update_user_quiz_questions( $questions, $quiz_id, $current_user_id ) {
		self::$quiz_id = $quiz_id;
		$random_mode   = get_post_meta( $quiz_id, '_lp_quiz_mode', true );
		if ( !$random_mode || $random_mode !== 'on' ) {
			return $questions;
		}

		$quiz_questions = get_user_meta( $current_user_id, '_lp_quiz_random_questions', true );
		if ( $quiz_questions && !empty( $quiz_questions[$quiz_id] ) && sizeof( $quiz_questions[$quiz_id] ) > 0 ) {
			return array_keys( $quiz_questions[$quiz_id] );
		}
		return $questions;
	}

	/**
	 * Get the questions of a quiz
	 *
	 * @param $questions
	 * @param $quiz_id
	 * @param $only_ids
	 *
	 * @return array
	 */
	function get_quiz_questions( $questions, $quiz_id, $only_ids ) {
		self::$quiz_id   = $quiz_id;
		$random_mode     = get_post_meta( $quiz_id, '_lp_quiz_mode', true );
		$current_user_id = learn_press_get_current_user_id();
		if ( !$random_mode || $random_mode !== 'on' || ( learn_press_user_has_started_quiz( null, $quiz_id ) && $questions ) ) {
			return $questions;
		}

		$quiz_questions = (array) get_user_meta( $current_user_id, '_lp_quiz_random_questions', true );

		$limit = get_post_meta( $quiz_id, '_lp_quiz_questions_limit', true );
		if ( is_null( $limit ) || $limit == 0 ) $limit = - 1;
		$query_args = array(
			'posts_per_page' => $limit,
			'post_type'      => 'lp_question',
			'post_status'    => 'publish',
			'orderby'        => 'rand'
		);

		if ( $tags = get_post_meta( $quiz_id, '_lp_quiz_questions_tags' ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'question_tag',
					'field'    => 'slug',
					'terms'    => $tags
				)
			);
		}

		$questions = array();
		$my_query  = new WP_Query( $query_args );
		if ( $my_query->have_posts() ) {
			global $post;
			while ( $my_query->have_posts() ) : $my_query->the_post();
				$questions[$post->ID] = $only_ids ? $post->ID : $post;
			endwhile;
		}
		wp_reset_query();
		$quiz_questions[$quiz_id] = $questions;
		update_user_meta( $current_user_id, '_lp_quiz_random_questions', $quiz_questions );
		return $questions;
	}

	/**
	 * Get the questions of a quiz
	 *
	 * @param $questions
	 * @param $quiz_id
	 * @param $user_id
	 *
	 * @return array
	 */
	function user_quiz_questions( $questions, $quiz_id, $user_id ) {
		self::$quiz_id   = $quiz_id;
		$random_mode     = get_post_meta( $quiz_id, '_lp_quiz_mode', true );
		$current_user_id = learn_press_get_current_user_id();
		if ( !$random_mode || $random_mode !== 'on' || ( learn_press_user_has_started_quiz( null, $quiz_id ) && $questions ) ) {
			return $questions;
		}

		$quiz_questions = (array) get_user_meta( $current_user_id, '_lp_quiz_random_questions', true );
		if ( !empty( $quiz_questions[$quiz_id] ) ) {
			return $quiz_questions[$quiz_id];
		}
		return false;
		if ( learn_press_user_has_started_quiz( null, $quiz_id ) &&
			!empty( $quiz_questions[$quiz_id] )
		) {
			$return = array();
			foreach ( $quiz_questions[$quiz_id] as $q ) {
				$return[$q] = $q;
			};
			return $return;
		}

		$quiz         = get_post( $quiz_id );
		$question_ids = array();
		$limit        = get_post_meta( $quiz_id, '_lp_quiz_questions_limit', true );
		if ( is_null( $limit ) || $limit == 0 ) $limit = - 1;
		$query_args = array(
			'posts_per_page' => $limit,
			'post_type'      => 'lp_question',
			'post_status'    => 'publish',
			'orderby'        => 'rand'
		);

		if ( $tags = get_post_meta( $quiz_id, '_lp_quiz_questions_tags' ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'question_tag',
					'field'    => 'slug',
					'terms'    => $tags
				)
			);
		}

		$questions = array();
		$my_query  = new WP_Query( $query_args );
		if ( $my_query->have_posts() ) {
			global $post;
			while ( $my_query->have_posts() ) : $my_query->the_post();
				$questions[$post->ID] = $only_ids ? $post->ID : $post;
			endwhile;
		}
		wp_reset_query();
		$quiz_questions[$quiz_id] = $questions;

		update_user_meta( $current_user_id, '_lp_quiz_random_questions', $quiz_questions );
		return $questions;
	}

	/**
	 * Update quiz meta
	 */
	function learn_press_update_quiz_mode( $post_id ) {
		if ( !isset( $_POST ) ) {
			return;
		}

		if ( isset( $_POST['_lp_quiz_current_mode'] ) && $_POST['_lp_quiz_current_mode'] === 'on' ) {
			update_post_meta( $post_id, '_lp_quiz_mode', 'on' );
		} else {
			delete_post_meta( $post_id, '_lp_quiz_mode' );
		}
	}

	/**
	 * Add fields to metabox settings
	 *
	 * @param array $metabox
	 *
	 * @return mixed
	 */
	function random_quiz_settings( $metabox ) {
		/*$new_settings = array(
			array(
				'name' => __( 'Limit', 'learnpress' ),
				'desc' => __( 'The number of questions for quiz each time the quiz is generated. Set 0 select all', 'learnpress' ),
				'id'   => '_lp_quiz_questions_limit',
				'type' => 'number',
				'min'  => 0,
				'max'  => 99999,
				'std'  => 10
			),
			array(
				'name'     => __( 'Tags', 'learnpress' ),
				'desc'     => '',
				'id'       => '_lp_quiz_questions_tags',
				'type'     => 'select_advanced',
				'multiple' => true,
				'desc'     => __( 'Filter by question tags', 'learnpress' ),
				'options'  => array()
			)
		);
		$tag_taxonomy = get_categories( 'taxonomy=question_tag&orderby=name' );
		if ( $tag_taxonomy ) {
			foreach ( $tag_taxonomy as $tag ) {
				$new_settings[1]['options'][$tag->slug] = $tag->slug;
			}
		}
		if ( !empty( $_REQUEST['post'] ) && $post_id = absint( $_REQUEST['post'] ) ) {
			$quiz_mode = get_post_meta( $post_id, '_lp_quiz_mode', true );
			if ( $quiz_mode ) {
				if ( !empty( $metabox['fields'] ) ) {
					foreach ( $metabox['fields'] as $k => $field ) {
						if ( empty( $field['class'] ) ) {
							$field['class'] = 'hide-if-js';
						} else {
							$field['class'] .= ' hide-if-js';
						}
						$metabox['fields'][$k] = $field;
					}
				}
			} else {
				//new RWMB_Quiz_Question_Field();
				foreach ( $new_settings as $k => $new_one ) {
					$new_settings[$k]['class'] = 'hide-if-js';
				}
			}
		}*/
		array_unshift( $metabox['fields'], array(
			'name'    => __( 'Random Questions', 'learnpress' ),
			'desc'    => __( 'Mix all available questions in this quiz', 'learnpress' ),
			//'id'   => '_lp_quiz_current_mode',
			'id'      => '_lp_random_mode',
			//'type' => 'switcher_button',
			'type'    => 'radio',
			'options' => array(
				'yes' => __( 'Yes', 'learnpress' ),
				'no'  => __( 'No', 'learnpress' ),
			),
			'std'     => 'no'
			//'off'  => __( 'No', 'learnpress' ),
			//'on'   => __( 'Yes', 'learnpress' )
		) );
		//$metabox['fields'] = array_merge( $metabox['fields'], $new_settings );
		return $metabox;
	}


	function frontend_script() {
		wp_enqueue_script( 'learn-press-random-quiz', untrailingslashit( plugins_url( '/script.js', __FILE__ ) ), null, null, true );
	}

	function frontend_print_script() {
		?>
		<script type="text/javascript">
			if (typeof ajaxurl == 'undefined') {
				window.ajaxurl = '<?php echo admin_url( 'admin-ajax.php' );?>'
			}
		</script>
		<?php
	}

	static function load_text_domain() {
		if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
			learn_press_load_plugin_text_domain( LP_RANDOM_QUIZ_QUESTIONS_PATH, true );
		}
	}

	public static function admin_notice() {
		?>
		<div class="error">
			<p><?php printf( __( '<strong>Random Quiz Questions</strong> addon version %s requires <strong>LearnPress</strong> version %s or higher', 'learnpress' ), LP_RANDOM_QUIZ_QUESTIONS_VER, LP_RANDOM_QUIZ_QUESTIONS_REQUIRE_VER ); ?></p>
		</div>
		<?php
	}

	static function instance() {

		if ( !defined( 'LEARNPRESS_VERSION' ) || ( version_compare( LEARNPRESS_VERSION, LP_RANDOM_QUIZ_QUESTIONS_REQUIRE_VER, '<' ) ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
			return false;
		}

		if ( !self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

add_action( 'learn_press_ready', array( 'LP_Addon_Random_Quiz_Questions', 'instance' ) );
