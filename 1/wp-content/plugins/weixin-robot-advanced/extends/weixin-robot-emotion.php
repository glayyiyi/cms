<?php
/*
Plugin Name: 微信机器人高级版-表情回复
Plugin URI: http://blog.wpjam.com/project/weixin-robot-emotion/
Description: 微信机器人第三方插件，表情回复，用户发送表情之后，设置回复。
Version: 2.1
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_filter('weixin_custom_keyword','wpjam_weixin_robot_emotion_custom_keyword',18,2);
function wpjam_weixin_robot_emotion_custom_keyword($false, $keyword){
	if($false === false){
		$emotions = array('/::)','/::~','/::B','/::|','/:8-)','/::<','/::$','/::X','/::Z','/::\'(','/::-|','/::@','/::P','/::D','/::O','/::(','/::+','/:Cb','/::Q','/::T','/:,@P','/:,@-D','/::d','/:,@o','/::g','/:|-)','/::!','/::L','/::>','/::,@','/:,@f','/::-S','/:?','/:,@x','/:,@@','/::8','/:,@!','/:!!!','/:xx','/:bye','/:wipe','/:dig','/:handclap','/:&-(','/:B-)','/:<@','/:@>','/::-O','/:>-|','/:P-(','/::\'|','/:X-)','/::*','/:@x','/:8*','/:pd','/:<W>','/:beer','/:basketb','/:oo','/:coffee','/:eat','/:pig','/:rose','/:fade','/:showlove','/:heart','/:break','/:cake','/:li','/:bome','/:kn','/:footb','/:ladybug','/:shit','/:moon','/:sun','/:gift','/:hug','/:strong','/:weak','/:share','/:v','/:@)','/:jj','/:@@','/:bad','/:lvu','/:no','/:ok','/:love','/:<L>','/:jump','/:shake','/:<O>','/:circle','/:kotow','/:turn','/:skip','/[]','/:#-0','/[]','/:kiss','/:<&','/:&>');
		$emotions_text = array('微笑','伤心','美女','发呆','墨镜','哭','羞','哑','睡','哭','囧','怒','调皮','笑','惊讶','难过','酷','汗','抓狂','吐','笑','快乐','奇','傲','饿','累','吓','汗','高兴','闲','努力','骂','疑问','秘密','乱','疯','哀','鬼','打击','bye','汗','抠','鼓掌','糟糕','恶搞','什么','什么','累','看','难过','难过','坏','亲','吓','可怜','刀','水果','酒','篮球','乒乓','咖啡','美食','动物','鲜花','枯','唇','爱','分手','生日','电','炸弹','刀','足球','虫','臭','月亮','太阳','礼物','伙伴','赞','差','握手','优','恭','勾','顶','坏','爱','不','好的','爱','吻','跳','怕','尖叫','圈','拜','回头','跳','天使','激动','舞','吻','瑜伽','太极');

		if(in_array($keyword, $emotions)){
			global $wechatObj;

			echo sprintf($wechatObj->get_textTpl(), '我也会发表情哦，而且一次三个：'.$keyword.$keyword.$keyword);

			$wechatObj->set_response('emotions');
			return true;
		}

	}
	return $false;
}


add_filter('weixin_response_types','wpjam_emotions_response_types');
function wpjam_emotions_response_types($response_types){
	$response_types['emotions'] = '表情回复';
	return $response_types;
}