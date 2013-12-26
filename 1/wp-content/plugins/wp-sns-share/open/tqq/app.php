<?php

function WPSNS_tqq_send_weibo($text, $key, $key_secret, $token, $token_secret){
	$api_url = 'http://open.t.qq.com/api/t/add';
	$consumer = new OAuthConsumer($key, $key_secret);
	$token = new OAuthConsumer($token, $token_secret);
	$params = array('content' => $text, 'format' => 'json');

	$response = WPSNS_send_content($text, $api_url, $consumer, $token, $params);
	$msg = json_decode($response, true);
	
	if ($msg === false || $msg === null){
		$error = "发生错误";
	}
	if (isset($msg['errcode']) && $msg['errcode'] != 0){
		$error = '错误代码： '.$msg['errcode'].';  错误信息: '.$msg['msg'];
	}
	if(isset($error)){
		return $error;
	}
	else{
		return $text;
	}
}

function WPSNS_tqq_upload($text, $img_url, $key, $key_secret, $token, $token_secret){
	$api_url = 'http://open.t.qq.com/api/t/add_pic_url';
	$consumer = new OAuthConsumer($key, $key_secret);
	$token = new OAuthConsumer($token, $token_secret);
	$params = array('content' => $text, 'format' => 'json', 'pic_url'=>$img_url);
	
	$response = WPSNS_send_content($text, $api_url, $consumer, $token, $params);
	$msg = json_decode($response, true);

	if ($msg === false || $msg === null){
		$error = "发生错误";
	}
	if (isset($msg['errcode']) && $msg['errcode'] != 0){
		$error = '错误代码： '.$msg['errcode'].';  错误信息: '.$msg['msg'];
	}
	if(isset($error)){
		return $error;
	}
	else{
		return '[图] '.$text;
	}
}

if(!function_exists('WPSNSShare_get_tqq_app_key_and_secret')){
function WPSNSShare_get_tqq_app_key_and_secret($tqqOption){
	if(!empty($tqqOption['qq_app_key']) && !empty($tqqOption['qq_app_secret'])){
		$key = $tqqOption['qq_app_key'];
		$secret = $tqqOption['qq_app_secret'];
	}
	else{
		$key = $tqqOption['key'];
		$secret = $tqqOption['secret'];
	}
	return array($key, $secret);
}
}
