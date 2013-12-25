<?php
/*
Plugin Name: 微信股票行情
Plugin URI: http://wpjam.net/item/wpjam-weixin-stock/
Description: 股票行情实时查询。
Version: 1.1
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_action('wpjam_net_item_ids','wpjam_weixin_stock_wpjam_net_item_id');
function wpjam_weixin_stock_wpjam_net_item_id($item_ids){
    $item_ids['135'] = __FILE__;
    return $item_ids;
}

add_filter('weixin_builtin_reply', 'wpjam_weixin_stock_builtin_reply');
function wpjam_weixin_stock_builtin_reply($weixin_builtin_replies){
    $weixin_builtin_replies['gp'] = $weixin_builtin_replies['股票'] = array('type'=>'prefix',   'reply'=>'获取股票信息',  'function'=>'wpjam_weixin_stock_reply');
    return $weixin_builtin_replies;
}

add_filter('weixin_response_types','wpjam_weixin_stock_response_types');
function wpjam_weixin_stock_response_types($response_types){
    $response_types['stock-no-code']    = '没有加上要查询的股票代码';
    $response_types['stock-fail']       = '查询股票行情出错';
    $response_types['stock']            = '股票行情结果回复';
    return $response_types;
}

add_filter('weixin_setting','wpjam_weixin_stock_fileds',11);
function wpjam_weixin_stock_fileds($sections){
    $stock_fileds = array(
        'stock_default_code'    => array('title'=>'默认股票代码', 'type'=>'text', 'description'=>'用户发送“股票”时默认查询的股票代码。上证指数请填写999999；企业如上市，可填写本公司股票代码'),
    );
    $sections['stock'] = array('title'=>'股票行情', 'callback'=>'', 'fileds'=>$stock_fileds);

    return $sections;
}

add_filter('weixin_default_option','wpjan_weixin_stock_default_option',10,2);
function wpjan_weixin_stock_default_option($defaults_options, $option_name){
    if($option_name == 'weixin-robot-basic'){
        $stock_default_options = array(
            'stock_default_code'   => '',
        );
        return array_merge($defaults_options, $stock_default_options);
    }
    return $defaults_options;
}

function wpjam_weixin_stock_reply($keyword){
    global $wechatObj;

    if($keyword == '股票' || $keyword == 'gp'){
        if(weixin_robot_get_setting('stock_default_code')){
            $code = weixin_robot_get_setting('stock_default_code');
        }else{
            $code = '';
        }
    }else{
        $code = str_replace(array('股票','gp'), '', $keyword);
    }

    if(!$code){
        echo sprintf($wechatObj->get_textTpl(), '股票后面加上需要查询的股票代码，比如：股票000001。');
        $wechatObj->set_response('stock-no-code');
    }else{
        $results = wpjam_weixin_get_stock_results($code);
        if($results){
            echo sprintf($wechatObj->get_textTpl(), $results);
            $wechatObj->set_response('stock');
        }else{
            echo sprintf($wechatObj->get_textTpl(), '获取股票行情数据失败');
            $wechatObj->set_response('stock-fail');
        }
    }   
}

function wpjam_weixin_get_stock_results($stock_code){

    //完整版：        http://hq.sinajs.cn/list=sh601006
    //简化版：        http://hq.sinajs.cn/list=s_sh601006

    //上证指数：    http://hq.sinajs.cn/list=sh000001
    //深圳成指：    http://hq.sinajs.cn/list=sz399001
    //沪深300       http://hq.sinajs.cn/list=sh000300
    //中小板指      http://hq.sinajs.cn/list=sz399005
    //创业板指      http://hq.sinajs.cn/list=sz399006
    //Ｂ股指数      http://hq.sinajs.cn/list=sh000003

    //上海A股 6		http://hq.sinajs.cn/list=sh600189
    //上海B股 9		http://hq.sinajs.cn/list=sh900935
    //深圳A股 0,3	http://hq.sinajs.cn/list=sz000001
    //				http://hq.sinajs.cn/list=sz300356
    //深圳B股 2		http://hq.sinajs.cn/list=sz200020
    //中小板  0		http://hq.sinajs.cn/list=sz002001 深圳
    //创业板  3		http://hq.sinajs.cn/list=sz300002 深圳

	// if ($stock_code == "")){
		// $stock_code = "999999"; //默认回复上证指数
	// }

    if (!preg_match("/^\d{6}$/",$stock_code)){
		return "发送股票加上6位数字代码，例如“股票000063”";
	}

    $stock_index = array(
      '999999' => 'sh000001',
      '399001' => 'sz399001',
      '000300' => 'sh000300',
      '399005' => 'sz399005',
      '399006' => 'sz399006',
      '000003' => 'sh000003'
    );
	if(array_key_exists($stock_code, $stock_index)){
        $url = "http://hq.sinajs.cn/list=".$stock_index[$stock_code];
	}else {
        $exchange = (substr($stock_code,0,1) < 5)?"sz":"sh";
        $url = "http://hq.sinajs.cn/list=".$exchange.$stock_code;
    }

    $response = wp_remote_get($url);
    $data = $response['body'];
    
    $result = iconv("GBK", "UTF-8//IGNORE", $data);

    $start = strpos($result,'"');   //第一次出现的位置
    $last  = strripos($result,'"'); //最后一次出现的位置
    $stock_string = substr($result, $start + 1, $last - $start - 1);
    $stock_array = explode(",",$stock_string);

    if (count($stock_array) <> 33){ return "不存在的股票代码？"; }

	$stock_title = $stock_array[0]."[".$stock_code."]\n";
    $stock_info = 
    "最新：".$stock_array[3]."\n".
	"涨跌：".round($stock_array[3]-$stock_array[2], 3)."\n".
    "涨幅：".round(($stock_array[3]-$stock_array[2])/$stock_array[2]*100, 3)."%\n".
    "今开：".$stock_array[1]."\n".
    "昨收：".$stock_array[2]."\n".
    "最高：".$stock_array[4]."\n".
    "最低：".$stock_array[5]."\n".
    "总手：".
    ((substr($stock_code,0,1) != 3)?
        (array_key_exists($stock_code, $stock_index)?round(($stock_array[8]/100000000),3)."亿":round(($stock_array[8]/1000000),3)."万")
        :(array_key_exists($stock_code, $stock_index)?round(($stock_array[8]/10000000000),3)."亿":round(($stock_array[8]/1000000),3)."万"))
    ."\n".
    "金额：".(array_key_exists($stock_code, $stock_index)?round(($stock_array[9]/100000000),3)."亿":round(($stock_array[9]/10000),3)."万")."\n".
    "更新：".$stock_array[30]." ".$stock_array[31];

    return trim($stock_title.$stock_info);
}

