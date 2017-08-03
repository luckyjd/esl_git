<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/6/2016
 * Time: 9:40 AM
 */
class LP_Commission_Payment {

	private static $_instance = null;
	private $paypal_url = 'https://www.sandbox.paypal.com/';

	public static function instance() {
		if ( !self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * This function do no things
	 * @param type $withdrawal_id
	 * @return string
	 */
	public function get_request_url( $withdrawal_id ) {
		return '';
	}

	/**
	 * Get access_token
	 * @return json string
	 */
	public static function get_access_token() {
		$settings = LP()->settings;
		$client_id = $settings->get( 'commission_paypal_app_client_id' );
		$client_secret = $settings->get( 'commission_paypal_app_secret' );
		$data = array('grant_type' => 'client_credentials');
		$prefix = self::get_paypal_url_prefix();
		$url = $prefix . '/v1/oauth2/token';
		$httpheader = array('Accept: application/json', 'Accept-Language: en_US');

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $httpheader );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_USERPWD, $client_id . ':' . $client_secret );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 100 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		$res = curl_exec( $ch );

		if ( curl_errno( $ch ) || !$res ) {
			$fields_string = '';
			foreach ( $data as $key => $value ) {
				$fields_string .= $key . '=' . $value . '&';
			}
			rtrim( $fields_string, '&' );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
			$res = curl_exec( $ch );
		}
		curl_close( $ch );
		return $res;
	}

	public static function payment_payouts( $receiver, $value, $sender_item_id ) {
		$email_subject = __( 'You have a payment', 'learnpress' );
		$note = __( 'Payment for withdrawal', 'learnpress' ) . ' ' . $sender_item_id;
		$currency = learn_press_get_currency();
		$settings = LP()->settings;
		$data = <<<MIT
{
	"sender_batch_header":{
		"email_subject":"{$email_subject}"
	},
	"items":[
		{
			"recipient_type":"EMAIL",
			"amount":{
				"value":{$value},
				"currency":"{$currency}"
			},
			"receiver":"{$receiver}",
			"note":"{$note}",
			"sender_item_id":"{$sender_item_id}"
		}
	]
}
MIT;

		$data = trim( $data );
		$prefix = self::get_paypal_url_prefix();
		$url = $prefix . '/v1/payments/payouts?sync_mode=true';
		$access_token_json = self::get_access_token();
		$access_token_obj = json_decode( $access_token_json );
		$access_token = $access_token_obj->access_token;

		$httpheader = array(
			'Accept:application/json',
			'Content-Type:application/json',
			'Authorization:Bearer ' . $access_token);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $httpheader );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_URL, $url );

		curl_setopt( $ch, CURLOPT_TIMEOUT, 100 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		$res = curl_exec( $ch );
		if ( curl_errno( $ch ) ) {
//			echo '<pre>' . print_r( curl_error( $ch ), true ) . '</pre>';
		}
		if ( curl_errno( $ch ) ) {
			$fields_string = 'data=' . $data;
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
			$res = curl_exec( $ch );
		}
		curl_close( $ch );
		return $res;
	}

	public static function get_paypal_url_prefix() {
		$settings = LP()->settings;
		$sandbox_mod = $settings->get( 'commission_enable_paypal_sandbox_mode' );
		$url = '';
		if ( $sandbox_mod == 'yes' ) {
			$url = 'https://api.sandbox.paypal.com';
		} else {
			$url = 'https://api.paypal.com';
		}
		return $url;
	}

}
