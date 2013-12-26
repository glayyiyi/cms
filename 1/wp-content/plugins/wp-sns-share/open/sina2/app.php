<?php

function WPSNS_sina_predo_weibo($text){
	$t = urlencode($text);
	$t = str_replace('%3A', ':', $t);
	$t = str_replace('%2F', '/', $t);
	return $t;
}

function WPSNS_sina_send_weibo($text, $token){
	$api_url = 'https://api.weibo.com/2/statuses/update.json';
	$text = WPSNS_sina_predo_weibo($text);
	$postfields = 'access_token='.$token.'&status='.$text.'&source=25328097';
	
	$response = WPSNS_send_post($api_url, $postfields);
	$msg = json_decode($response, true);

	if ($msg === false || $msg === null){
		$error = "发生错误";
	}
	if (isset($msg['error_code']) && isset($msg['error'])){
		$error = '错误代码： '.$msg['error_code'].';  错误信息: '.$msg['error'];
	}
	if(isset($error)){
		return $error;
	}
	else{
		$message = $msg['text'];
		return $message;
	}
}

function WPSNS_sina_upload($text, $img_url, $token){
	$api_url = 'https://upload.api.weibo.com/2/statuses/upload.json';
	$text = WPSNS_sina_predo_weibo($text);
	$postfields = array('access_token'=>$token, 'status'=>$text, 'pic'=>$img_url);
	$boundary = uniqid('------------------');
	$body = WPSNS_build_http_query_multi($postfields, $boundary);
	$headers = array();
	$headers['Content-Type'] = "multipart/form-data; boundary=".$boundary;
	$headers['Expect'] = '';
	$json = WPSNS_http($api_url, 'POST', $body, $headers);
	if (isset($json->error_code) && isset($json->error)){
		return '错误代码： '.$json->error_code.';  错误信息: '.$json->error;
	}
	else{
		return '[图] '.$json->text;
	}
}

function WPSNSShare_get_sina_app_key_and_secret($sinaOption){
	if(!empty($sinaOption['sina_app_key']) && !empty($sinaOption['sina_app_secret'])){
		$key = $sinaOption['sina_app_key'];
		$secret = $sinaOption['sina_app_secret'];
	}
	else{
		$key = $sinaOption['key'];
		$secret = $sinaOption['secret'];
	}
	return array($key, $secret);
}

/**
 * 
 * @param $sinaOption
 * @return true invalid, flase still valid
 */
function WPSNSShare_sina_token_expire($sinaOption){
	if(time() > $sinaOption['token_expires']){
		return true;
	}
	else{
		return false;
	}
}

function WPSNSShare_sina_refresh_token($sinaOption){
	return false;
}

