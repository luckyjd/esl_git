<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class LP_Settings_Commission extends LP_Settings_Base {

    /**
     * @var null
     */
    protected static $_instance = null;

    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'commission';
        $this->text = __( 'Commissions', 'learnpress' );
        parent::__construct();
    }

    /**
     * Return unique instance of LP_Settings_Commission
     */
    public static function instance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Tab's sections
     *
     * @return mixed
     */
    public function get_sections() {
        $sections = array(
            'general' => array(
                'id' => 'general',
                'title' => __( 'Settings', 'learnpress' )
            ),
            'manage' => array(
                'id' => 'manage',
                'title' => __( 'Managing', 'learnpress' )
            ),
            'withdrawal_paypal' => array(
                'id' => 'withdrawal_paypal',
                'title' => __( 'Withdrawal Paypal', 'learnpress' )
            ),
        );

        return apply_filters( 'learn_press_settings_sections_' . $this->id, $sections );
    }

    public function output_section_general() {
        include LP_ADDON_COMMISSION_PATH . '/inc/views/commission.php';
    }

    public function output_section_withdrawal_paypal() {
        include LP_ADDON_COMMISSION_PATH . '/inc/views/withdrawal_paypal.php';
    }

    public function output_section_manage() {
        wp_enqueue_script( 'datatables', '//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js', array( 'jquery' ), false, true );
        wp_enqueue_style( 'datatables', '//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css' );
        include LP_ADDON_COMMISSION_PATH . '/inc/views/manager.php';
    }

    public function get_settings() {
        return apply_filters(
                'learn_press_commission_settings', array(
            array(
                'title' => __( 'Enable commission feature', 'learnpress' ),
                'desc' => __( 'Enable commission feature', 'learnpress' ),
                'id' => $this->get_field_name( 'enable_commission' ),
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __( 'Commission percent', 'learnpress' ),
                'desc' => __( 'Commission percent', 'learnpress' ),
                'id' => $this->get_field_name( 'commission_percent' ),
                'default' => 0,
                'type' => 'number',
                'custom_attributes' => array(
                    'min' => 0,
                    'max' => 100
                )
            ),
            array(
                'title' => sprintf( __( 'Min (%s)', 'learnpress' ), learn_press_get_currency_symbol() ),
                'desc' => __( 'Min (%s)', 'learnpress' ),
                'id' => $this->get_field_name( 'commission_min' ),
                'default' => 1,
                'type' => 'number',
                'custom_attributes' => array(
                    'min' => 0,
                )
            ),
            array(
                'title' => __( 'Support offline payment', 'learnpress' ),
                'desc' => __( 'Enable/Disable offline payment', 'learnpress' ),
                'id' => $this->get_field_name( 'commission_offline_payment' ),
                'default' => 'yes',
                'type' => 'checkbox',
            ),
                )
        );
    }

    public function get_withdrawal_paypal() {
        return apply_filters(
                'learn_press_commission_setting_withdrawal_paypal', array(
            array(
                'title' => __( 'Enable', 'learnpress' ),
                'desc' => __( 'Enable/Disable withdrawal via paypal', 'learnpress' ),
                'id' => $this->get_field_name( 'commission_enable_paypal_withdrawal_method' ),
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __( 'Sandbox Mode', 'learnpress' ),
                'desc' => __( 'Enable/Disable Sandbox Mode', 'learnpress' ),
                'id' => $this->get_field_name( 'commission_enable_paypal_sandbox_mode' ),
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __( 'Client ID', 'learnpress' ),
                'desc' => __( 'Client ID is generated in PayPal\'s REST API apps. You can create REST API apps at https://developer.paypal.com/developer/applications/', 'learnpress' ),
                'id' => $this->get_field_name( 'commission_paypal_app_client_id' ),
                'default' => '',
                'type' => 'text',
            ),
            array(
                'title' => __( 'Secret', 'learnpress' ),
                'desc' => __( 'Secret Key is generated in PayPal\'s REST API apps. You can create REST API apps at https://developer.paypal.com/developer/applications/', 'learnpress' ),
                'id' => $this->get_field_name( 'commission_paypal_app_secret' ),
                'default' => '',
                'type' => 'text',
            ),
                )
        );
    }

}

add_filter( 'learn_press_settings_class_commission', 'lp_pmpro_filter_class_setting_commission' );

function lp_pmpro_filter_class_setting_commission() {
    return 'LP_Settings_Commission';
}

return new LP_Settings_Commission();
