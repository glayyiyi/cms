<?php 

//后台菜单
add_action( 'admin_menu', 'weixin_robot_admin_menu' );
function weixin_robot_admin_menu() {
	//add_menu_page('微信机器人', '微信机器人',	'manage_options',	'weixin-robot',	'weixin_robot_basic_page',	WEIXIN_ROBOT_PLUGIN_URL.'/static/weixin-16.ico');
	add_menu_page('微信机器人', '微信机器人',	'edit_product',	'weixin-robot',	'weixin_robot_basic_page');

	weixin_robot_add_submenu_page('basic', '设置', 'weixin-robot');

	if(wpjam_net_check_domain()){
		weixin_robot_add_submenu_page('advanced', '高级回复');
		weixin_robot_add_submenu_page('custom-reply', '自定义回复');
	
		$weixin_robot_basic = weixin_robot_get_option('weixin-robot-basic');

		if(($weixin_robot_basic['weixin_app_id'] && $weixin_robot_basic['weixin_app_secret'])||($weixin_robot_basic['yixin_app_id'] && $weixin_robot_basic['yixin_app_secret'])) {
			weixin_robot_add_submenu_page('custom-menu', '自定义菜单');
		}
		if(empty($weixin_robot_basic['weixin_disable_stats'])){
			weixin_robot_add_submenu_page('stats2', '微信统计分析');
			//weixin_robot_add_submenu_page('summary', '微信回复统计分析');
			weixin_robot_add_submenu_page('messages', '微信最新消息');
		}
		do_action('weixin_admin_menu');
		weixin_robot_add_submenu_page('tables','数据表检测');
	}

	//weixin_robot_add_submenu_page('about', '关于和更新');
}

function weixin_robot_add_submenu_page($key, $title, $slug='', $cap='edit_product'){
	if(!$slug) $slug = 'weixin-robot-'.$key;
	add_submenu_page( 'weixin-robot', $title.' &lsaquo; 微信机器人', $title, $cap, $slug, 'weixin_robot_'.str_replace('-', '_', $key).'_page');
}

add_action('wpjam_net_item_ids','weixin_robot_wpjam_net_item_id');
function weixin_robot_wpjam_net_item_id($item_ids){
	$item_ids['56'] = WEIXIN_ROBOT_PLUGIN_FILE;
	return $item_ids;
}

add_action('admin_head','weixin_robot_admin_head');
function weixin_robot_admin_head(){
?>
<style type="text/css">.icon16.icon-settings:before, #adminmenu .toplevel_page_weixin-robot div.wp-menu-image:before{content: "\f125";}</style>
<?php
}

add_action( 'admin_init', 'weixin_robot_admin_init' );
function weixin_robot_admin_init() {
	wpjam_add_settings(weixin_robot_get_basic_option_labels(),	weixin_robot_get_default_basic_option());
	wpjam_add_settings(weixin_robot_get_advanced_option_labels(),weixin_robot_get_default_advanced_option());
}

function weixin_robot_basic_page() {
	if(wpjam_net_check_domain()){
		$labels = weixin_robot_get_basic_option_labels();
		wpjam_option_page($labels, $title='设置', $type='tab', $icon='weixin-robot');
	}else{
		?>
		<div class="wrap">
			<div id="icon-weixin-robot" class="icon32"><br></div><h2>微信机器人</h2>
			<p>你还没有授权域名，点击这里：<a href="http://wpjam.net/wp-admin/admin.php?page=orders&domain_limit=1&product_id=56" class="button">授权域名</a></p>
		</div>
		<?php
	}
}

function weixin_robot_advanced_page() {
	$labels = weixin_robot_get_advanced_option_labels();
	wpjam_option_page($labels, $title='高级回复', $type='default', $icon='weixin-robot');
}

/* 基本设置的字段 */

function weixin_robot_get_basic_option_labels(){
	$option_group               =   'weixin-robot-basic-group';
    $option_name = $option_page =   'weixin-robot-basic';
    $field_validate				=	'weixin_robot_basic_validate';

    $basic_section_fields = array(
		'weixin_token'					=> array('title'=>'微信 Token',		'type'=>'text'),
		'weixin_default'				=> array('title'=>'默认缩略图',		'type'=>'text'),
		'weixin_keyword_allow_length'	=> array('title'=>'搜索关键字最大长度','type'=>'text',		'description'=>'一个汉字算两个字节，一个英文单词算两个字节，空格不算，搜索多个关键字可以用空格分开！'),
		'weixin_count'					=> array('title'=>'返回结果最大条数',	'type'=>'text',		'description'=>'微信接口最多支持返回10个。'), 
		'weixin_disable_stats'			=> array('title'=>'屏蔽统计',			'type'=>'checkbox',	'description'=>'屏蔽统计之后，就无法统计用户发的信息和系统的回复。'), 
    );

    $default_reply_section_fields = array(
    	'weixin_welcome'				=> array('title'=>'用户关注默认回复',	'type'=>'textarea', 'rows'=>7),
		'weixin_keyword_too_long'		=> array('title'=>'超过最大长度回复',	'type'=>'textarea',	'rows'=>5,	'description'=>'设置超过最大长度提示语，留空则不回复！'),
		'weixin_not_found'				=> array('title'=>'搜索结果为空回复',	'type'=>'textarea', 'rows'=>5,	'description'=>'可以使用 [keyword] 代替相关的搜索关键字，留空则不回复！'),
    	'weixin_default_voice'			=> array('title'=>'语音默认回复',		'type'=>'textarea', 'rows'=>5,	'description'=>'设置语言的默认回复文本，留空则不回复！'),
    	'weixin_default_location'		=> array('title'=>'位置默认回复',		'type'=>'textarea', 'rows'=>5,	'description'=>'设置位置的默认回复文本，留空则不回复！'),
    	'weixin_default_image'			=> array('title'=>'图片默认回复',		'type'=>'textarea', 'rows'=>5,	'description'=>'设置图片的默认回复文本，留空则不回复！'),
    	'weixin_default_link'			=> array('title'=>'链接默认回复',		'type'=>'textarea', 'rows'=>5,	'description'=>'设置链接的默认回复文本，留空则不回复！'),
    );

    $app_section_fields = array(
		'weixin_app_id'					=> array('title'=>'微信AppID',		'type'=>'text',		'description'=>'设置自定义菜单的所需的 AppID，如果没申请，可不填！'),
		'weixin_app_secret'				=> array('title'=>'微信APPSecret',	'type'=>'text',		'description'=>'设置自定义菜单的所需的 APPSecret，如果没申请，可不填！'),
		'weixin_advanced_api'			=> array('title'=>'开启微信高级接口',	'type'=>'checkbox',	'description'=>'如果你申请了服务号的高级接口，才开启该功能，否则会出错'),
		'weixin_enter'					=> array('title'=>'进入公众号默认回复','type'=>'textarea', 'rows'=>7,	'description'=>'用户进入微信公众号之后的默认回复，一天内只回复一次（你可以通过 <code>weixin_enter_time</code> 这个 filter 来更改时长）。<br />这个功能只有开通了高级接口的服务号才能使用，并且在用户确认允许公众号使用其地理位置才可使用。'),
    );

    $credit_section_fields = array(
		'weixin_credit'					=> array('title'=>'开启微信积分系统',	'type'=>'checkbox',	'description'=>'开启积分系统，用户既可以签到和分享文章来获取积分'),
		'weixin_day_credit_limit'		=> array('title'=>'每日积分上限',		'type'=>'text',		'description'=>'设置每日积分上限，防止用户刷分。'),
		'weixin_checkin_credit'			=> array('title'=>'签到积分',			'type'=>'text',		'description'=>'用户点击签到菜单，或者发送签单之后获取的积分。'),
		'weixin_SendAppMessage_credit'	=> array('title'=>'发送给好友积分',	'type'=>'text',		'description'=>'用户每次发送文章给好友所能获取的积分，每篇文章只能获取一次。'),
		'weixin_ShareTimeline_credit'	=> array('title'=>'分享到朋友圈积分',	'type'=>'text',		'description'=>'用户每次分享文章到朋友圈所能获取的积分，每篇文章只能获取一次。'),
		'weixin_ShareWeibo_credit'		=> array('title'=>'分享到腾讯微博积分','type'=>'text',		'description'=>'用户每次分享文章到腾讯微博所能获取的积分，每篇文章只能获取一次。'),
    );

    $sections = array(
    	'basic'			=> array('title'=>'基本设置',		'fields'=>$basic_section_fields,			'callback'=>'weixin_robot_basic_section_callback' ),
    	'default_reply'	=> array('title'=>'默认回复',		'fields'=>$default_reply_section_fields,	'callback'=>''),
    	'app'			=> array('title'=>'接口设置',		'fields'=>$app_section_fields,				'callback'=>''),
    	'credit'		=> array('title'=>'积分设置',		'fields'=>$credit_section_fields,			'callback'=>'')
	);

	$sections = apply_filters('weixin_setting',$sections);

	return compact('option_group','option_name','option_page','sections','field_validate');
}

function weixin_robot_get_default_basic_option(){
	$default_options = array(
		'weixin_token'					=> 'weixin',
		'weixin_default'				=> '',
		'weixin_keyword_allow_length'	=> '16',
		'weixin_count'					=> '5',
		'weixin_disable_stats'			=> '0',
		'weixin_welcome'				=> "输入 n 返回最新日志！\n输入 r 返回随机日志！\n输入 t 返回最热日志！\n输入 c 返回最多评论日志！\n输入 t7 返回一周内最热日志！\n输入 c7 返回一周内最多评论日志！\n输入 h 获取帮助信息！",
		'weixin_keyword_too_long'		=> '你输入的关键字太长了，系统没法处理了，请等待公众账号管理员到微信后台回复你吧。',
		'weixin_not_found'				=> '抱歉，没有找到与[keyword]相关的文章，要不你更换一下关键字，可能就有结果了哦 :-)',
		'weixin_default_voice'			=> "系统暂时还不支持语音回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。",
		'weixin_default_location'		=> "系统暂时还不支持位置回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。",
		'weixin_default_image'			=> "系统暂时还不支持图片回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。",
		'weixin_default_link'			=> "已经收到你分享的信息，感谢分享。\n获取更多帮助信息请输入：h。",
		'weixin_advanced_api'			=> '0',
		'weixin_enter'					=> "输入 n 返回最新日志！\n输入 r 返回随机日志！\n输入 t 返回最热日志！\n输入 c 返回最多评论日志！\n输入 t7 返回一周内最热日志！\n输入 c7 返回一周内最多评论日志！\n输入 h 获取帮助信息！",
		'weixin_credit'					=> 1,
		'weixin_day_credit_limit'		=> 100,
		'weixin_checkin_credit'			=> 10,
		'weixin_SendAppMessage_credit'	=> 5,
		'weixin_ShareTimeline_credit'	=> 10,
		'weixin_ShareWeibo_credit'		=> 5,
		
	);
	return apply_filters('weixin_default_option',$default_options,'weixin-robot-basic');
}

function weixin_robot_basic_section_callback(){
	echo '<p style="font-weight:bold;">友情提示：查看<a href="http://blog.wpjam.com/m/weixin-robot-advanced-faq/">微信机器人高级版常见问题汇总</a>可以解决你使用当中碰到的绝大多数问题。</p>';
}

function weixin_robot_basic_validate( $weixin_robot_basic ) {
	$current = get_option( 'weixin-robot-basic' );

	if ( !is_numeric( $weixin_robot_basic['weixin_keyword_allow_length'] ) ){
		$weixin_robot_basic['weixin_keyword_allow_length'] = $current['weixin_keyword_allow_length'];
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '搜索关键字最大长度必须为数字。' );
	}

	if ( !is_numeric( $weixin_robot_basic['weixin_count'] ) ){
		$weixin_robot_basic['weixin_count'] = $current['weixin_count'];
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '返回结果最大条数必须为数字。' );
	}elseif($weixin_robot_basic['weixin_count'] > 10){
		$weixin_robot_basic['weixin_count'] = 10;
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '返回结果最大条数不能超过10。' );
	}

	if(empty($weixin_robot_basic['weixin_disable_stats'])){ //checkbox 未选，Post 的时候 $_POST 中是没有的，
		$weixin_robot_basic['weixin_disable_stats'] = 0;
	}

	if(empty($weixin_robot_basic['weixin_credit'])){ //checkbox 未选，Post 的时候 $_POST 中是没有的，
		$weixin_robot_basic['weixin_credit'] = 0;
	}

	if(empty($weixin_robot_basic['weixin_advanced_api'])){ //checkbox 未选，Post 的时候 $_POST 中是没有的，
		$weixin_robot_basic['weixin_advanced_api'] = 0;
	}

	return $weixin_robot_basic;
}

/* 高级回复的字段 */
function weixin_robot_get_advanced_option_labels(){
	$option_group               =   'weixin-robot-advanced-group';
	$option_name = $option_page =   'weixin-robot-advanced';
	$field_validate				=	'';

    $advanced_section_fields = array(
		'new'			=>array('title'=>'返回最新日志关键字',			'type'=>'text'),
		'rand'			=>array('title'=>'返回随机日志关键字',			'type'=>'text'),
		'hot'			=>array('title'=>'返回浏览最高日志关键字',		'type'=>'text',	'description'=>'博客必须首先安装 Postview 插件！'),
		'comment'		=>array('title'=>'返回留言最高日志关键字',		'type'=>'text'),
		'hot-7'			=>array('title'=>'返回7天内浏览最高日志关键字',	'type'=>'text',	'description'=>'博客必须首先安装 Postview 插件！'),
		'comment-7'		=>array('title'=>'返回7天内留言最高日志关键字',	'type'=>'text'),
		
	);

	$advanced_section_fields = apply_filters('weixin_advanced_fields',$advanced_section_fields);

	$sections = array( 
    	'advanced'=>array('title'=>'',	'callback'=>'weixin_robot_advanced_section_callback',	'fields'=>$advanced_section_fields)
	);

	return compact('option_group','option_name','option_page','sections','field_validate');
}

function weixin_robot_get_default_advanced_option(){
 	$default_options = array(
		'new'		=> 'n',
		'rand'		=> 'r', 
		'hot'		=> 't',
		'comment'	=> 'c',
		'hot-7'		=> 't7',
		'comment-7'	=> 'c7',
		'hot-30'	=> 't30',
		'comment-30'=> 'c30'
	);
	return apply_filters('weixin_default_option',$default_options,'weixin-robot-advanced');
}

function weixin_robot_advanced_section_callback(){
	echo '<p style="color:red; font-weight:bold;">修改下面的关键字，请主要修改下基本设置中欢迎语中对应的关键字。</p>';
}

function weixin_robot_tables_page() {
	$weixin_tables = array(
		'自定义回复'		=> 'weixin_robot_custom_replies_create_table',
		'微信用户'		=> 'weixin_robot_users_create_table',
	);

	if(weixin_robot_get_setting('weixin_credit')){
		$weixin_tables['微信用户积分'] = 'weixin_robot_credits_create_table';
	}

	if(weixin_robot_get_setting('weixin_disable_stats')==false){
		$weixin_tables['微信消息'] = 'weixin_robot_messages_create_table';
	}

	$weixin_tables = apply_filters('weixin_tables',$weixin_tables);
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon-users icon32"><br></div>
		<h2>数据表检测</h2>
		<p>点击该页面会自动创建或者检测微信机器人所需的数据库表，所以建议每次升级或者安装附加组件之后请点击该页面：</p>
		<ol>
		<?php foreach ($weixin_tables as $name => $function) {
			call_user_func($function);
			echo '<li><strong>'.$name.'</strong>表已经创建</li>';
		}
		?>
		</ol>
	</div>
	<?php
}
