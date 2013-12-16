<?php
function weixin_robot_credits_table(){
	global $wpdb;
	return $wpdb->prefix.'weixin_credits';
}

register_activation_hook( WEIXIN_ROBOT_PLUGIN_FILE,'weixin_robot_credits_create_table');
function weixin_robot_credits_create_table() {	
	
	global $wpdb;

	$weixin_credits_table = weixin_robot_credits_table();

	if($wpdb->get_var("show tables like '{$weixin_credits_table}'") != $weixin_credits_table) {
		$sql = "
		CREATE TABLE IF NOT EXISTS `{$weixin_credits_table}` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `weixin_openid` varchar(30) NOT NULL,
		  `operator_id` bigint(20) default NULL,
		  `credit_change` int(10) NOT NULL COMMENT '本次变动的积分',
		  `credit` int(10) NOT NULL COMMENT '变动后的总积分',
		  `exp_change` int(10) NOT NULL COMMENT '本次变动的经验值',
		  `exp` int(10) NOT NULL COMMENT '变动后的总经验值',
		  `type` varchar(20) NOT NULL COMMENT '积分变动类型',
		  `post_id` bigint(20) NOT NULL default '0',
		  `note` varchar(255) NOT NULL COMMENT '备注',
		  `limit` int(1) NOT NULL default '0' COMMENT '是否到每日积分上限',
		  `time` datetime NOT NULL COMMENT '+8时区',
		  `url` char(255) NOT NULL COMMENT '操作的相关 URL',
		  PRIMARY KEY  (`id`),
		  KEY `type` (`type`),
		  KEY `weixin_openid` (`weixin_openid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
		";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
		dbDelta($sql);
	}
}

function weixin_robot_get_credit($weixin_openid){
	$credit = wp_cache_get($weixin_openid, 'weixin_user_credit');

	if($credit === false){
		global $wpdb;
		$weixin_credits_table = weixin_robot_credits_table();

		$credit = $wpdb->get_var($wpdb->prepare("SELECT credit FROM {$weixin_credits_table} WHERE weixin_openid=%s ORDER BY id DESC LIMIT 0,1",$weixin_openid));

		if(!$credit){
			$credit = 0;
		}

		wp_cache_set($weixin_openid,$credit,'weixin_user_credit');
	}

	return $credit;
}

function weixin_robot_get_exp($weixin_openid){
	$exp = wp_cache_get($weixin_openid,'weixin_user_exp');

	if($exp === false){
		global $wpdb;
		$weixin_credits_table = weixin_robot_credits_table();

		$exp = $wpdb->get_var($wpdb->prepare("SELECT exp FROM {$weixin_credits_table} WHERE weixin_openid=%s ORDER BY id DESC LIMIT 0,1",$weixin_openid));

		if(!$exp){
			$exp = 0;
		}

		wp_cache_set($weixin_openid, $exp,'weixin_user_exp');
	}

	return $exp;
}

function weixin_robot_add_credit($arg){
	if(!is_array($arg) || count($arg)<1) wp_die('系统错误(1000)，请通知管理员。');

	global $wpdb;
	$weixin_credits_table = weixin_robot_credits_table();

	$default_args = array(
		'type'			=> '', 		// 类型
		'post_id'		=> 0, 		// 动作是否和日志有关
		'weixin_openid'	=> 0, 		// 微信 ID
		'operator_id'	=> 0, 		// 默认为0
		'credit_change'	=> 0, 		// 改动的积分
		'exp_change'	=> 0, 		// 改动的经验值
		'note'			=> '', 		// 注释
		'multiple'		=> 1 		// 删除的倍数
	);

	extract(wp_parse_args($arg, $default_args));

	if(!$type) wp_die('未知动态类型。');

	if(!$weixin_openid )  wp_die('weixin_openid 为空或非法。');

	$weixin_user = weixin_robot_get_user($weixin_openid); 

	$old_credit	= weixin_robot_get_credit($weixin_openid);
	$old_exp 	= weixin_robot_get_exp($weixin_openid);;

	$credit_change = intval($credit_change) * intval($multiple);
	if(!$exp_change){
		$exp_change = $credit_change;
	}

	$limit = 0;

	if($credit_change > 0 && $operator_id == 0 ){
		$today_credit_sum =  (int)$wpdb->get_var($wpdb->prepare("SELECT SUM(credit_change) FROM {$weixin_credits_table} WHERE weixin_openid=%s AND time<=%s AND time>=%s AND credit_change > 0 AND operator_id = 0",$weixin_openid,date('Y-m-d', current_time('timestamp')).' 23:59:59',date('Y-m-d', current_time('timestamp')).' 00:00:00'));

		if($today_credit_sum >= weixin_robot_get_setting('weixin_day_credit_limit')){
			$credit_change = 0;
			$limit = 1;
		}
	}

	$credit = $old_credit + $credit_change;
	$exp 	= $old_exp + $exp_change;

	// 积分变化，需要清理用户缓存
	wp_cache_delete($weixin_openid, 'weixin_user_credit'); 
	wp_cache_delete($weixin_openid, 'weixin_user_exp'); 

	$data = array(
		'weixin_openid'		=> $weixin_openid,
		'operator_id'		=> $operator_id,
		'credit_change'		=> $credit_change,
		'credit'			=> $credit,
		'exp_change'		=> $exp_change,
		'exp'				=> $exp,
		'type'				=> $type,
		'post_id'			=> $post_id,
		'note'				=> $note,
		'limit'				=> $limit,
		'time'				=> current_time('mysql'),
		'url'				=> $_SERVER['REQUEST_URI']
	);

	$format = array( '%s', '%d', '%d', '%d', '%d', '%d', '%s', '%d', '%s', '%s', '%s' );

	$wpdb->insert($weixin_credits_table, $data, $format);

	return $credit_change;
}

/*
自定义hook 用于积分处理
*/
// 签到回复
add_filter('weixin_custom_keyword','wpjam_checkin_weixin_custom_keyword',10,2);
function wpjam_checkin_weixin_custom_keyword($false,$keyword){
	if($false === false){
		if( in_array($keyword, array( '签到', 'checkin') ) ) {
			global $wechatObj;
			$weixin_openid = $wechatObj->get_fromUsername();

			$credit_change = weixin_robot_daily_credit_checkin_2($weixin_openid);

			$credit = weixin_robot_get_credit($weixin_openid);

			if($credit_change){
				$checkin = apply_filters('weixin_checkin_success','签到成功，添加 [credit_change]积分。你现在共有[credit]积分！',$weixin_openid);
			}else{
				$checkin = apply_filters('weixin_checkined','你在24小时内已经签到过了。你现在共有[credit]积分！',$weixin_openid);
			}
			
			$checkin = str_replace(array('[credit_change]','[credit]'), array($credit_change, $credit), $checkin);
			echo sprintf($wechatObj->get_textTpl(), $checkin);
			$wechatObj->set_response('checkin');
			wpjam_do_weixin_custom_keyword();
		}
	}
    return $false;
}

function weixin_robot_daily_credit_checkin($weixin_openid){ // 过了 24 小时才能签到

	if(!$weixin_openid) wp_die('weixin_openid 为空。');

	$last_checkin_time = wp_cache_get($weixin_openid, 'last_checkin_time');
	$type = 'checkin';

	if($last_checkin_time === false){
		global $wpdb;
		$weixin_credits_table = weixin_robot_credits_table();
		$last_checkin_time = $wpdb->get_var($wpdb->prepare("SELECT SQL_NO_CACHE `time` FROM {$weixin_credits_table} WHERE `type`=%s AND weixin_openid=%s ORDER BY id DESC LIMIT 1",$type,$weixin_openid));
		if($last_checkin_time){
			$last_checkin_time = (int)strtotime($last_checkin_time);
			wp_cache_set($weixin_openid, $last_checkin_time, 'last_checkin_time', 60*60*24);
		}else{
			$last_checkin_time = 0;
		}
	}

	if(current_time('timestamp') - $last_checkin_time > 86400){
		$credit_change = weixin_robot_get_setting('weixin_checkin_credit');
		
		$credit_change = weixin_robot_add_credit(array('type'=>$type, 'weixin_openid'=>$weixin_openid, 'credit_change'=>$credit_change, 'note'=>'每日签到'));
		$new_last_checkin_time = current_time('timestamp');
		wp_cache_set($weixin_openid, $new_last_checkin_time, 'last_checkin_time', 60*60*24);
		return $credit_change;
	}else{
		return false;
	}
}

function weixin_robot_daily_credit_checkin_2($weixin_openid){ //过了 0 点就能签到

	if(!$weixin_openid) wp_die('weixin_openid 为空。');

	$type = 'checkin';
	$current_time = current_time('timestamp');

	$current_date = date('Ymd',$current_time);

	$has_checkin = wp_cache_get($weixin_openid, 'has_checkin_'.$current_date);

	if($has_checkin === false){
		global $wpdb;
		$weixin_credits_table = weixin_robot_credits_table();

		$last_checkin_time = $wpdb->get_var($wpdb->prepare("SELECT SQL_NO_CACHE `time` FROM {$weixin_credits_table} WHERE `type`=%s AND weixin_openid=%s AND ( ( YEAR( time ) = %d AND MONTH( time ) = %d AND DAYOFMONTH( time ) = %d ) )  ORDER BY id DESC LIMIT 1", $type, $weixin_openid, date('Y',$current_time), date('m',$current_time), date('d',$current_time) ));
		
		if($last_checkin_time){	
			$has_checkin = 1;
			wp_cache_set($weixin_openid, 1, 'has_checkin_'.$current_date, 60*60*24);
		}else{
			$has_checkin = 0;
		}
	}

	if($has_checkin == 0){
		$credit_change = weixin_robot_get_setting('weixin_checkin_credit');
		$credit_change =  weixin_robot_add_credit(array('type'=>$type, 'weixin_openid'=>$weixin_openid, 'credit_change'=>$credit_change, 'note'=>'每日签到'));
		wp_cache_set($weixin_openid, 1, 'has_checkin_'.$current_date, 60*60*24);
		return $credit_change;
	}else{
		return false;
	}

}

add_filter('weixin_response_types','weixin_robot_credit_response_types');
function weixin_robot_credit_response_types($response_types){
    $response_types['checkin']	= '签到回复';
    return $response_types;
}

add_filter('weixin_url','weixin_robot_url_add_query_id');
function weixin_robot_url_add_query_id($url){
	global $wechatObj;
	$weixin_openid = $wechatObj->get_fromUsername();

	$query_id = weixin_robot_user_get_query_id($weixin_openid);

	return add_query_arg('weixin_user_id', $query_id, $url);
}

add_action('weixin_share','weixin_robot_share_credit');
function weixin_robot_share_credit($share_type){
	if(isset($_GET['weixin_user_id'])){
?>
weixin_robot_credit_share('<?php echo $share_type;?>' );
<?php
	}
}

add_action('wp_footer','weixin_robot_credit_footer',99);
function weixin_robot_credit_footer(){
	if(is_singular() && is_weixin() && isset($_GET['weixin_user_id'])){
		global $post;
		$nonce = wp_create_nonce( 'weixin_share' );
?>
<script type="text/javascript">
function weixin_robot_credit_share(share_type){
	jQuery.ajax({
		type: "post",
		url: "<?php echo admin_url('admin-ajax.php');?>",
		data: { 
			action:			'weixin_share', 
			share_type:		share_type,
			post_id: 		'<?php echo $post->ID;?>',
			weixin_user_id: '<?php echo $_GET['weixin_user_id'];?>', 
			_ajax_nonce: 	'<?php echo $nonce; ?>' 
		},
		success: function(html){
			alert(html);
		}
	});
}
</script>
<?php
	}
}

add_action( 'wp_enqueue_scripts', 'weixin_robot_enqueue_scripts' );
function weixin_robot_enqueue_scripts() {
	wp_enqueue_script('jquery');
}

add_action('wp_ajax_weixin_share', 'weixin_robot_credit_share_action_callback');
add_action('wp_ajax_nopriv_weixin_share', 'weixin_robot_credit_share_action_callback');

function weixin_robot_credit_share_action_callback(){
	check_ajax_referer( "weixin_share" );

	$weixin_openid 	= weixin_robot_user_get_openid($_POST['weixin_user_id']);

	if($weixin_openid == false){
		exit;
	}

	$share_type		= $_POST['share_type'];
	$post_id		= $_POST['post_id'];

	if($weixin_openid && $share_type && $post_id){
		if($share_type == 'SendAppMessage'){
			$credit_change = weixin_robot_get_setting('weixin_SendAppMessage_credit');
			$share_message = '发送文章给朋友';
		}elseif($share_type == 'ShareTimeline'){
			$credit_change = weixin_robot_get_setting('weixin_ShareTimeline_credit');
			$share_message = '分享文章到朋友圈';
		}elseif($share_type == 'ShareWeibo'){
			$credit_change = weixin_robot_get_setting('weixin_ShareWeibo_credit');
			$share_message = '分享文章到腾讯微博';
		}elseif($share_type == 'ShareFB'){
			$credit_change = weixin_robot_get_setting('weixin_SendAppMessage_credit');
			$share_message = '分享文章到Facebook';
		}

		global $wpdb;

		$weixin_credits_table = weixin_robot_credits_table();

		if($wpdb->query($wpdb->prepare("SELECT * FROM {$weixin_credits_table} WHERE weixin_openid=%s AND type=%s AND post_id=%d",$weixin_openid,$share_type,$post_id))){
			$share_message = '你已经执行过该操作了';
		}else{
			$credit_change = weixin_robot_add_credit(array('type'=>$share_type, 'weixin_openid'=>$weixin_openid, 'post_id'=>$post_id, 'credit_change'=>$credit_change, 'note'=>$share_message));

			if($credit_change == 0 ){
				$share_message = '你当日加分已经超过'.weixin_robot_get_setting('weixin_day_credit_limit').'分了。';
			}else{
				$share_message = $share_message .'，获取 '.$credit_change.' 积分！';
			}
		}

		echo $share_message;
	}
	exit;
}