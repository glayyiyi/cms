<?php

/**
 * @return true for invalid, false for valid
 */
function WPSNSShare_renren_token_expire($renrenOption){
	if(time() > $renrenOption['token_expires']){
		return true;
	}
	else{
		return false;
	}
}

function WPSNS_renren_post_status($content, $token, $app_secret){
	$api_url = 'http://api.renren.com/restserver.do?';
	$postfields = array();
	$postfields['method'] = 'status.set';
	$postfields['v'] = '1.0';
	$postfields['status'] = $content;
	$postfields['access_token'] = $token;
	$postfields['format'] = 'JSON';
	$postfields['sig'] = WPSNS_get_sig($postfields, $app_secret);
	$ret = WPSNS_post_content($api_url, 'POST', $postfields);
	$json = json_decode($ret);
	if($json && isset($json->result) && $json->result == 1){
		return $content;
	}
	else{
		if (isset($json->error_code) && isset($json->error_msg)){
			$error = '错误代码： '.$json->error_code.';  错误信息: '.$json->error_msg;
		}
		return $error;
	}
}

function WPSNS_renren_refresh_token($token, $key, $secret){
	$api_url = 'https://graph.renren.com/oauth/token?';
	$api_url .= 'grant_type=refresh_token';
	$api_url .= '&refresh_token='.$token;
	$api_url .= '&client_id='.$key;
	$api_url .= '&client_secret='.$secret;
	$response = @file_get_contents($api_url);
	$json = json_decode($response);
	if($json && isset($json->access_token)){
		$access_token = $json->access_token;
		$refresh_token = $json->refresh_token;
		$expires_in = $json->expires_in;
		return array($access_token, $refresh_token, $expires_in);
	}
	else{
		return Null;
	}
}

