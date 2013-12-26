<?php

@session_start();
include_once('../func.php');

$request_token_url = 'https://open.t.qq.com/cgi-bin/request_token';
$authorize_url = 'https://open.t.qq.com/cgi-bin/authorize';
$access_token_url = 'https://open.t.qq.com/cgi-bin/access_token';
$callback_url = $_GET['siteurl'].'/wp-content/plugins/wp-sns-share/open/tqq/login.php';

if(!isset($_SESSION['tqq_key'])){
	if(isset($_GET['key'])){
		$_SESSION['tqq_key'] = $_GET['key'];
		$_SESSION['tqq_secret'] = $_GET['secret'];
	}
	else{
		print_r($_SESSION);
		exit('no tqq key, please contact with http://blog.11034.org/wp-sns-share.html');
	}
}
$consumer = new OAuthConsumer($_SESSION['tqq_key'], $_SESSION['tqq_secret']);

if(!isset($_SESSION['tqq_oauth_token'])){
	$params = array('oauth_callback' => $callback_url);
	$ret = WPSNS_request_token($request_token_url, $consumer, $params);
	$_SESSION['tqq_oauth_token'] = $ret['oauth_token'];
	$_SESSION['tqq_oauth_token_secret'] = $ret['oauth_token_secret'];
	WPSNS_authorize($authorize_url, $ret['oauth_token'], $callback_url);
}
else{
	$oauth_verifier = $_GET['oauth_verifier'];
	$token = new OAuthConsumer($_SESSION['tqq_oauth_token'], $_SESSION['tqq_oauth_token_secret']);
	$ret = WPSNS_access_token($access_token_url, $oauth_verifier, $consumer, $token);
	
	unset($_SESSION['tqq_oauth_token']);
	unset($_SESSION['tqq_oauth_token_secret']);
	$token = $ret['oauth_token'];
	$secret = $ret['oauth_token_secret'];
	$tqq_name = $ret['name'];

	echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
			<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<script type="text/javascript">
			  pd = window.opener.document;';

	if($token != '' && $secret != ''){
		echo "pd.getElementById('tqq_submit').value = 1;
			  pd.getElementById('tqq_token').value = '$token';
			  pd.getElementById('tqq_secret').value = '$secret';
			  pd.getElementById('tqq_name').value = '$tqq_name';
			  pd.getElementById('setting_form').submit();";
		echo 'window.close();</script>';
	}
	else{
		echo "pd.getElementById('tqq_text').innerHTML = '登录失败';";
		echo 'window.close();</script>';
	}
}
