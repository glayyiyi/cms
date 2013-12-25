<?php

//自定义回复，内置回复，函数回复，关键字太长处理等。
add_filter('weixin_custom_keyword','wpjam_weixin_custom_keyword',1,2);
function wpjam_weixin_custom_keyword($false,$keyword){
	
	global $wechatObj;

	if(empty( $keyword ) || strpos($keyword, '#') !== false ) {
		echo "";
		$wechatObj->set_response('need-manual');
		return true;
	}

	$weixin_custom_keywords = weixin_robot_get_custom_keywords();

	if(isset($weixin_custom_keywords[$keyword])) {
		$weixin_custom_reply = $weixin_custom_keywords[$keyword];

		if($weixin_custom_reply->type == 'text'){	
			$wechatObj->set_response('custom-text');
			echo sprintf($wechatObj->get_textTpl(), str_replace("\r\n", "\n", $weixin_custom_reply->reply));
		}elseif($weixin_custom_reply->type == 'img'){
			add_filter('weixin_query','wpjam_custom_weixin_query_img_repy');

			$wechatObj->set_response('custom-img');
			$wechatObj->query($keyword);
		}elseif($weixin_custom_reply->type == 'function'){
			call_user_func($weixin_custom_reply->reply, $keyword);
		}

		return true;
	}

	//内置回复 -- 完全匹配
	$weixin_builtin_replies = weixin_robot_get_builtin_replies('full');

	if(isset($weixin_builtin_replies[$keyword])) {
		call_user_func($weixin_builtin_replies[$keyword]['function'], $keyword);
		return true;
	}

	//内置回复 -- 前缀匹配，目前只支持2个字
	$prefix_keyword = mb_substr($keyword, 0,2);
	$weixin_builtin_replies = weixin_robot_get_builtin_replies('prefix');

	if(isset($weixin_builtin_replies[$prefix_keyword])) {
		call_user_func($weixin_builtin_replies[$prefix_keyword]['function'], $keyword);
		return true;
	}

	// 检测关键字是不是太长了
	$keyword_length = mb_strwidth(preg_replace('/[\x00-\x7F]/','',$keyword),'utf-8')+str_word_count($keyword)*2;

	$weixin_keyword_allow_length = weixin_robot_get_setting('weixin_keyword_allow_length');
	
	if($keyword_length > $weixin_keyword_allow_length){
		$weixin_keyword_too_long = weixin_robot_get_setting('weixin_keyword_too_long');
		if($weixin_keyword_too_long){
			echo sprintf($wechatObj->get_textTpl(), $weixin_keyword_too_long);
		}
		$wechatObj->set_response('too-long');

		return true;
	}
	
	return $false;
}

//如果搜索关键字是分类名或者 tag 名，直接返回该分类或者tag下最新日志
add_filter('weixin_query','wpjam_advanced_weixin_query_catgory_tag', 99);
function wpjam_advanced_weixin_query_catgory_tag($weixin_query_array){
	if(isset($weixin_query_array['s'])){
		$keystr=$weixin_query_array['s'];
		global $wpdb;
		$term = $wpdb->get_row("SELECT term_id, taxonomy FROM {$wpdb->prefix}term_taxonomy INNER JOIN {$wpdb->prefix}terms USING ( term_id ) WHERE lower({$wpdb->prefix}terms.name) = '{$weixin_query_array['s']}' OR {$wpdb->prefix}terms.slug = '{$weixin_query_array['s']}' LIMIT 0 , 1");
		
		if($term){
			$weixin_query_array = wpjam_advanced_weixin_query_new($weixin_query_array);
			
			if($term->taxonomy == 'category'){
				$weixin_query_array['cat']		= $term->term_id;
			}elseif ($term->taxonomy == 'post_tag') {
				$weixin_query_array['tag_id']	= $term->term_id;
			}elseif ($term->taxonomy == 'product_tag') {//By Glay
				
				$weixin_query_array['post_type']	='product';
				$weixin_query_array['product_tag']	=$keystr;
			}
		}
	}
	return $weixin_query_array;
}

// 通过自定义字段设置改变图文的链接
add_filter('weixin_url','wpjam_weixin_url');
function wpjam_weixin_url($url){
	global $post;
	if($weixin_url = get_post_meta($post->ID, 'weixin_url', true)){
		return $weixin_url;
	}else{
		return $url;
	}
}

// 设置如果系统安装了七牛存储或者 WPJAM Thumbnail 高级缩略图插件，则使用它们截图
add_filter('weixin_thumb','wpjam_weixin_thumb_filter',10,3);
function wpjam_weixin_thumb_filter($thumb,$size,$post){
	if(function_exists('wpjam_get_post_thumbnail_src')){
		if(wpjam_has_post_thumbnail()){
			$thumb = wpjam_get_post_thumbnail_src($post, $size);
		}	
	}
	return $thumb;
}

function wpjam_custom_weixin_query_img_repy($weixin_query_array){
	$weixin_custom_keywords = weixin_robot_get_custom_keywords();
	$weixin_custom_reply = $weixin_custom_keywords[$weixin_query_array['s']];
	$post_ids = explode(',', $weixin_custom_reply->reply);

	$weixin_query_array['post__in']		= $post_ids;
	$weixin_query_array['orderby']		= 'post__in';

	unset($weixin_query_array['s']);
	$weixin_query_array['post_type']	= 'any';

	return $weixin_query_array;
}

//按照时间排序
function wpjam_advanced_weixin_query_new($weixin_query_array){
	unset($weixin_query_array['s']);
	return $weixin_query_array;
}

//随机排序
function wpjam_advanced_weixin_query_rand($weixin_query_array){
	$weixin_query_array['orderby']		= 'rand';
	return $weixin_query_array;
}

//按照浏览排序
function wpjam_advanced_weixin_query_hot($weixin_query_array){
	$weixin_query_array['meta_key']		= 'views';
	$weixin_query_array['orderby']		= 'meta_value_num';
	return $weixin_query_array;
}

//按照留言数排序
function wpjam_advanced_weixin_query_comment($weixin_query_array){
	$weixin_query_array['orderby']		= 'comment_count';
	return $weixin_query_array;
}

//设置时间为最近7天
function wpjam_advanced_filter_where_7( $where = '' ) {
	return $where . " AND post_date > '" . date('Y-m-d', strtotime('-7 days')) . "'";
}

//设置时间为最近30天
function wpjam_advanced_filter_where_30( $where = '' ) {
	return $where . " AND post_date > '" . date('Y-m-d', strtotime('-60 days')) . "'";
}

/*
function wpjam_basic_filter($original){
	$weixin_robot_basic = weixin_robot_get_basic_option();

	global $wp_current_filter;

	//最后一个才是当前的 filter
	$wpjam_current_filter = $wp_current_filter[count($wp_current_filter)-1];

	if(isset($weixin_robot_basic[$wpjam_current_filter])){
		if($weixin_robot_basic[$wpjam_current_filter ]){
			return $weixin_robot_basic[$wpjam_current_filter];
		}
	}else{
		return $original;
	}
}
*/