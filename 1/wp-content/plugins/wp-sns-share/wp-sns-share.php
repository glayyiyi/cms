<?php
/*
 Plugin Name: wp-sns-share
 Version: 2.8
 Plugin URI: http://blog.11034.org/2010-08/wp-sns-share.html
 Description: 将您的博客文章和图片自动和手动同步更新到新浪微博、腾讯微博、Twitter，
 手动分享支持更多SNS平台；分享您的博客和信息给您的同学和朋友，为您的博客带来巨大流量，
 增加您网站的外链有益于SEO
 Author: –Flyぁ梦–
 Author URI: http://blog.11034.org/
 */

define('SHARESNS_DIR', dirname(__FILE__));
define('SHARESNS_NAME', dirname(plugin_basename(__FILE__)));
define('SHARESNS_HOME', get_bloginfo('wpurl').'/wp-content/plugins/'.SHARESNS_NAME);
define('SHARESNS_IMAGE_HOME', SHARESNS_HOME.'/images');
define('SHARESNS_OPTION', 'ShareSNSOptions');
define('SHARESNS_VERSION', '2.8');

include(SHARESNS_DIR.'/functions.php');

include(SHARESNS_DIR.'/WPShareSNS.php');

if (class_exists('ShareSNS')) {
	$wp_shareSNS = new ShareSNS();
}

if (!function_exists('WPSNSShare_init')) {
	function WPSNSShare_init(){
		global $wp_shareSNS;
		if(!isset($wp_shareSNS)){
			$wp_shareSNS = new ShareSNS();
		}
		$wp_shareSNS->updateOptions();
	}
}

if (!function_exists('WPSNSShare_adminPanel')) {
	function WPSNSShare_adminPanel() {
		global $wp_shareSNS;
		if (!isset($wp_shareSNS)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('分享到SNS', '分享到SNS', 9,
				basename(__FILE__), array(&$wp_shareSNS, 'printAdminPage'));
		}
	}
}

include(SHARESNS_DIR.'/sharebar_functions.php');
include(SHARESNS_DIR.'/sync_functions.php');

//微博同步
include(SHARESNS_DIR.'/open/func.php');
include(SHARESNS_DIR.'/open/functions.php');
include(SHARESNS_DIR.'/open/sina2/app.php');
include(SHARESNS_DIR.'/open/tqq/app.php');
include(SHARESNS_DIR.'/open/renren/app.php');
include(SHARESNS_DIR.'/open/twitter/app.php');

//启用插件时，初始化插件参数
add_action('activate_wp-sns-share/wp-sns-share.php', 'WPSNSShare_init');

//打印插件页面
add_action('admin_menu', 'WPSNSShare_adminPanel');


add_action('admin_menu', 'WPSNSShare_add_widget');

//添加wp-sns-share.js到<head>
add_action('init', 'WPSNSShare_addJS');

add_action('publish_post', 'WPSNSShare_sync', 10, 2);
add_action('future_to_publish', 'WPSNSShare_sync_for_future', 10);

//插件列表中为插件增加“设置”选项
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 
	'WPSNSShare_addSettingsLink' );



$options = get_option(SHARESNS_OPTION);

//自动输出SNS图标横栏
if($options['output']['auto'] == 1){
	add_filter('the_content', 'WPSNSShare_filter_insert', 99);
}

$sinaOption = $options['sync']['sina'];
if(!empty($sinaOption['oauth_token'])){
	if(WPSNSShare_sina_token_expire($options['sync']['sina'])){
		$ret = WPSNSShare_sina_refresh_token($sinaOption);
		if($ret){
		}
	}
}

$renrenOption = $options['sync']['renren'];
if(!empty($renrenOption['oauth_token'])){
	if(WPSNSShare_renren_token_expire($renrenOption)){
		$refresh_token = $renrenOption['refresh_token'];
		$key = $renrenOption['key'];
		$secret = $renrenOption['secret'];
		$ret = WPSNS_renren_refresh_token($refresh_token, $key, $secret);
		if($ret){
			list($access_token, $refresh_token, $expires_in) = $ret;
			$options['sync']['renren']['oauth_token'] = $access_token;
			$options['sync']['renren']['refresh_token'] = $refresh_token;
			$options['sync']['renren']['token_expires'] = time() + intval($expires_in);
			update_option(SHARESNS_OPTION, $options);
		}
	}
}


//发送测试微博
if(isset($_POST['shareSNS_textWeibo'])){
	$weibo = $_POST['weiboText'];
	if($weibo != ''){
		$test_source = $_POST['test_source'];
		if($test_source == 'sina'){
			$sinaOption = $options['sync']['sina'];
			$token = $sinaOption['oauth_token'];
			if($token != ''){
				$message = WPSNS_sina_send_weibo($weibo, $token);
				$options['sync']['sina']['message'] = $message;
				update_option(SHARESNS_OPTION, $options);
			}
		}
		else if($test_source == 'tqq'){
			$tqqOption = $options['sync']['tqq'];
			$key = $tqqOption['key'];
			$key_secret = $tqqOption['secret'];
			$token = $tqqOption['oauth_token'];
			$token_secret = $tqqOption['oauth_token_secret'];
			if($token != '' && $token_secret != ''){
				$message = WPSNS_tqq_send_weibo($weibo, $key, $key_secret, 
									$token, $token_secret);
				$options['sync']['tqq']['message'] = $message;
				update_option(SHARESNS_OPTION, $options);
			}
		}
		else if($test_source == 'renren'){
			$renrenOption = $options['sync']['renren'];
			$key = $renrenOption['key'];
			$token = $renrenOption['oauth_token'];
			if($key != '' && $token != ''){
				$message = WPSNS_renren_post_status($weibo, $key, $token);
				$options['sync']['renren']['message'] = $message;
				update_option(SHARESNS_OPTION, $options);
			}
		}
		else if($test_source == 'twitter'){
			$twitterOption = $options['sync']['twitter'];
			$oauth_token = $twitterOption['oauth_token'];
			$oauth_token_secret = $twitterOption['oauth_token_secret'];
			if($oauth_token != '' && $oauth_token_secret != ''){
				$message = WPSNS_send_twitter($weibo, $oauth_token, $oauth_token_secret);
				$options['sync']['twitter']['message'] = $message;
				update_option(SHARESNS_OPTION, $options);
			}
		}
	}
}
