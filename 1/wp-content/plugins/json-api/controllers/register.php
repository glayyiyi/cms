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
		global $wpdb;
		global $mycred;

		$device_id = $_GET['device_id'];
		$exist_users = get_users(array(
					'meta_key'     => 'device_id',
					'meta_value'   => $device_id));

		$user_is_empty = empty($exist_users);

		if ( ! class_exists( 'myCRED_Settings' ) ) {
			$json_api->error(" not found myCRED" );
		}


		$signInToday = true;

		if(!$user_is_empty){
			$userid = $exist_users[0]->id;
			if ( class_exists( 'myCRED_Hook_Logging_In' ) ) {
				$mycred1 = new myCRED_Hook_Logging_In();
				if ( $mycred1->reward_login( $userid , false) )
					$signInToday = false;
			}

			return array(
					"uid" => $userid,
					"loginname" => $exist_users[0]->user_login,
					"points" => $mycred->get_users_cred( $userid, '' ),
					"referral_id" => get_user_meta($userid, 'referral_id', true),
					"havePassword" => get_user_meta($userid, 'havePassword', true),
					"qq" => get_user_meta($userid, 'qq', true),
					"alipay" => get_user_meta($userid, 'alipay', true),
					"haveSignInToday" => $signInToday,
					"mobile" => get_user_meta($userid, 'mobile', true)
				    );
		}



		$usercount =  $wpdb->get_var('select  MAX(id) from wp_users');  //get_option('usercount');
		$username = sanitize_user(empty($usercount) ?10000:($usercount+1) );

		if ( empty( $username ) || ! validate_username( $username ) ) {
			return array("status" =>  'error'
					, "message" => '请输入注册的账号名'
				    );
		}

		if ( username_exists( $username ) )
			return array("status" =>  'error'
					, "message" => '注册的账号已存在，请重新选择'
				    );

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
			return array("status" =>  'error'
					, "message" => '注册账号失败'
				    );
		}else{
			// give some points.
		}


		update_user_meta($customer_id, 'device_id', $device_id);
		update_user_meta($customer_id, 'device_type', $_GET['device_type'] );
		$refer_id = $_COOKIE['referral_id'];
		if (!class_exists( 'myCRED_Settings' )){
			return array("status" =>  'error'
					, "message" => '注册账号失败'
				    );
		}
		$credSetting = new myCRED_Settings();
		$credSetting->add_creds('register', $customer_id, 10, '注册账号');

		if (isset($refer_id)) {
			$credSetting->add_creds('inviter', $refer_id, 10, '推荐其他人注册', $customer_id);
			update_user_meta($customer_id, 'referral_id', $refer_id);
		}

		//update_user_meta($customer_id, 'havePassword', false);


		return array("uid" =>  $customer_id 
				, "points" => $mycred->get_users_cred( $customer_id, '' )
				, "key" => $randstr
			    );
	}

	public function login(){
		global $mycred;

		//			wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-login' );

		try {
			$creds  = array();

			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_GET['uid'], $_GET['password'] );

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
				return array('status'=> 'error', 'message'=> __( 'username or password is wrong', 'woocommerce' ) );
			} else {
				$userid = $user->ID;
				$signInToday = true;
				if ( class_exists( 'myCRED_Hook_Logging_In' ) ) {
					$mycred1 = new myCRED_Hook_Logging_In();
					if ( $mycred1->reward_login( $user->id , false) )
						$signInToday = false;
				}
				return array(
						"uid" => $userid,
						"loginname" => $user->user_login,
						"points" => $mycred->get_users_cred( $userid, '' ),
						'referral_id' => get_user_meta($userid, 'referral_id', true),
						'qq' => get_user_meta($userid, 'qq', true),
						'alipay' => get_user_meta($userid, 'alipay', true),
						'haveSignInToday' => $signInToday,
						'mobile' => get_user_meta($userid, 'mobile', true)
					    );
			}

		} catch (Exception $e) {
			//				wc_add_notice( apply_filters('login_errors', $e->getMessage() ), 'error' );
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

	function modify_user($result){
		$userid = $result['uid'];
		//	echo '    \n'.$result['uid'];
		if (empty($userid)){
			return array('status'=>'error', 'message'=>__( 'user does not exist', 'woocommerce' ));
		}
		$user = get_user_by('id', $userid);
		$userid = $user->ID;
		$qq = $result['qq'];
		if (!empty($qq)){
			update_user_meta($userid, 'qq', $qq);
		}
		$alipay = $result['alipay'];
		if (!empty($alipay)){
			update_user_meta($userid, 'alipay', $alipay);
		}

		$mobile = $result['mobile'];
		if (!empty($mobile)){
			update_user_meta($userid, 'mobile', $mobile);
			//wp_update_user( array ( 'id' => $userid, 'user_login' => $mobile ) ) ;
		}

		$refid = $result['referral_id'];
		if (!empty($refid)){
			update_user_meta($userid, 'referral_id', $refid);
		}
		return array('message'=>'success');
	}

	function addInviter(){
		$referral = $_POST['referral_id'];
		$user = get_user_by('login', $referral);

		if ( isset( $user->user_login ) ){
			$_POST['referral_id'] = $user->ID;
			return $this::modify_user($_POST);
		} else {
			return array('status'=>"error", 'message'=>__( 'inviter account does not exist', 'woocommerce' ));
		}
	}

	function modifyuser(){
		$data = $GLOBALS['HTTP_RAW_POST_DATA'];
		$result = json_decode(trim($data), true);
		//echo '    \n'.$result;
		//var_dump($result);
		return $this::modify_user($result);
	}

	function reset_password(){
		$data = $GLOBALS['HTTP_RAW_POST_DATA'];
		$result = json_decode(trim($data), true);
		$password_is_reset = get_user_meta($result['uid'], 'havePassword', true);
		if( $password_is_reset == 'true' && empty($result['key'] )){
			return array('status' =>'error', 'msg' =>__( 'Please input old password', 'woocommerce' ));
		}
		if( empty($password_is_reset ) && empty($result['key']) )
			$user = get_user_by('id', $result['uid'] );
		else
			$user = wp_authenticate( $result['uid'], $result['key'] );

		if ( is_wp_error( $user ) ) {
			return array('status'=>'error', 'msg'=>$user->get_error_message());
		}
		//$user = WC_Shortcode_My_Account::check_password_reset_key( $result['key'], $result['uid'] );
		//if ( !is_object( $user ) ) {
		//	return array('status' =>'error', 'msg'=>__( 'Password reset is not allowed for this user', 'woocommerce' ));
		//}

		WC_Shortcode_My_Account::reset_password( $user, wc_clean( $result['newkey'] ) );
		do_action( 'woocommerce_customer_reset_password', $user );
		update_user_meta($user->id, 'havePassword', 'true');


		return array('message'=>__( 'Your password has been reset.', 'woocommerce' ));
	}

	function domob_request_adfeed( ){
		//$url = "http://".get_option( 'domob_adfeed_url' )."?ipb=".$ipb;
		$url = "http://r.ow.domob.cn/ow/interface/common/adfeed".get_option( 'domob_adfeed_url' )."?ipb=96ZJ1pPwzeOxHwTAII";

		$json_ret = file_get_contents($url);
		$result = json_decode(trim($json_ret));
		$num = count($result);
		$resultArray = array();


       // $current_blog = '';
       // if (is_multisite()){
          //  $uri = $_SERVER['REQUEST_URI'];
          //  $blog_list = get_blog_list( 0, 'all' ); //显示全部站点列表

            // foreach ($blog_list AS $blog)
            // {
              //  $ret = strpos($uri, $blog['path']);

               // if($ret === 0) {
                //    $current_blog = $blog['blog_id'];
              //      break;
            //    }
          //  }
        //}


		$uid = $_REQUEST['uid'];
		global $wpdb;
		$table = $wpdb->prefix.'mycred_log';
        $sql = 'SELECT ref_id FROM '.$table.' WHERE ref = %s and user_id= %s;';
		$refs = $wpdb->get_col( $wpdb->prepare( $sql, 'download', $uid) );

		if ( $refs ) {
			$refs = array_unique( $refs );
		}

		for($i=0;$i<$num;++$i){
			$key = $result[$i]->app_store_url;
			$existResult = $resultArray[$key];
			$isEmpty = empty($existResult);

			if ( $isEmpty || $existResult->point < $result[$i]->point){
				if (!$isEmpty){
					$result[$i]->banner_image_url = $existResult->banner_image_url;
				}
				$existResult = $result[$i];
			}

			if (empty($existResult->banner_image_url)){
				$existResult->banner_image_url = "";
			}

            $existResult->installed = in_array($existResult->adid, $refs);
            $resultArray[$key] = $existResult;
        }
        return array("data"=>array_values($resultArray));
    }

    function list_banner(){
        $args = array(
            'category'        => '58',
            'orderby'         => 'post_date',
            'order'           => 'DESC',
        );
        $posts_array = get_posts( $args );
        $result_array = array();
        for ($i=0;$i<count($posts_array);++$i){
            $post_id = $posts_array[$i]->ID;

            $img_id = get_post_thumbnail_id($post_id); // 35 being the ID of the Post
            $img_url = wp_get_attachment_image_src($img_id, array(320, 150));
            $img_url = $img_url[0];

            $attachments = wp_get_attachment_url($post_id );

            $post = array();
            $post['img_url'] = $img_url;
            if($attachments){
                $post['attach_url'] = $attachments;
            }

            $result_array[] = $post;
        }

        return array("banner_list" =>$result_array);
    }


}

?>
