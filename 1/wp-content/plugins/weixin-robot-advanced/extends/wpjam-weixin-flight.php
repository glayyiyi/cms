<?php
/*
Plugin Name: 微信航班查询
Plugin URI: http://wpjam.net/item/wpjam-weixin-flight/
Description: 实时航班信息查询
Version: 1.1
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_action('wpjam_net_item_ids','wpjam_weixin_flight_wpjam_net_item_id');
function wpjam_weixin_flight_wpjam_net_item_id($item_ids){
    $item_ids['143'] = __FILE__;
    return $item_ids;
}

add_filter('weixin_builtin_reply', 'wpjam_weixin_flight_builtin_reply');
function wpjam_weixin_flight_builtin_reply($weixin_builtin_replies){
    $weixin_builtin_replies['航班'] = array('type'=>'prefix',   'reply'=>'获取航班信息',  'function'=>'wpjam_weixin_flight_reply');
    return $weixin_builtin_replies;
}

add_filter('weixin_response_types','wpjam_weixin_flight_response_types');
function wpjam_weixin_flight_response_types($response_types){
    $response_types['flight-not-entity']         = '没有加上要查询的航班名称';
    $response_types['flight-fail']               = '查询航班查询出错';
    $response_types['flight']                    = '回复航班查询结果';
    return $response_types;
}

function wpjam_weixin_flight_reply($keyword){
    if($keyword == '航班'){
        global $wechatObj;
        echo sprintf($wechatObj->get_textTpl(), '请在“航班”后面加上航班名称，例如“航班CA1111”');
        $wechatObj->set_response('flight_not_entity_reply');
    }else{
        global $wechatObj;
        $keyword = str_replace(array('航班'), '', $keyword);
        $results = wpjam_weixin_get_flight_results($keyword);
        
        if($results && is_array($results)){
            $items = '';
            foreach ($results as $result) {
                $items .= $wechatObj->get_item($result['Title'], $result['Description'], $result['PicUrl'], $result['Url']);
            }
            echo sprintf($wechatObj->get_picTpl(), count($results), $items);
            $wechatObj->set_response('flight'); 
        }else{
            if(!$results){ $results = '航班查询失败'; }
            echo sprintf($wechatObj->get_textTpl(), $results);   
            $wechatObj->set_response('flight-fail'); 
        }
    }
}

function wpjam_weixin_get_flight_results($keyword){
    include_once(WEIXIN_ROBOT_PLUGIN_DIR.'/include/simple_html_dom.php');
    try {
        $keyword = strtoupper($keyword);
        $url = "http://www.veryzhun.com/fnumber/num/$keyword.html";

        $flight_url = 'http://p.yiqifa.com/c?s=6bad921b&w=441572&c=17301&i=39119&l=0&e=&t=http://m.ctrip.com/html5/index.html';

        $html_flight = file_get_html($url);
        if (!isset($html_flight)){
            $html_flight->clear();
            return "检索出错！";
        }

        $flightInfo = $html_flight->find('div[class="numdap"]');
        //无航班判断
        if ((!isset($flightInfo)) || count($flightInfo) == 0){
            $html_flight->clear();
            return "抱歉，您所查询的航班未搜到！";
        }

        $companies = array (
            "CZ" => "南方航空",
            "3U" => "四川航空",
            "8L" => "祥鹏航空",
            "9C" => "春秋航空",
            "BK" => "奥凯航空",
            "CA" => "中国国航",
            "CN" => "大新华航空",
            "EU" => "成都航空",
            "FM" => "上海航空",
            "HO" => "吉祥航空",
            "HU" => "海南航空",
            "JD" => "首都航空",
            "JR" => "幸福航空",
            "KN" => "联合航空",
            "KY" => "昆明航空",
            "MF" => "厦门航空",
            "MU" => "东方航空",
            "NS" => "河北航空",
            "PN" => "西部航空",
            "SC" => "山东航空",
            "ZH" => "深圳航空",
            "TV" => "西藏航空"
        );

        $company_code = substr(strtoupper($keyword),0, 2);
        
        if (isset($companies[$company_code])){
            $company_pic = WEIXIN_ROBOT_PLUGIN_URL."/static/flight/".$company_code.".jpg";
        }else{
            $company_pic = "";
        }

        $flight_array = array();

        $flight_array[] = array(
            "Title"         => $keyword."航班动态查询结果",
            "Description"   => "",
            "PicUrl"        => WEIXIN_ROBOT_PLUGIN_URL."/static/flight/logo.png", 
            "Url"           => $flight_url
        );

        for($i = 0; $i < count($flightInfo); $i++){
            //起飞
            $departure              = $html_flight->find('div[class="numdap"]', $i);
            $departurecity          = $departure->find('ul li', 0)->plaintext;
            $departureweather       = $departure->find('ul li div table tr td a img', 0)->alt;
            $departureweatherpic    = $departure->find('ul li div table tr td a img', 0)->src;
            $departuretemp          = $departure->find('ul li div table tr td', 1)->plaintext;
            $departurenjd           = $departure->find('ul li', 2)->plaintext;
            $departurestate         = $departure->find('ul li', 3)->plaintext;
            $departuretime          = $departure->find('div[class="numtime"]', 0);
            $departuretimeplan      = $departuretime->find('p', 0)->plaintext;
            $departuretimeactual    = $departuretime->find('p', 2)->plaintext;

            //到达
            $arrival                = $html_flight->find('div[class="numarr"]', $i);
            $arrivalcity            = $arrival->find('ul li', 0)->plaintext;
            $arrivalweather         = $arrival->find('ul li div table tr td a img', 0)->alt;
            $arrivalweatherpic      = $arrival->find('ul img', 0)->src;
            $arrivaltemp            = $arrival->find('ul li', 1)->plaintext;
            $arrivalnjd             = $arrival->find('ul li', 2)->plaintext;
            $arrivalstate           = $arrival->find('ul li', 3)->plaintext;
            $arrivaltime            = $arrival->find('div[class="numtime"]', 0);
            $arrivaltimeplan        = $arrivaltime->find('p', 0)->plaintext;
            $arrivaltimeactual      = $arrivaltime->find('p', 2)->plaintext;

            //航班
            $flight                 = $html_flight->find('div[class="numinfo"]', $i);
            $flightnumber           = $flight->find('ul li', 0)->plaintext;
            $flightstate            = $flight->find('div', 0)->plaintext;
            $flightstatepic         = $flight->find('div img', 0)->src;
            $flightcompany          = $flight->find('ul table tr', 0)->plaintext;
            $flightontime           = str_replace("%%", "%", $flight->find('ul table tr', 1)->plaintext);
            //$flightcompany_pic      = $flight->find('ul table tr img', 0)->src;
            $flightcompany_pic      = $company_pic;
            $flightcplanepic        = $flight->find('ul table tr img', 1)->src;
            $flightposition         = $flight->find('ul li p', 0)->plaintext;


            $flight_array[] = array(
                "Title"         =>$flightcompany." ".$flightnumber."\n".$flightontime,
                "Description"   =>"", 
                "PicUrl"        =>$flightcompany_pic,
                "Url"           => $flight_url
            );

            $flight_array[] = array(
                "Title"         =>$departurecity." ".$departureweather." ".$departuretemp."\n".$departuretimeplan."\n".$departuretimeactual."\n". $departurenjd."\n".$departurestate,
                "Description"   =>"",
                "PicUrl"        =>$departureweatherpic,
                "Url"           =>$flight_url
            );

            $flight_array[] = array(
                "Title"         =>$arrivalcity." ".$arrivalweather." ".$arrivaltemp."\n".$arrivaltimeplan."\n".$arrivaltimeactual."\n".$arrivalnjd."\n".$arrivalstate,
                "Description"   =>"",
                "PicUrl"        =>$arrivalweatherpic,
                "Url"           =>$flight_url
            );

            $flight_array[] = array(
                "Title"         =>$flightstate."\n".$flightposition,
                "Description"   =>"",
                "PicUrl"        =>$flightstatepic,
                "Url"           =>$flight_url
            );
        }

        $html_flight->clear();
        return $flight_array;
    }catch (Exception $e){

    }
}
