<?php

function WPSNS_send_post($url, $postfields = NULL){
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

	curl_setopt($ci, CURLOPT_POST, TRUE);
	if (!empty($postfields)) {
		curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
	}
	
	curl_setopt($ci, CURLOPT_HTTPHEADER, array());
	curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
	curl_setopt($ci, CURLOPT_URL, $url);
	$content = curl_exec($ci);
	curl_close($ci);
	return $content;
}

function WPSNS_build_http_query_multi($params, $boundary = '') {
	if (!$params) return '';
	if (is_string($params)) return $params;
	uksort($params, 'strcmp');
	$pairs = array();
	if($boundary == ''){
		$boundary = uniqid('------------------');
	}
	$MPboundary = '--'.$boundary;
	$endMPboundary = $MPboundary. '--';
	$multipartbody = '';

	foreach ($params as $parameter => $value) {
		if(in_array($parameter, array('pic', 'image', 'img'))) {
			$url = ltrim( $value, '@' );
			$content = file_get_contents( $url );
			$filename = reset(explode('?' , basename($url)));

			$multipartbody .= $MPboundary . "\r\n";
			$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
			$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
			$multipartbody .= $content. "\r\n";
		}else{
			$multipartbody .= $MPboundary . "\r\n";
			$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
			$multipartbody .= $value."\r\n";
		}
	}
	$multipartbody .= "$endMPboundary\r\n";
	return $multipartbody;
}

function WPSNS_http($url, $method, $postfields = NULL, $headers = array()){
	$http = new WP_Http();
	$response = $http->request($url, array(
					"method" => $method,
					"timeout" => 50,
					"sslverify" => false,
					"user-agent" => 'wp_sns_share',
					"body" => $postfields,
					"headers" => $headers
				));
	if(is_object($response)){
		
	}
	else{
		$json = json_decode(trim($response['body']));
		return $json;
	}
}


function WPSNS_get_sig($postfields, $app_secret){
	ksort($postfields);
	$s = '';
	foreach($postfields as $key => $value){
		$s .= $key.'='.$value;
	}
	$s .= $app_secret;
	return md5($s);
}

