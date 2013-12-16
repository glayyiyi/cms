<?php
/*
 * Plugin Name: 微服务 
 * Plugin URI: http://commerce.duapp.com/project/wp-wechat/ 
 * Description: 微服务的主要功能就是能够将你的公众账号和你的业务系统联系起来，搜索和用户发送信息匹配的内容，并自动回复用户，让你使用微信进行营销事半功倍。 
 * Version: 2.0 
 * Author: Glay 
 * URI:http://commerce.duapp.com/
 */
require_once ('wp-wechat-wechat.php');
require_once ('wp-wechat-account.php');
// 定义微信 Token
define ( "WEIXIN_TOKEN", "weixin" );
//请回复 h或help 查看帮助。或点击<a href='" . get_option ( 'siteurl' ) . "/?weixin-api&weixin-openid=WEIXIN_OPENID'>关于</a>了解我们。
//define ( "WEIXIN_DEFAULT_WELCOME", "爱普精选，必属精品！爱普精选全球最前沿、最新潮的实用科技产品，为粉丝们传递全球精品科技生活！爱普精选的科技消费品，无论是个人实用、时尚把玩，还是商务馈赠，都是好品。本期推荐<a href='http://www.appcn100.com/cms/product/soundlink_mini?weixin-api&weixin-openid=WEIXIN_OPENID'>BOSE出品浑厚一体超级震撼mini无线音箱soundlink mini</a>" );

define ( "WEIXIN_DEFAULT_WELCOME", "©爱普世纪科技");
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


add_action ( 'pre_get_posts', 'wpjam_wechat_redirect', 4 );
function wpjam_wechat_redirect($wp_query) {
	if (isset ( $_GET ['weixin-address'] )) {
		$url = "http://api.map.baidu.com/geocoder?address=" . urldecode ( $_GET ['weixin-address'] ) . "&output=html&src=appo2o|appo2o";
		echo "<SCRIPT LANGUAGE='JavaScript'>";
		echo "location.href='$url'";
		echo "</SCRIPT>";
		exit ();
	}
	
	if (isset ( $_GET ['weixin-register'] ) && ! is_admin () && $wp_query->is_main_query ()) {
		// echo '=======开始创建用户======' . $_GET ['wx_register'];
		// wp_create_user ( $_GET ['wx_register'], '1234', 'wwwww' );
		$userdata = array (
				'user_login' => $_GET ['weixin-register'],
				'user_pass' => "1234",
				'user_email' => $_GET ['weixin-register'] . "@appcn100.com",
				'user_url' => "",
				'role' => 'subscriber' 
		);
		$user_id = wp_insert_user ( $userdata );
		
		if (is_int ( $user_id ))
			exit ( '1' );
		else
			exit ( '0' );
	}
	
	if (isset ( $_GET ['weixin-api'] ) && ! is_admin () && $wp_query->is_main_query ()) {
		
		global $wechatObj;
		global $weAccount;
		
		if (! isset ( $wechatObj )) {
			$weixin_token = apply_filters ( 'weixin_token', WEIXIN_TOKEN );
			
			$wechatObj = new wechatCallback ( $weixin_token, FALSE );
			
			if (isset ( $_GET ['weixin-search'] )) {
				//echo "===开始search====";
				// $keyword = $_GET ['weixin-search'];
				// $fromUsername = $_GET ['fromUsername'];
				// $toUsername = $_GET ['toUsername'];
				// $action = $_GET ['action'];
				
				// if (! $keyword || ! $fromUsername || ! $toUsername)
				// exit ( "错误，参数不全！ weixin-search,toUsername,fromUsername为必填参数" );
				
				$wechatObj->onText ();
				exit ();
			}
			if (isset ( $_GET ['weixin-sendmessage'] )) {
				$msg = $_GET ['weixin-sendmessage'];
				$openId = $_GET ['weixin-openid'];
				
				if (! $msg || ! $openId)
					exit ( "错误，参数不全!" );
				
				if ($openId) {
					if (! isset ( $weAccount )) {
						$weAccount = initNewWeAccount ();
					}
					$userInfo = $weAccount->getAndUpdateUserInfoWithMatchedFakeId ( $openId );
					if ($userInfo)
						$fakeId = $userInfo ['FakeId'];
					
					if ($fakeId) {
						
						$sendResult = $weAccount->send ( $fakeId, $msg );
						// $send=json_encode($send);
						// $data = json_decode ( $sendResult ['body'], true );
						// print_r ( $sendResult );
						if ($sendResult ['ret'] == 0 && $sendResult ['msg'] == 'ok')
							exit ( true );
					}
				}
				
				exit ( "无匹配的FakeId，或发送失败！" );
			}
			
			if (isset ( $_GET ['weixin-sendmessage-post'] )) {
				$msg = $_POST ['message'];
				$openId = $_POST ['openid'];
				$msg = str_replace ( "\\", "", $msg );
				if (! $msg || ! $openId)
					exit ( "错误，参数不全!" );
				
				if ($openId) {
					
					if (! isset ( $weAccount )) {
						$weAccount = initNewWeAccount ();
					}
					$userInfo = $weAccount->getAndUpdateUserInfoWithMatchedFakeId ( $openId );
					if ($userInfo)
						$fakeId = $userInfo ['FakeId'];
					
					if ($fakeId) {
						
						$sendResult = $weAccount->send ( $fakeId, $msg );
						// $send=json_encode($send);
						// $data = json_decode ( $sendResult ['body'], true );
						// print_r ( $sendResult );
						if ($sendResult ['ret'] == 0 && $sendResult ['msg'] == 'ok')
							exit ( true );
					}
				}
				
				exit ( "无匹配的FakeId，或发送失败！" );
			}
			
			if (isset ( $_GET ['weixin-openid'] )) {
				
				// echo '=======start debug=====';
				// $wechatObj->onText ();
				// echo '=======finish debug=====';
				// exit ();
				
				$openId = $_GET ['weixin-openid'];
				// echo '====openId====='.$openId;
				if (! isset ( $weAccount )) {
					$weAccount = initNewWeAccount ();
				}
				$userInfo = $weAccount->getAndUpdateUserInfoWithMatchedFakeId ( $openId );
			}
			
			$wechatObj->run ();
			// exit ();
		}
	}
}
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

// 回复发微信
function comment_weixin_notify($comment_id) {
	$comment = get_comment ( $comment_id ); // 获取评论内容
	$comment_parent = $comment->comment_parent;
	$spam_confirmed = $comment->comment_approved; // 是否垃圾评论
	$author = trim ( $comment->comment_author ); // 作者
	
	if ($author != 'AUTO_REPLY' && $comment_parent != 0 && $spam_confirmed != 'spam') {
		$parent_comment = get_comment ( $comment_parent );
		$openId = trim ( $parent_comment->comment_author_email );
		global $weAccount;
		if (! isset ( $weAccount )) {
			$weAccount = initNewWeAccount ();
		}
		$n = strpos ( $openId, '@' ); // 寻找位置
		if ($n)
			$openId = substr ( $openId, 0, $n ); // 删除后面
		
		$fakeId_url = $parent_comment->comment_author_url;
		if ($fakeId_url) {
			$fakeId = substr ( trim ( $fakeId_url ), 7 ); // 去除前面固定的http://
		} else if ($openId) {
			$userInfo = $weAccount->getAndUpdateUserInfoWithMatchedFakeId ( $openId );
			if ($userInfo)
				$fakeId = $userInfo ['FakeId'];
		}
		// $title = get_the_title ( $parent_comment->comment_post_ID ); // 文章标题
		$title = trim ( $parent_comment->comment_content );
		$comment = trim ( $comment->comment_content ); // 评论内容
		$msg = "【" . $author . "】回复了《" . $title . "》：" . $comment;
		// echo '=============comment_weixin_notify==========' . $msg;
		if ($fakeId) {
			$sendResult = $weAccount->send ( $fakeId, $msg );
			if ($sendResult ['ret'] == 0 && $sendResult ['msg'] == 'ok')
				wp_set_comment_status ( $comment_parent, 'approve' );
		}
	}
}
add_action ( 'comment_post', 'comment_weixin_notify' );
function test_modify_user_table($column) {
	$column ['nick'] = '显示名';
	$column ['url'] = '微信外链';
	
	return $column;
}

add_filter ( 'manage_users_columns', 'test_modify_user_table' );
function test_modify_user_table_row($val, $column_name, $user_id) {
	$user = get_userdata ( $user_id );
	
	switch ($column_name) {
		case 'url' :
			return $user->user_url;
			break;
		case 'nick' :
			return $user->display_name;
			break;
		
		default :
	}
	
	return $return;
}

add_filter ( 'manage_users_custom_column', 'test_modify_user_table_row', 10, 3 );
class wechatCallback extends Wechat {
	/**
	 * 用户关注时触发，回复「欢迎关注」
	 *
	 * @return void
	 */
	public function onSubscribe() {
		$fromUsername = $this->fromUsername;
		// $indentifyText = substr ( md5 ( $fromUsername ), 0, 16 );
		
		$welcomeInfo = str_replace ( 'WEIXIN_OPENID', $fromUsername, WEIXIN_DEFAULT_WELCOME );
		$this->responseText ( $welcomeInfo );
		
		// 延迟10秒进行匹配绑定
		// usleep(5000000);
		// global $weAccount;
		// if (! isset ( $weAccount )) {
		// $weAccount=initNewWeAccount();
		// }
		// $weAccount->getAndUpdateUserInfoWithMatchedFakeId ( $fromUsername );
		exit ();
	}
	
	/**
	 * 用户取消关注时触发
	 *
	 * @return void
	 */
	public function onUnsubscribe() {
		// $fromUsername = $this->fromUsername;
		// $indentifyText = substr ( md5 ( $fromUsername ), 0, 16 );
		// $this->responseText ( '为您提供基于微信的专业O2O服务，回复h 查看帮助。@' . $indentifyText );
		// 「悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。」
	}
	
	/**
	 * 收到文本消息时触发，回复收到的文本消息内容
	 *
	 * @return void
	 */
	public function onText() {
		
		// For Debug
		// $fromUsername='o_sssjiarwbczfvgcqiv4ugwmyue';
		// $toUsername='gh_aaaaaa';
		$fromUsername = $this->fromUsername;
		$toUsername = $this->toUsername;
		if (! $fromUsername || ! $toUsername) {
			$this->responseText ( "查询参数不全!" );
			exit ();
		}
		
		if (isset ( $_GET ['weixin-search'] )) {
			$action = $_GET ['action'];
			$keyword = $_GET ['weixin-search'];
		} else {
			
			if ($this->getRequest ( 'event' ))
				$keyword = $this->getRequest ( 'eventkey' );
			else
				$keyword = $this->getRequest ( 'content' );
		}
		
		// echo '=====keword参数为======'.$keyword;
		$welcomeInfo = str_replace ( 'WEIXIN_OPENID', $fromUsername, WEIXIN_DEFAULT_WELCOME );
		
		if (empty ( $keyword )) {
			
			$this->responseText ( $welcomeInfo );
			exit ();
		}
		
		// 记录发过来的微信消息
		$commentdata = array (
				'comment_post_ID' => 1,
				'comment_author' => $fromUsername,
				'comment_author_email' => $fromUsername . "@appcn100.com",
				'comment_content' => $keyword 
		);
		$comment_id = wp_new_comment ( $commentdata );
		if ($comment_id)
			wp_set_comment_status ( $comment_id, 'approve' );
		
		global $weAccount;
		if (! isset ( $weAccount )) {
			$weAccount = initNewWeAccount ();
		}
		
		$userInfo = $weAccount->getAndUpdateUserInfo ( $fromUsername );
		if ($userInfo) {
			$userName = empty ( $userInfo ['Username'] ) ? $fromUsername : $userInfo ['NickName'];
			$fakeId = $userInfo ['FakeId'];
			
			$email_prefix = empty ( $fakeId ) ? $fromUsername : $fakeId;
			$commentdata = array (
					'comment_ID' => $comment_id,
					'comment_post_ID' => 1,
					'comment_author' => $userName,
					'comment_author_email' => $email_prefix . "@appcn100.com",
					'comment_author_url' => $fakeId,
					'comment_content' => $keyword 
			);
			// 如果找到匹配微信帐户，则更新刚发的comment
			wp_update_comment ( $commentdata );
		}
		
		// if (strtolower ( $keyword ) == "my") {
		// $current_weiservice_option = get_option ( 'wp-wechat-basic' );
		// $crm_url = $current_weiservice_option ['weixin_default'];
		// $branch_id = $current_weiservice_option ['weixin_crm_shop_id'];
		
		// // By Glay
		// $body = array (
		// 'branch_id' => $branch_id,
		// 'open_id' => $openId
		// );
		// print_r ( $body );
		// if ($crm_url) {
		// $request_crm = new WP_Http ();
		// $result_crm = $request_crm->request ( $crm_url, array (
		// 'method' => 'POST',
		// 'body' => $body
		// ) );
		// // echo '====crm url============'.$crm_url;
		// // print_r(result_crm);
		// $this->responseText ( $crm_url . '===Result===' . json_encode ( $result_crm ) );
		// exit ();
		// }
		// }
		
		$keyword = trim ( str_replace ( '。', '', $keyword ) );
		if (substr ( $keyword, 0, 6 ) == "位置" || substr ( $keyword, 0, 3 ) == "去" || substr ( $keyword, 0, 3 ) == "到") {
			if (substr ( $keyword, 0, 6 ) == "位置")
				$entityName = substr ( $keyword, 6, strlen ( $keyword ) );
			else
				$entityName = substr ( $keyword, 3, strlen ( $keyword ) );
			
			if ($keyword == "位置导航" || $entityName == "") {
				$contentStr = "发送“位置”或“去”加上具体位置名，如“去广州塔”";
				$this->responseText ( $contentStr );
				exit ();
			}
			$picurl1 = get_option ( 'siteurl' ) . "/wp-content/plugins/wp-wechat/images/mo_icon3.png";
			$picurl2 = get_option ( 'siteurl' ) . "/wp-content/plugins/wp-wechat/images/mo_icon6.png";
			$url = get_option ( 'siteurl' ) . "/?weixin-address=" . urlencode ( $entityName );
			
			$items = array (
					new NewsResponseItem ( $entityName, '', $picurl1, $url ),
					new NewsResponseItem ( '查看' . $entityName . '具体位置信息', $entityName . '的具体位置信息', $picurl2, $url ) 
			);
			$this->responseNews ( $items );
			exit ();
		} else if (substr ( $keyword, 0, 6 ) == "点歌" || substr ( $keyword, 0, 6 ) == "听歌") {
			$entityName = substr ( $keyword, 6, strlen ( $keyword ) );
			if ($entityName == "") {
				$contentStr = "发送“点歌”加上歌名，如“点或听歌最炫民族风”";
				$this->responseText ( $contentStr );
				exit ();
			}
			$apihost = "http://api2.sinaapp.com/";
			$apimethod = "search/music/?";
			$apiparams = array (
					'appkey' => "0020120430",
					'appsecert' => "fa6095e113cd28fd",
					'reqtype' => "music" 
			);
			$apikeyword = "&keyword=" . urlencode ( $entityName );
			$apicallurl = $apihost . $apimethod . http_build_query ( $apiparams ) . $apikeyword;
			$api2str = file_get_contents ( $apicallurl );
			$api2json = json_decode ( $api2str, true );
			$musicUrl = $api2json ['music'] ['hqmusicurl'];
			if ($musicUrl == "") {
				$contentStr = "没有找到音乐，可能不是歌名或者检索失败，请换首歌试试！";
				$this->responseText ( $contentStr );
				exit ();
			} else {
				$this->responseMusic ( $api2json ['music'] ['title'], '听首歌，放松下心情！', $musicUrl, $api2json ['music'] ['hqmusicurl'] );
				exit ();
			}
		}
		
		// print_r ( '======FakeId Info=====' . $fakeId );
		// print_r ( '======User Name=====' . $userName );
		
		$current = get_option ( 'wp-wechat-basic' );
		$weixin_default_image = $current ['weixin_default_image'];
		$weixin_count = $current ['weixin_count'];
		if (! $weixin_count)
			$weixin_count = 5;
		
		$its = array ();
		$query_array1 = array (
				'tag' => $keyword,
				'post_type'=>'any',
				//'posts_per_page' => $weixin_count,
				//'category_name' => 'cat_wx,cat_wx_rpl_txt,cat_wx_rpl_news,cat_wx_rpl_music',
				'post_status' => 'publish' 
		);
		
		$its1= $this->query ( $query_array1 );
		foreach ( $its1 as $post ) 
			$its[]= $post;
		$query_array2 = array (
				'product_tag' => $keyword,
				'post_type'=>'any',
				//'posts_per_page' => $weixin_count,
				//'category_name' => 'cat_wx,cat_wx_rpl_txt,cat_wx_rpl_news,cat_wx_rpl_music',
				'post_status' => 'publish'
		);
		$its2= $this->query ( $query_array2 );
		foreach ( $its2 as $post ) 
			$its[]= $post;
		//print_r($its);
		
		// $this->responseText('收到了文字消息：' . $keyword.$toUsername.$fromUsername);
		if ($its) {
			
			$counter = 1;
			$strItems = '';
			$items = array ();
			
			foreach ( $its as $post ) {
				if ($counter>$weixin_count)
					break;
				//print_r($post);
				$title = get_the_title ( $post );
				//echo "==========" . $title;
				
				$excerpt = get_post_excerpt ( $post, 320 );
				$link = get_permalink ( $post );
				
				$post_cats = get_the_category ( $post->ID );
				$cats = array ();
				foreach ( $post_cats as $cat ) {
					$cats[]=$cat->slug;
				}
				
				//echo '====分类====' . $post_cat;
				if (in_array('cat_wx_rpl_txt', $cats)) {
					$strItems=strip_tags ( do_shortcode ( $post->post_content ) ) ;
					$this->responseText ($strItems);
				}else if (in_array('cat_wx_rpl_music', $cats)) {
					// 如果有声音格式内容，则优先回复，并退出
						$musicUrl = get_post_first_audio ( $post->post_content );
						$strItems="《<a target='_blank' href='" . $musicUrl . "'>" . $title . "</a>》";
						$this->responseMusic ( $title, $excerpt, $musicUrl, $musicUrl );
				}else{
						$thumbnail_id = get_post_thumbnail_id ( $post->ID );
						if ($thumbnail_id) {
							if ($counter == 1) {
								$thumb = wp_get_attachment_image_src ( $thumbnail_id, array (
										640,
										320 
								) );
							} else {
								$thumb = wp_get_attachment_image_src ( $thumbnail_id, array (
										80,
										80 
								) );
							}
							$thumb = $thumb [0];
						} else {
							$thumb = get_post_first_image ( $post->post_content );
						}
						
						if (empty ( $thumb )) {
							$thumb = apply_filters ( 'weixin_default', $weixin_default_image );
						} else {
							$thumb = apply_filters ( 'weixin_thumb', $thumb, $counter );
						}
						//$thumb=rawurlencode($thumb);
						$items [] = new NewsResponseItem ( $title, $excerpt, $thumb, $link );
				}
				
				$counter ++;
				$strItems .= "《<a target='_blank' href='" . $link . "'>" . $title . "</a>》";
			}
			
			if (! empty ( $items )) {
				$this->responseNews ( $items );
				//exit ();
			}
			
			if ($comment_id) {
				
				$commentdata = array (
						'comment_post_ID' => 1,
						'comment_author' => "AUTO_REPLY",
						'comment_author_email' => "auto_reply@appcn100.com",
						'comment_author_url' => "1121610100",
						'comment_parent' => $comment_id,
						'comment_content' => $strItems 
				);
				
				$new_comment_id = wp_new_comment ( $commentdata );
				// echo '======发消息后创建回复new_comment_id===' . $new_comment_id;
				
				wp_set_comment_status ( $new_comment_id, 'approve' );
			}
			
			exit ();
			// echo '===========responseNews========';
			// print_r($strItems);
		} else {
			
			$weixin_not_found = "抱歉，没有找到与[" . $keyword . "]相关的内容！" . $welcomeInfo;
			
			// 如果接口里action不为空，表示仅为记录用，不用设为未审批的回复
			if (! $action && $comment_id)
				wp_set_comment_status ( $comment_id, 'hold' );
			$this->responseText ( $weixin_not_found );
			exit ();
		}
	}
	
	/**
	 * 收到图片消息时触发，回复由收到的图片组成的图文消息
	 *
	 * @return void
	 */
	public function onImage() {
		$fromUsername = $this->fromUsername;
		// $indentifyText = substr ( md5 ( $fromUsername ), 0, 16 );
		$welcomeInfo = str_replace ( 'WEIXIN_OPENID', $fromUsername, WEIXIN_DEFAULT_WELCOME );
		$this->responseText ( $welcomeInfo );
		exit ();
	}
	
	/**
	 * 收到地理位置消息时触发，回复收到的地理位置
	 *
	 * @return void
	 */
	public function onLocation() {
		// $num = 1 / 0;
		// 故意触发错误，用于演示调试功能
		// $this->responseText ( '收到了位置消息：' . $this->getRequest ( 'location_x' ) . ',' . $this->getRequest ( 'location_y' ) );
		$picurl1 = get_option ( 'siteurl' ) . "/wp-content/plugins/wp-wechat/images/mo_icon3.png";
		$picurl2 = get_option ( 'siteurl' ) . "/wp-content/plugins/wp-wechat/images/mo_icon6.png";
		$url = 'http://map.baidu.com/mobile';
		// $url="http://api.map.baidu.com/geocoder?location=".$this->getRequest ( 'location_x' ).",".$this->getRequest ( 'location_y' )."&coord_type=gcj02&output=html&src=appo2o|appo2o";
		// $url="http://api.map.baidu.com/marker?location=".$this->getRequest ( 'location_x' ).",".$this->getRequest ( 'location_y' )."&title=我的位置&content=我所在的位置&output=html&src=appo2o|appo2o";
		$items = array (
				new NewsResponseItem ( '我的位置', '', $picurl1, $url ),
				new NewsResponseItem ( '查看所在具体位置', '您所在的具体位置', $picurl2, $url ) 
		);
		$this->responseNews ( $items );
	}
	
	/**
	 * 收到链接消息时触发，回复收到的链接地址
	 *
	 * @return void
	 */
	public function onLink() {
		$fromUsername = $this->fromUsername;
		// $indentifyText = substr ( md5 ( $fromUsername ), 0, 16 );
		$welcomeInfo = str_replace ( 'WEIXIN_OPENID', $fromUsername, WEIXIN_DEFAULT_WELCOME );
		$this->responseText ( $welcomeInfo );
		exit ();
		
		// 延迟 10秒
		// usleep(10000000);
		// global $weAccount;
		// if (! isset ( $weAccount )) {
		// $weAccount=initNewWeAccount();
		// }
		// $userInfo = $weAccount->getAndUpdateUserInfoWithMatchedFakeId ( $fromUsername );
		// $this->Text ( '收到了链接：' . $this->getRequest ( 'url' ) );
	}
	
	/**
	 * 收到未知类型消息时触发，回复收到的消息类型
	 *
	 * @return void
	 */
	public function onUnknown() {
		// $fromUsername = $this->fromUsername;
		// $indentifyText = substr ( md5 ( $fromUsername ), 0, 16 );
		// $welcomeInfo =str_replace('WEIXIN_OPENID', $fromUsername, WEIXIN_DEFAULT_WELCOME) ;
		// $this->responseText ( $welcomeInfo );
		// exit ();
	}
	
	/**
	 * 通过关键字数组进行查询
	 *
	 * @return items
	 */
	public function query($query_array) {
		// echo '=======start query======';
		global $wp_query;
		
		$items = $wp_query->query ( $query_array );
		
		// echo "==========items==========";
		// var_dump ( $items );
		return $items;
	}
	/**
	 * 拼装news格式的单个item
	 *
	 * @return item
	 */
	private function get_item($title, $description, $picUrl, $url) {
		if (! $description)
			$description = $title;
		
		return '
		<item>
			<Title><![CDATA[' . $title . ']]></Title>
			<Discription><![CDATA[' . $description . ']]></Discription>
			<PicUrl><![CDATA[' . $picUrl . ']]></PicUrl>
			<Url><![CDATA[' . $url . ']]></Url>
		</item>
		';
	}
}

// 加强搜索相关性
if (! function_exists ( 'wpjam_search_orderby' )) {
	
	add_filter ( 'posts_orderby_request', 'wpjam_search_orderby' );
	function wpjam_search_orderby($orderby = '') {
		global $wpdb, $wp_query;
		
		$keyword = stripslashes ( $wp_query->query_vars ['s'] );
		
		if ($keyword) {
			
			$n = ! empty ( $q ['exact'] ) ? '' : '%';
			
			preg_match_all ( '/".*?("|$)|((?<=[\r\n\t ",+])|^)[^\r\n\t ",+]+/', $keyword, $matches );
			$search_terms = array_map ( '_search_terms_tidy', $matches [0] );
			
			$case_when = "0";
			
			foreach ( ( array ) $search_terms as $term ) {
				$term = esc_sql ( like_escape ( $term ) );
				
				$case_when .= " + (CASE WHEN {$wpdb->posts}.post_title LIKE '{$term}' THEN 3 ELSE 0 END) + (CASE WHEN {$wpdb->posts}.post_title LIKE '{$n}{$term}{$n}' THEN 2 ELSE 0 END) + (CASE WHEN {$wpdb->posts}.post_content LIKE '{$n}{$term}{$n}' THEN 1 ELSE 0 END)";
			}
			
			return "({$case_when}) DESC, {$wpdb->posts}.post_modified DESC";
		} else {
			return $orderby;
		}
	}
}

add_action ( 'admin_menu', 'weixin_robot_admin_menu' );
function weixin_robot_admin_menu() {
	add_menu_page ( '微服务', '微服务', 'manage_options', 'wp-wechat', 'weixin_robot_basic_setting_page', WP_CONTENT_URL . '/plugins/wp-wechat/weixin-16.ico' );
	add_submenu_page ( 'wp-wechat', '基本设置 &lsaquo; 微服务', '基本设置', 'manage_options', 'wp-wechat', 'weixin_robot_basic_setting_page' );
	add_submenu_page ( 'wp-wechat', '微信用户列表 &lsaquo; 微服务', '匹配验证', 'manage_options', 'weixin-users', 'weixin_user_page' );
	add_submenu_page ( 'wp-wechat', '帮助中心 &lsaquo; 微服务', '帮助', 'manage_options', 'wp-wechat-about', 'weixin_robot_about_page' );
}

add_action ( 'admin_head', 'weixin_robot_admin_head' );
function weixin_robot_admin_head() {
	?>
<style>
#icon-wp-wechat {
	background-image:
		url("<?php echo WP_CONTENT_URL?>/plugins/wp-wechat/weixin-32.png");
	background-repeat: no-repeat;
}
</style>
<?php
}
function weixin_robot_get_plugin_file() {
	return __FILE__;
}

// $weixin_account = WP_CONTENT_DIR . '/plugins/wp-wechat/account.php';
// include ($weixin_account);
$weixin_robot_options = WP_CONTENT_DIR . '/plugins/wp-wechat/wp-wechat-options.php';
include ($weixin_robot_options);
