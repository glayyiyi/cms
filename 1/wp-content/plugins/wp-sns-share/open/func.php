<?php
if (!class_exists('OAuthRequest')) {
	include_once('OAuth.php');
}

function WPSNS_get_oauth_content($url){
	$opt = array(
		'connecttimeout' => 30,
		'timeout' => 30,
	);

	$ci = curl_init();
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $opt['connecttimeout']);
	curl_setopt($ci, CURLOPT_TIMEOUT, $opt['timeout']);

	curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ci, CURLOPT_URL, $url);
	$content = curl_exec($ci);

	curl_close($ci);
	return $content;
}

function WPSNS_get_signature_method(){
	return new OAuthSignatureMethod_HMAC_SHA1();
}

function WPSNS_request_token($api_url, $consumer, $params = array()){
	$request_token_url = $api_url;
	$request = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", 
					$request_token_url, $params);
	$request->sign_request(WPSNS_get_signature_method(), $consumer, NULL);
	$url = $request->to_url();
	$content = WPSNS_get_oauth_content($url);
	$ret = array();
	parse_str($content, $ret);
	return $ret;
}

function WPSNS_authorize($api_url, $token, $callback){
	$authorize_url = $api_url;
	$url = $authorize_url . "?oauth_token=$token&oauth_callback=".urlencode($callback);
	Header("Location: $url");
	exit();
}

function WPSNS_access_token($api_url, $oauth_verifier, $consumer, $token, $params = array()){
	$access_token_url = $api_url;
	$params = array_merge($params, array('oauth_verifier' => $oauth_verifier));

	$request = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", 
					$access_token_url, $params);
	$request->sign_request(WPSNS_get_signature_method(), $consumer, $token);
	$url = $request->to_url();
	$content = WPSNS_get_oauth_content($url);
	$ret = array();
	parse_str($content, $ret);
	return $ret;
}

function WPSNS_post_content($url, $method, $postfields = NULL){
	$opt = array(
		'userAgent' => 'Sae T OAuth v0.2.0-beta2',
		'connecttimeout' => 30,
		'timeout' => 30,
	);

	$ci = curl_init();
	curl_setopt($ci, CURLOPT_USERAGENT, $opt['userAgent']);
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $opt['connecttimeout']);
	curl_setopt($ci, CURLOPT_TIMEOUT, $opt['timeout']);
	curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ci, CURLOPT_HEADER, FALSE);

	switch ($method) {
		case 'POST':
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if (!empty($postfields)) {
				curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
			}
	}
	curl_setopt($ci, CURLOPT_HTTPHEADER, array());
	curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
	curl_setopt($ci, CURLOPT_URL, $url);
	$content = curl_exec($ci);
	$ci_info = curl_getinfo($ci);

	curl_close($ci);
	return $content;
}

function WPSNS_send_content($text, $api_url, $consumer, $token, $params = array()){
	$params = array_merge($params, array('content' => $text));
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', 
					$api_url, $params);
	$request->sign_request(WPSNS_get_signature_method(), $consumer, $token);
	
	$url_obj = parse_url($request->to_url());
	$response = WPSNS_post_content($api_url, 'POST', $url_obj['query']);
	return $response;
}
