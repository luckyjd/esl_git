<?php
/*
Plugin Name: LearnPress - Instructor Commission
Plugin URI: http://thimpress.com/learnpress
Description: Commission add-on for LearnPress
Author: ThimPress
Version: 2.0.1
Author URI: http://thimpress.com
Tags: learnpress, lms
Text Domain: learnpress
Domain Path: /languages/
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'LP_ADDON_COMMISSION_FILE', __FILE__ );
define( 'LP_ADDON_COMMISSION_PATH', dirname( __FILE__ ) );
define( 'LP_ADDON_COMMISSION_URI', plugins_url( '/', LP_ADDON_COMMISSION_FILE ) );
define( 'LP_ADDON_COMMISSION_VERSION', '2.0.1' );

/**
 * Class LP_Addon_Commission
 */
class LP_Addon_Commission {

    /**
     * @var null
     */
    protected static $_instance = null;

    /**
     * LP_Addon_PMPRO constructor.
     */
    private function __construct() {
        $this->_require();

        if ( is_admin() ) {
            $this->admin_require();
        }
        $this->_init_hooks();
    }

    /**
     * Init hooks
     */
    private function _init_hooks() {
        add_action( 'init', array( __CLASS__, 'load_text_domain' ) );
        add_action( 'learn_press_settings_tabs_array', array( $this, 'add_tab' ) );
        //add_action( 'admin_init', array( $this, 'add_meta_box' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
        add_action( 'learn_press_settings_save_commission', array( $this, 'update_setting' ) );
    }

    /**
     * Require in admin
     */
    private function admin_require() {
        $this->add_setting();
    }

    /**
     * Add tab setting commission for LearnPress
     *
     * @param $tabs
     *
     * @return mixed
     */
    public function add_tab( $tabs ) {
        $tabs['commission'] = __( 'Commissions', 'learnpress' );
        return $tabs;
    }

    /**
     * Add page setting commission
     */
    public function add_setting() {
        require_once LP_ADDON_COMMISSION_PATH . '/inc/classes/setting-commission.php';
    }

    /**
     * Add metabox commission for course
     */
    public function add_meta_box() {
        new RW_Meta_Box( $this->meta_box() );
    }

    /**
     * Metabox commission
     *
     * @return mixed|null|void
     */
    function meta_box() {
        $prefix = '_lp_';
        $course_id = isset( $_GET['post'] ) ? $_GET['post'] : false;
        $course_id = isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : $course_id;

        $instructors = lp_commission_get_instructors_by_course_id( $course_id );

        $array_fields = array();
        $commission_global = lp_commission_get_commission_global();
        foreach ( $instructors as $index => $instructor ) {
            $data_user = $instructor->data;
            $field = array(
                'name' => $data_user->display_name,
                'id' => LPC()->key_main_instructor,
                'type' => 'number',
                'std' => $commission_global,
                'attributes' => array(
                    'max' => 100,
                    'min' => 0
                )
            );
            $array_fields[] = $field;
        }

        $meta_box = array(
            'id' => 'course_commission',
            'title' => __( 'Setting commission', 'learnpress' ),
            'priority' => 'high',
            'pages' => array( 'lp_course' ),
            'fields' => $array_fields
        );

        return apply_filters( 'learn_press_commission_meta_box_args', $meta_box );
    }

    /**
     * Require core
     */
    public function _require() {
        require_once LP_ADDON_COMMISSION_PATH . '/inc/core/lp-commission.php';
        require_once LP_ADDON_COMMISSION_PATH . '/inc/core/lp-payment.php';
        require_once LP_ADDON_COMMISSION_PATH . '/inc/core/request-withdrawal.php';
        require_once LP_ADDON_COMMISSION_PATH . '/inc/core/class-lp-withdrawal.php';
        require_once LP_ADDON_COMMISSION_PATH . '/inc/classes/commission-list-table.php';
        require_once LP_ADDON_COMMISSION_PATH . '/inc/functions.php';
        require_once LP_ADDON_COMMISSION_PATH . '/inc/profile.php';
        require_once LP_ADDON_COMMISSION_PATH . '/inc/commission.php';
    }

    /**
     * Enqueue scripts in admin
     *
     * @param $hook
     */
    public function enqueue_admin( $hook ) {
        if ( $hook === 'settings_page_learn_press_settings' ) {
            $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : false;

            if ( $tab === 'commission' ) {
                $this->enqueue_admin_setting_commission();
            }
        }
    }

    /**
     * Enqueue scripts in page setting commission
     */
    private function enqueue_admin_setting_commission() {
        wp_enqueue_style( 'lp_commission_manage', LP_ADDON_COMMISSION_URI . 'assets/css/admin.css', array(), LP_ADDON_COMMISSION_VERSION );
    }

    public function update_setting() {
        $section = isset( $_GET['section'] ) ? $_GET['section'] : false;
        if ( $section === 'manage' ) {
            $this->update_manage_commission();
        }
    }

    /**
     * Update commission value of commission
     */
    private function update_manage_commission() {
        $post_data = $this->serialize_post_data();

        foreach ( $post_data as $key => $value ) {
            // Update value commission
            $key_main_instructor = LPC()->key_main_instructor;
            if ( $key === $key_main_instructor ) {
                $array_main_commission = (array) $value;
                foreach ( $array_main_commission as $course_id => $v ) {
                    update_post_meta( $course_id, $key_main_instructor, $v );
                }

                continue;
            }

            //Update active
            $key_active = LPC()->key_active;
            if ( $key === $key_active ) {
                $array_active = (array) $value;

                foreach ( $array_active as $course_id => $v ) {
                    if ( $v === 'yes' ) {
                        update_post_meta( $course_id, $key_active, 1 );
                    } else {
                        update_post_meta( $course_id, $key_active, 0 );
                    }
                }

                continue;
            }
        }
    }

    /**
     * Serialize post data commission
     *
     * @return mixed
     */
    private function serialize_post_data() {
        $post_data = $_POST;
        foreach ( $post_data as $key => $value ) {
            $key_prefix = LPC()->prefix;
            if ( strpos( $key, $key_prefix ) === false ) {
                unset( $post_data[$key] );
            }
        }

        return $post_data;
    }

    /**
     * Return TRUE if LearnPress plugin is activated.
     *
     * @return bool
     */
    public static function learnpress_is_active() {
        if ( !function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        return is_plugin_active( 'learnpress/learnpress.php' );
    }

    /**
     * Load plugin text domain
     */
    public static function load_text_domain() {
        if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
            learn_press_load_plugin_text_domain( LP_ADDON_COMMISSION_PATH, true );
        }
    }

    /**
     * Notifications
     */
    public static function notifications() {
        ?>
        <div class="error">
            <p><?php printf( __( '<strong>Commission</strong> addon version %s requires <strong>LearnPress</strong> version %s or higher', 'learnpress' ), LP_ADDON_COMMISSION_VERSION, '2.0' ); ?></p>
        </div>
        <?php
    }

    /**
     * Return unique instance of LP_Addon_Commission
     */
    public static function instance() {
        if ( self::learnpress_is_active() ) {
            if ( !self::$_instance ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        } else {
            add_action( 'admin_notices', array( __CLASS__, 'notifications' ) );
        }
    }

}

add_action( 'plugins_loaded', array( 'LP_Addon_Commission', 'instance' ), 10 );
