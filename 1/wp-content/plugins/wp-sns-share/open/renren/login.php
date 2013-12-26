<?php

include(dirname(dirname(__FILE__)).'/param.php');

$token = $_GET['token'];
$siteurl = $_GET['siteurl'];
if($siteurl){
	$app_id = 'bbf00f68725a407c8a0a9e4eb10652ab';
	$callback = $site_domain.'/OAuth/renren/renren.php';
	$redirect = $siteurl.'/wp-content/plugins/wp-sns-share/open/renren/login.php';
	$callback .= '?url='.$redirect;
	$callback = urlencode($callback);
	$authorize_url = 'http://graph.renren.com/oauth/authorize?client_id='. $app_id.
				'&scope=status_update&response_type=code&redirect_uri=' . $callback;
	Header("Location: $authorize_url");
	exit();
}
else if($token){
	$refresh_token = $_GET['refresh_token'];
	$expires_in = $_GET['expires_in'];
	$name = $_GET['name'];
	echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
			<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<script type="text/javascript">
			  pd = window.opener.document;';

		echo "pd.getElementById('renren_submit').value = 1;
			  pd.getElementById('renren_token').value = '$token';
			  pd.getElementById('renren_name').value = '$name';
			  pd.getElementById('renren_expires').value = '$expires_in';
			  pd.getElementById('renren_refresh_token').value = '$refresh_token';
			  pd.getElementById('setting_form').submit();";
		echo 'window.close();</script>';
}
else{
	echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
			<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<script type="text/javascript">
			  pd = window.opener.document;';
	echo "pd.getElementById('renren_text').innerHTML = '登录失败';";
	echo 'window.close();</script>';
}
