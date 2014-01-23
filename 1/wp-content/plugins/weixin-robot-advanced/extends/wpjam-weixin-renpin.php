<?php
/*
Plugin Name: 微信人品计算器
Plugin URI: http://wpjam.net/item/wpjam-weixin-renpin/
Description: 微信人品计算器，计算你当日的人品值。
Version: 1.1
Author: Denis
Author URI: http://blog.wpjam.com/
*/
add_action('wpjam_net_item_ids','wpjam_weixin_renpin_wpjam_net_item_id');
function wpjam_weixin_renpin_wpjam_net_item_id($item_ids){
    $item_ids['170'] = __FILE__;
    return $item_ids;
}

add_filter('weixin_response_types','wpjam_weixin_renpin_response_types');
function wpjam_weixin_renpin_response_types($response_types){
    $response_types['renpin-no-name']	= '人品后面没有名字';
    $response_types['renpin']       	= '人品查询';
    return $response_types;
}

add_filter('weixin_builtin_reply', 'wpjam_weixin_renpin_builtin_reply');
function wpjam_weixin_renpin_builtin_reply($weixin_builtin_replies){
    $weixin_builtin_replies['rp'] = $weixin_builtin_replies['人品'] = array('type'=>'prefix',   'reply'=>'人品计算',  'function'=>'wpjam_weixin_renpin_reply');
    return $weixin_builtin_replies;
}

function wpjam_weixin_renpin_reply($keyword){
    global $wechatObj;

    if($keyword == '人品' || $keyword == 'rp'){
        $name = '';
    }else{
        $name = str_replace(array('人品','rp'), '', $keyword);
    }

    if(!$name){
        echo sprintf($wechatObj->get_textTpl(), '人品后面加上姓名哦，比如：人品张三。');
        $wechatObj->set_response('renpin-no-name');
    }else{
        $results = wpjam_weixin_get_renpin_results($name);
        
        echo  sprintf($wechatObj->get_textTpl(), $results);
        $wechatObj->set_response('renpin');
    }   
}


function wpjam_weixin_get_renpin_results($name){
	
	$name		= htmlspecialchars($name);	
	$results	= '你的大名是：'.$name."\n";
	$a=0;
	for($i = 0;$i < strlen($name); $i++){
		$a=$a+ord($name[$i]);
	}
	$value=($a+round(time()/86400))%102;

	$results .= '你的得分是：'.$value."\n得分评价：";

	if ($value== 0) {
		$results .=  "你一定不是人吧？怎么一点人品都没有？！";
	} elseif (($value>0)&&($value<=5)) {
	    $results .= "算了，跟你没什么人品好谈的...";
	} else if (($value > 5) && ($value <= 10)) {
       $results .=   "是我不好...不应该跟你谈人品问题的...";
    } else if (($value > 10) && ($value <= 15)) {
       $results .=   "杀过人没有？放过火没有？你应该无恶不做吧？";
    } else if (($value > 15) && ($value <= 20)) {
       $results .=   "你貌似应该三岁就偷看隔壁大妈洗澡的吧...";
    } else if (($value > 20) && ($value <= 25)) {
       $results .=   "你的人品之低下实在让人惊讶啊...";
    } else if (($value > 25) && ($value <= 30)) {
       $results .=   "你的人品太差了。你应该有干坏事的嗜好吧？";
    } else if (($value > 30) && ($value <= 35)) {
       $results .=   "你的人品真差!肯定经常做偷鸡摸狗的事...";
    } else if (($value > 35) && ($value <= 40)) {
       $results .=   "你拥有如此差的人品请经常祈求佛祖保佑你吧...";
    } else if (($value > 40) && ($value <= 45)) {
       $results .=   "老实交待..那些论坛上面经常出现的偷拍照是不是你的杰作？";
    } else if (($value > 45) && ($value <= 50)) {
       $results .=   "你随地大小便之类的事没少干吧？";
    } else if (($value > 50) && ($value <= 55)) {
       $results .=   "你的人品太差了..稍不小心就会去干坏事了吧？";
    } else if (($value > 55) && ($value <= 60)) {
       $results .=   "你的人品很差了..要时刻克制住做坏事的冲动哦..";
    } else if (($value > 60) && ($value <= 65)) {
       $results .=   "你的人品比较差了..要好好的约束自己啊..";
    } else if (($value > 65) && ($value <= 70)) {
       $results .=   "你的人品勉勉强强..要自己好自为之..";
    } else if (($value > 70) && ($value <= 75)) {
       $results .=   "有你这样的人品算是不错了..";
    } else if (($value > 75) && ($value <= 80)) {
       $results .=   "你有较好的人品..继续保持..";
    } else if (($value > 80) && ($value <= 85)) {
       $results .=   "你的人品不错..应该一表人才吧？";
    } else if (($value > 85) && ($value <= 90)) {
       $results .=   "你的人品真好..做好事应该是你的爱好吧..";
    } else if (($value > 90) && ($value <= 95)) {
       $results .=   "你的人品太好了..你就是当代活雷锋啊...";
    } else if (($value > 95) && ($value <= 99)) {
       $results .=   "你是世人的榜样！";
    } else if ($value == 100) {
       $results .=   "天啦！你不是人！你是神！！！";
    } else {
       $results .=   "你的人品竟然溢出了...我对你无语..";
    }

    $results .= "\n\n温馨提示：本次测试仅供娱乐参考！";

    return $results;
}