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

add_action('weixin_admin_menu', 'weixin_robot_user_admin_menu',1);
function weixin_robot_user_admin_menu(){
	weixin_robot_add_submenu_page('user', 	'微信用户列表');
}
function weixin_robot_user_page(){
	global $wpdb, $plugin_page;
	
	global $wpdb;
	$current_page 		= isset($_GET['paged']) ? $_GET['paged'] : 1;
	$number_per_page	= 50;
	$start_count		= ($current_page-1)*$number_per_page;
	$limit				= 'LIMIT '.$start_count.','.$number_per_page;

	
	$weixin_users_table = weixin_robot_users_table();

	if(weixin_robot_get_setting('weixin_credit')){
		$weixin_credits_table = weixin_robot_credits_table();
		$sql = "SELECT SQL_CALC_FOUND_ROWS wut.*, wct.credit FROM  $weixin_users_table wut LEFT JOIN $weixin_credits_table wct ON wut.openid = wct.weixin_openid WHERE  subscribe = '1' AND wct.id in (SELECT MAX( id ) FROM $weixin_credits_table GROUP BY weixin_openid) ORDER BY wct.credit desc $limit ";
	}else{
		$sql = "SELECT SQL_CALC_FOUND_ROWS wut.* FROM  $weixin_users_table wut WHERE subscribe = '1' $limit ";
	}

	$weixin_users = $wpdb->get_results($sql);
	$total_count = $wpdb->get_var("SELECT FOUND_ROWS();");

?>
<div class="wrap">
	<div id="icon-weixin-robot" class="icon-users icon32"><br></div>
	<h2>微信用户记录</h2>
	<?php if($weixin_users) { ?>
	<style>.widefat td { padding:4px 10px;vertical-align: middle;}</style>
	<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th>微信 OpenID</th>
				<?php if(weixin_robot_get_setting('weixin_credit')){ ?>
				<th>积分</th>
				<?php } ?>
				<?php if(weixin_robot_get_setting('weixin_advanced_api')) {?>
				<th colspan="2">用户</th>
				<th>性别</th>
				<th>地址</th>
				<th>订阅时间</th>
				<?php }else{ ?>
				<th>姓名</th>
				<th>电话</th>
				<th>地址</th>
				<?php } ?>
				<th>详细</th>
			</tr>
		</thead>

		<tbody>
		<?php $alternate = '';?>
		<?php foreach($weixin_users as $weixin_user){ $alternate = $alternate?'':'alternate';?>
			<tr class="<?php echo $alternate;?>">
				<td><?php echo $weixin_user->openid; ?></td>
				<?php if(weixin_robot_get_setting('weixin_credit')){ ?>
				<td><?php echo $weixin_user->credit; ?></td>
				<?php } ?>
				<?php if(weixin_robot_get_setting('weixin_advanced_api')) {?>
				<td>
				<?php 
				$weixin_user_avatar = '';
				if(!empty($weixin_user->headimgurl)){
					$weixin_user_avatar = WEIXIN_ROBOT_PLUGIN_URL.'/include/timthumb.php?src='.$weixin_user->headimgurl;
				?>
					<img src="<?php echo $weixin_user_avatar; ?>" width="32" />
				<?php }?>
				</td>
				<td><?php echo $weixin_user->nickname; ?></td>
				<td><?php if($weixin_user->sex == 1) { echo '男'; }else{ echo '女'; } ?></td>
				<td><?php echo $weixin_user->country.' '.$weixin_user->province.' '.$weixin_user->city; ?></td>
				<td><?php echo date( 'Y-m-d H:m:s', $weixin_user->subscribe_time+get_option('gmt_offset')*3600 ); ?></td>
				<?php }else{ ?>
				<td><?php echo $weixin_user->name; ?></td>
				<td><?php echo $weixin_user->phone; ?></td>
				<td><?php echo $weixin_user->address; ?></td>
				<?php } ?>
				
				<td>
					<?php if(weixin_robot_get_setting('weixin_credit')){ ?><a href="<?php echo admin_url('admin.php?page=weixin-robot-credit&openid='.$weixin_user->openid)?>">积分历史</a> | <?php } ?>
					<a href="<?php echo admin_url('admin.php?page=weixin-robot-messages&openid='.$weixin_user->openid)?>">消息历史</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php wpjam_admin_pagenavi($total_count,$number_per_page); ?>
	<?php } ?>
	
</div>
<?php 
}
