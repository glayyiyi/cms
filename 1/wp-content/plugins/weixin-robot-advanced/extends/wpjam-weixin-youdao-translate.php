<?php
/*
Plugin Name: 微信中英翻译（基于有道翻译）
Plugin URI: http://wpjam.net/item/wpjam-weixin-youdao-translate/
Description: 基于有道翻译 API 在微信上进行中英翻译
Version: 1.2
Author: Denis
Author URI: http://blog.wpjam.com/
*/


if(!defined('YOUDAO_TRANSLATE_API_KEY')){
    define('YOUDAO_TRANSLATE_API_KEY',weixin_robot_get_setting('youdao_translate_api_key'));
}

if(!defined('YOUDAO_TRANSLATE_KEY_FROM')){
    define('YOUDAO_TRANSLATE_KEY_FROM',weixin_robot_get_setting('youdao_translate_key_from'));
}


add_action('wpjam_net_item_ids','wpjam_weixin_youdao_translate_wpjam_net_item_id');
function wpjam_weixin_youdao_translate_wpjam_net_item_id($item_ids){
    $item_ids['121'] = __FILE__;
    return $item_ids;
}

function wpjam_weixin_get_youdao_translate_results($keyword){
    $url = 'http://fanyi.youdao.com/openapi.do?keyfrom='.YOUDAO_TRANSLATE_KEY_FROM."&key=".YOUDAO_TRANSLATE_API_KEY.'&type=data&doctype=json&version=1.1&q='.urlencode($keyword);
    //test
    //$url = "http://fanyi.youdao.com/openapi.do?keyfrom=doucube&key=1845007487&type=data&doctype=json&version=1.1&q=$keyword";
    
    $responese = wp_remote_get($url);

    if(is_wp_error($responese)){
        return false;
    }
    $youdao = json_decode($responese['body']);
    
    $result = "";
    if (isset($youdao->errorCode)){
        switch ($youdao->errorCode){
            case 0:
                $translation = $youdao->translation;
                $result .= $translation[0]."\n";
                if (isset($youdao->basic)){
                    $result .= isset($youdao->basic->phonetic)?($youdao->basic->phonetic)."\n":"";
                    foreach ($youdao->basic->explains as $value) {
                        $result .= $value."\n";
                    }
                }
                break;
            case 20:
                $result = "错误：要翻译的文本过长";
                break;
            case 30:
                $result = "错误：无法进行有效的翻译";
                break;
            case 40:
                $result = "错误：不支持的语言类型";
                break;
            case 50:
                $result = "错误：无效的密钥";
                break;
            default:
                $result = "错误：原因未知，错误码：".$youdao->errorCode;
                break;
        }
        return trim($result);
    }else{
        return false;
    }
}

add_filter('weixin_custom_keyword','wpjam_translate_weixin_custom_keyword',10,2);
function wpjam_translate_weixin_custom_keyword($false,$keyword){
    if($false === false){
        if($keyword == '翻译' || $keyword == 'fy'){
            global $wechatObj;
            echo sprintf($wechatObj->get_textTpl(), weixin_robot_get_setting('youdao_translate_default_reply'));
            $wechatObj->set_response('translate-not-words');
            wpjam_do_weixin_custom_keyword();
        }elseif(strpos($keyword, '翻译') === 0 || strpos($keyword, 'fy') === 0){
            global $wechatObj;
            $keyword = str_replace(array('翻译','fy'), '', $keyword);
            $results = wpjam_weixin_get_youdao_translate_results($keyword);
            if($results){
                echo sprintf($wechatObj->get_textTpl(), $results);
                $wechatObj->set_response('translate'); 
            }else{
                echo sprintf($wechatObj->get_textTpl(), '翻译失败');   
                $wechatObj->set_response('translate-fail'); 
            }
            wpjam_do_weixin_custom_keyword();
        }
    }
    return $false;
}

add_filter('weixin_response_types','wpjam_weixin_translate_response_types');
function wpjam_weixin_translate_response_types($response_types){
    $response_types['translate-not-words']          = '没有加上要翻译的字句';
    $response_types['translate-fail']               = '翻译调用出错';
    $response_types['translate']                    = '回复翻译结果';
    return $response_types;
}

add_filter('weixin_setting','wpjam_weixin_translate_fileds',11);
function wpjam_weixin_translate_fileds($sections){
    if(wpjam_net_check_domain(121)){
        $youdao_translate_fileds = array(
            'youdao_translate_api_key'          => array('title'=>'有道翻译API Key',    'type'=>'text',     'description'=>'点击<a href="http://fanyi.youdao.com/openapi?path=data-mode">这里</a>申请有道翻译API！'),
            'youdao_translate_key_from'         => array('title'=>'有道翻译KEY FROM',   'type'=>'text',     'description'=>'申请有道翻译API的时候同时填写并获得KEY FROM'),
            'youdao_translate_default_reply'    => array('title'=>'默认翻译回复',        'type'=>'textarea', 'description'=>'用户只发送翻译两个词时候的默认回复'    )
        );
        $sections['youdao_translate'] = array('title'=>'有道翻译', 'callback'=>'', 'fileds'=>$youdao_translate_fileds);
        unset($sections['default_reply']['fileds']['weixin_default_translate']);
    }
    return $sections;
}

add_filter('weixin_default_option','wpjan_weixin_translate_default_option',10,2);
function wpjan_weixin_translate_default_option($defaults_options, $option_name){
    if(wpjam_net_check_domain(121)){
        if($option_name == 'weixin-robot-basic'){
            $youdao_translate_default_options = array(
                'youdao_translate_api_key'         => '',
                'youdao_translate_key_from'        => '',
                'youdao_translate_default_reply'   => "发送【翻译 xxx】来执行翻译：\n\n1、翻译中文为英语，如翻译你好，则发送【翻译你好】\n2、翻译英文为中文，如Hello，发送【翻译Hello】",
            );
            return array_merge($defaults_options, $youdao_translate_default_options);
        }
    }
    return $defaults_options;
}

