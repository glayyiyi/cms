<?php
/*
Plugin Name: 微信机器人高级版-扩展
Plugin URI: http://blog.wpjam.com/project/weixin-robot-ext/
Description: 微信机器人第三方插件。
Version: 1.0
Author: Glay
Author URI: http://blog.wpjam.com/
*/

add_action('init', 'wpjam_weixin_auth_redirect', 11);
function wpjam_weixin_auth_redirect($wp){
	if(isset($_GET['weixin-oauth2']) ){
		$request = new WP_Http;
		
		$tkn_url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx29f139b356296675&secret=SECRET&code=".$_GET['code']."&grant_type=authorization_code"
		echo "=====".$tkn_url;
		$result = $request->request ( $tkn_url );
		
		$json = $result ['body'];
		
		$json_arr = json_decode ( $json, true );
		$refresh_token  = $json_arr ['refresh_token'];
		$openid = $json_arr ['openid'];
		
		$refresh_token_url="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=wx29f139b356296675&grant_type=refresh_token&refresh_token=".$refresh_token;
		$result = $request->request ( $refresh_token_url );
		$json = $result ['body'];
		$json_arr = json_decode ( $json, true );
		$access_token  = $json_arr ['access_token'];
		
		
		
		$user_info_url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
		
		$result = $request->request ( $user_info_url );
		$json = $result ['body'];
		$json_arr = json_decode ( $json, true );
		echo "OPENID=".$openid."========";
		print_r($json_arr);
		exit;
		
		
	}
}

add_filter('woocommerce_paypal_args', 'convert_rmb_to_usd');
function convert_rmb_to_usd($paypal_args){
	if ( $paypal_args['currency_code'] == 'RMB'){
		$convert_rate = 6.09028235; //Set converting rate
		$paypal_args['currency_code'] = 'USD';// Set currency code
		$paypal_args['amount_1'] = round( $paypal_args['amount_1'] / $convert_rate, 2); //Convert product price
		$paypal_args['amount_2'] = round( $paypal_args['amount_2'] / $convert_rate, 2); //Convert shipping costs
	}
	return $paypal_args;
}

// 如果接口里action不为空，表示仅为记录用，不用设为未审批的回复
// Remove upgrade tip messages.////////修改后台显示更新的代码，，，，
// add_filter('pre_site_transient_update_core', create_function('$a', "return null;")); // 关闭核心提示
// add_filter('pre_site_transient_update_plugins', create_function('$a', "return null;")); // 关闭插件提示
// add_filter('pre_site_transient_update_themes', create_function('$a', "return null;")); // 关闭主题提示
// remove_action('admin_init', '_maybe_update_core'); // 禁止 Wordpress 检查更新
// remove_action('admin_init', '_maybe_update_plugins'); // 禁止 Wordpress 更新插件
// remove_action('admin_init', '_maybe_update_themes'); // 禁止 Wordpress 更新主题
function remove_admin_bar_links() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu ( 'wp-logo' );
	// $wp_admin_bar->remove_menu('wp-logo-external');
	// $wp_admin_bar->remove_menu('view-site');
	// $wp_admin_bar->remove_menu('new-content');
	// $wp_admin_bar->remove_menu('comments');
}
add_action ( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

function enable_duplicate_comments_preprocess_comment($comment_data)
{
	//add some random content to comment to keep dupe checker from finding it
	$random = md5(time());
	$comment_data['comment_content'] .= "disabledupes{" . $random . "}disabledupes";
	return $comment_data;
}
add_filter('preprocess_comment', 'enable_duplicate_comments_preprocess_comment');
function enable_duplicate_comments_comment_post($comment_id)
{
	global $wpdb;
	//remove the random content
	$comment_content = $wpdb->get_var("SELECT comment_content FROM $wpdb->comments WHERE comment_ID = '$comment_id' LIMIT 1");
	$comment_content = preg_replace("/disabledupes\{.*\}disabledupes/", "", $comment_content);
	$wpdb->query("UPDATE $wpdb->comments SET comment_content = '" . $wpdb->escape($comment_content) . "' WHERE comment_ID = '$comment_id' LIMIT 1");
	/*
	 add your own dupe checker here if you want
	*/
}
add_action('comment_post', 'enable_duplicate_comments_comment_post');



function save_crm_event($post_ID, $post) {
	// $post = get_post ( $post_ID );
	// print_r($post);
	$post_cats = get_the_category ( $post_ID );
	$message_status = ($post->post_status == 'publish') ? 'MESSAGE_SEND' : 'MESSAGE_DRAFT';
	$img_url = get_post_first_image ( $post->post_content );
	$excerpt = get_post_excerpt ( $post, 320 );
	$link = get_permalink ();
	// echo '类别＝＝＝＝';
	// print_r ( $post_cats [0]->slug );
	$current = get_option ( 'wp-wechat-basic' );
	$crm_url = $current ['weixin_default'];
	$parentOpenId = $current ['weixin_original_account'];

	if ($post_cats [0]->slug == 'cat_wx_event') {
		$request = new WP_Http ();
		// $status = strip_tags( $_POST['post_title'] ) . ' ' . urlencode( get_permalink($post_ID) );
		// $headers = array( 'Authorization' => 'Basic ' . base64_encode("$username:$password") );

		$crm_event_id = get_post_meta ( $post_ID, 'crm-event-id', true );
		echo '原crm_event_id＝＝＝＝＝＝＝' . $crm_event_id;
		if (! $crm_event_id) {
			$api_url = $crm_url . '/api/message/createMessage.n';
			$post_body = array (
					'body' => array (
							'message' => array (
									'title' => $post->post_title,
									'messageTypeEnumId' => 'MESSAGE_TYPE_EVENT_NOTICE',
									'summary' => $excerpt,
									'statusId' => $message_status,
									'content' => $post->post_content,
									'imageUrl' => $img_url
							),
							'sendMessage' => true
					)
			);
			$body = array (
					'json' => json_encode ( $post_body )
			);
				
			$result = $request->post ( $api_url, array (
					'body' => $body
			) );
				
			$result_array = json_decode ( $result ['body'], true );
			$result_code = $result_array ['code'];
			// echo '＝＝＝＝＝新增CRM Event结果：'.$result_code;
			if ($result_code == "0") {
				$crm_event_id = $result_array ['body'] ['id'];
				add_post_meta ( $post_ID, 'crm-event-id', $crm_event_id, true );

				echo '＝＝＝＝＝新增成功＝＝＝＝＝';
			} else {
				// echo '新增Event失败,状态从'.$post->post_status.'变为draft';
				// $post->post_status = 'draft';
				// wp_update_post($post);
				exit ( '新增CRM Event失败！' );
			}
		}
		if ($crm_event_id) {
			$cotent_ext = $crm_url . '/web/l/weiXinParty/caiZhi/fetchEvent.n?messageId=' . $crm_event_id . '&parentOpenId=' . $parentOpenId;
			$url_body = array (
					'data' => $cotent_ext
			);
				
			$url_result = $request->post ( 'http://nutz.cn/api/create/url', array (
					'body' => $url_body
			) );
			$url_result_array = json_decode ( $url_result ['body'], true );
			$url_result_code = $url_result_array ['ok'];
				
			if ($url_result_code == true)
				$short_cotent_ext = 'http://nutz.cn/' . $url_result_array ['code'];
				
			$api_url = $crm_url . '/api/message/editMessage.n';
			$post_body = array (
					'body' => array (
							'message' => array (
									'id' => $crm_event_id,
									'title' => $post->post_title,
									'messageTypeEnumId' => 'MESSAGE_TYPE_EVENT_NOTICE',
									'summary' => $excerpt,
									'statusId' => $message_status,
									'content' => $post->post_content . ' 能否出席请回复短信，或点击' . $short_cotent_ext,
									'imageUrl' => $img_url
							),
							'sendMessage' => false
					)
			);
				
			$body = array (
					'json' => json_encode ( $post_body )
			);
				
			$result = $request->post ( $api_url, array (
					'body' => $body
			) );
				
			$result_array = json_decode ( $result ['body'], true );
			$result_code = $result_array ['code'];
			// echo '＝＝＝＝＝编辑CRM Event结果：';
			// var_dump($result_code);
			if ($result_code == "0") {
				echo '＝＝＝＝＝编辑成功＝＝＝＝＝';
			} else {
				// echo '编辑Event失败,状态从'.$post->post_status.'变为draft';
				// $post->post_status = 'draft';
				// wp_update_post($post);
				exit ( '编辑CRM Event失败！' );
			}
		}
		// echo 'API URL========' . $api_url;
		// echo 'POST BODY========';
		// print_r ( $body);
		// echo '＝＝＝＝＝＝保存CRM Event结果＝＝crm_event_id＝＝'.$crm_event_id;
		// print_r ( $result_array );
	}
}
add_action ( 'save_post', 'save_crm_event', 10, 2 );
