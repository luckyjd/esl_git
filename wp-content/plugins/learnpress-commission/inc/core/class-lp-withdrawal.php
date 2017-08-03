<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 23/6/2016
 * Time: 9:00 AM
 */
class LP_Withdrawal {

    /**
     * The course (post) ID.
     *
     * @var int
     */
    public $ID = 0;

    /**
     * $post Stores post data
     *
     * @var $post WP_Post
     */
    public $post = null;
    public $author_id;

    function __construct( $id ) {
        $this->ID = $id;
        $this->post = get_post( $id );
        $this->author_id = intval( $this->post->post_author );
    }

    private function get_meta( $field ) {
        $value = get_post_meta( $this->ID, $field, true );

        if ( !empty( $value ) ) {
            return is_array( $value ) ? stripslashes_deep( $value ) : stripslashes( wp_kses_decode_entities( $value ) );
        } else {
            return false;
        }
    }

    public function get_status() {
        $status = get_post_meta( $this->ID, 'lp_status', true );

        if ( empty( $status ) ) {
            return 'pending';
        }

        return (string) $status;
    }

    public function is_complete() {
        $status = $this->get_status();

        if ( $status !== 'complete' ) {
            return false;
        }

        return true;
    }

    public function is_reject() {
        $status = $this->get_status();

        if ( $status !== 'reject' ) {
            return false;
        }

        return true;
    }

    public function is_resolve() {
        $status = $this->get_status();
        if ( $status !== 'pending' ) {
            return true;
        }

        return false;
    }

    public function complete() {
        $update = update_post_meta( $this->ID, 'lp_status', 'complete' );
        if ( $update ) {
            $this->resolve();
        }
        return $update;
    }

    public function reject() {
        $update = update_post_meta( $this->ID, 'lp_status', 'reject' );
        if ( $update ) {
            $this->resolve();
            $value = $this->get_value();
            lp_commission_add_commission( $this->author_id, $value );
        }
        return $update;
    }

    public function pending() {
        $update = update_post_meta( $this->ID, 'lp_status', 'pending' );
        if ( $update ) {
            $this->resolve();
        }
        return $update;
    }

    public function payon() {
        #pay withdrawal

        $receiver = $this->post->post_content;
        $value = $this->get_value();
        $sender_item_id = 'WD' . $this->post->ID;
        $res = LP_Commission_Payment::payment_payouts( $receiver, $value, $sender_item_id );
        $res_arr = json_decode( $res, true );
        if ( isset( $res_arr['batch_header']['batch_status'] ) && $res_arr['batch_header']['batch_status'] == 'SUCCESS' && isset( $res_arr['items'][0]['transaction_status'] ) && $res_arr['items'][0]['transaction_status'] == 'SUCCESS' && isset( $res_arr['items'][0]['transaction_id'] ) ) {
//                    var_dump($res_arr);
//			exit();
            $update = update_post_meta( $this->ID, 'lp_status', 'complete' );
            if ( $update ) {
                $this->resolve();
            }
            return $update;
        }
        return false;
    }

    public function resolve() {
        $time = time();
        update_post_meta( $this->ID, 'lp_time_resolve', $time );
    }

    public static function get_all_status() {
        $status = array(
            'pending' => __( 'Pending', 'learnpress' ),
            'complete' => __( 'Complete', 'learnpress' ),
            'reject' => __( 'Reject', 'learnpress' ),
        );

        return $status;
    }

    public function get_title_status() {
        $status = self::get_all_status();
        $key = $this->get_status();

        if ( !array_key_exists( $key, $status ) ) {
            return 'Unknown';
        }

        return $status[$key];
    }

    public static function convert_timestamp_to_datetime( $timestamp ) {
        $offset = get_option( 'gmt_offset' );
        $timestamp += $offset * HOUR_IN_SECONDS;
        $time = new DateTime();
        $time->setTimestamp( $timestamp );

        return $time;
    }

    public function get_time_request() {
        $timestamp = $this->get_meta( 'lp_time_request' );
        $timestamp = floatval( $timestamp );
        $time = self::convert_timestamp_to_datetime( $timestamp );

        return $time;
    }

    public function get_time_resolve() {
        $timestamp = $this->get_meta( 'lp_time_resolve' );
        if ( empty( $timestamp ) ) {
            return null;
        }

        $timestamp = floatval( $timestamp );
        $time = self::convert_timestamp_to_datetime( $timestamp );

        return $time;
    }

    public function get_method_payment() {
        $method = $this->get_meta( 'lp_payment_method' );

        return $method;
    }

    public function get_key_method_payment() {
        $method = $this->get_method_payment();
        reset( $method );
        $method_key = key( $method );

        return $method_key;
    }

    public function get_title_method_payment() {
        $method = $this->get_method_payment();
        $method_title = reset( $method );

        return $method_title;
    }

    public function get_value() {
        $value = $this->get_meta( 'lp_value' );
        $value = floatval( $value );

        return $value;
    }

    public function get_receiver() {
        $value = $this->get_meta( 'lp_withdrawals_email' );
        return $value;
    }

}
