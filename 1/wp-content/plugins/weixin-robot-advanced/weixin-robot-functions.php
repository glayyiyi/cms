<?php
function is_weixin(){ // 判断当前用户是否为微信用户
	if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') !== false ) {
			return true;
		}
	}
	return false;
}

if(!function_exists('get_post_excerpt')){
    //获取日志摘要
    function get_post_excerpt($post, $excerpt_length=240){
        if(!$post) $post = get_post();

        $post_excerpt = $post->post_excerpt;

        if($post_excerpt == ''){
            $post_content	= $post->post_content;
            $post_content	= do_shortcode($post_content);
            $post_content	= wp_strip_all_tags( $post_content );
            $excerpt_length	= apply_filters('excerpt_length', $excerpt_length);     
            $excerpt_more	= apply_filters('excerpt_more', ' ' . '&hellip;');
            $post_excerpt	= mb_strimwidth($post_content,0,$excerpt_length,$excerpt_more,'utf-8');
        }

        $post_excerpt = wp_strip_all_tags( $post_excerpt );
        $post_excerpt = trim( preg_replace( "/[\n\r\t ]+/", ' ', $post_excerpt ), ' ' );

        return $post_excerpt;
    }

    //获取第一段
    function get_first_p($text){
        if($text){
            $text = explode("\n",strip_tags($text)); 
            $text = trim($text['0']); 
        }
        return $text;
    }
}

if(!function_exists('get_post_first_image')){
	function get_post_first_image($post_content){
		preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post_content, $matches);
		if($matches){	 
			return $matches[1][0];
		}else{
			return false;
		}
	}
}

function weixin_robot_check_domain($id=56){
	return wpjam_net_check_domain($id);
}

function get_post_weixin_thumb($post,$size){
	$thumb = apply_filters('weixin_thumb',false,$size,$post);

	if($thumb===false){
		$thumbnail_id = get_post_thumbnail_id($post->ID);
		if($thumbnail_id){
			$thumb = wp_get_attachment_image_src($thumbnail_id, $size);
			$thumb = $thumb[0];
		}else{
			$thumb = get_post_first_image($post->post_content);
		}

		if(empty($thumb)){
			$thumb = weixin_robot_get_setting('weixin_default');
		}
	}
	if (!empty($thumb)){//By Glay
		$pos = strrpos($thumb,'/');
		$thumb= substr($thumb, 0,$pos+1).urlencode(substr($thumb, $pos+1));
	}
	return $thumb;
}

function weixin_robot_get_setting($setting_name){
	$option = weixin_robot_get_basic_option();
	return wpjam_get_setting($option, $setting_name);
}

function weixin_robot_get_option($option_name){
	$defaults = weixin_robot_get_default_option($option_name);
	return wpjam_get_option($option_name,$defaults);
}

/* 向下兼容 */
function weixin_robot_get_basic_option(){
	return weixin_robot_get_option('weixin-robot-basic' );
}

function weixin_robot_get_advanced_option(){
	return weixin_robot_get_option('weixin-robot-advanced' );
}

function weixin_robot_get_option_labels($option_name){
	if($option_name == 'weixin-robot-basic'){
		return weixin_robot_get_option_basic_labels();
	}elseif($option_name == 'weixin-robot-advanced'){
		return weixin_robot_get_option_advanced_labels();
	}
}

function weixin_robot_get_default_option($option_name){
	if($option_name == 'weixin-robot-basic'){
		return weixin_robot_get_default_basic_option();
	}elseif($option_name == 'weixin-robot-advanced'){
		return weixin_robot_get_default_advanced_option();
	}
}






