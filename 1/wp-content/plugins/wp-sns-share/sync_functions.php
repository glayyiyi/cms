<?php

if (!function_exists('WPSNSShare_sync')) {
	function WPSNSShare_sync_for_future($post){
		$options = get_option(SHARESNS_OPTION);
		$content = $post->post_content;
		
		$weibo = WPSNSShare_create_weibo($options, $post->ID, $post->post_title, $content);
		$img = WPSNSShare_getIMG($content);
		
		$array = array('sina'=>true, 'qq'=>true, 'renren'=>true, 'twitter'=>true);
		WPSNSShare_send_weibos($options, $weibo, $img, $array);
	}
	
	function WPSNSShare_sync($postID, $post){
		if(!isset($_POST['WPSNSShare_widget_sync'])){	// kick off future post
			return;
		}
		$options = get_option(SHARESNS_OPTION);
		$this_post = $post;
		
		$sina_sync = false;
		$qq_sync = false;
		$renren_sync = false;
		$twitter_sync = false;
		// determine whether and which to sync
		$widget_sync = intval($_POST['WPSNSShare_widget_sync']);
		if($widget_sync <= 0 || $widget_sync >= 3){
			$widget_sync = 1;
		}
		if($widget_sync == 1){		//默认
			$sina_sync = true;
			$qq_sync = true;
			$renren_sync = true;
			$twitter_sync = true;
			if($options['sync']['open'] == 0){
				return;
			}
			$post_sync = $options['sync']['post_sync'];
			$update_sync = $options['sync']['update_sync'];
			$post_date = $this_post->post_date;
			$post_modified = $this_post->post_modified;
			if($post_date == $post_modified && $post_sync == 0){	//post situation
				return;
			}
			if($post_date != $post_modified && $update_sync == 0){	//update situation
				return;
			}
		}
		else if($widget_sync == 2){ //选择同步
			if(isset($_POST['WPSNSShare_widget_sync_sina']) 
					&& $_POST['WPSNSShare_widget_sync_sina'] == '1'){
				$sina_sync = true;
			}
			if(isset($_POST['WPSNSShare_widget_sync_qq']) 
					&& $_POST['WPSNSShare_widget_sync_qq'] == '1'){
				$qq_sync = true;
			}
			if(isset($_POST['WPSNSShare_widget_sync_renren']) 
					&& $_POST['WPSNSShare_widget_sync_renren'] == '1'){
				$renren_sync = true;
			}
			if(isset($_POST['WPSNSShare_widget_sync_twitter']) 
					&& $_POST['WPSNSShare_widget_sync_twitter'] == '1'){
				$twitter_sync = true;
			}
			if(!$sina_sync && !$qq_sync && !$renren_sync && !$twitter_sync){
				return;
			}
		}
		// end
		
		// create the weibo, replace the user setting
		$text_diy = trim($_POST['WPSNSShare_widget_text']);
		$content = $this_post->post_content;
		if(empty($text_diy)){
			$weibo = WPSNSShare_create_weibo($options, $postID, $this_post->post_title, $content);
		}
		else{
			$weibo = WPSNSShare_create_weibo($options, $postID, $this_post->post_title, $text_diy);
		}
		$img = WPSNSShare_getIMG($content);
		// end
		
		// send weibo
		$array = array('sina'=>$sina_sync, 'qq'=>$qq_sync, 
							'renren'=>$renren_sync, 'twitter'=>$twitter_sync);
		WPSNSShare_send_weibos($options, $weibo, $img, $array);
		// end
	}
	
	function WPSNSShare_send_weibos(&$options, $weibo, $img, $array){
		$sina_sync = $array['sina'];
		$qq_sync = $array['qq'];
		$renren_sync = $array['renren'];
		$twitter_sync = $array['twitter'];
		
		if(isset($options['sync']['sina']) && $sina_sync){
			$sinaOption = $options['sync']['sina'];
			$token = $sinaOption['oauth_token'];
			if($token != ''){
				$sina_weibo = $weibo;
				if($img && $options['sync']['image_sync'] == 1){
					$message = WPSNS_sina_upload($sina_weibo, $img, $token);
				}
				else{
					$message = WPSNS_sina_send_weibo($sina_weibo, $token);
				}
				$options['sync']['sina']['message'] = $message;
			}
		}
		if(isset($options['sync']['tqq']) && $qq_sync){
			$tqqOption = $options['sync']['tqq'];
			list($key, $key_secret) = WPSNSShare_get_tqq_app_key_and_secret($tqqOption);
			$token = $tqqOption['oauth_token'];
			$token_secret = $tqqOption['oauth_token_secret'];
			if($token != '' && $token_secret != ''){
				$qq_weibo = $weibo;
				if($img && $options['sync']['image_sync'] == 1){
					$message = WPSNS_tqq_upload($qq_weibo, $img, $key, 
										$key_secret, $token, $token_secret);
				}
				else{
					$message = WPSNS_tqq_send_weibo($qq_weibo, $key, 
										$key_secret, $token, $token_secret);
				}
				$options['sync']['tqq']['message'] = $message;
			}
		}
		if(isset($options['sync']['renren']) && $renren_sync){
			$renrenOption = $options['sync']['renren'];
			$token = $renrenOption['oauth_token'];
			$secret = $renrenOption['secret'];
			$renren_weibo = $weibo;
			if($token != '' && $secret != ''){
				$message = WPSNS_renren_post_status($renren_weibo, $token, $secret);
				$options['sync']['renren']['message'] = $message;
			}
		}
		if(isset($options['sync']['twitter']) && $twitter_sync){
			$twitterOption = $options['sync']['twitter'];
			$oauth_token = $twitterOption['oauth_token'];
			$oauth_token_sccret = $twitterOption['oauth_token_secret'];
			$twitter_weibo = $weibo;
			if($oauth_token != '' && $oauth_token_sccret != ''){
				$message = WPSNS_send_twitter($twitter_weibo, $oauth_token, $oauth_token_sccret);
				$options['sync']['twitter']['message'] = $message;
			}
		}
		update_option(SHARESNS_OPTION, $options);
	}
	
	function WPSNSShare_create_weibo(&$options, $postID, $post_title, $content){
		$url = get_permalink($postID);
		$weibo = $options['sync']['format'];
		
		$content = WPSNSShare_remove_caption($content);
		$content = strip_tags($content);
		
		if(strstr($weibo, '%blog')){
			$weibo = str_replace('%blog', get_option('blogname'), $weibo);
		}
		if(strstr($weibo, '%title')){
			$weibo = str_replace('%title', $post_title, $weibo);
		}
		if(strstr($weibo, '%url')){
			$weibo = str_replace('%url', $url, $weibo);
		}
		if(strstr($weibo, '%desc')){
			$weibo_desc = WPSNSShare_weibo_get_short_desc($weibo, $content, 140);
			$weibo = str_replace('%desc', $weibo_desc, $weibo);
		}
		return $weibo;
	}
	
	function WPSNSShare_weibo_get_short_desc($weibo, $content, $allow){
		$l = strlen($weibo);
		$length = 0;
		for($i = 0;$i < $l;$i++){		//count the weibo without short desc's length
			$c = $weibo[$i];
			$n = ord($c);
			if(($n >> 7) == 0){			//0xxx xxxx, asci, single
				$length += 0.5;
			}
			else if(($n >> 4) == 15){ 	//1111 xxxx, first in four char
				if(isset($weibo[$i + 1])){
					$i++;
					if(isset($weibo[$i + 1])){
						$i++;
						if(isset($weibo[$i + 1])){
							$i++;
						}
					}
				}
				$length++;
			}
			else if(($n >> 5) == 7){ 	//111x xxxx, first in three char
				if(isset($weibo[$i + 1])){
					$i++;
					if(isset($weibo[$i + 1])){
						$i++;
					}
				}
				$length++;
			}
			else if(($n >> 6) == 3){ 	//11xx xxxx, first in two char
				if(isset($weibo[$i + 1])){
					$i++;
				}
				$length++;
			}
		}
		$length -= 2.5; // for $desc
		$append = $allow - $length - 10; // 10 is for safe
		$ret = '';
		$ll = strlen($content);
		for($i = 0;$i < $ll && $append > 0;$i++){
			$c = $content[$i];
			$n = ord($c);
			if(($n >> 7) == 0){			//0xxx xxxx, asci, single
				$ret .= $c;
				$append -= 0.5;
			}
			else if(($n >> 5) == 6){ 	//110x xxxx, first in two char
				$ret .= $c;
				if(isset($content[$i + 1])){
					$ret .= $content[$i + 1];
					$i++;
				}
				$append -= 1;
			}
			else if(($n >> 4) == 14){ 	//1110 xxxx, first in three char
				$ret .= $c;
				if(isset($content[$i + 1])){
					$ret .= $content[$i + 1];
					$i++;
					if(isset($content[$i + 1])){
						$ret .= $content[$i + 1];
						$i++;
					}
				}
				$append -= 1;
			}
			else if(($n >> 3) == 30){	//1111 0xxx, first in four char
				$ret .= $c;
				if(isset($content[$i + 1])){
					$ret .= $content[$i + 1];
					$i++;
					if(isset($content[$i + 1])){
						$ret .= $content[$i + 1];
						$i++;
						if(isset($content[$i + 1])){
							$ret .= $content[$i + 1];
							$i++;
						}
					}
				}
				$append -= 1;
			}
		}
		$ret .= '...';
		return $ret;
	}
	
	function WPSNSShare_getIMG($content){
		$imgNo = intval($_POST['WPSNSShare_widget_imgno']);
		if($imgNo == 0) $imgNo = 1;
		$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
		preg_match_all($pattern, $content, $m);
		$imgs = $m[1];
		$count = count($imgs);
		$imglist = array();
		for($i = 0;$i < $count;$i++){
			if(stripos($imgs[$i], 'wp-includes') > 0){
				continue;
			}
			if(strlen($imgs[$i]) > 0 && $imgs[$i][0] == '/'){
				$home = home_url();
				if($home[strlen($home) - 1] == '/'){
					$imgs[$i] = $home.substr($imgs[$i], 1);
				}
				else{
					$imgs[$i] = $home.$imgs[$i];
				}
			}
			$imglist[] = $imgs[$i];
		}
		$count = count($imglist);
		if($count == 0) return '';
		else if($count < $imgNo) return $imglist[$count - 1];
		else return $imglist[$imgNo - 1];
	}
	

	function WPSNSShare_get_login_url($open_login, $param){
		$site_url = get_bloginfo('wpurl');
		$base = SHARESNS_HOME."/open/$open_login/login.php?siteurl=$site_url&";
		if($open_login == 'twitter'){
			$url = $base;
		}
		else if($open_login == 'sina2'){
			$url = $base.'key='.$param['key'].'&secret='.$param['secret'];
		}
		else if($open_login == 'tqq'){
			$url = $base.'key='.$param['key'].'&secret='.$param['secret'];
		}
		else if($open_login == 'renren'){
			$url = $base.'key='.$param['key'];
		}
		return $url;
	}
	
	//写文章区域添加widget同步控制
	function WPSNSShare_widget() {
		$option = get_option(SHARESNS_OPTION);
		$sinaOption = $option['sync']['sina'];
		if(!empty($sinaOption['oauth_token']) && WPSNSShare_sina_token_expire($sinaOption)){
			$url = get_bloginfo('wpurl').'/wp-admin/options-general.php?page=wp-sns-share.php#sina_oauth';
			echo '<a href="'.$url.'"><p><strong style="color:red;font-size:16px">新浪认证授权已过期，请重新授权</strong></p></a>';
		}
		echo '<p>
			<input type="radio" name="WPSNSShare_widget_sync" value="1" checked="checked" onclick="document.getElementById(\'WPSNSShare_widget_sync_choose\').style.display=\'none\'" /> 默认设置&nbsp;&nbsp;
			<input type="radio" name="WPSNSShare_widget_sync" value="2" onclick="document.getElementById(\'WPSNSShare_widget_sync_choose\').style.display=\'\'" /> 自主选择
			</p>';
		echo '<div id="WPSNSShare_widget_sync_choose" style="display:none">';
		echo '<p>';
		if($option['sync']['sina']['oauth_token']){
			echo '<input type="checkbox" value="1" checked="checked" name="WPSNSShare_widget_sync_sina" /> 新浪&nbsp;&nbsp;';
		}
		if($option['sync']['tqq']['oauth_token']){
			echo '<input type="checkbox" value="1" checked="checked" name="WPSNSShare_widget_sync_qq" /> 腾讯&nbsp;&nbsp;';
		}
		if($option['sync']['renren']['oauth_token']){
			echo '<input type="checkbox" value="1" checked="checked" name="WPSNSShare_widget_sync_renren" /> 人人&nbsp;&nbsp;';
		}
		if($option['sync']['twitter']['oauth_token']){
			echo '<input type="checkbox" value="1" checked="checked" name="WPSNSShare_widget_sync_twitter" /> Twitter&nbsp;&nbsp;';
		}
		echo '</p>';
		echo '<p>';
		echo '选择要同步第几张图片：<input name="WPSNSShare_widget_imgno" size="3" value="1" />';
		echo '</p>';
		echo '我要自定义摘要  <input type="checkbox" value="1" onclick="if(this.checked){document.getElementById(\'WPSNSSHARE_TEXT\').style.display=\'block\'}else{document.getElementById(\'WPSNSSHARE_TEXT\').style.display=\'none\'}" />';
		echo '<p>';
		echo '<textarea id="WPSNSSHARE_TEXT" name="WPSNSShare_widget_text" style="display:none;width:250px;height:50px"></textarea>';
		echo '</p>';
		echo '</div>';
	} 
	
	function WPSNSShare_add_widget() {
		add_meta_box('WPSNSShare_widget', 'wp_sns_share 微博同步设置', 'WPSNSShare_widget', 'post', 'side', 'high');
	}

}
