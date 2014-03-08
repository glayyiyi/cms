<?php
/*
   The follows is all the operation of the points.


 */
//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JSON_API_Points_Controller{

	public function checkIfsignInToday(){
		global $json_api;

		$user = get_user_by( 'id', $_GET['uid']);
		if ( is_wp_error($user) ) {
			if ( $user->get_error_codes() == array('empty_username', 'empty_password') ) {
				$user = new WP_Error('', '');
				$json_api->error("user not found.");
			}

			$json_api->error("error , not found.");
		}
		if( empty($user->id ) )
			return array( "user_emplty" => $user->user_login);

		if ( class_exists( 'myCRED_Hook_Logging_In' ) ) {
			$mycred = new myCRED_Hook_Logging_In();
			if ( $mycred->reward_login( $user->ID ) ) //return false;
				return array( "status" => "fail");
		}
			return array( "status" => "ok");
		//return true;
	}

	public function  signInPerDay() {

		global $json_api;

		$user = get_user_by( 'id', $_GET['uid']);

		if ( is_wp_error($user) ) {
			if ( $user->get_error_codes() == array('empty_username', 'empty_password') ) {
				$user = new WP_Error('', '');
				$json_api->error("user not found.");
			}

			$json_api->error("error , not found.");
		}
		if( empty($user->id ) )
			return array( "user_emplty" => $user->user_login);

		//wp_set_auth_cookie($user->ID, $credentials['remember'], $secure_cookie);
		//--- use  wp_login ---> mycred_login function, to add creds.
		do_action('wp_login', $user->user_login, $user);

		return array( "user" => $user->user_login);
	}


	public function myPoints(){
		global $json_api;

		$user = get_user_by( 'id', $_GET['uid']);
		if ( is_wp_error($user) ) {
			if ( $user->get_error_codes() == array('empty_username', 'empty_password') ) {
				//$user = new WP_Error('', '');
				$json_api->error("user not found.");
			}

			$json_api->error("error , not found.");
		}

		if( empty($user->id ) )
			return array( "user_emplty" => $user->user_login);

		if ( ! class_exists( 'myCRED_Settings' ) ) {
			$json_api->error(" not found myCRED" );
		}

		$mycred = new myCRED_Settings();
		if ( empty($mycred)  ) {
			$json_api->error(" not found myCRED" );
		}

		return array( "points" => $mycred->get_users_cred( $user->id, '' )  );

	}



	public function  listMyCoins() {

		global $json_api;
		global $wpdb;

		$user = get_user_by( 'id', $_GET['UID']);

		if ( !class_exists( 'myCRED_Query_Log' ) ){
			$json_api->error(" not found myCRED" );
		}

		if ( $user_id === NULL )
			$user_id = get_current_user_id();
		$args = array();
		$args['user_id'] = $user_id;

		if ( $number !== NULL )
			$args['number'] = $number;

		if ( $time !== NULL )
			$args['time'] = $time;

		if ( $ref !== NULL )
			$args['ref'] = $ref;

		if ( $order !== NULL )
			$args['order'] = $order;

		$log = new myCRED_Query_Log($args);

		//$log->result ;
		// Loop
		if ( $this->have_entries() ) {
			$alt = 0;
			$entrys = array();
			foreach ( $this->results as $log_entry ) {
				$entity = array(
						'date' => $log_entry->time,
						'coins' => $log_entry->creds,
						'entry' => $log_entry->entry
					       );
				array_push($entrys, $entity);
			}
		}
		//wp_set_auth_cookie($user->ID, $credentials['remember'], $secure_cookie);

		return array( "count" =>  count($entrys), "logs" => $entrys);
	}
}





?>
