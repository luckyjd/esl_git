<?php
/**
 * Defines new type of question
 */
if ( class_exists( 'LP_Abstract_Question' ) ) {
	/**
	 * Class LP_Question_Sorting_Choice
	 *
	 * @extend LP_Question_Abstract
	 */
	class LP_Question_Sorting_Choice extends LP_Abstract_Question {
		/**
		 * LP_Question_Sorting_Choice constructor.
		 *
		 * @param null $the_question
		 * @param null $args
		 */
		function __construct( $the_question = null, $args = null ) {
			add_filter( 'learn_press_save_default_question_types', array( $this, 'add_type' ) );
			add_filter( 'learn_press_check_question_answers', array( $this, '_check_answer' ), 5, 4 );

			/*
			add_filter( 'learn_press_question_answers_data', array( $this, 'answer_data' ), 10, 3 );
			add_action( 'learn_press_after_display_quiz_question', array( $this, 'update_user_question' ), 10, 3 );
			add_action( 'learn_press_user_retake_quiz', array( $this, 'retake_quiz' ), 10, 3 );
			add_action( 'learn_press_user_start_quiz', array( $this, 'start_quiz' ), 10, 2 );
			add_action( 'learn_press_user_finish_quiz', array( $this, 'finish_quiz' ), 10, 2 );

			add_action( 'learn_press_quiz_question_display_hint_' . $this->id, array( $this, 'show_hint' ), 10, 2 );*/
			parent::__construct( $the_question, $args );

		}

		/**
		 * @param $checked
		 * @param $question_id
		 * @param $quiz_id
		 * @param $user_id
		 *
		 * @return string
		 */
		function _check_answer( $checked, $question_id, $quiz_id, $user_id ) {
			//
			if ( $question_id == $this->id ) {
				ob_start();
				$this->render();
				$checked = ob_get_clean();
			}
			return $checked;
		}

		function show_hint( $quiz_id, $user_id ) {
			if ( !( $user_id && $user = learn_press_get_user( $user_id ) ) ) {
				$user = LP()->user;
			}
			$question_answers = $this->get_displaying_answers( $quiz_id, $user->id );
			$this->render();
		}

		function add_type( $types ) {
			$types[] = $this->type;
			return $types;
		}

		function retake_quiz( $data, $quiz_id, $user_id ) {
			$this->reset_options( $quiz_id, $user_id );
		}

		function start_quiz( $quiz_id, $user_id ) {
			$this->reset_options( $quiz_id, $user_id );
		}

		function finish_quiz( $quiz_id, $user_id ) {
			$this->reset_options( $quiz_id, $user_id );
		}

		function reset_options( $quiz_id, $user_id ) {
			$quiz = LP_Quiz::get_quiz( $quiz_id );
			if ( $quiz && $quiz->has( 'question', $this->id ) ) {
				$session = LP()->get_session();
				$session->set( 'learn-press-question-sorting-choice-' . $session->id . '-' . $this->id, '' );
			}
		}

		function update_user_question( $question, $quiz, $user ) {
			if ( !is_user_logged_in() ) {
				return;
			}
			if ( !$question ) {
				return;
			}
			if ( $question->id != $this->id ) {
				return;
			}
			if ( $user->current_quiz_status( $quiz->id ) == 'started' ) {
			}
		}

		function answer_data( $data, $posted, $q ) {
			if ( $q->id == $this->id && !empty( $posted ) ) {
				if ( $data && !empty( $posted['position'] ) ) {
					foreach ( $data as $k => $answer ) {
						$pos                                 = array_search( $answer['answer_data']['value'], $posted['position'] );
						$data[$k]['answer_data']['position'] = $pos + 1;
					}
				}
			}
			return $data;
		}

		function save( $data = null ) {
			$session     = LP()->get_session();
			$session_key = 'learn-press-question-sorting-choice-' . $session->id . '-' . $this->id;
			$session->set( $session_key, '' );
			parent::save( $data );
		}

		function get_option_position() {
			$answers = $this->answers;
			usort( $answers, array( $this, '_cmp' ) );
			return $answers;
		}

		function get_random_answers( $force = false ) {
			$session     = LP()->get_session();
			$session_key = 'learn-press-question-sorting-choice-' . $session->id . '-' . $this->id;
			$answers     = $session->get( $session_key );
			if ( !$answers || $force ) {
				$answers = $this->answers;
				if ( $answers && sizeof( $answers ) > 1 ) {
					$keys          = array_keys( $answers );
					$shuffled_keys = $keys;
					shuffle( $shuffled_keys );
					$diff = array_diff_assoc( $keys, $shuffled_keys );
					$loop = 0;
					while ( !sizeof( $diff ) && $loop ++ < 10 ) {
						shuffle( $shuffled_keys );
						$diff = array_diff_assoc( $keys, $shuffled_keys );
					};
					$shuffled = array();
					foreach ( $shuffled_keys as $key ) {
						$shuffled[$key] = $answers[$key];
					}
					$answers = $shuffled;
				}
				$session->set( $session_key, $answers );
			}
			return $answers;
		}

		function get_displaying_answers( $quiz_id, $user_id = null, $course_id = 0 ) {
			$answered = $this->_get_user_question_answered( $user_id, $quiz_id, $course_id );
			if ( $answered && ( $_answers = $this->answers ) ) {
				$answers = array();
				foreach ( $answered as $id => $value ) {
					if ( isset( $_answers[$id] ) ) {
						$answers[$id] = $_answers[$id];
					} else {
						foreach ( $_answers as $answer_option ) {
							if ( $answer_option['value'] == $value ) {
								$answers[] = $answer_option;
								break;
							}
						}
					}
				}
			} else {
				$answers = $this->get_random_answers();
			}

			return $answers;
		}

		function _get_user_question_answered( $user_id, $quiz_id, $course_id = 0 ) {
			global $wpdb;
			$answered = false;
			if ( version_compare( LEARNPRESS_VERSION, '1.0.8', '>' ) ) {
				$answered = learn_press_get_user_question_answer( array( 'question_id' => $this->id, 'user_id' => $user_id, 'quiz_id' => $quiz_id, 'course_id' => $course_id ) );
			} else {
				$query = $wpdb->prepare( "
					SELECT max(user_quiz_id)
					FROM {$wpdb->prefix}learnpress_user_quizzes uq
					WHERE user_id = %d
					AND quiz_id = %d
				", $user_id, $quiz_id );
				if ( $object_id = $wpdb->get_var( $query ) ) {
					$answered = learn_press_get_user_quiz_meta( $object_id, 'question_answers' );
					if ( !empty( $answered[$this->id] ) ) {
						$answered = $answered[$this->id];
						if ( $answered && $this->answers ) {

						}
					} else {
						$answered = false;
					}
				}
			}
			return $answered;
		}

		function _cmp( $a, $b ) {
			if ( empty( $a['position'] ) ) {
				$a['position'] = 1;
			}
			if ( empty( $b['position'] ) ) {
				$b['position'] = 1;
			}
			return $a['position'] > $b['position'];
		}

		function admin_interface( $args = array() ) {
			ob_start();

			$view = learn_press_get_admin_view( 'admin-sorting-choice-options', LP_QUESTION_SORTING_CHOICE_FILE );
			include $view;
			$output = ob_get_clean();

			if ( !isset( $args['echo'] ) || ( isset( $args['echo'] ) && $args['echo'] === true ) ) {
				echo $output;
			}
			return $output;
		}

		function render( $args = array() ) {
			$args     = wp_parse_args(
				$args,
				array(
					'answered' => null
				)
			);
			$answered = !empty( $args['answered'] ) ? $args['answered'] : null;
			if ( null === $answered ) {
				$answered = $this->get_user_answered( $args );
			}
			$tmpl = LP_Addon_Question_Sorting_Choice::locate_template( 'answer-options.php' );
			include $tmpl;
		}

		function check( $args = null ) {
			static $checked = array();
			$check_key = md5( serialize( $args ) );
			if ( !empty( $checked[$check_key] ) ) {
				return $checked[$check_key];
			}
			//$question_mark = learn_press_get_question_mark( $this->id );
			//$mark_result   = get_post_meta( $this->id, '_lpr_sorting_choice_mark_result', true );

			$return = array(
				'fills'   => array(),
				'correct' => false,
				'mark'    => 0
			);

			$answers         = $this->answers;
			$total           = sizeof( $answers );
			$correct         = 0;
			$check_answer    = (array) $args;
			$pos             = 0;
			$correct_answers = array();
			if ( $answers ) foreach ( $answers as $i => $answer ) {
				/**
				 * Search position of a choice and compare it with the position of a corresponding choice of user
				 * If two positions is equals so this position is correct
				 */
				/*$pos++;
				$index = array_search( !empty( $answer['value'] ) ? $answer['value'] : '', $check_answer );
				echo "[$index, $i";
				print_r($check_answer);
				print_r($answers);
				echo $answer['value'];
				echo "]";
				if ( $index !== false && $index === $pos ) {
					$correct ++;
				}*/
				$correct_answers[] = $answer['value'];
			}

			$check_answer = array_values( $check_answer );
			$diff         = array_diff_assoc( $check_answer, $correct_answers );
			$diff_size    = sizeof( $diff );

			/*if ( $mark_result != 'correct_all' ) {
				$return['mark'] = ( $correct / $total ) * $question_mark;
			} elseif ( $correct == $total ) {
				$return['mark'] = $question_mark;
			}*/
			$return['correct_count'] = $total - $diff_size;
			$return['correct']       = $diff_size == 0;
			$return['mark']          = $diff_size == 0 ? $this->mark : 0;

			$checked[$check_key] = $return;
			return $return;
		}
	}
}