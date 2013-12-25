<?php 

function weixin_robot_users_table(){
	global $wpdb;
	return $wpdb->prefix.'weixin_users';
}

register_activation_hook( WEIXIN_ROBOT_PLUGIN_FILE,'weixin_robot_users_create_table');
function weixin_robot_users_create_table(){
	global $wpdb;
	$weixin_users_table = weixin_robot_users_table();

	if($wpdb->get_var("show tables like '{$weixin_users_table}'") != $weixin_users_table) {
		$sql = "
		CREATE TABLE IF NOT EXISTS `{$weixin_users_table}` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `openid` varchar(30) NOT NULL,
		  `nickname` varchar(50) NOT NULL COMMENT '昵称',
		  `name` varchar(50) NOT NULL COMMENT '姓名',
		  `phone` varchar(20) NOT NULL COMMENT '电话号码',
		  `id_card` varchar(18) NOT NULL COMMENT '身份证',
		  `address` text NOT NULL COMMENT '地址',
		  `subscribe` int(1) NOT NULL default '1',
		  `subscribe_time` int(10) NOT NULL,
		  `sex` int(1) NOT NULL,
		  `city` varchar(255) NOT NULL,
		  `country` varchar(255) NOT NULL,
		  `province` varchar(255) NOT NULL,
		  `language` varchar(255) NOT NULL,
		  `headimgurl` varchar(255) NOT NULL,
		  `last_update` int(10) NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `weixin_openid` (`openid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
		";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
		dbDelta($sql);
	}
}

function weixin_robot_get_remote_user($weixin_openid){
	$weixin_robot_access_token = weixin_robot_get_access_token();

	$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$weixin_robot_access_token.'&openid='.$weixin_openid;

	$response = wp_remote_get($url,array('sslverify'=>false));

	if(is_wp_error($response)){
		echo $response->get_error_code().'：'. $response->get_error_message();
		exit;
	}

	$weixin_user = json_decode($response['body'],true);	

	$weixin_user['last_update'] = current_time('timestamp');

	return $weixin_user;
}

function weixin_robot_get_user($weixin_openid,$force=0){

	if(!$weixin_openid )  wp_die('weixin_openid 为空或非法。');
	if(strlen($weixin_openid) < 28) wp_die('非法 weixin_openid');

	$weixin_user = wp_cache_get($weixin_openid,'weixin_user');

	if($weixin_user === false){

		global $wpdb;

		$weixin_users_table = weixin_robot_users_table();

		$weixin_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$weixin_users_table} WHERE openid=%s",$weixin_openid),ARRAY_A);

		if($weixin_user){
			if(weixin_robot_get_setting('weixin_advanced_api') && (current_time('timestamp') - $weixin_user['last_update']) > 86400*30 ) {
				$weixin_user = weixin_robot_get_remote_user($weixin_openid);
				$wpdb->update($weixin_users_table,$weixin_user,array('openid'=>$weixin_openid));

				wp_cache_set($weixin_openid, $weixin_user, 'weixin_user',3600);
			}
		}else{
			if($force){
				return false;
			}else{
				if(weixin_robot_get_setting('weixin_advanced_api')){
					$weixin_user = weixin_robot_get_remote_user($weixin_openid);
				}else{
					$weixin_user = array('openid'=>trim($weixin_openid));
				}
				if(isset($weixin_user['openid'])){
					$wpdb->insert($weixin_users_table,$weixin_user);
					wp_cache_set($weixin_openid, $weixin_user, 'weixin_user',3600);
				}
			}
		}
	}
	return $weixin_user;
}

function weixin_robot_update_user($weixin_openid,$weixin_user){ // 更新自定义字段
	global $wpdb;

	$weixin_users_table = weixin_robot_users_table();

	$old_user = weixin_robot_get_user($weixin_openid);
	
	$weixin_user = wp_parse_args($weixin_user,$old_user);

	$wpdb->update($weixin_users_table,$weixin_user,array('openid'=>$weixin_openid));

	wp_cache_delete($weixin_openid, 'weixin_user');

	return $weixin_user;
}

function weixin_rebot_sent_user($weixin_openid, $content, $reply_type='text'){
	$weixin_robot_access_token = weixin_robot_get_access_token();
	$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$weixin_robot_access_token;

	$request = array();

	$request['touser']	= $weixin_openid;

	if($reply_type == 'text'){
		$request['msgtype']	= 'text';
		$request['text']	= array('content' => urlencode($content));
	}elseif($reply_type == 'img'){

	}
	

	$response = wp_remote_post($url,array( 'body' => urldecode(json_encode($request)),'sslverify'=>false));

	if(is_wp_error($response)){
		echo $response->get_error_code().'：'. $response->get_error_message();
		exit;
	}

	$response = json_decode($response['body'],true);

	if($response['errcode']){
		return $response['errcode'].': '.$response['errmsg'];
	}else{
		return '发送成功';
	}
}


function weixin_robot_user_get_query_id($weixin_openid){
	$weixin_robot_user_md5 = apply_filters('weixin_robot_user_md5','weixin');
    $check = substr(md5($weixin_robot_user_md5.$weixin_openid),0,2);
    return $check . $weixin_openid;
}

function weixin_robot_user_get_openid($query_id){
    $weixin_openid = substr($query_id, 2);
    if($query_id == weixin_robot_user_get_query_id($weixin_openid)){
        return $weixin_openid;
    }else{
        return false;
    }
}
