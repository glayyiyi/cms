<?php
/*
Plugin Name: 易信机器人高级版
Plugin URI: http://blog.wpjam.com/project/yixin-robot-advanced/
Description: 易信机器人的主要功能就是能够将你的公众账号和你的 WordPress 博客联系起来，搜索和用户发送信息匹配的日志，并自动回复用户，让你使用易信进行营销事半功倍。
Version: 3.8
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_action('init', 'wpjam_yixin_robot_redirect', 4);
function wpjam_yixin_robot_redirect($wp){
	if(isset($_GET['yixin']) ){
		global $wechatObj;
		if(!isset($wechatObj)){
			//file_put_contents(WP_CONTENT_DIR.'/uploads/yixin.html',var_export($_SERVER,true));
			$wechatObj = new wechatCallback();
			$wechatObj->valid();
			exit;
		}
	}
}

/*
add_action('admin_head','yixin_robot_admin_head');
function yixin_robot_admin_head(){
	global $plugin_page;
	if(in_array($plugin_page, array('yixin-robot-stats', 'yixin-robot-summary', 'yixin-robot-messages'))){
?>
	<style>
	#icon-weixin-robot{background-image: url("<?php echo WEIXIN_ROBOT_PLUGIN_DIR?>/template/yixin-32.png");background-repeat: no-repeat;}
	</style>
<?php
	}
}*/

add_action('wpjam_net_item_ids','yixin_robot_wpjam_net_item_id');
function yixin_robot_wpjam_net_item_id($item_ids){
	$item_ids['104'] = __FILE__;
	return $item_ids;
}

add_action( 'weixin_admin_menu', 'yixin_robot_admin_menu' );
//add_action( 'admin_menu', 'yixin_robot_admin_menu' );
function yixin_robot_admin_menu() {
	if(wpjam_net_check_domain(104)){
		if(weixin_robot_get_setting('weixin_disable_stats')==false) {
			weixin_robot_add_submenu_page( 'stats2', 		'易信统计分析',	'yixin-robot-stats2');
			//weixin_robot_add_submenu_page( 'summary',		'易信回复统计分析',	'yixin-robot-summary');
			weixin_robot_add_submenu_page( 'messages',		'易信最新消息', 		'yixin-robot-messages');
		}
	}
}

add_filter('weixin_setting','wpjam_yixin_add_fields');
function wpjam_yixin_add_fields($sections){
	$sections['app']['fields']['yixin_app_id']		= array('title'=>'易信AppID',		'type'=>'text',	'description'=>'设置易信自定义菜单的所需的 AppID，如果没申请，可不填！');
 	$sections['app']['fields']['yixin_app_secret']	= array('title'=>'易信APPSecret',	'type'=>'text',	'description'=>'设置易信自定义菜单的所需的 APPSecret，如果没申请，可不填！');
 	return $sections;
}

function yixin_robot_get_access_token(){

	if(weixin_robot_get_setting('yixin_app_id') && weixin_robot_get_setting('yixin_app_secret')){
		
		$yixin_robot_access_token = get_transient('yixin-robot-access-token');

		if($yixin_robot_access_token === false){
			$url = 'https://api.yixin.im/cgi-bin/token?grant_type=client_credential&appid='.weixin_robot_get_setting('yixin_app_id').'&secret='. weixin_robot_get_setting('yixin_app_secret');

			$yixin_robot_access_token = wp_remote_get($url,array('sslverify'=>false));
			if(is_wp_error($yixin_robot_access_token)){
				echo $yixin_robot_access_token->get_error_code().'：'. $yixin_robot_access_token->get_error_message();
				exit;
			}
			$yixin_robot_access_token = json_decode($yixin_robot_access_token['body'],true);

			if(isset($yixin_robot_access_token['access_token'])){
				set_transient('yixin-robot-access-token',$yixin_robot_access_token['access_token'],$yixin_robot_access_token['expires_in']);
				return $yixin_robot_access_token['access_token'];
			}else{
				print_r($yixin_robot_access_token);
				exit;
			}
		}else{
			return $yixin_robot_access_token;
		}
	}
}

add_filter('weixin_robot_post_custom_menus','yixin_robot_post_custom_menus',10,2);
function yixin_robot_post_custom_menus($message, $weixin_robot_custom_menus){
	if(weixin_robot_get_setting('yixin_app_id') && weixin_robot_get_setting('yixin_app_secret')){

		$yixin_robot_access_token = yixin_robot_get_access_token();

		if($yixin_robot_access_token){
			$url = 'https://api.yixin.im/cgi-bin/menu/create?access_token='.$yixin_robot_access_token;
			$request = weixin_robot_create_buttons_request($weixin_robot_custom_menus);
			$result = weixin_robot_post_custom_menus_core($url,$request);
			if($result){
				$message = $message?$message.'<br />':$message;
				return $message.'易信：'.$result;
			}
		}
	}

	return $message;
}

add_filter('weixin_messages_table','wpjam_yixin_messages_table');
function wpjam_yixin_messages_table($messages_table){

	global $wpdb;

	if(isset($_GET['yixin'])){
		return $wpdb->prefix.'yixin_messages';
	}

	global $plugin_page;
	if(in_array($plugin_page, array('yixin-robot-stats2','yixin-robot-stats', 'yixin-robot-summary', 'yixin-robot-messages'))){
		return $wpdb->prefix.'yixin_messages';
	}

	return $messages_table;
}

add_filter('weixin_tables','yixin_robot_messages_weixin_tables');
function yixin_robot_messages_weixin_tables($weixin_tables){
	$weixin_tables['易信消息'] = 'yixin_robot_messages_crate_table';
	return $weixin_tables;
}

register_activation_hook( WEIXIN_ROBOT_PLUGIN_FILE,'yixin_robot_messages_crate_table');
function yixin_robot_messages_crate_table() {	
	global $wpdb;
 
	$weixin_messages_table = $wpdb->prefix.'yixin_messages';
	if($wpdb->get_var("show tables like '$weixin_messages_table'") != $weixin_messages_table) {
		$sql = "
		CREATE TABLE IF NOT EXISTS ".$weixin_messages_table." (
			`id` bigint(20) NOT NULL auto_increment,
			`MsgId` bigint(64) NOT NULL,
			`FromUserName` varchar(30) character set utf8 NOT NULL,
			`MsgType` varchar(10) character set utf8 NOT NULL,
			`CreateTime` int(10) NOT NULL,

			`Content` longtext character set utf8 NOT NULL,

			`PicUrl` varchar(255) character set utf8 NOT NULL,

			`Location_X` double(10,6) NOT NULL,
			`Location_Y` double(10,6) NOT NULL,
			`Scale` int(10) NOT NULL,
			`label` varchar(255) character set utf8 NOT NULL,

			`Title` text character set utf8 NOT NULL,
			`Description` longtext character set utf8 NOT NULL,
			`Url` varchar(255) character set utf8 NOT NULL,

			`Event` varchar(255) character set utf8 NOT NULL,
			`EventKey` varchar(255) character set utf8 NOT NULL,

			`Format` varchar(255) character set utf8 NOT NULL,
			`MediaId` text character set utf8 NOT NULL,
			`Recognition` text character set utf8 NOT NULL,
		 
			`Response` varchar(255) character set utf8 NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
		dbDelta($sql);
	}
}

add_action('admin_head','yixin_robot_stats_admin_head',999);
function yixin_robot_stats_admin_head(){
	global $plugin_page;
	if(in_array($plugin_page, array('yixin-robot-stats', 'yixin-robot-summary', 'yixin-robot-messages'))){
?>
<link rel="stylesheet" href="http://cdn.staticfile.org/morris.js/0.4.2/morris.min.css" />
<script type='text/javascript' src="http://cdn.staticfile.org/raphael/2.1.0/raphael-min.js"></script>
<script type='text/javascript' src="http://cdn.staticfile.org/morris.js/0.4.2/morris.min.js"></script>
<style type="text/css">
input[type="date"]{ background-color: #fff; border-color: #dfdfdf; border-radius: 3px; border-width: 1px; border-style: solid; color: #333; outline: 0; box-sizing: border-box; }
.widefat td { padding:4px 10px; vertical-align: middle;}
</style>
<?php
	}
}


