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
  	  
  		$device_id = $_POST['device_id'];
		$exist_users = get_users(array(
			'meta_key'     => 'device_id',
			'meta_value'   => $device_id)); 
		$user_is_empty = empty($exist_users);
  	  	if(!$user_is_empty){
  	  	  return array(
    		"user" => $exist_users[0]);
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

	$new_customer_data = apply_filters( 'woocommerce_new_customer_data', array(
		'user_login' => $username,
		'user_pass'  => '11111',
		//'user_email' => $email,
		'role'       => 'customer'
	) );

	$customer_id = wp_insert_user( $new_customer_data );

	if ( is_wp_error( $customer_id ) ) {
		return new WP_Error( 'registration-error', '<strong>' . __( 'ERROR', 'woocommerce' ) . '</strong>: ' . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'woocommerce' ) );
	}
	
	if ($user_is_empty){
  	  	  update_usermeta($customer_id, 'device_id', $device_id);
  	  	  update_usermeta($customer_id, 'device_type', $_POST['device_type'] );
  	  	  update_usermeta($customer_id, 'referral_id', $_COOKIE['referral_id'] );
  	}
	
	return array("uid" => get_userdata( $customer_id));
  }
  
  public function login(){
  
//			wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-login' );

			try {
				$creds  = array();

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['uid'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $_POST['uid'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Username is required.', 'woocommerce' ) );
				}

				if ( empty( $_POST['password'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Password is required.', 'woocommerce' ) );
				}

				if ( is_email( $_POST['uid'] ) ) {
					$user = get_user_by( 'email', $_POST['uid'] );

					if ( isset( $user->user_login ) ) {
						$creds['user_login'] 	= $user->user_login;
					} else {
						throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'A user could not be found with this email address.', 'woocommerce' ) );
					}

				} else {
					$creds['user_login'] 	= $_POST['uid'];
				}

				$creds['user_password'] = $_POST['password'];
				$creds['remember']      = isset( $_POST['rememberme'] );
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
        $mobile = $_POST['mobile'];
        $xml = '<?xml version="1.0" encoding="UTF-8"?><MtPacket><cpid>010000001969</cpid><mid>0</mid><cpmid>'.time().'</cpmid><mobile>'.$mobile.'</mobile><port>010121</port><msg>'.$captcha.'（果子助手验证码，十分钟内有效）【爱普科技】</msg><signature>'.md5('d7d32d62942801a3811e297db1a81164'.$time).'</signature><timestamp>'.$time.'</timestamp><validtime>0</validtime></MtPacket>';

        $url = 'http://api.10690909.com/providermt';
//接收XML地址

        $header = 'Content-type: text/xml';
//定义content-type为xml
        $ch = Curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);
//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//设置是否返回信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, 1);
//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
//POST数据
        $response = curl_exec($ch);
//接收返回信息
        if(curl_errno($ch)){
//出错则显示错误信息
            return array('status'=>'error', 'message'=>curl_error($ch));
        } else {
            update_option($mobile, $captcha);
        }
        curl_close($ch);
//关闭curl链接
        
        $xml = simplexml_load_string($response);		
		$result = (string) $xml->result;
        
        return array('result'=>$result);
    }

function verifypass(){
	$user = wp_authenticate( $_POST['uid'], $_POST['password'] );
	if ( is_wp_error( $user ) ) {
		return array('status'=>'error', 'message'=>$user->get_error_message());
	}
	return array('message'=> 'success');
}

function modifyuser(){
	$userid = $_POST['id'];
	if (empty($userid)){
		return array('status'=>'error', 'message'=>__( 'user does not exist', 'woocommerce' ));
	}
	
	$qq = $_POST['qq'];
	if (!empty($qq)){
		update_usermeta($userid, 'qq', $qq);
	}
	$alipay = $_POST['alipay'];
	if (!empty($alipay)){
		update_usermeta($userid, 'alipay', $alipay);
	}
	
	$mobile = $_POST['mobile'];
	if (!empty($mobile)){
		wp_update_user( array ( 'id' => $userid, 'user_login' => $mobile ) ) ;		
	}
	
	$refid = $_POST['refid'];
	if (!empty($refid)){
		update_usermeta($userid, 'refid', $refid);
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
