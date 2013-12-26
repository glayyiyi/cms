<?php

include(dirname(dirname(__FILE__)).'/param.php');

$oauth_token = $_GET['oauth_token'];
$siteurl = $_GET['siteurl'];
if($siteurl){
	$login_url = $site_domain.'/OAuth/twitter/twitter.php';
	$redirect = $siteurl.'/wp-content/plugins/wp-sns-share/open/twitter/login.php';
	$login_url .= '?url='.$redirect;
	Header("Location: $login_url");
	exit();
}
else if($oauth_token){
	$oauth_token_secret = $_GET['oauth_token_secret'];
	$user_id = $_GET['user_id'];
	$screen_name = $_GET['screen_name'];
	echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
			<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<script type="text/javascript">
			  pd = window.opener.document;';

		echo "pd.getElementById('twitter_submit').value = 1;
			  pd.getElementById('twitter_token').value = '$oauth_token';
			  pd.getElementById('twitter_secret').value = '$oauth_token_secret';
			  pd.getElementById('twitter_name').value = '$screen_name';
			  pd.getElementById('setting_form').submit();";
		echo 'window.close();</script>';
}
else{
	echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
			<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<script type="text/javascript">
			  pd = window.opener.document;';
	echo "pd.getElementById('twitter_text').innerHTML = '登录失败';";
	echo 'window.close();</script>';
}


