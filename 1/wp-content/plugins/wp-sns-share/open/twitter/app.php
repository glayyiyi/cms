<?php

function WPSNS_send_twitter($text, $token, $token_secret){
	$api_url = 'http://api.twitter.com/1/statuses/update.json';
	$consumer = WPSNS_twitter_get_consumer();
	$token = WPSNS_twitter_get_token($token, $token_secret);
	$param = array('status'=>$text);
	$response = WPSNS_send_content($text, $api_url, $consumer, $token, $param);
	$msg = json_decode($response, true);
	if ($msg === false || $msg === null){
		$error = "发生错误";
	}
	if (isset($msg['error'])){
		$error = '错误信息: '.$msg['error'];
	}
	if(isset($error)){
		return $error;
	}
	else{
		return $text;
	}
}

function WPSNS_send_twitter_image($text, $img_url, $token, $token_secret){
	$api_url = 'https://upload.twitter.com/1/statuses/update_with_media.json';
	$postfields = array('access_token'=>$token, 'status'=>$text, 'pic'=>$img_url);
	$boundary = uniqid('------------------');
	$body = WPSNS_build_http_query_multi($postfields, $boundary);
	$headers = array();
	$headers['Content-Type'] = "multipart/form-data; boundary=".$boundary;
	$headers['Expect'] = '';
	$msg = WPSNS_http($api_url, 'POST', $body, $headers);
	if ($msg === false || $msg === null){
		$error = "发生错误";
	}
	if (isset($msg->error)){
		$error = '错误信息: '.$msg->error;
	}
	if(isset($error)){
		return $error;
	}
	else{
		return $text;
	}
}

function WPSNS_twitter_get_consumer(){
	$info =  array(
		'key' => 'E0sAXIvVGp1SNNnVJxhOA',
		'secret' => '6UGxtR5QOXWynw0rTnNtPmKFBOlRvC57tVjq3g',
	);
	return new OAuthConsumer($info['key'], $info['secret']);
}

function WPSNS_twitter_get_token($oauth_token, $oauth_token_secret){
	return new OAuthConsumer($oauth_token, $oauth_token_secret);
}
