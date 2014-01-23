<?php

//自定义回复，内置回复，函数回复，关键字太长处理等。
add_filter('weixin_custom_keyword','weixin_robot_custom_keyword',1,2);
function weixin_robot_custom_keyword($false,$keyword){
	
	global $wechatObj;

	if(empty( $keyword ) || strpos($keyword, '#') !== false ) {
		echo "";
		$wechatObj->set_response('need-manual');
		return true;
	}

	// 前缀匹配，只支持2个字
	$prefix_keyword = mb_substr($keyword, 0,2);

	$weixin_custom_keywords = weixin_robot_get_custom_keywords();
	$weixin_custom_keywords_prefix = weixin_robot_get_custom_keywords('prefix');

	if(isset($weixin_custom_keywords[$keyword]) ){
		$weixin_custom_reply = $weixin_custom_keywords[$keyword];
	}elseif(isset($weixin_custom_keywords_prefix[$prefix_keyword])) {
		$weixin_custom_reply = $weixin_custom_keywords_prefix[$prefix_keyword];
	}else{
		$weixin_custom_reply = '';
	}

	if($weixin_custom_reply){
		if($weixin_custom_reply->type == 'text'){	
			$wechatObj->set_response('custom-text');
			$weixin_text_reply =  weixin_robot_str_replace($weixin_custom_reply->reply, $wechatObj);
			echo sprintf($wechatObj->get_textTpl(), $weixin_text_reply);
		}elseif($weixin_custom_reply->type == 'img'){
			add_filter('weixin_query','weixin_robot_img_reply_query');
			$wechatObj->set_response('custom-img');
			$wechatObj->query($keyword);
		}elseif($weixin_custom_reply->type == 'function'){
			call_user_func($weixin_custom_reply->reply, $keyword);
		}elseif($weixin_custom_reply->type == '3rd'){
			weixin_robot_3rd_reply();
		}

		return true;
	}

	//内置回复 -- 完全匹配
	$weixin_builtin_replies = weixin_robot_get_builtin_replies('full');
	//内置回复 -- 前缀匹配
	$weixin_builtin_replies_prefix = weixin_robot_get_builtin_replies('prefix');

	if(isset($weixin_builtin_replies[$keyword])) {
		$weixin_reply_function = $weixin_builtin_replies[$keyword]['function'];
	}elseif(isset($weixin_builtin_replies_prefix[$prefix_keyword])){
		$weixin_reply_function = $weixin_builtin_replies_prefix[$prefix_keyword]['function'];
	}

	if(isset($weixin_reply_function)){
		call_user_func($weixin_reply_function, $keyword);
		return true;
	}

	// 检测关键字是不是太长了
	$keyword_length = mb_strwidth(preg_replace('/[\x00-\x7F]/','',$keyword),'utf-8')+str_word_count($keyword)*2;

	$weixin_keyword_allow_length = weixin_robot_get_setting('weixin_keyword_allow_length');
	
	if($keyword_length > $weixin_keyword_allow_length){

		$weixin_keyword_too_long = weixin_robot_str_replace(weixin_robot_get_setting('weixin_keyword_too_long'),$wechatObj);

		if($weixin_keyword_too_long){
			echo sprintf($wechatObj->get_textTpl(), $weixin_keyword_too_long);
		}
		$wechatObj->set_response('too-long');

		return true;
	}
	
	return $false;
}

//获取自定义回复列表
function weixin_robot_get_custom_keywords($match='full'){
	global $wpdb;

	$weixin_custom_keywords = get_transient('weixin_custom_keywords_'.$match);

	if($weixin_custom_keywords === false){
		$weixin_custom_keywords_table = weixin_robot_get_custom_replies_table();
		$sql = "SELECT keyword,reply,type FROM $weixin_custom_keywords_table WHERE {$weixin_custom_keywords_table}.match = '{$match}' AND status = 1";
		$weixin_custom_original_keywords = $wpdb->get_results($sql,OBJECT_K);
		
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

		set_transient('weixin_custom_keywords_'.$match,$weixin_custom_keywords,3600);
	}
	return $weixin_custom_keywords;
}
//获取内置回复列表
function weixin_robot_get_builtin_replies($type = ''){

	$weixin_builtin_replies = get_transient('weixin_builtin_replies');

	if($weixin_builtin_replies === false){
		$weixin_builtin_replies = array();
		
		$weixin_builtin_replies['[voice]'] 			= array('type'=>'full',	'reply'=>'默认语音回复',		'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[location]'] 		= array('type'=>'full',	'reply'=>'默认地理位置回复',	'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[image]'] 			= array('type'=>'full',	'reply'=>'默认图片回复',		'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[link]'] 			= array('type'=>'full',	'reply'=>'默认链接回复',		'function'=>'weixin_robot_default_reply');
		$weixin_builtin_replies['[video]'] 			= array('type'=>'full',	'reply'=>'默认视频回复',		'function'=>'weixin_robot_default_reply');

		if(weixin_robot_get_setting('weixin_advanced_api') ){
			$weixin_builtin_replies['[event-location]']	= array('type'=>'full',	'reply'=>'获取用户地理位置',	'function'=>'weixin_robot_location_event_reply');
		}

		foreach (array( 'hi', 'h', 'help', '帮助', '您好', '你好') as $welcome_keyword) {
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

		if( weixin_robot_get_setting('weixin_credit')){
			$weixin_builtin_replies['checkin']	= $weixin_builtin_replies['签到']	= array('type'=>'full', 'reply'=>'签到', 	'function'=>'weixin_robot_checkin_reply');
			$weixin_builtin_replies['credit']	= $weixin_builtin_replies['积分']	= array('type'=>'full', 'reply'=>'获取积分',	'function'=>'weixin_robot_credit_reply');
		}

		$weixin_builtin_replies = apply_filters('weixin_builtin_reply', $weixin_builtin_replies);

		set_transient('weixin_builtin_replies',$weixin_builtin_replies,60);
	}

	if($type){
		$weixin_builtin_replies_new = get_transient('weixin_builtin_replies_new');
		if($weixin_builtin_replies_new === false){
			$weixin_builtin_replies_new = array();
			foreach ($weixin_builtin_replies as $key => $weixin_builtin_reply) {
				$weixin_builtin_replies_new[$weixin_builtin_reply['type']][$key] = $weixin_builtin_reply;
			}
			set_transient('weixin_builtin_replies_new',$weixin_builtin_replies_new,60);
		}
		return $weixin_builtin_replies_new[$type];
	}else{
		return $weixin_builtin_replies;
	}
}

// 把微信的 XML 提交给第三方微信平台
function weixin_robot_3rd_reply(){
	global $wechatObj;

	$third_token	= weixin_robot_get_setting('weixin_3rd_token');
	$timestamp		= (string)time();
	$nonce 			= (string)(time()-rand(1000,10000));

	$signature		= array($third_token, $timestamp, $nonce);
	sort($signature);
	$signature		= implode( $signature );
	$signature		= sha1( $signature );

	$third_url		= weixin_robot_get_setting('weixin_3rd_url');
	$third_url		= add_query_arg(array('timestamp'=>$timestamp,'nonce'=>$nonce,'signature'=>$signature),$third_url);

	$postStr		= (isset($GLOBALS["HTTP_RAW_POST_DATA"]))?$GLOBALS["HTTP_RAW_POST_DATA"]:'';

	$response = wp_remote_post(
		$third_url, 
		array( 
			'headers' => array( 'Content-Type' => 'text/xml' ),
			'body'=>$postStr
		)
	);

	//file_put_contents(WP_CONTENT_DIR.'/uploads/test.html',var_export($postStr,true));
	//file_put_contents(WP_CONTENT_DIR.'/uploads/test.html',var_export($response,true));

	echo $response['body'];
	$wechatObj->set_response('3rd');
}

// 欢迎回复
function weixin_robot_welcome_reply(){
	global $wechatObj;
	$weixin_welcome = weixin_robot_str_replace(weixin_robot_get_setting('weixin_welcome'),$wechatObj);
	echo sprintf($wechatObj->get_textTpl(), $weixin_welcome);
	$wechatObj->set_response('welcome');
}

// 第一次订阅回复
function weixin_robot_subscribe_reply(){
	global $wechatObj;
	weixin_robot_welcome_reply();
	$weixin_openid = $wechatObj->get_fromUsername();
	if($weixin_openid){
		$weixin_user = array('subscribe'=>1);
		weixin_robot_update_user($weixin_openid,$weixin_user);
	}
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
	$weixin_default = weixin_robot_str_replace(weixin_robot_get_setting('weixin_default_'.$keyword),$wechatObj);
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
    	$weixin_enter = weixin_robot_str_replace(weixin_robot_get_setting('weixin_enter'),$wechatObj);
    	echo sprintf($wechatObj->get_textTpl(), $weixin_enter);
    	wp_cache_set($weixin_openid, current_time('timestamp'), 'weixin_enter_reply', 60*60*24);
    	$wechatObj->set_response('enter-reply');
	}else{
		$wechatObj->set_response('location');
	}
}


//设置时间为最近7天
function weixin_robot_posts_where_7( $where = '' ) {
	return $where . " AND post_date > '" . date('Y-m-d', strtotime('-7 days')) . "'";
}

//设置时间为最近30天
function weixin_robot_posts_where_30( $where = '' ) {
	return $where . " AND post_date > '" . date('Y-m-d', strtotime('-60 days')) . "'";
}

function weixin_robot_advanced_reply(){
	global $wechatObj;
	$wechatObj->set_response('advanced');
	$wechatObj->query();
}

//按照时间排序
function weixin_robot_new_posts_reply(){
	add_filter('weixin_query','weixin_robot_new_query');
	weixin_robot_advanced_reply();
}
function weixin_robot_new_query($weixin_query_array){
	unset($weixin_query_array['s']);
	return $weixin_query_array;
}
//随机排序
function weixin_robot_rand_posts_reply(){
	add_filter('weixin_query','weixin_robot_rand_query');
	weixin_robot_advanced_reply();
}
function weixin_robot_rand_query($weixin_query_array){
	unset($weixin_query_array['s']);
	$weixin_query_array['orderby']		= 'rand';
	return $weixin_query_array;
}
//按照浏览排序
function weixin_robot_hot_posts_reply(){
	add_filter('weixin_query','weixin_robot_hot_query');
	weixin_robot_advanced_reply();
}
function weixin_robot_hot_query($weixin_query_array){
	unset($weixin_query_array['s']);
	$weixin_query_array['meta_key']		= 'views';
	$weixin_query_array['orderby']		= 'meta_value_num';
	return $weixin_query_array;
}
//按照留言数排序
function weixin_robot_comment_posts_reply(){
	add_filter('weixin_query','weixin_robot_comment_query');
	weixin_robot_advanced_reply();
}
function weixin_robot_comment_query($weixin_query_array){
	unset($weixin_query_array['s']);
	$weixin_query_array['orderby']		= 'comment_count';
	return $weixin_query_array;
}
//7天内最热
function weixin_robot_hot_7_posts_reply(){
	ad_filter('weixin_query','weixin_robot_hot_query');
	add_filter('posts_where', 'weixin_robot_posts_where_7' );
	weixin_robot_advanced_reply();
}
//7天内留言最多 
function weixin_robot_comment_7_posts_reply(){
	add_filter('weixin_query','weixin_robot_comment_query');
	add_filter('posts_where', 'weixin_robot_posts_where_7' );
	weixin_robot_advanced_reply();
}
//7天内最热
function weixin_robot_hot_30_posts_reply(){
	add_filter('weixin_query','weixin_robot_hot_query');
	add_filter('posts_where', 'weixin_robot_posts_where_30' );
	weixin_robot_advanced_reply();
}
//30天内留言最多
function weixin_robot_comment_30_posts_reply(){
	add_filter('weixin_query','weixin_robot_comment_query');
	add_filter('posts_where', 'weixin_robot_posts_where_30' );
	weixin_robot_advanced_reply();
}
//如果搜索关键字是分类名或者 tag 名，直接返回该分类或者tag下最新日志
add_filter('weixin_query','weixin_robot_taxonomy_query', 99);
function weixin_robot_taxonomy_query($weixin_query_array){
	if(isset($weixin_query_array['s'])){
		global $wpdb;
		$keyword = $weixin_query_array['s'];
		$term = $wpdb->get_row("SELECT term_id, slug, taxonomy FROM {$wpdb->prefix}term_taxonomy tt INNER JOIN {$wpdb->prefix}terms t USING ( term_id ) WHERE lower(t.name) = '{$keyword}' OR t.slug = '{$keyword}' LIMIT 0 , 1");

		if($term){
			if($term->taxonomy == 'category'){
				unset($weixin_query_array['s']);
				$weixin_query_array['cat']		= $term->term_id;
			}elseif ($term->taxonomy == 'post_tag') {
				unset($weixin_query_array['s']);
				$weixin_query_array['tag_id']	= $term->term_id;
			}
			$weixin_query_array = apply_filters('weixin_taxonomy_query',$weixin_query_array,$term);
			
		}
	}
	return $weixin_query_array;
}
//自定义图文日志查询
function weixin_robot_img_reply_query($weixin_query_array){
	$weixin_custom_keywords = weixin_robot_get_custom_keywords();
	$weixin_custom_reply = $weixin_custom_keywords[$weixin_query_array['s']];
	$post_ids = explode(',', $weixin_custom_reply->reply);

	$weixin_query_array['post__in']		= $post_ids;
	$weixin_query_array['orderby']		= 'post__in';

	unset($weixin_query_array['s']);
	$weixin_query_array['post_type']	= 'any';

	return $weixin_query_array;
}
// 通过自定义字段设置改变图文的链接
// 给用户添加 query_id，用于访问页面时，获取当前用户
add_filter('weixin_url','weixin_robot_url_add_query_id', 99);
function weixin_robot_url_add_query_id($url){
	global $wechatObj, $post;

	if($weixin_url = get_post_meta($post->ID, 'weixin_url', true)){
		$url = $weixin_url;
	}

	$weixin_openid = $wechatObj->get_fromUsername();

	$query_id = weixin_robot_user_get_query_id($weixin_openid);

	$query_key = apply_filters('weixin_user_query_string_key','weixin_user_id');

	return add_query_arg($query_key, $query_id, $url);	
}

// 设置如果系统安装了七牛存储或者 WPJAM Thumbnail 高级缩略图插件，则使用它们截图
add_filter('weixin_thumb','wpjam_weixin_thumb_filter',10,3);
function wpjam_weixin_thumb_filter($thumb,$size,$post){
	if(function_exists('wpjam_get_post_thumbnail_src')){
		if(wpjam_has_post_thumbnail()){
			$thumb = wpjam_get_post_thumbnail_src($post, $size);
		}	
	}
	return $thumb;
}
// 获取用户的最新的地理位置并缓存10分钟。
function weixin_robot_get_user_location($weixin_openid){
	$location = wp_cache_get($weixin_openid,'weixin_location');
	if($location === false){
		global $wpdb;
		$weixin_messages_table = weixin_robot_get_messages_table();

		$time = current_time('timestamp') - 3600*(2+get_option('gmt_offset'));

		$location = $wpdb->get_row($wpdb->prepare("SELECT Location_X as x, Location_Y as y FROM {$weixin_messages_table} WHERE Location_X >0 AND Location_Y >0 AND FromUserName=%s AND CreateTime>%d ORDER BY CreateTime DESC LIMIT 0,1;",$weixin_openid,$time),ARRAY_A);
		wp_cache_set($weixin_openid, $location,'weixin_location', 600);
	}
	return $location;
}


// 常用函数

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

function weixin_robot_str_replace($str, $wechatObj){
	$weixin_openid = $wechatObj->get_fromUsername();
	if($weixin_openid){
		$query_id = weixin_robot_user_get_query_id($weixin_openid);	
		return str_replace(array("\r\n",'[openid]','[query_id]'),array("\n",$weixin_openid,$query_id),$str);
	}else{
		return $str;
	}
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


/*
function wpjam_basic_filter($original){
	$weixin_robot_basic = weixin_robot_get_basic_option();

	global $wp_current_filter;

	//最后一个才是当前的 filter
	$wpjam_current_filter = $wp_current_filter[count($wp_current_filter)-1];

	if(isset($weixin_robot_basic[$wpjam_current_filter])){
		if($weixin_robot_basic[$wpjam_current_filter ]){
			return $weixin_robot_basic[$wpjam_current_filter];
		}
	}else{
		return $original;
	}
}
*/
/*function weixin_robot_get_welcome_keywords(){
	return array( 'hi', 'h', 'help', '帮助', '您好', '你好');
}*/