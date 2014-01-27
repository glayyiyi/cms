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

		if(!$credit) $credit = 0;

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

		if(!$exp) $exp = 0;

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
		'exp_change'	=> false, 	// 改动的经验值
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
	if($exp_change === false){ // 传递进来 0 就不加
		$exp_change = $credit_change;
	}

	$limit = 0;

	if($credit_change > 0 && $operator_id == 0 ){ // 有 operator_id 就不检测每日上限
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

	do_action('weixin_credit',$arg);

	return $credit_change;
}

// 回复用户现有的积分
function weixin_robot_credit_reply(){
	global $wechatObj;
	$weixin_openid = $wechatObj->get_fromUsername();
	$query_id = weixin_robot_user_get_query_id($weixin_openid);
	$profile_link = home_url('/?weixin_user_profile&weixin_user_id='.$query_id);
	
	$credit = weixin_robot_get_credit($weixin_openid);
	$credit_reply = apply_filters('weixin_credit_reply','你现在共有[credit]积分，点击这里查看<a href="[profile_link]">积分历史</a>。',$weixin_openid);

	$credit_reply = str_replace(array('[credit]','[profile_link]'), array($credit,$profile_link), $credit_reply);
	echo sprintf($wechatObj->get_textTpl(), $credit_reply);
	$wechatObj->set_response('credit');
}

/*
自定义hook 用于积分处理
*/
// 签到回复
function weixin_robot_checkin_reply(){
	
	if(isset($_GET['yixin']) ){
		global $wechatObj;
		echo sprintf($wechatObj->get_textTpl(), '易信不支持签到和积分系统。');
		$wechatObj->set_response('checkin');
		wpjam_do_weixin_custom_keyword();
	}

	global $wechatObj;
	$weixin_openid = $wechatObj->get_fromUsername();

	$credit_change = weixin_robot_daily_credit_checkin_2($weixin_openid);

	$credit = weixin_robot_get_credit($weixin_openid);

	if($credit_change === false){
		$checkin_reply = apply_filters('weixin_checkined','你在24小时内已经签到过了。你现在共有[credit]积分！',$weixin_openid);
	}else{
		$checkin_reply = apply_filters('weixin_checkin_success','签到成功，添加 [credit_change]积分。你现在共有[credit]积分！',$weixin_openid);
	}
	
	$checkin_reply = str_replace(array('[credit_change]','[credit]'), array($credit_change, $credit), $checkin_reply);
	echo sprintf($wechatObj->get_textTpl(), $checkin_reply);
	$wechatObj->set_response('checkin');

	do_action('weixin_checkin',$credit_change);
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
    $response_types['checkin']		= '回复签到';
    $response_types['credit']		= '回复积分';
    return $response_types;
}

// 需要加载 jQuery 用于 AJAX 获取积分。
add_action( 'wp_enqueue_scripts', 'weixin_robot_enqueue_scripts' );
function weixin_robot_enqueue_scripts() {
	wp_enqueue_script('jquery');
}

// 用户微信分享的脚本
add_action("wp_head","weixin_robot_share_head",99);
function weixin_robot_share_head(){

	if(is_singular() && is_weixin()){
	global $post;
	$nonce = wp_create_nonce( 'weixin_share' );
?>
<script type="text/javascript">
function htmlEncode(e) {
    return e.replace(/&/g, "&amp;").replace(/ /g, "&nbsp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br />").replace(/"/g, "&quot;")
}

function htmlDecode(e) {
    return e.replace(/&#39;/g, "'").replace(/<br\s*(\/)?\s*>/g, "\n").replace(/&nbsp;/g, " ").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"').replace(/&amp;/g, "&")
}

<?php if(isset($_GET['weixin_user_id']) && $_GET['weixin_user_id']) { ?>
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
<?php }?>

var 
	appId	= "",
	img		= "<?php echo get_post_weixin_thumb($post,array(120,120)); ?>",
	link	= "<?php if(isset($_GET['weixin_user_id'])) { echo add_query_arg('weixin_user_id', $_GET['weixin_user_id'], get_permalink($post->ID)); } else {echo get_permalink($post->ID);} ;?>",
	link	="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29f139b356296675&redirect_uri=http://www.appcn100.com/cms/appcn100/?weixin-oauth2&weixin-url="+link+"&response_type=code&scope=snsapi_base&state=base#wechat_redirect";
	title	= htmlDecode("<?php echo $post->post_title; ?>"),
	desc	= htmlDecode("<?php echo get_post_excerpt($post); ?>"),
	fakeid	= "";

	desc = desc || link;
(function(){
	var onBridgeReady=function(){

		//WeixinJSBridge.call("hideOptionMenu");

		//WeixinJSBridge.call('hideToolbar');

	    /*jQuery("#weixin-user").on('click', function(){
            WeixinJSBridge.invoke('profile',{
                'username':'gh_d0e8fa0609a2',
                'scene':'57'
            });
        });
		*/

		/*WeixinJSBridge.invoke('getNetworkType',{},
		function(e){
	    	alert(e.err_msg);
	    });*/

		// 发送给好友; 
		WeixinJSBridge.on('menu:share:appmessage', function(argv){
			WeixinJSBridge.invoke('sendAppMessage',{
				"appid":		appId,
				"img_url":		img,
				"img_width":	"120",
				"img_height":	"120",
				"link":			link,
				"desc":			desc,
				"title":		title
			}, function(res){
				weixin_robot_credit_share('SendAppMessage');
				<?php do_action('weixin_share','SendAppMessage');?>
			});
		});
		// 分享到朋友圈;
		WeixinJSBridge.on('menu:share:timeline', function(argv){
			<?php //do_action('weixin_share','ShareTimeline');?>
			WeixinJSBridge.invoke('shareTimeline',{
				"img_url":		img,
				"img_width":	"120",
				"img_height": 	"120",
				"link":			link,
				"desc":			desc,
				"title":		title
			}, function(res){
				weixin_robot_credit_share('ShareTimeline');
				<?php do_action('weixin_share','ShareTimeline');?>
			});
		});
		// 分享到微博;
		WeixinJSBridge.on('menu:share:weibo', function(argv){
			WeixinJSBridge.invoke('shareWeibo',{
				"content":		title+' '+link,
				"url":			link
			}, function(res){
				weixin_robot_credit_share('ShareWeibo');
				<?php do_action('weixin_share','ShareWeibo');?>
			});
		});
		// 分享到Facebook
		WeixinJSBridge.on('menu:share:facebook', function(argv){
			weixin_robot_credit_share('ShareFB');
			<?php do_action('weixin_share','ShareFB');?>
			WeixinJSBridge.invoke('shareFB',{
				"img_url":		img,
				"img_width":	"120",
				"img_height":	"120",
				"link":			link,
				"desc":			desc,
				"title":		title
			}, function(res){});
		});
	};
	if(document.addEventListener){
		document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
	}else if(document.attachEvent){
		document.attachEvent('WeixinJSBridgeReady',		onBridgeReady);
		document.attachEvent('onWeixinJSBridgeReady',	onBridgeReady);
	}
})();
</script>
<?php 
	}
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

//定义微信个人中心模板
add_action('init','weixin_robot_user_parse_request');
function weixin_robot_user_parse_request($wp){
	if(isset($_GET['weixin_user_profile']) && isset($_GET['weixin_user_id'])){
		if(file_exists(TEMPLATEPATH.'/weixin/weixin-user-profile.php')){
			include(TEMPLATEPATH.'/weixin/weixin-user-profile.php');
		}else{
			include(WEIXIN_ROBOT_PLUGIN_DIR.'/template/weixin-user-profile.php');
		}
        exit;
	}
}


add_action('weixin_admin_menu', 'weixin_robot_credit_admin_menu',2);
function weixin_robot_credit_admin_menu(){
	weixin_robot_add_submenu_page('credit', '微信积分记录');
}

function weixin_robot_credit_page(){
	global $plugin_page, $current_user;

	if(isset($_POST['weixin_robot_credit_nonce']) && wp_verify_nonce($_POST['weixin_robot_credit_nonce'], 'weixin_robot' )){
		$weixin_openid	= stripslashes( trim( $_POST['weixin_openid'] ) );
		$credit_change	= stripslashes( trim( $_POST['credit_change'] ) );
		$note			= stripslashes( trim( $_POST['note'] ) );
		
		if( empty($weixin_openid) || empty($credit_change)){
			$err_msg = '微信 OpenID 和 积分不能为空';
		}elseif(weixin_robot_get_user($weixin_openid,1) === false){
			$err_msg = '微信OpenID不存在';
		}elseif (!is_numeric($credit_change)) {
			$err_msg = '积分必须为数字';
		}

		if(empty($err_msg)){
			$args = array(
				'type'			=> 'manual', 
				'weixin_openid'	=> $weixin_openid,
				'operator_id'	=> $current_user->ID,
				'credit_change'	=> $credit_change,
				'exp_change'	=> 0,
				'note'			=> $note,
			);
			weixin_robot_add_credit($args);	
			$succeed_msg = '修改成功';
		}
		
	}

?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
			<h2>
				<?php if(isset($_GET['action']) && $_GET['action'] == 'add'){ ?>
					手工修改积分
					<a href="<?php echo admin_url('admin.php?page='.$plugin_page); ?>" class="add-new-h2">返回列表</a>
				<?php } else { ?>
					微信积分记录 
					<a href="<?php echo admin_url('admin.php?page='.$plugin_page); ?>&amp;action=add" class="add-new-h2">手工修改</a>
				<?php } ?>
			</h2>

			<?php if(!empty($succeed_msg)){?>
			<div class="updated">
				<p><?php echo $succeed_msg;?></p>
			</div>
			<?php }?>
			<?php if(!empty($err_msg)){?>
			<div class="error" style="color:red;">
				<p>错误：<?php echo $err_msg;?></p>
			</div>
			<?php }?>
		<?php 
			if(isset($_GET['action']) && $_GET['action'] == 'add'){
				weixin_robot_credit_add();
			}else{
				weixin_robot_credit_list();
			}
		 ?>
	</div>
<?php
}

function weixin_robot_credit_add(){
	global $plugin_page;	
?>
<?php 
$form_fields = array(
	'weixin_openid'	=> array( 'title'=>'微信 OpenID',	'value'=>'',	'type'=>'text',		'description'=>''),
	'credit_change'	=> array( 'title'=>'积分',			'value'=>'',	'type'=>'text',		'description'=>''),
	'note'			=> array( 'title'=>'备注',			'value'=>'',	'type'=>'textarea',	'description'=>''),
);

?>
<form method="post" action="<?php echo admin_url('admin.php?page='.$plugin_page); ?>" enctype="multipart/form-data" id="form">
	<?php wpjam_admin_display_fields($form_fields); ?>
	<?php wp_nonce_field('weixin_robot','weixin_robot_credit_nonce'); ?>
	<p class="submit"><input class="button-primary" type="submit" value="手工修改" /></p>
</form>
<?php 
}

function weixin_robot_credit_list(){
	global $plugin_page, $succeed_msg,$plugin_page;

	global $wpdb;
	$current_page 		= isset($_GET['paged']) ? $_GET['paged'] : 1;
	$number_per_page	= 50;
	$start_count		= ($current_page-1)*$number_per_page;
	$limit				= 'LIMIT '.$start_count.','.$number_per_page;

	$weixin_credits_table = weixin_robot_credits_table();
	$weixin_users_table = weixin_robot_users_table();

	$where = '';
	if(isset($_GET['openid'])){
		$where = "AND wct.weixin_openid = '{$_GET['openid']}'";	
	}

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $weixin_credits_table as wct LEFT JOIN $weixin_users_table wut ON wct.weixin_openid = wut.openid WHERE wut.subscribe = '1'  $where ORDER BY wct.id DESC $limit";

    $weixin_robot_credits = $wpdb->get_results($sql);
    
    $total_count = $wpdb->get_var("SELECT FOUND_ROWS();");
?>
	<?php if($weixin_robot_credits) { ?>
	<form action="<?php echo admin_url('admin.php?page='.$plugin_page); ?>" method="POST">
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th>微信 OpenID</th>
				<th<?php if(weixin_robot_get_setting('weixin_advanced_api')) { echo ' colspan="2"'; }?>>用户</th>
				<th>积分</th>
				<th>变动</th>
				<th>积分类型</th>
				<th>时间</th>
				<th>备注</th>
			</tr>
		</thead>

		<tbody>
		<?php $alternate = '';?>
		<?php foreach($weixin_robot_credits as $weixin_robot_credit){ $alternate = $alternate?'':'alternate'; ?>
			<tr class="<?php echo $alternate;?>">
				<td><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&openid='.$weixin_robot_credit->weixin_openid)?>"><?php echo $weixin_robot_credit->weixin_openid; ?></a></td>
			<?php if(weixin_robot_get_setting('weixin_advanced_api')) {?>
				<?php if($weixin_robot_credit->subscribe){ ?>
				<td>
				<?php 
				$weixin_user_avatar = '';
				if(!empty($weixin_robot_credit->headimgurl)){
					$weixin_user_avatar = WEIXIN_ROBOT_PLUGIN_URL.'/include/timthumb.php?src='.$weixin_robot_credit->headimgurl;
				?>
					<img src="<?php echo $weixin_user_avatar; ?>" width="32" />
				<?php }?>
				</td>
				<td><?php echo $weixin_robot_credit->nickname;?></td>
				<?php } else { ?>
				<td colspan="2"><span style="color:red">*取消关注*</td>
				<?php }?>
			<?php }elseif($weixin_robot_credit->name){ ?>
				<td><?php echo $weixin_robot_credit->name; ?></td>
			<?php }else{ ?>
				<td></td>
			<?php } ?>	
				<td><?php echo $weixin_robot_credit->credit; ?></td>
				<td><?php echo $weixin_robot_credit->credit_change; ?></td>
				<td><?php echo $weixin_robot_credit->type; ?>
				<?php if($weixin_robot_credit->operator_id){
					$operator_user = get_userdata($weixin_robot_credit->operator_id);
					echo '<br />操作人：'.$operator_user->display_name;
				}?></td>
				<td><?php echo $weixin_robot_credit->time; ?></td>
				<td><?php echo $weixin_robot_credit->note; ?></td>
			</tr>
		<?php } ?>
		</tbody>
		</table>
	</form>
	<?php wpjam_admin_pagenavi($total_count,$number_per_page); ?>
	<?php } else{ ?>
		<p>还没有积分历史记录</p>
	<?php } ?>
<?php
}

