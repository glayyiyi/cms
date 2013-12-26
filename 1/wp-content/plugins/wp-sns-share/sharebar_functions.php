<?php

if (!function_exists('wp_sns_share')) {
	function wp_sns_share() {
		$options = get_option(SHARESNS_OPTION);
		$text = WPSNSShare_create_bar($options);
		return $text;
	}
	
	function WPSNSShare_filter_insert($content){
		if(is_home() || is_archive() || is_category()){
			return $content;
		}
		if(!is_single()){
			return $content;
		}
		$options = get_option(SHARESNS_OPTION);
		$ret = $content;
		$text = WPSNSShare_create_bar($options);
		if($options['output']['content_start'] == 1){
			$ret = $text.$ret;
		}
		if($options['output']['content_end'] == 1){
			$ret = $ret.$text;
		}
		return $ret;
	}
	
	function WPSNSShare_sinaurl($url, $key = '1925972150') {
	    $opts['http'] = array('method' => "GET", 'timeout'=>60);
	    $context = stream_context_create($opts);
	    $url = "http://api.t.sina.com.cn/short_url/shorten.json?source=$key&url_long=$url";
	    $html = @file_get_contents($url, false, $context);
	    $url = json_decode($html, true);
	    if (!empty($url[0]['url_short'])) {
	        return $url[0]['url_short'];
	    }
	}
	
	function WPSNSShare_create_bar($options){
		if(is_single()){
			if($options['sync']['single_desc'] == 1){
				global $post;
				$content = $post->post_content;
				$content = WPSNSShare_remove_caption($content);
				$content = strip_tags($content);
				$desc = WPSNSShare_weibo_get_short_desc('', $content, 60);
			}
			$from = 'single';
		}
		else{
			$from = 'other';
			$desc = get_option('blogdescription');;
		}
		$text = "\n\n<!-- wp-sns-share part begin -->\n";
		$text .= '<div class="WPSNS_main" style="margin:20px 0;">'."\n";
		$text .= '<input id="wp-sns-share-desc" type="hidden" value="'.$desc.'" />'."\n";
		$text .= '<input id="wp-sns-share-blog" type="hidden" value="'.trim(get_option('blogname')).'" />'."\n";
		$text .= '<input id="wp-sns-share-from" type="hidden" value="'.$from.'" />'."\n";
		$use_tiny = 0;
		if(isset($options['tiny']['tinyurl']) && isset($options['tiny']['sinaurl'])){
			$use_tiny = 1;
		}
		if($use_tiny == 1){
			$postURL = 'http://'.$_SERVER['SERVER_NAME'];
			if($_SERVER['SERVER_PORT'] != '80')
				$postURL .= ':'.$_SERVER['SERVER_PORT'];
			$postURL .= $_SERVER['REQUEST_URI'];
			if($options['tiny']['tinyurl'] == 1){
				$tiny = file_get_contents('http://tinyurl.com/api-create.php?url='.$postURL);
			}
			else if($options['tiny']['sinaurl'] == 1){
				$tiny = WPSNSShare_sinaurl($postURL);
			}
			else{
				$tiny = '';
			}
			$text .= '<input id="wp-sns-share-tiny" type="hidden" value="'.$tiny.'" />'."\n";
		}
		$line = '<div width="95%" style="border-top:1px dotted #D4D0C8;height:1px"></div>'."\n";
		if($options['output']['hr'] == 1){
			$text .= $line;
		}
		$text .= '<div style="margin:15px 0;height:27px;">'."\n";
		$text .= '<ul class="WPSNS_ul" style="list-style:none;margin:0;padding:0;">'."\n";
		$first = true;
		if(count($options['SNS']) > 0){
			$itemList = $options['SNS'];
			usort($itemList, 'shareItemSort');
			global $wp_shareSNS;
			$position = $wp_shareSNS->p;
			$b_pos = $position['b'];
			foreach ($itemList as $array){
				if($array['c'] == 1){
					if($first) {
						$text .= '<span style="margin:3px 10px 0 0;height:27px;display:block;float:left;font-size:16px;">'.$options['output']['share']."</span>\n";
						$first = false;
					}
					$title = '分享到'.$array['site'];
					$onclick = "shareToSNS('".$array['name']."',".$use_tiny.")";
					$li_style = "position:relative;float:left;display:inline;width:".$options['output']['distance']."px;margin:0;padding:0;";
					$icon_style = 'z-index:2;width:16px;height:16px;margin:6px 0;padding:0;border:none;text-decoration:none;float:left;position:relative;';
					$icon_style .= "background:url(".SHARESNS_IMAGE_HOME."/icons.png) no-repeat 0 ".$position[$array['name']]."px;";
					$cover_style = "width:26px;height:26px;top:2px;left:-5px;display:none;position:absolute;background:transparent url(".SHARESNS_IMAGE_HOME."/icons.png) no-repeat 0 {$b_pos}px";
					$text .= "<li class=\"WPSNS_item\" style=\"$li_style\">\n".
								"<a href=\"javascript:void(0)\" title=\"$title\" onclick=\"$onclick\" style=\"$icon_style\"></a>\n".
								"<em style=\"$cover_style\"></em>\n</li>\n";
				}
			}
			$text .= $options['output']['ending']."\n";
		}
		$text .= "</ul>\n";
		$text .= "</div>\n";
		if($options['output']['hr'] == 1){
			$text .= $line;
		}
		$text .= "</div>\n";
		$text .= "<!-- wp-sns-share part end -->\n\n";
		return $text;
	}
}
