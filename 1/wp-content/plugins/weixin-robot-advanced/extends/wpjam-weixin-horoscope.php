<?php
/*
Plugin Name: 微信 星座运势
Plugin URI: http://wpjam.net/item/wpjam-weixin-horoscope/
Description: 每日星座运势
Version: 1.0
Author: Denis
Author URI: http://blog.wpjam.com/
*/


add_action('wpjam_net_item_ids','wpjam_weixin_horoscope_wpjam_net_item_id');
function wpjam_weixin_horoscope_wpjam_net_item_id($item_ids){
    $item_ids['131'] = __FILE__;
    return $item_ids;
}

function wpjam_weixin_get_horoscopes(){
	return array(
		'白羊座' => '1',
		'金牛座' => '2',
		'双子座' => '3',
		'巨蟹座' => '4',
		'狮子座' => '5',
		'处女座' => '6',
		'天秤座' => '7',
		'天蝎座' => '8',
		'射手座' => '9',
		'摩羯座' => '10',
		'水瓶座' => '11',
		'双鱼座' => '12',

		'白羊' => '1',
		'金牛' => '2',
		'双子' => '3',
		'巨蟹' => '4',
		'狮子' => '5',
		'处女' => '6',
		'天秤' => '7',
		'天蝎' => '8',
		'射手' => '9',
		'摩羯' => '10',
		'水瓶' => '11',
		'双鱼' => '12',
	);
}

function wpjam_weixin_get_horoscope_results($keyword){
    include(WEIXIN_ROBOT_PLUGIN_DIR.'/include/simple_html_dom.php');
	try {
		$horoscopes = wpjam_weixin_get_horoscopes();

		if (empty($horoscopes[$keyword])){
			return "星座名只有以下这些0：\n白羊座 金牛座 双子座 巨蟹座 狮子座 处女座 天秤座 天蝎座 射手座 摩羯座 水瓶座 双鱼座";
		}


		$result = get_transient('horoscope_'.$keyword);

		if($result === false){
			$url = "http://dp.sina.cn/dpool/astro/starent/starent.php?type=day&ast=".$horoscopes[$keyword]."&vt=4";

			$html_horoscope = file_get_html($url);
			if (!isset($html_horoscope)){
				$html_horoscope->clear();
				return "检索出错！\n如果经常这样，请给我们留言。";
			}
			$result = "==".$keyword." 今日运势==\n\n";

			//指数类
			$part1 = $html_horoscope->find('div[class="xz_cont"]', 0);
			foreach($part1->find('p') as $singleLuck) {
				$result .= trim($singleLuck->plaintext);
				foreach($singleLuck->find('img') as $part1Anchor) {
					if (stristr($part1Anchor->src,"star_1")){
						$result .= '★';
					}else{
						$result .= '☆';
					}
				}
				$result .= "\n";
			}

	        /*
			//爱情
			$part3 = $html_horoscope->find('div[class="xz_cont"]', 2);
			$result .= "\n【爱情运】\n".trim($part3->plaintext)."\n";

			//事业/学业运
			$result .= "\n【事业运&财运】\n";
			$part4 = $html_horoscope->find('div[class="xz_cont"]', 3);
			foreach($part4->find('p') as $part4p) {
				$result .=trim($part4p->plaintext)."\n";
			}
	        */
	        
			//整体运
			$part2 = $html_horoscope->find('div[class="xz_cont"]', 1);
			$result .= "\n".trim($part2->plaintext)."\n";

			$html_horoscope->clear();
			$result = trim($result);

			return trim($result);

			$expire = strtotime(substr(current_time('mysql'),0,10).' 23:59:59', current_time('timestamp')) - current_time('timestamp');
			if(!$expire) $expire = 1;

			if($expire < 0 || $expire > 86400) $expire = 3600;

			set_transient('horoscope_'.$keyword, $result, $expire);
		}

	}catch (Exception $e){
        return false;
	}
}

add_filter('weixin_custom_keyword','wpjam_horoscope_weixin_custom_keyword',10,2);
function wpjam_horoscope_weixin_custom_keyword($false,$keyword){
    if($false === false){
    	$horoscopes = wpjam_weixin_get_horoscopes();

        if($keyword == '星座'){
            global $wechatObj;
            echo sprintf($wechatObj->get_textTpl(), '请输入星座查看你今天的星座运程。');
            $wechatObj->set_response('horoscope');
            wpjam_do_weixin_custom_keyword();
        }elseif(isset($horoscopes[$keyword])){
            global $wechatObj;
            $results = wpjam_weixin_get_horoscope_results($keyword);
            if($results){
                echo sprintf($wechatObj->get_textTpl(), $results);
                $wechatObj->set_response('horoscope'); 
            }else{
                echo sprintf($wechatObj->get_textTpl(), '查询失败');   
                $wechatObj->set_response('horoscope-fail'); 
            }
            wpjam_do_weixin_custom_keyword();
        }
    }
    return $false;
}

add_filter('weixin_response_types','wpjam_weixin_horoscope_response_types');
function wpjam_weixin_horoscope_response_types($response_types){
    $response_types['horoscope-fail']               = '查询星座运势出错';
    $response_types['horoscope']                    = '回复星座运势查询结果';
    return $response_types;
}

