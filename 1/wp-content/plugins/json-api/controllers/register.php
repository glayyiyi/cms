<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094
 * 
 * @param  array  $controllers
 * @return array
 */
class json_api_register_controller {
	public function register_user() {
		global $json_api;
  	  
  		$device_id = $_POST['device_id'];
		$exist_users = get_users(array(
					'meta_key'     => 'device_id',
					'meta_value'   => $device_id)); 
		$user_is_empty = empty($exist_users);

		if ( ! class_exists( 'myCRED_Settings' ) ) {
			$json_api->error(" not found myCRED" );
		}

		$mycred = new myCRED_Settings();
		if ( empty($mycred)  ) {
			$json_api->error(" not found myCRED" );
		}

		if(!$user_is_empty){
			return array(
					"uid" => $exist_users[0]->id, 
					"points" => $mycred->get_users_cred( $exist_users[0]->id, '' )
				    );
		}
  	  
  	  $usercount = get_option('usercount');
  	   $username = sanitize_user(empty($usercount) ?10000:($usercount+1) );

		if ( empty( $username ) || ! validate_username( $username ) ) {
			return new WP_Error( 'registration-error', __( 'Please enter a valid account username.', 'woocommerce' ) );
		}

		if ( username_exists( $username ) )
			return new WP_Error( 'registration-error', __( 'An account is already registered with that username. Please choose another.', 'woocommerce' ) );
	
	// WP Validation
	$validation_errors = new WP_Error();

	$randstr = "";  
	$randpwdLenth = 6;  
	for ($i = 0; $i < $randpwdLenth ; $i++)  
	{  
		$randstr .= chr(mt_rand(65,90));  
	}  

	$new_customer_data = apply_filters( 'woocommerce_new_customer_data', array(
		'user_login' => $username,
		'user_pass'  => $randstr,
		//'user_email' => $email,
		'role'       => 'customer'
	) );

	$customer_id = wp_insert_user( $new_customer_data );

	if ( is_wp_error( $customer_id ) ) {
		return new WP_Error( 'registration-error', '<strong>' . __( 'ERROR', 'woocommerce' ) . '</strong>: ' . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'woocommerce' ) );
	}
	

      update_user_meta($customer_id, 'device_id', $device_id);
      update_user_meta($customer_id, 'device_type', $_POST['device_type'] );
      update_user_meta($customer_id, 'referral_id', $_COOKIE['referral_id'] );
    update_user_meta($customer_id, 'password_is_reset', false);


	return array("uid" =>  $customer_id 
			, "points" => $mycred->get_users_cred( $customer_id, '' )
			, "key" => $randstr
		  );
	
  }
  
  public function login(){
  
//			wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-login' );

			try {
				$creds  = array();

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $_GET['uid'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Username is required.', 'woocommerce' ) );
				}

				if ( empty( $_GET['password'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Password is required.', 'woocommerce' ) );
				}

				if ( is_email( $_GET['uid'] ) ) {
					$user = get_user_by( 'email', $_POST['uid'] );

					if ( isset( $user->user_login ) ) {
						$creds['user_login'] 	= $user->user_login;
					} else {
						throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'A user could not be found with this email address.', 'woocommerce' ) );
					}

				} else {
					$creds['user_login'] 	= $_GET['uid'];
				}

				$creds['user_password'] = $_GET['password'];
				$creds['remember']      = isset( $_GET['rememberme'] );
				$secure_cookie          = is_ssl() ? true : false;
				$user                   = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), $secure_cookie );

				if ( is_wp_error( $user ) ) {
					throw new Exception( $user->get_error_message() );
				} else {
					return array('user'=> $user);					
				}

			} catch (Exception $e) {
				wc_add_notice( apply_filters('login_errors', $e->getMessage() ), 'error' );
			}		
			return array('status'=> 'error');					
  }  
  
    function post_message(){
        $time = date('ymdHi');
        $captcha = rand(1000,9999);
        $mobile = $_GET['mobile'];
        $xml = '<?xml version="1.0" encoding="UTF-8"?><MtPacket><cpid>010000001969</cpid><mid>0</mid><cpmid>'.time().'</cpmid><mobile>'.$mobile.'</mobile><port>010121</port><msg>'.$captcha.'������������֤�룬ʮ��������Ч�������տƼ���</msg><signature>'.md5('d7d32d62942801a3811e297db1a81164'.$time).'</signature><timestamp>'.$time.'</timestamp><validtime>0</validtime></MtPacket>';

        $url = 'http://api.10690909.com/providermt';

        $header = 'Content-type: text/xml';

        $ch = Curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $response = curl_exec($ch);

        if(curl_errno($ch)){
            return array('status'=>'error', 'message'=>curl_error($ch));
        } else {
            update_option($mobile, $captcha);
        }
        curl_close($ch);
        
        $xml = simplexml_load_string($response);		
		$result = (string) $xml->result;
        
        return array('result'=>$result);
    }

function verifypass(){
	$user = wp_authenticate( $_GET['uid'], $_GET['password'] );
	if ( is_wp_error( $user ) ) {
		return array('status'=>'error', 'message'=>$user->get_error_message());
	}
	return array('message'=> 'success');
}

function modifyuser(){
	$userid = $_POST['uid'];
	if (empty($userid)){
		return array('status'=>'error', 'message'=>__( 'user does not exist', 'woocommerce' ));
	}
	$user = get_user_by('user_login', $userid);
    $userid = $user->ID;
	$qq = $_POST['qq'];
	if (!empty($qq)){
		update_user_meta($userid, 'qq', $qq);
	}
	$alipay = $_POST['alipay'];
	if (!empty($alipay)){
		update_user_meta($userid, 'alipay', $alipay);
	}
	
	$mobile = $_POST['mobile'];
	if (!empty($mobile)){
		wp_update_user( array ( 'id' => $userid, 'user_login' => $mobile ) ) ;		
	}
	
	$refid = $_POST['refid'];
	if (!empty($refid)){
		update_user_meta($userid, 'refid', $refid);
	}
	return array('message'=>'success');
}

function reset_password(){
	$user = WC_Shortcode_My_Account::check_password_reset_key( $_POST['key'], $_POST['uid'] );
	if ( !is_object( $user ) ) {
		return array('error'=>__( 'Password reset is not allowed for this user', 'woocommerce' ));
	}
	WC_Shortcode_My_Account::reset_password( $user, wc_clean( $_POST['newkey'] ) );
	do_action( 'woocommerce_customer_reset_password', $user );
	
	return array('message'=>__( 'Your password has been reset.', 'woocommerce' ));
}


  
}

?>
