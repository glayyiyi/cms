<?php
/*
Plugin Name: 微信中英翻译（基于有道翻译）
Plugin URI: http://wpjam.net/item/wpjam-weixin-youdao-translate/
Description: 基于有道翻译 API 在微信上进行中英翻译
Version: 1.4
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_action('wpjam_net_item_ids','wpjam_weixin_youdao_translate_wpjam_net_item_id');
function wpjam_weixin_youdao_translate_wpjam_net_item_id($item_ids){
    $item_ids['121'] = __FILE__;
    return $item_ids;
}

add_filter('weixin_builtin_reply', 'wpjam_weixin_youdao_translate_builtin_reply');
function wpjam_weixin_youdao_translate_builtin_reply($weixin_builtin_replies){
    $weixin_builtin_replies['fy'] = $weixin_builtin_replies['翻译'] = array('type'=>'prefix',   'reply'=>'中英翻译',  'function'=>'wpjam_weixin_youdao_translate_reply');
    return $weixin_builtin_replies;
}

add_filter('weixin_response_types','wpjam_weixin_translate_response_types');
function wpjam_weixin_translate_response_types($response_types){
    $response_types['translate-not-words']          = '没有加上要翻译的字句';
    $response_types['translate-fail']               = '翻译调用出错';
    $response_types['translate']                    = '回复翻译结果';
    return $response_types;
}

add_filter('weixin_setting','wpjam_weixin_translate_fields',11);
function wpjam_weixin_translate_fields($sections){
    if(wpjam_net_check_domain(121)){
        $youdao_translate_fields = array(
            'youdao_translate_api_key'          => array('title'=>'有道翻译API Key',    'type'=>'text',     'description'=>'点击<a href="http://fanyi.youdao.com/openapi?path=data-mode">这里</a>申请有道翻译API！'),
            'youdao_translate_key_from'         => array('title'=>'有道翻译KEY FROM',   'type'=>'text',     'description'=>'申请有道翻译API的时候同时填写并获得KEY FROM'),
            'youdao_translate_default_reply'    => array('title'=>'默认翻译回复',        'type'=>'textarea', 'description'=>'用户只发送翻译两个词时候的默认回复'    )
        );
        $sections['youdao_translate'] = array('title'=>'有道翻译', 'callback'=>'', 'fields'=>$youdao_translate_fields);
        unset($sections['default_reply']['fields']['weixin_default_translate']);
    }
    return $sections;
}

add_filter('weixin_default_option','wpjan_weixin_translate_default_option',10,2);
function wpjan_weixin_translate_default_option($defaults_options, $option_name){
    
    if($option_name == 'weixin-robot-basic'){
        $youdao_translate_default_options = array(
            'youdao_translate_api_key'         => '',
            'youdao_translate_key_from'        => '',
            'youdao_translate_default_reply'   => "发送【翻译 xxx】来执行翻译：\n\n1、翻译中文为英语，如翻译你好，则发送【翻译你好】\n2、翻译英文为中文，如Hello，发送【翻译Hello】",
        );
        return array_merge($defaults_options, $youdao_translate_default_options);
    }

    return $defaults_options;
}

function wpjam_weixin_youdao_translate_reply($keyword){
    if($keyword == '翻译' || $keyword == 'fy'){
        global $wechatObj;
        echo sprintf($wechatObj->get_textTpl(), weixin_robot_get_setting('youdao_translate_default_reply'));
        $wechatObj->set_response('translate-not-words');
    }else{
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
    }
}

function wpjam_weixin_get_youdao_translate_results($keyword){
    $url = 'http://fanyi.youdao.com/openapi.do?keyfrom='.weixin_robot_get_setting('youdao_translate_key_from')."&key=".weixin_robot_get_setting('youdao_translate_api_key').'&type=data&doctype=json&version=1.1&q='.urlencode($keyword);
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



