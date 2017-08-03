<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class LP_Request_Withdrawal {

    private static $_instance = null;
    public static $tab_slug = 'withdrawals';
    public static $key_action = 'lp_withdraw';
    public static $key_post_type = 'lp_withdraw';

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action( 'init', array( $this, 'custom_post_type' ) );
        add_filter( 'learn_press_user_profile_tabs', array( $this, 'add_profile_tab' ), 10, 2 );
        add_filter( 'learn_press_profile_tab_endpoints', array( $this, 'profile_tab_endpoints' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_post' ) );
        add_action( 'template_redirect', array( $this, 'hidden_front_end' ), 1 );
        add_filter( 'manage_lp_withdraw_posts_columns', array( $this, 'add_columns_header' ) );
        add_action( 'manage_lp_withdraw_posts_custom_column', array( $this, 'add_columns_data' ), 10, 2 );
    }

    public function enqueue_admin( $hook ) {
        $post_type = get_post_type();

        if ( $hook === 'post.php' && $post_type == self::$key_post_type ) {
            $this->enqueue_admin_withdrawal();
        }
    }

    private function enqueue_admin_withdrawal() {
        wp_enqueue_style( 'lp_commission_manage', LP_ADDON_COMMISSION_URI . 'assets/css/admin.css', array(), LP_ADDON_COMMISSION_VERSION );
        wp_enqueue_script( 'lp_withdrawal', LP_ADDON_COMMISSION_URI . 'assets/js/admin.js', array( 'jquery' ), LP_ADDON_COMMISSION_VERSION );
    }

    public function save_post( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $status = !empty( $_POST['lp_status'] ) ? $_POST['lp_status'] : '';

        $check_username = isset( $_POST['check_username'] ) && $_POST['check_username'] ? $_POST['check_username'] : '';
        $check_password = isset( $_POST['check_password'] ) && $_POST['check_password'] ? $_POST['check_password'] : '';

        if ( !self::checkUserPass( $check_username, $check_password ) ) {
            return false;
        }

        if ( $status === 'complete' ) {
            $wd = new LP_Withdrawal( $post_id );
            $wd->complete();
        }

        if ( $status === 'reject' ) {
            $wd = new LP_Withdrawal( $post_id );
            $wd->reject();
        }

        if ( $status === 'pending' ) {
            $wd = new LP_Withdrawal( $post_id );
            $wd->pending();
        }

        if ( $status === 'payon' ) {
            $wd = new LP_Withdrawal( $post_id );
            $wd->payon();
        }
    }

    function add_meta_box() {
        add_meta_box(
                'lp_details', __( 'Details', 'learnpress' ), array( $this, 'meta_box_html' ), 'lp_withdraw', 'normal', 'high'
        );
    }

    public function meta_box_html( $post ) {
        $post_id = $post->ID;
        $withdrawal = new LP_Withdrawal( $post_id );
        $wd_status = $withdrawal->get_status();

        $datetime_format = 'H:i:s d/m/Y';
        $time_request = $withdrawal->get_time_request();
        $time_request_str = $time_request->format( $datetime_format );
        $time_resolve = $withdrawal->get_time_resolve();
        $time_resolve_str = '-:-:- -/-/-';
        if ( $time_resolve ) {
            $time_resolve_str = $time_resolve->format( $datetime_format );
        }
        $method_title = $withdrawal->get_title_method_payment();
        $method_key = $withdrawal->get_key_method_payment();
        ?>

        <h1><?php echo esc_html( $post->post_title ); ?></h1>
        <br>

        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e( 'ID', 'learnpress' ); ?></th>
                    <th><?php _e( 'Time request', 'learnpress' ); ?></th>
                    <th><?php _e( 'Time resolve', 'learnpress' ); ?></th>
                    <th><?php _e( 'Amount', 'learnpress' ); ?></th>
                    <th><?php _e( 'Method', 'learnpress' ); ?></th>
                    <th><?php _e( 'Status', 'learnpress' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $post_id; ?></td>
                    <td><?php echo $time_request_str; ?></td>
                    <td><?php echo $time_resolve_str; ?></td>
                    <td><?php echo esc_html( $withdrawal->get_value() . learn_press_get_currency_symbol() ); ?></td>
                    <td><?php echo esc_html( $method_title ); ?> (<?php echo esc_html( $withdrawal->post->post_content ); ?>)</td>
                    <td><?php echo $withdrawal->get_title_status(); ?></td>
                </tr>
            </tbody>
        </table>
        <div class="check_user_pass">
            <label><?php _e( 'Username', 'learnpress' ); ?></label>
            <input type="text" name="check_username"/>
            <label><?php _e( 'Password', 'learnpress' ); ?></label>
            <input type="password" name="check_password"/>
        </div>
        <div class="paid">
        <?php if ( !$withdrawal->is_resolve() ): ?>

            <?php if ( $method_key !== 'offline' ):
                ?>
                    <button id="lp_paid" type="submit" class="button button-primary button-large"><?php _e( 'Pay On', 'learnpress' ); ?></button>
                <?php else : ?>
                    <button id="lp_complete" type="submit" class="button button-primary button-large"><?php _e( 'Pay Off', 'learnpress' ); ?></button>
                <?php endif; ?>
                <button id="lp_reject" type="submit" class="button button-secondary button-large"><?php _e( 'Reject', 'learnpress' ); ?></button>
            <?php endif; ?>
            <input id="lp_input_status" name="lp_status" type="hidden" value="" data-reject="reject">
            <select name="withdraw_status" id="lp_withdraw_status_select_box">
                <option value="pending"<?php echo $wd_status == 'pending' ? 'selected="selected"' : ''; ?>><?php _e( 'Pending', 'learnpress' ); ?></option>
                <option value="reject"<?php echo $wd_status == 'reject' ? 'selected="selected"' : ''; ?>><?php _e( 'Reject', 'learnpress' ); ?></option>
                <option value="complete"<?php echo $wd_status == 'complete' ? 'selected="selected"' : ''; ?>><?php _e( 'Complete', 'learnpress' ); ?></option>
            </select>
            <a href="javascript:void(0);" class="button button-secondary" id="lp_withdraw_apply_btn"><?php _e( 'Apply', 'learnpress' ); ?></a>
        </div>
        <?php
    }

    public function custom_post_type() {
        $labels = array(
            'name' => _x( 'Withdrawals', 'Post Type General Name', 'learnpress' ),
            'singular_name' => _x( 'Withdrawal', 'Post Type Singular Name', 'learnpress' ),
            'menu_name' => __( 'Withdrawal', 'learnpress' ),
            'name_admin_bar' => __( 'Post Type', 'learnpress' ),
            'archives' => __( 'Item Archives', 'learnpress' ),
            'parent_item_colon' => __( 'Parent Item:', 'learnpress' ),
            'all_items' => __( 'Withdrawals', 'learnpress' ),
            'add_new_item' => __( 'Add New Item', 'learnpress' ),
            'add_new' => __( 'Add New', 'learnpress' ),
            'new_item' => __( 'New Item', 'learnpress' ),
            'edit_item' => __( 'Edit Item', 'learnpress' ),
            'update_item' => __( 'Update Item', 'learnpress' ),
            'view_item' => __( 'View Item', 'learnpress' ),
            'search_items' => __( 'Search Item', 'learnpress' ),
            'not_found' => __( 'Not found', 'learnpress' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'learnpress' ),
            'featured_image' => __( 'Featured Image', 'learnpress' ),
            'set_featured_image' => __( 'Set featured image', 'learnpress' ),
            'remove_featured_image' => __( 'Remove featured image', 'learnpress' ),
            'use_featured_image' => __( 'Use as featured image', 'learnpress' ),
            'insert_into_item' => __( 'Insert into item', 'learnpress' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'learnpress' ),
            'items_list' => __( 'Items list', 'learnpress' ),
            'items_list_navigation' => __( 'Items list navigation', 'learnpress' ),
            'filter_items_list' => __( 'Filter items list', 'learnpress' ),
        );
        $capabilities = array(
            'edit_post' => 'edit_post',
            'read_post' => 'read_post',
            'delete_posts' => 'delete_posts',
            'edit_posts' => 'edit_posts',
            'edit_others_posts' => 'edit_others_posts',
            'publish_posts' => 'publish_posts',
            'read_private_posts' => 'read_private_posts',
            'create_posts' => 'do_not_allow',
        );

        $args = array(
            'label' => __( 'Withdrawal', 'learnpress' ),
            'description' => __( 'Withdraw', 'learnpress' ),
            'labels' => $labels,
            'supports' => array( '' ),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'learn_press',
            'menu_position' => 10,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => false,
            'capability_type' => 'post',
//            'capabilities' => $capabilities,
        );
        register_post_type( self::$key_post_type, $args );
    }

    function hidden_front_end() {
        global $wp_query;

        if ( is_singular( self::$key_post_type ) ) :
            $url = get_bloginfo( 'url' );

            wp_redirect( esc_url_raw( $url ), 301 );
            exit();
        endif;
    }

    public static function instance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function enqueue_scripts( $hook ) {
        if ( learn_press_is_profile() ) {
            $this->enqueue_scripts_profile();
        }
    }

    private function enqueue_scripts_profile() {
        global $wp;
        $query_vars = $wp->query_vars;
        if ( array_key_exists( 'view', $query_vars ) && $query_vars['view'] === self::$tab_slug ) {
            wp_enqueue_script( 'lp_withdrawals', LP_ADDON_COMMISSION_URI . 'assets/js/withdrawals.js', array( 'jquery' ), '1.0.0' );
        }
    }

    /*
     * Add profile tab
     */

    public function add_profile_tab( $tabs, $user ) {
        if ( !LPC()->is_enable() ) {
            return $tabs;
        }
        $current_user = learn_press_get_current_user();
        if ( $current_user->ID !== $user->ID ) {
            return $tabs;
        }

        $tabs[$this->get_tab_slug()] = array(
            'title' => __( 'Withdrawals', 'learnpress' ),
            'callback' => array( $this, 'withdrawals_tab_content' )
        );

        return $tabs;
    }

    public function get_tab_slug() {
        return sanitize_title( self::$tab_slug );
    }

    public function withdrawals_tab_content( $tab, $tabs, $user ) {
        $notifications = array();
        if ( strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post' ) {
            $notifications = $this->handle_withdrawal_request( $user );
        }

        ob_start();
        learn_press_get_template( 'withdrawals.php', array(
            'tab' => $tab,
            'tabs' => $tabs,
            'user' => $user,
            'notifications' => $notifications,
                ), learn_press_template_path() . '/addons/commission/', LP_ADDON_COMMISSION_PATH . '/templates/' );

        return ob_get_clean();
    }

    public function profile_tab_endpoints( $endpoints ) {
        $endpoints[] = $this->get_tab_slug();

        return $endpoints;
    }

    public static function nonce() {
        wp_nonce_field( self::$key_action );
    }

    private function handle_withdrawal_request( $user ) {
        $nonce = $_POST['_wpnonce'];
        $post_data = $_POST;

        $verify = wp_verify_nonce( $nonce, self::$key_action );
        if ( !$verify ) {
            return array(
                'return' => false,
                'msg' => __( 'Verify nonce is wrong!', 'learnpress' ),
                'code' => 'NONCE_INVALID',
                'old' => $post_data,
            );
        }

        $cuser = wp_get_current_user();
        $code = $_POST['lp_withdrawals_secret_code'] ? $_POST['lp_withdrawals_secret_code'] : '';
        if ( !self::checkUserPass( $cuser->user_login, $code ) ) {
            return array(
                'return' => false,
                'msg' => __( 'Verify password is wrong!', 'learnpress' ),
                'code' => 'PASS_INVALID',
                'old' => $post_data,
            );
            ;
        }
        $email = $_POST['lp_withdrawals_email'] ? $_POST['lp_withdrawals_email'] : '';
        if ( !$email || empty( $email ) ) {
            return array(
                'return' => false,
                'msg' => __( 'Please enter a paypal email account!', 'learnpress' ),
                'code' => 'ENTER_PAYPAL_EMAIL_ACCOUNT',
                'old' => $post_data,
            );
        }

        $current_user = learn_press_get_current_user();
        if ( $user->ID !== $current_user->ID ) {
            return array(
                'return' => false,
                'msg' => __( 'Something went wrong!', 'learnpress' ),
                'code' => 'USER_NOT_MATCH',
                'old' => $post_data,
            );
        }

        $value_commission_request = !empty( $_POST['lp_withdrawals'] ) ? intval( $_POST['lp_withdrawals'] ) : 0;
        $current_commission = lp_commission_get_total_commission( $current_user->ID );

        if ( $value_commission_request > $current_commission ) {
            return array(
                'return' => false,
                'msg' => __( 'You do not have enough money to withdraw!', 'learnpress' ),
                'code' => 'NOT_ENOUGH_MONEY',
                'old' => $post_data,
            );
        }

        if ( $value_commission_request <= 0 ) {
            return array(
                'return' => false,
                'msg' => __( 'The amount can not be zero!', 'learnpress' ),
                'code' => 'MONEY_ZERO',
                'old' => $post_data,
            );
        }

        $min = LPC()->get_commission_min();
        if ( $value_commission_request < $min ) {
            return array(
                'return' => false,
                'msg' => __( 'The amount is too small!', 'learnpress' ),
                'code' => 'TOO_SMALL',
                'old' => $post_data,
            );
        }

        $method_key = isset( $_POST['lp_payment_method'] ) && $_POST['lp_payment_method'] ? $_POST['lp_payment_method'] : '';
        if ( !$method_key || empty( $method_key ) ) {
            return array(
                'return' => false,
                'msg' => __( 'Please choose a payment method!', 'learnpress' ),
                'code' => 'CHOOSE_METHOD',
                'old' => $post_data,
            );
        }

        $all_method = self::get_withdrawal_methods();
        $method = array(
            $method_key => $all_method[$method_key]
        );

        $new_withdrawal_id = $this->newWithdrawal( $email, $value_commission_request, $method );
        if ( is_wp_error( $new_withdrawal_id ) ) {
            return array(
                'return' => false,
                'msg' => __( 'Create the withdrawal request failed!', 'learnpress' ),
                'code' => 'CREATE_WITHDRAWAL_ERROR',
                'old' => $post_data,
            );
        }

        /**
         * No any error. Subtract commission right here.
         */
        $update = lp_commission_subtract_commission( $current_user->ID, $value_commission_request );
        if ( !$update ) {
            return array(
                'return' => false,
                'msg' => __( 'Something went wrong!', 'learnpress' ),
                'code' => 'ERROR_UPDATE',
                'old' => $post_data,
            );
        }

        return array(
            'return' => true,
            'msg' => __( 'Withdrawals request is successful!', 'learnpress' ),
        );
    }

    private function newWithdrawal( $email, $value, $method, $user_id = null ) {
        if ( empty( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        $user = get_user_by( 'ID', $user_id );
        $user_data = $user->data;
        $user_display_name = $user_data->display_name;

        $now = new DateTime();
        $time_request = $now->format( 'd/m/Y' );

        $title = $user_display_name . ' - ' . $time_request . ' - ' . $value . learn_press_get_currency_symbol();

        $new_withdrawal = wp_insert_post( array(
            'post_title' => $title,
            'post_content' => $email,
            'post_type' => self::$key_post_type,
            'post_status' => 'publish',
            'post_author' => $user_id,
            'meta_input' => array(
                'lp_value' => $value,
                'lp_status' => 'pending',
                'lp_time_request' => time(),
                'lp_payment_method' => $method,
            )
                ) );

        return $new_withdrawal;
    }

    public static function get_withdrawals_by_user_id( $user_id ) {
        // WP_Query arguments
        $args = array(
            'post_type' => array( 'lp_withdraw' ),
            'author' => $user_id,
            'posts_per_page' => - 1
        );

        // The Query
        $query = new WP_Query( $args );

        $histories = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post = $query->post;
                $post_id = $post->ID;
                $withdrawal = new LP_Withdrawal( $post_id );

                $date_format = get_option( 'date_format' );
                $date_format = apply_filters( 'lp_commission_date_format', $date_format );
                $time_format = get_option( 'time_format' );
                $time_format = apply_filters( 'lp_commission_time_format', $time_format );
                $datetime_format = $time_format . ' ' . $date_format;
                $datetime_format = apply_filters( 'lp_commission_datetime_format', $datetime_format );

                $time_request = $withdrawal->get_time_request();
                $time_request_str = $time_request->format( $datetime_format );

                $time_resolve = $withdrawal->get_time_resolve();
                $time_resolve_str = '-:-:- -/-/-';
                if ( $time_resolve ) {
                    $time_resolve_str = $time_resolve->format( $datetime_format );
                }

                $status_str = $withdrawal->get_title_status();
                $value = $withdrawal->get_value();
                $method = $withdrawal->get_title_method_payment();

                $history = array(
                    'ID' => $post->ID,
                    'title' => $post->post_title,
                    'time_request' => $time_request_str,
                    'time_resolve' => $time_resolve_str,
                    'status' => $status_str,
                    'value' => $value,
                    'method_title' => $method,
                );

                $histories[] = $history;
            }
            wp_reset_postdata();
        }

        return $histories;
    }

    public static function get_payment_methods() {
        $gateways = self::get_gateways();
        $methods = $gateways;
        if ( LPC()->support_offline_payment() ) {
            $methods['offline'] = __( 'Offline', 'learnpress' );
        }
        return $methods;
    }

    public static function get_gateways() {
        $available = LP_Gateways::instance()->get_availabe_gateways();
        $arr = array();
        foreach ( $available as $key => $a ) {
            $arr[$key] = $a->method_title;
        }
        return $arr;
    }

    public static function get_withdrawal_methods() {
        $methods = array(
            'paypal' => __( 'Paypal', 'learnpress' ),
//			'skrill'=>__('Skrill (Moneybookers)','learnpress')
        );

        if ( LPC()->support_offline_payment() ) {
//			$methods['offline'] = __( 'Offline', 'learnpress' );
        }
        return $methods;
    }

    public static function get_withdrawal_form( $method, $total, $min, $currency ) {
        $class_name = 'LP_Commission_Withdrawal_Method_' . $method;
        if ( !class_exists( $class_name ) ) {
            require LP_ADDON_COMMISSION_PATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'lp-commission-withdrawal-method-' . $method . '.php';
        }
        if ( !class_exists( $class_name ) ) {
            return '';
        }
        $html = call_user_func_array( array( $class_name, 'getForm' ), array( $total, $min, $currency ) );
        return $html;
    }

    public static function add_columns_header( $columns ) {
        return array_merge( $columns, array( 'status' => __( 'Publisher' ) ) );
    }

    public static function add_columns_data( $column, $post_id ) {
        $all_stats = LP_Withdrawal::get_all_status();
        switch ( $column ) {
            case 'status' :
                $status = get_post_meta( $post_id, 'lp_status', true );

                if ( empty( $status ) ) {
                    $status = 'pending';
                }
                echo $all_stats[$status];
                break;
        }
    }

    public static function checkUserPass( $username, $password ) {
        if ( !$username || !$password ) {
            return false;
        }
        $cuser = wp_get_current_user();
        require_once( ABSPATH . 'wp-includes/class-phpass.php');
        $wp_hasher = new PasswordHash( 8, TRUE );
        if ( $wp_hasher->CheckPassword( $password, $cuser->data->user_pass ) ) {
            return true;
        } else {
            return false;
        }
    }

}

function LP_RW() {
    return LP_Request_Withdrawal::instance();
}

LP_RW();
