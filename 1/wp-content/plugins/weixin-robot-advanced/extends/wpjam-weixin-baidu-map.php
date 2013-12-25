<?php
/*
Plugin Name: 微信周边搜索（基于百度地图）
Plugin URI: http://wpjam.net/item/wpjam-weixin-baidu-map/
Description: 基于百度地图 API 在微信上进行附近搜索
Version: 1.3
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_action('wpjam_net_item_ids','wpjam_weixin_baidu_map_wpjam_net_item_id');
function wpjam_weixin_baidu_map_wpjam_net_item_id($item_ids){
	$item_ids['114'] = __FILE__;
	return $item_ids;
}

add_filter('weixin_builtin_reply', 'wpjam_baidu_map_builtin_reply');
function wpjam_baidu_map_builtin_reply($weixin_builtin_replies){
	unset($weixin_builtin_replies['[location]']);
	$weixin_builtin_replies['[location]']	= array('type'=>'full',		'reply'=>'获取地理位置',	'function'=>'wpjam_baidu_map_location_reply');
	$weixin_builtin_replies['天气'] 			= array('type'=>'prefix',	'reply'=>'获取天气数据',	'function'=>'wpjam_baidu_map_weather_reply');
	$weixin_builtin_replies['附近'] 			= array('type'=>'prefix',	'reply'=>'附近信息搜索',	'function'=>'wpjam_baidu_map_nearby_reply');
	return $weixin_builtin_replies;
}

add_filter('weixin_response_types','wpjam_weixin_location_response_types');
function wpjam_weixin_location_response_types($response_types){
	$response_types['location']						= '回复已获取位置';
	$response_types['need-location']				= '回复需要提供位置';
	$response_types['location-query']				= '附近搜索';
	$response_types['location-not-found']			= '附近搜索无匹配';
	$response_types['location-weather']				= '回复当地天气';
	$response_types['location-weather-not-found']	= '无当地天气数据';
	return $response_types;
}


add_filter('weixin_setting','wpjam_weixin_location_fileds',11);
function wpjam_weixin_location_fileds($sections){

	if(wpjam_net_check_domain(114)){
		$baidu_map_fileds = array(
			'baidu_map_app_key'			=> array('title'=>'百度地图 APP Key',		'type'=>'text',		'description'=>'点击<a href="http://lbsyun.baidu.com/apiconsole/key?application=key">这里</a>申请百度地图 APP KEY！'),
			'baidu_map_default_keyword'	=> array('title'=>'默认搜索关键字',		'type'=>'text',		'description'=>'设置用户发送地理位置之后直接到百度地图搜索的关键字，该选项设置后，下面默认回复的选项将失效。'),
			'baidu_map_default_reply'	=> array('title'=>'获取位置信息后回复',	'type'=>'textarea',	'rows'=>8,	'description'=>'获取用户发送位置信息之后，提示用户如何进行搜索的回复！'),
			'baidu_map_no_location'		=> array('title'=>'未获取位置信息时回复',	'type'=>'textarea',	'rows'=>4,	'description'=>'还未获取用户位置信息，但是用户已经发送【附近xxx】时的回复！'),
			'baidu_map_no_result'		=> array('title'=>'无周边商家时回复',		'type'=>'textarea',	'rows'=>4,	'description'=>'可以使用[keyword]代替搜索关键字！')
		);
		$sections['baidu_map'] = array('title'=>'百度地图', 'callback'=>'', 'fileds'=>$baidu_map_fileds);

		unset($sections['default_reply']['fileds']['weixin_default_location']);
	}

	return $sections;
}

add_filter('weixin_default_option','wpjan_weixin_location_default_option',10,2);
function wpjan_weixin_location_default_option($defaults_options, $option_name){
	if(wpjam_net_check_domain(114)){
		if($option_name == 'weixin-robot-basic'){
			$baidu_map_default_options = array(
				'baidu_map_app_key'			=> '',
				'baidu_map_default_keyword'	=> '',
				'baidu_map_default_reply'	=> "请回复附近XX来查询附近的商家\n1、查询附近的饭店，则发送【附近饭店】\n2、查询附近的某家店名，如711，发送【附近711】\n3、查询当地的天气，发送【天气】\n4、查询某地天气，发送天气xx，比如【天气广州】",
				'baidu_map_no_location'		=> '还未获取你的地理位置或者地理位置过期，请发过来吧。请点击“+号键”，选择“位置图标”，发送你的地理位置过来！',
				'baidu_map_no_result'		=> '附近没有[keyword]'
			);
			return array_merge($defaults_options, $baidu_map_default_options);
		}
	}
	return $defaults_options;
}

function wpjam_baidu_map_location_reply(){
	global $wechatObj;

	$baidu_map_default_keyword = weixin_robot_get_setting('baidu_map_default_keyword');
	if($baidu_map_default_keyword){
		return wpjam_baidu_map_nearby_reply('附近'.$baidu_map_default_keyword);
	}else{
		echo sprintf($wechatObj->get_textTpl(), weixin_robot_get_setting('baidu_map_default_reply'));
		$wechatObj->set_response('location');
	}
}

function wpjam_baidu_map_weather_reply($keyword){
	global $wechatObj;

	if($keyword == '天气'){
		$weixin_openid = $wechatObj->get_fromUsername();
		$location = weixin_robot_get_user_location($weixin_openid);

		if($location){

			$location = $location['y'].','.$location['x'];
			$results = wpjam_weixin_get_baidu_map_weather($location);
			if($results){
				echo sprintf($wechatObj->get_textTpl(), $results);
				$wechatObj->set_response('location-weather'); 
			}else{
				echo sprintf($wechatObj->get_textTpl(), '暂无该地区的天气数据');   
				$wechatObj->set_response('location-weather-not-found'); 
			}
		
		}else{
			echo sprintf($wechatObj->get_textTpl(), weixin_robot_get_setting('baidu_map_no_location'));
			$wechatObj->set_response('need-location');
		}
	}else{
		$keyword = str_replace('天气', '', $keyword);
		$results = wpjam_weixin_get_baidu_map_weather($keyword);
		if($results){
			echo sprintf($wechatObj->get_textTpl(), $results);
			$wechatObj->set_response('location-weather'); 
		}else{
			echo sprintf($wechatObj->get_textTpl(), '暂无该地区的天气数据');   
			$wechatObj->set_response('location-weather-not-found'); 
		}
	}
}

function wpjam_baidu_map_nearby_reply($keyword){
	global $wechatObj;

	$weixin_openid = $wechatObj->get_fromUsername();
	$location = weixin_robot_get_user_location($weixin_openid);

	if($location){
		
		if($keyword == '附近'){
			$baidu_map_default_keyword = weixin_robot_get_setting('baidu_map_default_keyword');
			if($baidu_map_default_keyword){
				wpjam_baidu_map_nearby_reply('附近'.$baidu_map_default_keyword);
			}else{
				echo sprintf($wechatObj->get_textTpl(), '附近后面要加上搜索的关键词，比如【附近饭店】');
				$wechatObj->set_response('location-query');
			}
		}else{
			$keyword = str_replace('附近', '', $keyword);
			$location = $location['x'].','.$location['y'];
			$results = wpjam_weixin_get_baidu_map_results($keyword,$location);
			if($results){
				echo sprintf($wechatObj->get_textTpl(),$results);
				$wechatObj->set_response('location-query');
			}else{
				$baidu_map_no_result = weixin_robot_get_setting('baidu_map_no_result');
				$baidu_map_no_result = str_replace('[keyword]', '【'.$keyword.'】', $baidu_map_no_result);
				echo sprintf($wechatObj->get_textTpl(), $baidu_map_no_result);
				$wechatObj->set_response('location-not-found');
			}
		}
	}else{
		echo sprintf($wechatObj->get_textTpl(), weixin_robot_get_setting('baidu_map_no_location'));
		$wechatObj->set_response('need-location');
	}
}

function wpjam_weixin_get_baidu_map_results($keyword,$location){
	$url = "http://api.map.baidu.com/place/v2/search?&page_size=6&query=".urlencode($keyword)."&location=".$location."&radius=3000&output=json&scope=2&ak=".weixin_robot_get_setting('baidu_map_app_key');

	$responese = wp_remote_get($url);

	if(is_wp_error($responese)){
		return false;
	}

	$responese = json_decode($responese['body']);

	if(isset($responese->results)){
		if(count($responese->results) <1){
			return false;
		}

		$data = "";
		foreach ($responese->results as $result) {
			//$data .= "店名：<a href='".$result['detail_info']['detail_url']."' >".$result['name']."</a>\r\n地址：".$result['address']."\r\n电话：".$result['telephone']."\r\n距离：".$result['detail_info']['distance']."米\r\n\r\n";
			$data .= "店名：".$result->name."\n";
			$data .= "地址：".$result->address."\n";
			if(isset($result->telephone)){
				$data .= "电话：".$result->telephone."\n";
			}
			$data .= "距离：".$result->detail_info->distance."米\n\n";
		}

		return $data;
	}
	return false;   
}

function wpjam_weixin_get_baidu_map_weather($location){
	$url = "http://api.map.baidu.com/telematics/v3/weather?location=".urlencode($location)."&output=json&scope=2&ak=".weixin_robot_get_setting('baidu_map_app_key');

	$responese = wp_remote_get($url);

	if(is_wp_error($responese)){
		//echo $responese->get_error_code().'：'. $responese->get_error_message();
		return false;
	}
	$responese = json_decode($responese['body']);

	if($responese->error || count($responese->results) <1){
		return false;
	}

	if(isset($responese->results)){
		$results = $responese->results;
		$weather_data = $results[0]->weather_data;

		$data = '';
		foreach ($weather_data as $weather) {
			$data .= $weather->date."\r";
			$data .= $weather->weather." ";
			$data .= $weather->wind."\r";
			$data .= $weather->temperature."\r\n";
		}

		return $data;
	}
	return false;
	
}







