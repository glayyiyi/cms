<?php
// 判断当前用户操作是否在微信内置浏览器中
function is_weixin(){ 
	if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') !== false ) {
			return true;
		}
	}
	return false;
}

if(!function_exists('get_post_excerpt')){
    //获取日志摘要
    function get_post_excerpt($post, $excerpt_length=240){
        if(!$post) $post = get_post();

        $post_excerpt = $post->post_excerpt;

        if($post_excerpt == ''){
            $post_content	= $post->post_content;
            $post_content	= do_shortcode($post_content);
            $post_content	= wp_strip_all_tags( $post_content );
            $excerpt_length	= apply_filters('excerpt_length', $excerpt_length);     
            $excerpt_more	= apply_filters('excerpt_more', ' ' . '&hellip;');
            $post_excerpt	= mb_strimwidth($post_content,0,$excerpt_length,$excerpt_more,'utf-8');
        }

        $post_excerpt = wp_strip_all_tags( $post_excerpt );
        $post_excerpt = trim( preg_replace( "/[\n\r\t ]+/", ' ', $post_excerpt ), ' ' );

        return $post_excerpt;
    }

    //获取第一段
    function get_first_p($text){
        if($text){
            $text = explode("\n",strip_tags($text)); 
            $text = trim($text['0']); 
        }
        return $text;
    }
}

if(!function_exists('get_post_first_image')){
	function get_post_first_image($post_content){
		preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post_content, $matches);
		if($matches){	 
			return $matches[1][0];
		}else{
			return false;
		}
	}
}

function weixin_robot_check_domain($id=56){
	return wpjam_net_check_domain($id);
}

function get_post_weixin_thumb($post,$size){
	$thumb = apply_filters('weixin_thumb',false,$size,$post);

	if($thumb===false){
		$thumbnail_id = get_post_thumbnail_id($post->ID);
		if($thumbnail_id){
			$thumb = wp_get_attachment_image_src($thumbnail_id, $size);
			$thumb = $thumb[0];
		}else{
			$thumb = get_post_first_image($post->post_content);
		}

		if(empty($thumb)){
			$thumb = weixin_robot_get_setting('weixin_default');
		}
	}
	
	return $thumb;
}

function weixin_robot_get_setting($setting_name){
	$option = weixin_robot_get_basic_option();
	return wpjam_get_setting($option, $setting_name);
}

function weixin_robot_get_option($option_name){
	$defaults = weixin_robot_get_default_option($option_name);
	return wpjam_get_option($option_name,$defaults);
}

/* 向下兼容 */
function weixin_robot_get_basic_option(){
	return weixin_robot_get_option('weixin-robot-basic' );
}

function weixin_robot_get_advanced_option(){
	return weixin_robot_get_option('weixin-robot-advanced' );
}

function weixin_robot_get_option_labels($option_name){
	if($option_name == 'weixin-robot-basic'){
		return weixin_robot_get_option_basic_labels();
	}elseif($option_name == 'weixin-robot-advanced'){
		return weixin_robot_get_option_advanced_labels();
	}
}

function weixin_robot_get_default_option($option_name){
	if($option_name == 'weixin-robot-basic'){
		return weixin_robot_get_default_basic_option();
	}elseif($option_name == 'weixin-robot-advanced'){
		return weixin_robot_get_default_advanced_option();
	}
}

function weixin_robot_get_user_location($weixin_openid){
	$location = wp_cache_get($weixin_openid,'weixin_location');
	if($location === false){
		global $wpdb;
		$weixin_messages_table = weixin_robot_get_messages_table();

		$time = current_time('timestamp') - 60*60*(2+8);

		$location = $wpdb->get_row($wpdb->prepare("SELECT Location_X as x, Location_Y as y FROM {$weixin_messages_table} WHERE Location_X >0 AND Location_Y >0 AND FromUserName=%s AND CreateTime>%d ORDER BY CreateTime DESC LIMIT 0,1;",$weixin_openid,$time),ARRAY_A);
		wp_cache_set($weixin_openid, $location,'weixin_location', 600);
	}
	return $location;
}

function weixin_robot_get_custom_keywords(){
	global $wpdb;

	$weixin_custom_keywords = get_transient('weixin_custom_keywords');

	if($weixin_custom_keywords === false){
		$weixin_custom_keywords_table = weixin_robot_get_custom_replies_table();
		$weixin_custom_original_keywords = $wpdb->get_results("SELECT keyword,reply,type FROM $weixin_custom_keywords_table WHERE status = 1",OBJECT_K);
		
		$weixin_custom_keywords = array(); 
		if($weixin_custom_original_keywords){
			foreach ($weixin_custom_original_keywords as $key => $value) {
				if(strpos($key,',')){
					foreach (explode(',', $key) as $new_key) {
						$new_key = strtolower(trim($new_key));
						if($new_key){
							$weixin_custom_keywords[$new_key] = $value;
						}
					}
				}else{
					$weixin_custom_keywords[strtolower($key)] = $value;
				}
			}
		}

		set_transient('weixin_custom_keywords',$weixin_custom_keywords,3600);
	}
	return $weixin_custom_keywords;
}


function weixin_robot_get_welcome_keywords(){
	return array( 'hi', 'h', 'help', '帮助', '您好', '你好');
}

function weixin_robot_get_builtin_replies($type = ''){

	$weixin_builtin_replies = wp_cache_get('weixin_builtin_replies','weixin_robot');

	if($weixin_builtin_replies === false){
		$weixin_builtin_replies = array();
		
		$weixin_builtin_replies['[voice]'] 			= array('type'=>'full',	'reply'=>'默认语音回复',		'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[location]'] 		= array('type'=>'full',	'reply'=>'默认地理位置回复',	'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[image]'] 			= array('type'=>'full',	'reply'=>'默认图片回复',		'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[link]'] 			= array('type'=>'full',	'reply'=>'默认链接回复',		'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[event-location]']	= array('type'=>'full',	'reply'=>'获取用户地理位置',	'function'=>'weixin_robot_location_event_reply');

		foreach (weixin_robot_get_welcome_keywords() as $welcome_keyword) {
			$weixin_builtin_replies[$welcome_keyword] = array('type'=>'full', 'reply'=>'欢迎回复', 'function'=>'weixin_robot_welcome_reply');
		}

		$weixin_builtin_replies['subscribe']	= array('type'=>'full',	'reply'=>'用户订阅',		'function'=>'weixin_robot_subscribe_reply');
		$weixin_builtin_replies['unsubscribe']	= array('type'=>'full',	'reply'=>'用户取消订阅',	'function'=>'weixin_robot_unsubscribe_reply');

		$weixin_robot_advanced = weixin_robot_get_advanced_option();

		$weixin_builtin_replies[$weixin_robot_advanced['new']] 		= array('type'=>'full',	'reply'=>'最新日志',			'function'=>'weixin_robot_new_posts_reply');
		$weixin_builtin_replies[$weixin_robot_advanced['rand']] 	= array('type'=>'full',	'reply'=>'随机日志',			'function'=>'weixin_robot_rand_posts_reply');
		$weixin_builtin_replies[$weixin_robot_advanced['hot']] 		= array('type'=>'full',	'reply'=>'最热日志',			'function'=>'weixin_robot_hot_posts_reply');
		$weixin_builtin_replies[$weixin_robot_advanced['comment']] 	= array('type'=>'full',	'reply'=>'留言最多日志',		'function'=>'weixin_robot_comment_posts_reply');
		$weixin_builtin_replies[$weixin_robot_advanced['hot-7']] 	= array('type'=>'full',	'reply'=>'一周内最热日志',	'function'=>'weixin_robot_hot_7_posts_reply');
		$weixin_builtin_replies[$weixin_robot_advanced['comment-7']]= array('type'=>'full',	'reply'=>'一周内留言最多日志',	'function'=>'weixin_robot_comment_7_posts_reply');

		$weixin_builtin_replies['checkin']	= $weixin_builtin_replies['签到']	= array('type'=>'full', 'reply'=>'签到', 	'function'=>'weixin_robot_checkin_reply');
		$weixin_builtin_replies['credit']	= $weixin_builtin_replies['积分']	= array('type'=>'full', 'reply'=>'获取积分',	'function'=>'weixin_robot_credit_reply');

		$weixin_builtin_replies = apply_filters('weixin_builtin_reply', $weixin_builtin_replies);

		wp_cache_set('weixin_builtin_replies',$weixin_builtin_replies,'weixin_robot',60);
	}

	if($type){
		$weixin_builtin_replies_new = array();
		foreach ($weixin_builtin_replies as $key => $weixin_builtin_reply) {
			$weixin_builtin_replies_new[$weixin_builtin_reply['type']][$key] = $weixin_builtin_reply;
		}
		return $weixin_builtin_replies_new[$type];
	}else{
		return $weixin_builtin_replies;
	}
}

// 欢迎回复
function weixin_robot_welcome_reply(){
	global $wechatObj;
	
	$weixin_welcome = weixin_robot_get_setting('weixin_welcome');
	echo sprintf($wechatObj->get_textTpl(), $weixin_welcome);
	$wechatObj->set_response('welcome');

	$weixin_openid = $wechatObj->get_fromUsername();
	if($weixin_openid){
		$weixin_user = array('subscribe'=>1);
		weixin_robot_update_user($weixin_openid,$weixin_user);
	}
}

// 第一次订阅回复
function weixin_robot_subscribe_reply(){
	weixin_robot_welcome_reply();
}

// 取消订阅回复
function weixin_robot_unsubscribe_reply(){

	global $wechatObj;
	$weixin_unsubscribe = "你怎么忍心取消对我的订阅？";
	echo sprintf($wechatObj->get_textTpl(), $weixin_unsubscribe);
	$wechatObj->set_response('byebye');

	$weixin_openid = $wechatObj->get_fromUsername();
	if($weixin_openid){
		$weixin_user = array('subscribe'=>0);
		weixin_robot_update_user($weixin_openid,$weixin_user);
	}
}

// 语音，图像，地理信息默认处理
function weixin_robot_default_reply($keyword){
	global $wechatObj;
	$keyword = str_replace(array('[',']'), '', $keyword);
	
	$weixin_default = weixin_robot_get_setting('weixin_default_'.$keyword);
	
	if($weixin_default){
		echo sprintf($wechatObj->get_textTpl(), $weixin_default);
	}

	$wechatObj->set_response($keyword);
}

// 用户自动上传地理位置时的回复
function weixin_robot_location_event_reply(){
	global $wechatObj, $wpdb;

    $weixin_messages_table = weixin_robot_get_messages_table();

    $weixin_openid = $wechatObj->get_fromUsername();

    $last_enter_reply = wp_cache_get($weixin_openid,'weixin_enter_reply');
    if($last_enter_reply === false) {
    	$last_enter_reply = $wpdb->get_var($wpdb->prepare("SELECT CreateTime FROM {$weixin_messages_table} WHERE MsgType='event' AND Event = 'LOCATION' AND Response='enter-reply' AND FromUserName=%s ORDER BY CreateTime DESC LIMIT 0,1;",$weixin_openid)); // 24 小时内写过的，就不再写入了。
    	if($last_enter_reply){
        	wp_cache_set($weixin_openid,$last_enter_reply,'weixin_enter_reply',60*60*24);
    	}else{
    		$last_enter_reply = 0;
    	}
    }

    if(current_time('timestamp') - $last_enter_reply > apply_filters('weixin_enter_time',60*60*24)+3600*8)  {
    	echo sprintf($wechatObj->get_textTpl(), weixin_robot_get_setting('weixin_enter'));
    	wp_cache_set($weixin_openid, current_time('timestamp'), 'weixin_enter_reply', 60*60*24);
    	$wechatObj->set_response('enter-reply');
	}else{
		$wechatObj->set_response('location');
	}
}

function weixin_robot_advanced_reply(){
	global $wechatObj;
	$wechatObj->set_response('advanced');
	$wechatObj->query();
}

function weixin_robot_new_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	weixin_robot_advanced_reply();
}

function weixin_robot_rand_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	add_filter('weixin_query','wpjam_advanced_weixin_query_rand');
	weixin_robot_advanced_reply();
}

function weixin_robot_hot_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	add_filter('weixin_query','wpjam_advanced_weixin_query_hot');
	weixin_robot_advanced_reply();
}

function weixin_robot_comment_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	add_filter('weixin_query','wpjam_advanced_weixin_query_comment');
	weixin_robot_advanced_reply();
}

function weixin_robot_hot_7_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	add_filter('weixin_query','wpjam_advanced_weixin_query_hot');
	add_filter('posts_where', 'wpjam_advanced_filter_where_7' );
	weixin_robot_advanced_reply();
}

function weixin_robot_comment_7_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	add_filter('weixin_query','wpjam_advanced_weixin_query_comment');
	add_filter('posts_where', 'wpjam_advanced_filter_where_7' );
	weixin_robot_advanced_reply();
}

function weixin_robot_hot_30_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	add_filter('weixin_query','wpjam_advanced_weixin_query_hot');
	add_filter('posts_where', 'wpjam_advanced_filter_where_30' );
	weixin_robot_advanced_reply();
}

function weixin_robot_comment_30_posts_reply(){
	add_filter('weixin_query','wpjam_advanced_weixin_query_new');
	add_filter('weixin_query','wpjam_advanced_weixin_query_comment');
	add_filter('posts_where', 'wpjam_advanced_filter_where_30' );
	weixin_robot_advanced_reply();
}
