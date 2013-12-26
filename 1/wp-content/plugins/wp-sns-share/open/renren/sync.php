<?php

/*
 * 旧时用的用户名和密码模式
 */

function WPSNSShare_send_renren_post($status, $username, $password){
	$cookie = tempnam('./tmp', 'wp_sns_share_renren');
	$ch = WPSNSShare_wp_getCurl($cookie, "http://passport.renren.com/PLogin.do");
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=' . rawurlencode($username) 
		. '&password=' . rawurlencode($password) 
		. '&autoLogin=true&origURL=http%3A%2F%2Fwww.renren.com%2FHome.do&domain=renren.com');
	$str = WPSNSShare_wp_update_result($ch);
	$pattern = "/get_check:'([^']+)'/";
	preg_match($pattern, $str, $matches);
	$get_check = $matches[1];
	$ch = WPSNSShare_wp_getCurl($cookie, "http://status.renren.com/doing/update.do");
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'c=' . rawurlencode($status) . '&raw=' 
			. rawurlencode($status) . '&isAtHome=1&publisher_form_ticket=' 
			. $get_check . '&requestToken=' . $get_check);
	curl_setopt($ch, CURLOPT_REFERER, 'http://status.renren.com/ajaxproxy.htm');
	WPSNSShare_wp_update_result($ch);
}

function WPSNSShare_wp_getCurl($cookie, $url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 
    	'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.12) 
    	Gecko/20101026 Firefox/3.6.12');
	curl_setopt($ch, CURLOPT_POST, 1);
	return $ch;
}

function WPSNSShare_wp_update_result($ch) {
	$str = curl_exec($ch);
	curl_close($ch);
	unset($ch);
	return $str;
}