<?php
/*
Plugin Name: 微信 PM2.5与空气质量指数
Plugin URI: http://wpjam.net/item/wpjam-weixin-air-quality/
Description: PM2.5(细颗粒物)及空气质量指数(AQI)实时查询
Version: 1.0
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_action('wpjam_net_item_ids','wpjam_weixin_air_quality_wpjam_net_item_id');
function wpjam_weixin_air_quality_wpjam_net_item_id($item_ids){
    $item_ids['139'] = __FILE__;
    return $item_ids;
}

function wpjam_weixin_get_air_quality_results($keyword){
    $url = "http://www.pm25.in/api/querys/aqi_details.json?stations=no&city=".urlencode($keyword)."&token=".weixin_robot_get_setting('air_quality_app_key');
    
    $city_air = wp_remote_get($url);

    if(is_wp_error($city_air)){
        return false;
    }

    $city_air = json_decode($city_air['body']);
    
    if (isset($city_air->error)){
        return $city_air->error;
    }else{
        $city_air = $city_air[0];
        $result = "【".$city_air->area."空气质量】"."\n".
                  "空气质量指数(AQI)：".$city_air->aqi."\n".
                  "空气质量等级：".$city_air->quality."\n".
                  "细颗粒物(PM2.5)：".$city_air->pm2_5."\n".
                  "可吸入颗粒物(PM10)：".$city_air->pm10."\n".
                  "一氧化碳(CO)：".$city_air->co."\n".
                  "二氧化氮(NO2)：".$city_air->no2."\n".
                  "二氧化硫(SO2)：".$city_air->so2."\n".
                  "臭氧(O3)：".$city_air->o3."\n".
                  "更新时间：".preg_replace("/([a-zA-Z])/i", " ", $city_air->time_point);
        return $result;
    }
}

add_filter('weixin_custom_keyword','wpjam_air_quality_weixin_custom_keyword',10,2);
function wpjam_air_quality_weixin_custom_keyword($false,$keyword){
    if($false === false){
        if(strpos($keyword, '空气') === 0 || strpos($keyword, 'kq') === 0){
            global $wechatObj;

            if($keyword == '空气' || $keyword == 'kq'){
                $city = weixin_robot_get_setting('air_quality_default_city');
            }else{
                $city = str_replace(array('空气','kq'), '', $keyword);
            }
            if(!$city){
                echo sprintf($wechatObj->get_textTpl(), '请在“空气”后面要跟上城市名，例如“空气北京”');
                $wechatObj->set_response('air_quality'); 
                wpjam_do_weixin_custom_keyword();
            }
            
            $results = wpjam_weixin_get_air_quality_results($city);
            if($results){
                echo sprintf($wechatObj->get_textTpl(), $results);
                $wechatObj->set_response('air_quality'); 
            }else{
                echo sprintf($wechatObj->get_textTpl(), '获取空气质量数据失败');   
                $wechatObj->set_response('air_quality-fail'); 
            }
            wpjam_do_weixin_custom_keyword();
        }
    }
    return $false;
}

add_filter('weixin_response_types','wpjam_weixin_air_quality_response_types');
function wpjam_weixin_air_quality_response_types($response_types){
    $response_types['air_quality-not-city']           = '空气质量查询没有加上城市名';
    $response_types['air_quality-fail']               = '查询空气质量出错';
    $response_types['air_quality']                    = '回复空气质量查询结果';
    return $response_types;
}

add_filter('weixin_setting','wpjam_weixin_air_quality_fileds',11);
function wpjam_weixin_air_quality_fileds($sections){
    
    $air_quality_fileds = array(
        'air_quality_app_key'       => array('title'=>'APPKey',     'type'=>'text',  'description'=>'点击<a href="http://pm25.in/api_doc">这里</a>申请空气质量数据接口。申请理由参考：为微信公众账号【XX在线】用户提供XX市空气质量查询服务,感谢贵网站提供数据接口！'),
        'air_quality_default_city'  => array('title'=>'默认城市',    'type'=>'text',  'description'=>'用户发送“空气”时，默认查询该城市空气质量数据。点击<a href="http://pm25.in/">查看</a>支持查询的城市。务必确定该城市已经开通查询')
    );
    $sections['air_quality'] = array('title'=>'空气质量', 'callback'=>'', 'fileds'=>$air_quality_fileds);
    
    return $sections;
}

add_filter('weixin_default_option','wpjan_weixin_air_quality_default_option',10,2);
function wpjan_weixin_air_quality_default_option($defaults_options, $option_name){
    if($option_name == 'weixin-robot-basic'){
        $air_quality_default_options = array(
            'air_quality_app_key'         => '',
            'air_quality_default_city'    => '',
        );
        return array_merge($defaults_options, $air_quality_default_options);
    }
    return $defaults_options;
}

