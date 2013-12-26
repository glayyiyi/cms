<?php

if(!function_exists("shareItemSort")){
	function shareItemSort($a, $b){
		if($a['seq'] <= $b['seq'])
			return -1;
		else
			return 1;
	}
	
	function WPSNSShare_update(&$new, $old){
		if(isset($old)){
			$new = $old;
		}
	}
}

if (!class_exists('ShareSNS')) {
	class ShareSNS {
		var $optionsName = SHARESNS_OPTION;
		var $options;
		var $p = array(
					'baidu' => 0,
					'delicious' => -26,
					'douban' => -52,
					'facebook' => -78,
					'fanfou' => -104,
					'gbuzz' => -130,
					'gmark' => -156,
					'kaixin' => -208,
					'linkedin' => -234,
					'qqzone' => -260,
					'renren' => -312,
					'sina' => -338,
					't163' => -364,
					'tqq' => -390,
					'tsohu' => -416,
					'twitter' => -442,
					'gplus' => -468,
					'b' => -490,
					'gplusone' => -516,
					'sinaLogin' => -537,
					'qqLogin' => -503,
				);

		function ShareSNS() {}

		function init() {
			$options = get_option($this->optionsName);
			if(empty($options)){
				$options = $this->defaultOptions();
				update_option($this->optionsName, $options);
			}
			else if($options['version'] != SHARESNS_VERSION){
				$options = $this->updateOptions();
			}
			$this->options = $options;
		}
		
		function defaultOptions() {
			return array(
				'version' => SHARESNS_VERSION,
				'SNS' => array(
					'renren' => array('c'=>1,'name'=>'renren','site'=>'人人网','seq'=>0,'note'=>''),
					'douban' => array('c'=>1,'name'=>'douban','site'=>'豆瓣','seq'=>1,'note'=>''),
					'qqzone' => array('c'=>1,'name'=>'qqzone','site'=>'QQ空间','seq'=>2,'note'=>'可同时分享到腾讯微博'),
					'kaixin' => array('c'=>1,'name'=>'kaixin','site'=>'开心网','seq'=>3,'note'=>''),
					'baidu' => array('c'=>1,'name'=>'baidu','site'=>'百度空间','seq'=>4,'note'=>'百度博客默认转载内容为空'),
					'sina' => array('c'=>1,'name'=>'sina','site'=>'新浪微博','seq'=>5,'note'=>'自带URL缩短：t.cn'),
					'tqq' => array('c'=>1,'name'=>'tqq','site'=>'腾讯微博','seq'=>6,'note'=>'自带URL缩短：url.cn'),
					't163' => array('c'=>1,'name'=>'t163','site'=>'网易微博','seq'=>7,'note'=>'自带URL缩短：163.fm'),
					'tsohu' => array('c'=>1,'name'=>'tsohu','site'=>'搜狐微博','seq'=>8,'note'=>'自带URL缩短：t.itc.cn'),
					'fanfou' => array('c'=>1,'name'=>'fanfou','site'=>'饭否','seq'=>9,'note'=>'无URL缩短'),
					'gmark' => array('c'=>1,'name'=>'gmark','site'=>'Google书签','seq'=>10,'note'=>''),
					'gplus' => array('c'=>0,'name'=>'gplus','site'=>'Google+','seq'=>11,'note'=>''),
//					'gbuzz' => array('c'=>1,'name'=>'gbuzz','site'=>'Google Buzz','seq'=>11,'note'=>''),
					'twitter' => array('c'=>0,'name'=>'twitter','site'=>'twitter','seq'=>12,'note'=>'适合使用tinyurl缩短功能'),
					'facebook' => array('c'=>0,'name'=>'facebook','site'=>'facebook','seq'=>13,'note'=>''),
					'linkedin' => array('c'=>0,'name'=>'linkedin','site'=>'linkedin','seq'=>14,'note'=>'商务社交网站'),
					'delicious' => array('c'=>0,'name'=>'delicious','site'=>'delicious','seq'=>15,'note'=>'美味书签'),
				),
				'output' => array(
					'auto' => 1,
					'share' => '分享到：',
					'ending' => '',
					'hr' => 1,
					'distance' => 40,
					'content_start' => 0,
					'content_end' => 1,
					'gplusone' => 0,
				),
				'tiny' => array(
					'tinyurl' => 0,
					'sinaurl' => 0,
				),
				'sync' => array(
					'open' => 1,
					'post_sync' => 1,
					'update_sync' => 0,
					'single_desc' => 1,
					'image_sync' => 1,
					'format' => '%blog的博客更新日志： 《%title》 %url %desc',
					'sina' => array(
						'key' => '1925972150',
						'secret' => '513cb05f0a200b691ebe4e28ebdd6391',
						'sina_name' => '',
						'oauth_token' => '',
						'token_expires' => '0',
						'message' => ''
					),
					'tqq' => array(
						'key' => '9bbb11f66ed44ed48802cc82d167813f',
						'secret' => 'c5c117679290c2e0166b1ef2d597ae03',
						'name' => '',
						'oauth_token' => '',
						'oauth_token_secret' => '',
						'message' => ''
					),
					'renren' => array(
						'key' => 'bbf00f68725a407c8a0a9e4eb10652ab',
						'secret' => '2cf6e992f23c479a81d3a2a873e77998',
						'name' => '',
						'oauth_token' => '',
						'refresh_token' => '',
						'token_expires' => '0',
						'message' => ''
					),
					'twitter' => array(
						'key' => 'E0sAXIvVGp1SNNnVJxhOA',
						'secret' => '6UGxtR5QOXWynw0rTnNtPmKFBOlRvC57tVjq3g',
						'name' => '',
						'oauth_token' => '',
						'oauth_token_secret' => '',
						'message' => ''
					)
				),
			);
		}
		
		function updateOptions() {
			$newOptions = $this->defaultOptions();
			$oldOptions = get_option($this->optionsName);
			
			//sns list
			if(isset($oldOptions['SNS'])){
				foreach($oldOptions['SNS'] as $sns => $array ){
					if(in_array($sns, array_keys($newOptions['SNS']))){
						$newOptions['SNS'][$sns]['c'] = $array['c'];
						if(isset($array['seq']))
							$newOptions['SNS'][$sns]['seq'] = $array['seq'];
					}
				}
			}
			
			//output format
			if(isset($oldOptions['output'])){
				foreach($oldOptions['output'] as $key => $value ){
					if(in_array($key, array_keys($newOptions['output']))){
						$newOptions['output'][$key] = $value;
					}
				}
			}
			
			//url short
			if(isset($oldOptions['tiny']) && is_array($oldOptions['tiny'])){
				$check = false;
				if($oldOptions['tiny']['open'] == 1){
					$sum = 0;
					foreach ($oldOptions['tiny'] as $key => $value){
						if($key != 'open') $sum += $value;
					}
					if($sum == 1) $check = true;
				}
				if($check){
					foreach($oldOptions['tiny'] as $key => $value ){
						if(in_array($key, array_keys($newOptions['tiny']))){
							$newOptions['tiny'][$key] = $value;
						}
					}
				}
			}
			
			//sync
			if(isset($oldOptions['sync']) && is_array($oldOptions['sync'])){
				if(isset($oldOptions['sync']['open'])){
					$newOptions['sync']['open'] = $oldOptions['sync']['open'];
					$newOptions['sync']['post_sync'] = $oldOptions['sync']['post_sync'];
					$newOptions['sync']['update_sync'] = $oldOptions['sync']['update_sync'];
					WPSNSShare_update($newOptions['sync']['single_desc'], $oldOptions['sync']['single_desc']);
					WPSNSShare_update($newOptions['sync']['image_sync'], $oldOptions['sync']['image_sync']);
					WPSNSShare_update($newOptions['sync']['format'], $oldOptions['sync']['format']);
				}
				if(isset($oldOptions['sync']['sina'])){
					$oldsina = $oldOptions['sync']['sina'];
					$newOptions['sync']['sina']['sina_name'] = $oldsina['sina_name'];
					$newOptions['sync']['sina']['oauth_token'] = $oldsina['oauth_token'];
					$newOptions['sync']['sina']['token_expires'] = $oldsina['token_expires'];
					$newOptions['sync']['sina']['message'] = $oldsina['message'];
				}
				if(isset($oldOptions['sync']['tqq'])){
					$oldtqq = $oldOptions['sync']['tqq'];
					$newOptions['sync']['tqq']['name'] = $oldtqq['name'];
					$newOptions['sync']['tqq']['oauth_token'] = $oldtqq['oauth_token'];
					$newOptions['sync']['tqq']['oauth_token_secret'] = $oldtqq['oauth_token_secret'];
					$newOptions['sync']['tqq']['message'] = $oldtqq['message'];
				}
				if(isset($oldOptions['sync']['renren'])){
					$oldrenren = $oldOptions['sync']['renren'];
					$newOptions['sync']['renren']['name'] = $oldrenren['name'];
					$newOptions['sync']['renren']['oauth_token'] = $oldrenren['oauth_token'];
					$newOptions['sync']['renren']['refresh_token'] = $oldrenren['refresh_token'];
					$newOptions['sync']['renren']['token_expires'] = $oldrenren['token_expires'];
					$newOptions['sync']['renren']['message'] = $oldrenren['message'];
				}
				if(isset($oldOptions['sync']['twitter'])){
					$oldtwitter = $oldOptions['sync']['twitter'];
					$newOptions['sync']['twitter']['name'] = $oldtwitter['name'];
					$newOptions['sync']['twitter']['oauth_token'] = $oldtwitter['oauth_token'];
					$newOptions['sync']['twitter']['oauth_token_secret'] = $oldtwitter['oauth_token_secret'];
					$newOptions['sync']['twitter']['message'] = $oldtwitter['message'];
				}
			}
				
			update_option($this->optionsName, $newOptions);
			return $newOptions;
		}

		function printAdminPage() {
			$this->init();
			if(isset($_POST['shareSNS_uninstall'])){
				delete_option($this->optionsName);
				include(SHARESNS_DIR.'/page/delete.php');
				return;
			}
			if(isset($_POST['shareSNS_update'])){
				$options = $this->defaultOptions();
				$oldOptions = get_option($this->optionsName);
				
				//init sns list
				foreach ($options['SNS'] as $sns => $array){
					$options['SNS'][$sns]['c'] = 0;
					$options['SNS'][$sns]['seq'] = 1000;
				}
				
				//to do with sns checkbox list
				$snsList = $_POST['c'];
				if(count($snsList) > 0){
					$index = 0;
					foreach ( $snsList as $sns ){
						if(in_array($sns, array_keys($options['SNS']))){
							$options['SNS'][$sns]['c'] = 1;
							$options['SNS'][$sns]['seq'] = $index;
						}
						$index++;
					}
				}
				
				//output format
				$options['output']['auto'] = $_POST['output_auto'];
				$options['output']['share'] = $_POST['output_share'];
				$options['output']['ending'] = $_POST['output_ending'];
				if(!isset($_POST['output_hr_no'])){
					$options['output']['hr'] = 0;
				}
				if(isset($_POST['output_gplusone'])){
					$options['output']['gplusone'] = 1;
				}
				if(intval($_POST['output_distance'])){
					$distance = intval($_POST['output_distance']);
					if($distance >= 20){
						$options['output']['distance'] = $_POST['output_distance'];
					}
				}
				
				//output position
				if(isset($_POST['output_content_start'])){
					$options['output']['content_start'] = 1;
				}
				else{
					$options['output']['content_start'] = 0;
				}
				if(isset($_POST['output_content_end'])){
					$options['output']['content_end'] = 1;
				}
				else{
					$options['output']['content_end'] = 0;
				}
				
				//url shorter
				if(isset($_POST['tiny'])){
					$tiny = $_POST['tiny'];
					if($tiny == 'tinyurl'){
						$options['tiny']['tinyurl'] = 1;
					}
					else if($tiny == 'sinaurl'){
						$options['tiny']['sinaurl'] = 1;
					}
				}
				
				//sync general option
				if(!isset($_POST['sync_open'])){
					$options['sync']['open'] = 0;
				}
				if(!isset($_POST['post_sync'])){
					$options['sync']['post_sync'] = 0;
				}
				if(isset($_POST['update_sync'])){
					$options['sync']['update_sync'] = 1;
				}
				if(!isset($_POST['single_desc'])){
					$options['sync']['single_desc'] = 0;
				}
				if(!isset($_POST['image_sync'])){
					$options['sync']['image_sync'] = 0;
				}
				$options['sync']['format'] = $_POST['sina_format'];
				
				//sina sync
				if($_POST['sina_submit'] == 1){	//login auto submit
					$options['sync']['sina']['sina_name'] = $_POST['sina_name'];
					$options['sync']['sina']['oauth_token'] = $_POST['sina_token'];
					$options['sync']['sina']['token_expires'] = time() + intval($_POST['sina_expires']);
				}
				else{
					if(isset($_POST['sina_logout'])){	//logout
						$options['sync']['sina']['sina_name'] = '';
						$options['sync']['sina']['oauth_token'] = '';
						$options['sync']['sina']['token_expires'] = 0;
						$options['sync']['sina']['message'] = '';
					}
					else{	//user submit to modify some checkbox or text fields
						$oldsina = $oldOptions['sync']['sina'];
						$options['sync']['sina']['sina_name'] = $oldsina['sina_name'];
						$options['sync']['sina']['oauth_token'] = $oldsina['oauth_token'];
						$options['sync']['sina']['token_expires'] = $oldsina['token_expires'];
						$options['sync']['sina']['message'] = $oldsina['message'];
					}
				}
				
				//tqq sync
				if($_POST['tqq_submit'] == 1){	//login auto submit
					$options['sync']['tqq']['name'] = $_POST['tqq_name'];
					$options['sync']['tqq']['oauth_token'] = $_POST['tqq_token'];
					$options['sync']['tqq']['oauth_token_secret'] = $_POST['tqq_secret'];
				}
				else{
					if(isset($_POST['tqq_logout'])){	//logout
						$options['sync']['tqq']['name'] = '';
						$options['sync']['tqq']['oauth_token'] = '';
						$options['sync']['tqq']['oauth_token_secret'] = '';
						$options['sync']['tqq']['message'] = '';
					}
					else{	//user submit to modify some checkbox or text fields
						$oldtqq = $oldOptions['sync']['tqq'];
						$options['sync']['tqq']['name'] = $oldtqq['name'];
						$options['sync']['tqq']['oauth_token'] = $oldtqq['oauth_token'];
						$options['sync']['tqq']['oauth_token_secret'] = $oldtqq['oauth_token_secret'];
						$options['sync']['tqq']['message'] = $oldtqq['message'];
					}
				}
				
				//renren sync
				if($_POST['renren_submit'] == 1){	//login auto submit
					$options['sync']['renren']['name'] = $_POST['renren_name'];
					$options['sync']['renren']['oauth_token'] = $_POST['renren_token'];
					$options['sync']['renren']['token_expires'] = time() + intval($_POST['renren_expires']);
					$options['sync']['renren']['refresh_token'] = $_POST['renren_refresh_token'];
				}
				else{
					if(isset($_POST['renren_logout'])){	//logout
						$options['sync']['renren']['name'] = '';
						$options['sync']['renren']['oauth_token'] = '';
						$options['sync']['renren']['refresh_token'] = '';
						$options['sync']['renren']['token_expires'] = 0;
						$options['sync']['renren']['message'] = '';
					}
					else{	//user submit to modify some checkbox or text fields
						$oldrenren = $oldOptions['sync']['renren'];
						$options['sync']['renren']['name'] = $oldrenren['name'];
						$options['sync']['renren']['oauth_token'] = $oldrenren['oauth_token'];
						$options['sync']['renren']['refresh_token'] = $oldrenren['refresh_token'];
						$options['sync']['renren']['token_expires'] = $oldrenren['token_expires'];
						$options['sync']['renren']['message'] = $oldrenren['message'];
					}
				}
				
				//twitter sync
				if($_POST['twitter_submit'] == 1){	//login auto submit
					$options['sync']['twitter']['name'] = $_POST['twitter_name'];
					$options['sync']['twitter']['oauth_token'] = $_POST['twitter_token'];
					$options['sync']['twitter']['oauth_token_secret'] = $_POST['twitter_secret'];
				}
				else{
					if(isset($_POST['twitter_logout'])){	//logout
						$options['sync']['twitter']['name'] = '';
						$options['sync']['twitter']['oauth_token'] = '';
						$options['sync']['twitter']['oauth_token_secret'] = '';
						$options['sync']['twitter']['message'] = '';
					}
					else{	//user submit to modify some checkbox or text fields
						$oldtwitter = $oldOptions['sync']['twitter'];
						$options['sync']['twitter']['name'] = $oldtwitter['name'];
						$options['sync']['twitter']['oauth_token'] = $oldtwitter['oauth_token'];
						$options['sync']['twitter']['oauth_token_secret'] = $oldtwitter['oauth_token_secret'];
						$options['sync']['twitter']['message'] = $oldtwitter['message'];
					}
				}
				
				update_option($this->optionsName, $options);
				$this->options = $options;
			}
			include(SHARESNS_DIR.'/page/home.php');
		}
	}
}


if (!function_exists('WPSNSShare_addJS')) {
	function WPSNSShare_addJS() {
		$js = SHARESNS_HOME.'/'.SHARESNS_NAME.'.js';
		$dept = Array();
		wp_enqueue_script(SHARESNS_NAME, $js, $dept, SHARESNS_VERSION);
		$option = get_option(SHARESNS_OPTION);
		if($option['output']['gplusone'] == 1){
			wp_enqueue_script('plusone', 'https://apis.google.com/js/plusone.js');
		}
	}
}

if (!function_exists('WPSNSShare_addSettingsLink')) {
	function WPSNSShare_addSettingsLink($links) { 
	  $settings_link = '<a href="options-general.php?page='.SHARESNS_NAME.'">设置</a>'; 
	  array_unshift($links, $settings_link);
	  return $links; 
	}
}
