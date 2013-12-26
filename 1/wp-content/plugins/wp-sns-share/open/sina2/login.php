<?php

include(dirname(dirname(__FILE__)).'/param.php');

$token = $_GET['token'];
$siteurl = $_GET['siteurl'];
if($siteurl){
	$callback = $site_domain.'/OAuth/sina/callback.php';
	$redirect = $siteurl.'/wp-content/plugins/wp-sns-share/open/sina2/login.php';
	$callback .= '?url='.$redirect;
	$authorize_url = 'https://api.weibo.com/oauth2/authorize?client_id=1925972150&'
					.'response_type=code&redirect_uri='.$callback;
	Header("Location: $authorize_url");
	exit();
}
else if($token){
	$uid = $_GET['uid'];
	$expires = $_GET['expires'];
	$name = WPSNS_sina_get_user($uid, $token);
	echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
			<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<script type="text/javascript">
			  pd = window.opener.document;';

		echo "pd.getElementById('sina_submit').value = 1;
			  pd.getElementById('sina_token').value = '$token';
			  pd.getElementById('sina_name').value = '$name';
			  pd.getElementById('sina_expires').value = '$expires';
			  pd.getElementById('setting_form').submit();";
		echo 'window.close();</script>';
}
else{
	echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
			<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<script type="text/javascript">
			  pd = window.opener.document;';
	echo "pd.getElementById('sina_text').innerHTML = '登录失败';";
	echo 'window.close();</script>';
}


function WPSNS_sina_get_user($uid, $token){
	$api_url = 'https://api.weibo.com/2/users/show.json';
	$api_url .= "?uid=$uid&access_token=$token";
	$ret = file_get_contents($api_url);
	$json = json_decode($ret);
	return $json->screen_name;
}
