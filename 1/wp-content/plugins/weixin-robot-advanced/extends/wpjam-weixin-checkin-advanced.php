<?php
/*
Plugin Name: 微信高级签到插件
Plugin URI: http://wpjam.net/item/wpjam-weixin-checkin-advanced/
Description: 微信高级签到 WordPress 插件，可以查看总共签到次数，连续签到次数，排名，击败多少用户。
Version: 1.1
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_action('wpjam_net_item_ids','wpjam_weixin_checkin_wpjam_net_item_id');
function wpjam_weixin_checkin_wpjam_net_item_id($item_ids){
    $item_ids['150'] = __FILE__;
    return $item_ids;
}

add_filter('weixin_builtin_reply', 'wpjam_weixin_checkin_builtin_reply');
function wpjam_weixin_checkin_builtin_reply($weixin_builtin_replies){
    $weixin_builtin_replies['tc'] = $weixin_builtin_replies['签到排行榜']		= array('type'=>'full',	'reply'=>'签到排行榜',	'function'=>'wpjam_weixin_top_checkin_users_reply');
	$weixin_builtin_replies['tc2'] = $weixin_builtin_replies['积分排行榜']	= array('type'=>'full',	'reply'=>'积分排行榜',	'function'=>'wpjam_weixin_top_credit_users_reply');
    return $weixin_builtin_replies;
}

add_filter('weixin_setting','wpjam_weixin_checkin_fileds',11);
function wpjam_weixin_checkin_fileds($sections){

    if(wpjam_net_check_domain(150)){
    	if(weixin_robot_get_setting('weixin_advanced_api')){
    		$sections['credit']['fileds']['weixin_checkin_success']		= array('title'=>'成功签到之后的回复',		'type'=>'textarea',	'rows'=>4,	'description'=>'');
	        $sections['credit']['fileds']['weixin_checkined']			= array('title'=>'已签到之后的回复',		'type'=>'textarea',	'rows'=>4,	'description'=>'');    
    	}else{
            $sections['credit']['fileds']['weixin_checkin_success_2']	= array('title'=>'成功签到之后完善信息之前的回复',		'type'=>'textarea',	'rows'=>4,	'description'=>'使用例子请参考：签到成功，添加 [credit_change]积分，你现在共有[credit]积分！你是今天第[order]位签到的用户，你已经连续签到了[continue_number]天，累积签到了[total_number]次，击败了[rank]%用户！发送tc还可以查看签到排行榜。点击&lt;a href="[profile_link]"&gt;点击完善资料&lt;/a&gt;注册后可以使用高级版签到功能。');
	        $sections['credit']['fileds']['weixin_checkin_success']		= array('title'=>'成功签到之后完善信息之后的回复',		'type'=>'textarea',	'rows'=>4,	'description'=>'');
	        $sections['credit']['fileds']['weixin_checkined_2']			= array('title'=>'已签到之后完善信息之前的回复',		'type'=>'textarea',	'rows'=>4,	'description'=>'');    
	        $sections['credit']['fileds']['weixin_checkined']			= array('title'=>'已签到之后完善信息之后的回复',		'type'=>'textarea',	'rows'=>4,	'description'=>'');    
    	}
    }

    return $sections;
}

add_filter('weixin_default_option','wpjan_weixin_checkin_default_option',10,2);
function wpjan_weixin_checkin_default_option($defaults_options, $option_name){
    if(wpjam_net_check_domain(150)){
        if($option_name == 'weixin-robot-basic'){
            $checkin_default_options = array(
                'weixin_checkin_success'	=> '签到成功，添加 [credit_change]积分，你现在共有[credit]积分！'."\n".'你是今天第[order]位签到的用户，你已经连续签到了[continue_number]天，累积签到了[total_number]天，击败了[rank]%用户！发送tc还可以查看签到排行榜。',
                'weixin_checkin_success_2'	=> '签到成功，添加 [credit_change]积分，你现在共有[credit]积分！'."\n".'你是今天第[order]位签到的用户，你已经连续签到了[continue_number]天，累积签到了[total_number]天，点击<a href="[profile_link]">点击完善资料</a>注册后可以使用高级版签到功能。',
                'weixin_checkined' 			=> '你今天已经签到过了，你现在共有[credit]积分！'."\n".'你是今天第[order]位签到的用户，你已经连续签到了[continue_number]次，累积签到了[total_number]天，击败了[rank]%用户！发送tc还可以查看签到排行榜。',
                'weixin_checkined_2' 		=> '你今天已经签到过了，你现在共有[credit]积分！'."\n".'你是今天第[order]位签到的用户，你已经连续签到了[continue_number]次，累积签到了[total_number]天，点击<a href="[profile_link]">点击完善资料</a>注册后可以使用高级版签到功能。',
            );
            return array_merge($defaults_options, $checkin_default_options);
        }
    }
    return $defaults_options;
}

add_filter('weixin_response_types','wpjam_weixin_checkin_response_types');
function wpjam_weixin_checkin_response_types($response_types){
    $response_types['top-credits']         = '回复积分排行榜';
    $response_types['top-checkin']         = '回复签到排行榜';
    return $response_types;
}

add_filter('weixin_tables','wpjam_checkin_weixin_tables');
function wpjam_checkin_weixin_tables($weixin_tables){
	$weixin_tables['高级签到'] = 'weixin_robot_continue_checkin_create_table';
	return $weixin_tables;
}

add_filter('weixin_checkin_success','wpjam_weixin_checkin_success',10,2);
function wpjam_weixin_checkin_success($checkin,$weixin_openid){

	$weixin_user = weixin_robot_get_user($weixin_openid);

	if(empty($weixin_user['nickname']) && empty($weixin_user['name'])){
		return wpjam_weixin_checkin_str_replace(weixin_robot_get_setting('weixin_checkin_success_2'),$weixin_openid,'update');
	}else{
		return wpjam_weixin_checkin_str_replace(weixin_robot_get_setting('weixin_checkin_success'),$weixin_openid,'update');
	}
}

add_filter('weixin_checkined','wpjam_weixin_checkined',10,2);
function wpjam_weixin_checkined($checkin,$weixin_openid){

	$weixin_user = weixin_robot_get_user($weixin_openid);

	if(empty($weixin_user['nickname']) && empty($weixin_user['name'])){
		return wpjam_weixin_checkin_str_replace(weixin_robot_get_setting('weixin_checkined_2'),$weixin_openid);
	}else{
		return wpjam_weixin_checkin_str_replace(weixin_robot_get_setting('weixin_checkined'),$weixin_openid);
	}
}

function wpjam_weixin_checkin_str_replace($checkin,$weixin_openid,$type=''){

	if($type =='update'){
		$continue_checkin_number = wpjam_weixin_set_continue_checkin_number($weixin_openid);
	}else{
		$continue_checkin_number = wpjam_weixin_get_continue_checkin_number($weixin_openid);
	}

	$checkin_order = wpjam_weixin_get_checkin_order($weixin_openid);

	$total_checkin_number = wpjam_weixin_get_total_checkin_number($weixin_openid);

	$checkin_rank = wpjam_weixin_get_checkin_rank($weixin_openid);

	$query_id = weixin_robot_user_get_query_id($weixin_openid);

	$profile_link = home_url('/?weixin_user_profile&weixin_user_id='.$query_id);

	return str_replace(array('[order]','[continue_number]','[total_number]','[rank]','[profile_link]'), array($checkin_order, $continue_checkin_number, $total_checkin_number, $checkin_rank, $profile_link), $checkin);

}

function weixin_robot_checkin_table(){
	global $wpdb;
	return $wpdb->prefix.'weixin_checkin';
}

register_activation_hook( WEIXIN_ROBOT_PLUGIN_FILE,'weixin_robot_continue_checkin_create_table');
function weixin_robot_continue_checkin_create_table() {	
	
	global $wpdb;

	$weixin_checkin_table = weixin_robot_checkin_table();

	if($wpdb->get_var("show tables like '{$weixin_checkin_table}'") != $weixin_checkin_table) {
		$sql = "
		CREATE TABLE IF NOT EXISTS `{$weixin_checkin_table}` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `weixin_openid` varchar(30) NOT NULL,
		  `continue_number` bigint(20) default NULL,
		  `total_number` bigint(20) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `weixin_openid` (`weixin_openid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
		dbDelta($sql);
	}
}

function wpjam_weixin_get_total_checkin_number($weixin_openid){
	global $wpdb;
	$weixin_credits_table = weixin_robot_credits_table();

	$current_time = current_time('timestamp');
	$current_date = date('Ymd',$current_time);

	$total_checkin_number = wp_cache_get($weixin_openid, 'total_checkin_number_'.$current_date);
	if($total_checkin_number == false){

		$total_checkin_number = $wpdb->get_var($wpdb->prepare("SELECT count(*) as total_checkin_number FROM {$weixin_credits_table} WHERE `type`='checkin' AND `weixin_openid` = %s ORDER BY id ASC",$weixin_openid));

		wp_cache_set($weixin_openid,$total_checkin_number,'total_checkin_number_'.$current_date);
	}

	return $total_checkin_number;
}


function wpjam_weixin_get_continue_checkin_number($weixin_openid){
	global $wpdb;

	$current_time = current_time('timestamp');
	$current_date = date('Ymd',$current_time);

	$continue_checkin_number = wp_cache_get($weixin_openid, 'continue_checkin_number_'.$current_date);

	if($continue_checkin_number === false){
		$weixin_checkin_table = weixin_robot_checkin_table();

		$continue_checkin_number = $wpdb->get_var($wpdb->prepare("SELECT continue_number FROM {$weixin_checkin_table} WHERE weixin_openid=%s LIMIT 1",$weixin_openid));

		if($continue_checkin_number == false){
			$continue_checkin_number = 1;
			$total_checkin_number = wpjam_weixin_get_total_checkin_number($weixin_openid);
			$wpdb->insert($weixin_checkin_table, array('weixin_openid'=>$weixin_openid,'continue_number'=>$continue_checkin_number,'total_number'=>$total_checkin_number));
		}

		wp_cache_set($weixin_openid, $continue_checkin_number, 'continue_checkin_number_'.$current_date, 60*60*24);
	}

	return $continue_checkin_number;	
}

function wpjam_weixin_set_continue_checkin_number($weixin_openid){
	global $wpdb;

	$weixin_checkin_table = weixin_robot_checkin_table();
	$weixin_credits_table = weixin_robot_credits_table();

	$current_time = current_time('timestamp');
	$yesterday_time = $current_time - 86400;

	$yesterday_checkin_time = $wpdb->get_var($wpdb->prepare("SELECT SQL_NO_CACHE `time` FROM {$weixin_credits_table} WHERE `type`='checkin' AND weixin_openid=%s AND ( ( YEAR( time ) = %d AND MONTH( time ) = %d AND DAYOFMONTH( time ) = %d ) )  ORDER BY id DESC LIMIT 1", $weixin_openid, date('Y',$yesterday_time), date('m',$yesterday_time), date('d',$yesterday_time) ));

	$continue_checkin_number = wpjam_weixin_get_continue_checkin_number($weixin_openid);
	if($yesterday_checkin_time){
		$continue_checkin_number = $continue_checkin_number + 1;
	}else{
		$continue_checkin_number = 1;
	}
	
	$total_checkin_number = wpjam_weixin_get_total_checkin_number($weixin_openid);

	$wpdb->update($weixin_checkin_table,array('weixin_openid'=>$weixin_openid,'continue_number'=>$continue_checkin_number,'total_number'=>$total_checkin_number),array('weixin_openid'=>$weixin_openid));


	$current_date = date('Ymd',$current_time);
	wp_cache_delete($weixin_openid,'continue_checkin_number_'.$current_date);

	return $continue_checkin_number;
}

function wpjam_weixin_get_checkin_order($weixin_openid){

	$current_time = current_time('timestamp');
	$current_date = date('Ymd',$current_time);

	$checkin_order = wp_cache_get($weixin_openid, 'checkin_order_'.$current_date);
	if($checkin_order == false){
		global $wpdb;
		$weixin_credits_table = weixin_robot_credits_table();		
		
		$first_checkin_time = $wpdb->get_var($wpdb->prepare("SELECT SQL_NO_CACHE `time` FROM {$weixin_credits_table} WHERE `type`='checkin' AND weixin_openid=%s AND ( ( YEAR( time ) = %d AND MONTH( time ) = %d AND DAYOFMONTH( time ) = %d ) ) ORDER BY id ASC LIMIT 1",$weixin_openid, date('Y',$current_time), date('m',$current_time), date('d',$current_time)));

		$checkin_order = $wpdb->get_var($wpdb->prepare("SELECT count(*)+1 FROM {$weixin_credits_table} WHERE `type`='checkin' AND `time` < %s AND ( ( YEAR( time ) = %d AND MONTH( time ) = %d AND DAYOFMONTH( time ) = %d ) ) ORDER BY id ASC", $first_checkin_time, date('Y',$current_time), date('m',$current_time), date('d',$current_time)));

		wp_cache_set($weixin_openid,$checkin_order,'checkin_order_'.$current_date, 60*60*24);
	}

	return $checkin_order;
}

function wpjam_weixin_get_checkin_rank($weixin_openid){
	$checkin_rank = wp_cache_get($weixin_openid, 'checkin_rank');
	if($checkin_rank == false){
		global $wpdb;
		$weixin_checkin_table = weixin_robot_checkin_table();

		$total_checkin_count = $wpdb->get_var("SELECT count(*) FROM {$weixin_checkin_table}");
		$total_checkin_count = $total_checkin_count*2;

		$total_checkin_number = wpjam_weixin_get_total_checkin_number($weixin_openid);

		$checkin_rank = $wpdb->get_var($wpdb->prepare("SELECT count(*)+1 as rank FROM {$weixin_checkin_table} WHERE `total_number` > %s ORDER BY total_number DESC",$total_checkin_number));

		$checkin_rank = ($total_checkin_count-$checkin_rank)/$total_checkin_count;
		$checkin_rank = round($checkin_rank, 4)*100;

		wp_cache_set($weixin_openid,$checkin_rank,'checkin_rank', 60);
	}

	return $checkin_rank;

}

function wpjam_weixin_top_checkin_users_reply(){
	global $wechatObj;
	$results = wpjam_get_top_checkin_users();

	echo sprintf($wechatObj->get_textTpl(), $results);
	$wechatObj->set_response('top-checkin');
}

function wpjam_get_top_checkin_users(){
	$top_checkin_users = get_transient('weixin_top_checkin_users'); //wp_cache_get('top_checkin_users', 'weixin_robot');
	if($top_checkin_users == false){
		global $wpdb;
		$weixin_checkin_table = weixin_robot_checkin_table();
		$weixin_users_table = weixin_robot_users_table();

		$top_checkin_users = $wpdb->get_results("SELECT weixin_openid,total_number,nickname,name FROM {$weixin_checkin_table} wxc INNER JOIN {$weixin_users_table} wxu ON (wxc.weixin_openid=wxu.openid) WHERE (wxu.nickname !='' OR wxu.name !='') ORDER BY total_number DESC LIMIT 0,20",OBJECT_K);

		set_transient('weixin_top_checkin_users',$top_checkin_users,'weixin_robot',3600);
	}

	$results = '签到排行榜'."\n";
	$i = 1;
	foreach ($top_checkin_users as $weixin_openid=>$checkin_user) {
		$weixin_user = weixin_robot_get_user($weixin_openid);
		$results .= $i.' ';
		if($checkin_user->nickname){
			$results .= $checkin_user->nickname;
		}elseif($checkin_user->name){
			$results .= $checkin_user->name;
		}
		$results .= ' '.$checkin_user->total_number."\n";
		$i++;
	}
	return $results;
}

function wpjam_weixin_top_credit_users_reply(){
	global $wechatObj;

	$results = wpjam_weixin_get_top_credit_users();

	echo sprintf($wechatObj->get_textTpl(), $results);
	$wechatObj->set_response('top-credits');
}

function wpjam_weixin_get_top_credit_users(){
	$top_credit_users = get_transient('weixin_top_credit_users');
	if($top_credit_users == false){
		global $wpdb;
		$weixin_credits_table = weixin_robot_credits_table();
		$weixin_users_table = weixin_robot_users_table();


		$top_credit_users = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS wut.*, wct.credit FROM  $weixin_users_table wut LEFT JOIN $weixin_credits_table wct ON wut.openid = wct.weixin_openid WHERE  subscribe = '1' AND wct.id in (SELECT MAX( id ) FROM $weixin_credits_table  WHERE name!='' OR nickname!='' GROUP BY weixin_openid) ORDER BY wct.credit desc limit 0,20 ");

		set_transient('weixin_top_credit_users',$top_credit_users,3600);
	}

	$results = '积分排行榜'."\n";
	$i = 1;
	foreach ($top_credit_users as $top_credit_user) {
		$results .= $i.' ';
		if($top_credit_user->nickname){
			$results .= $top_credit_user->nickname;
		}elseif($top_credit_user->name){
			$results .= $top_credit_user->name;
		}
		$results .= ' '.$top_credit_user->credit."\n";
		$i++;
	}
	return $results;
}

add_action('weixin_checkin','wpjam_weixin_checkin');
function wpjam_weixin_checkin($credit_change){
	if($credit_change){
		delete_transient('weixin_top_checkin_users');
	}
}

add_action('weixin_credit','wpjam_weixin_credit');
function wpjam_weixin_credit($args){
	delete_transient('weixin_top_credit_users');
}

