<?php
function weixin_user_page() {
	?>
<h2>用户匹配列表</h2>
<?php
	
	// 创建微信自定义菜单
	
$request = new WP_Http;
	$tkn_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx6b92ad6cb719bc58&secret=879100569de7c990773208e1177b0a15';
	$result = $request->request ( $tkn_url );
	
	$json = $result ['body'];
	
	$json_arr = json_decode ( $json, true );
	$access_token = $json_arr ['access_token'];
	 //print_r($access_token);
	if ($access_token) {
	echo '开始创建菜单... ';
	 $body = '{
	"button":[
	{
	  "name":"智能玩品",
           "sub_button":[
	 {
	 "type":"view",
	 "name":"全部",
	 "url":"http://www.appcn100.com/cms/商品类别/智能玩品/"
	 },
	 {
	 "type":"view",
	 "name":"智能电源",
	 "url":"http://www.appcn100.com/cms/product/belkinwemo-智能远程电源控制器/"
	 }]
	},
	{
	  "name":"精彩专题",
            "sub_button":[
	 {
	 "type":"view",
	 "name":"全部",
	 "url":"http://www.appcn100.com/cms/商品类别/精彩专题/"
	 },
	 {
	 "type":"view",
	 "name":"惬意生活",
	 "url":"http://www.appcn100.com/cms/product/惬意生活,可以投影的智能闹钟/"
	 }]
	},
	{
	  "name":"我",
            "sub_button":[
	 {
	 "type":"view",
	 "name":"客服",
	 "url":"http://www.appcn100.com/cms/service/"
	 },
	 {
	 "type":"view",
	 "name":"我的订单",
	 "url":"http://www.appcn100.com/cms/my-account/view-order/"
	 },
	{
	 "type":"view",
	 "name":"我的资料",
	 "url":"http://www.appcn100.com/cms/my-account/"
	 }
	]
	}
	]
	 }';
	 $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
	 $result = $request->request ( $url, array (
	 'method' => 'POST',
	 'body' => $body
	 ) );
	 echo '创建菜单结束';
	 print_r ( $result ['body'] );
	
	 }
	
	$blogusers = get_users ();
	
	// $blogusers = get_users('blog_id=1&orderby=nicename&role=subscriber');
	global $weAccount;
	if (! isset ( $weAccount )) {
		$weAccount = initNewWeAccount ();
	}
	foreach ( $blogusers as $user ) {
		
		echo '<li>登陆名:' . $user->user_login;
		if ($user->user_url) {
			echo '／' . $user->display_name . str_replace ( 'http://', '', $user->user_url ) . '</li>';
		} else {
			$userInfo = $weAccount->getAndUpdateUserInfoWithMatchedFakeId ( $user->user_login );
			if (! $userInfo)
				echo '／==================================暂无匹配</li>';
			else
				echo '／' . $userInfo ['NickName'] . $userInfo ['FakeId'] . '</li>';
		}
	}
	
	// $userlist = $weAccount->account_weixin_userlistInfo ( 0, 1000 );
	// var_dump($userlist);
}
function weixin_robot_about_page() {
	?>
<div class="wrap" style="width: 600px;">
	<div id="icon-weiservice" class="icon32">
		<br>
	</div>
	<h2>帮助中心</h2>
	<div class="form">
		<h3>1.开通微信公众平台</h3>
		<ul>
			<li>点击“<a target="_blank" href="http://mp.weixin.qq.com/">微信公众平台</a>”,或直接输入网址
				http://mp.weixin.qq.com/ 访问。
			</li>
			<li>“微信公众平台”是使用“QQ号”作为登录帐号的，您可以使用已有的帐号或是重新注册一个新的QQ号来登录平台。</li>
			<li>登录成功后，“输入账号基本信息”点击“确定”开通“微信公众平台”。</li>
			<li>至此，“微信公众平台”已经开通.</li>

		</ul>

	</div>
	<div class="form">
		<h3>2.绑定微信公众帐号</h3>
		<ul>
			<li>开通“微信公众平台”仅仅只是一个开始，您还需要做一些简单的设置，才能和本平台完美接入。</li>
			<li>点击“<strong>高级功能</strong>”前往设置接入参数。打开页面后，点击下方的“<strong>成为开发者</strong>”填写接入参数，如图：
				<p>
					<img
						src="<?php echo WP_CONTENT_URL?>/plugins/weiservice/images/help_wx_bind_1.jpg"
						width="500" />
				</p>
				<p>
					<img
						src="<?php echo WP_CONTENT_URL?>/plugins/weiservice/images/help_wx_bind_3.jpg"
						width="500" />
				</p>
			</li>
			<li>填写您的一些基本情况，其“接口配置信息”URL、Token可以在“基本设置”中查看，注意：Token必须两边保持一致。</li>
			<li>提交后，“微信公众平台”会提示你已成为开发者！
				<p>
					<img
						src="<?php echo WP_CONTENT_URL?>/plugins/weiservice/images/help_wx_bind_2.jpg"
						width="500" />
				</p>
			</li>
			<li>至此，已经完成绑定操作。</li>
		</ul>

	</div>
	<div class="form">
		<h3>3.什么是微信Token？</h3>
		<ul>
			<li>是沟通“微信公众平”与本系统之间的标识、密码。Token仅由您个人知道，并设置在“微信公众平台”中，保持与本系统中设置的一致。</li>
		</ul>
		<h3 class="mt20">设置微信Token</h3>
		<ul>
			<li>设置微信Token</li>
			<li>通常情况下您不需要更改此值。如果需要更改，请设置3-30之间的字母、数字字符。并保证与“微信公众平台”中的一致。</li>
		</ul>
	</div>
	<div class="form">
		<h3>4.什么是微信原始帐号？</h3>
		<ul>
			<li>是类似于 gh_x3122523x83x 此类字母串的形式。</li>
			<li>是“微信公众平台”系统给您的原始的帐号ID，此微信号可以在“设置”-> “帐号信息”中更改。但本系统需要的是<strong>原始帐号ID</strong></li>
		</ul>
		<h3 class="mt20">查看自己的微信原始帐号</h3>
		<ul>
			<li>设置微信原始帐号</li>
			<li>如果您未重新设置过“微信公众平台”的微信号时，可以通过“设置”-> “帐号信息”中查看到该帐号。如图：
				<p>
					<img
						src="<?php echo WP_CONTENT_URL?>/plugins/weiservice/images/help_wx_uid_1.jpg"
						width="500" />
				</p>
			</li>
			<li>如果您已经设置过。可以通过以下方式查看，如图：
				<p>
					<img
						src="<?php echo WP_CONTENT_URL?>/plugins/weiservice/images/help_wx_uid_2.jpg"
						width="500" />
				</p>
			</li>
			<li>保存图片到电脑上后，查看文件名中的帐号信息，如图：
				<p>
					<img
						src="<?php echo WP_CONTENT_URL?>/plugins/weiservice/images/help_wx_uid_3.jpg" />
				</p>
			</li>
		</ul>
	</div>

</div>
<?php
}
function weixin_robot_basic_setting_page() {
	?>
<div class="wrap">
	<div id="icon-weiservice" class="icon32">
		<br>
	</div>
	<h2>基本设置</h2>
	<form action="options.php" method="POST">
			<?php settings_fields( 'weiservice-basic-group' ); ?>
			<?php do_settings_sections( 'weiservice-basic' ); ?>
			<?php submit_button(); ?>
		</form>
</div>
<?php
}
function weixin_robot_advanced_setting_page() {
	?>
<div class="wrap">
	<div id="icon-weiservice" class="icon32">
		<br>
	</div>
	<h2>高级设置</h2>
	<form action="options.php" method="POST">
			<?php settings_fields( 'weiservice-advanced-group' ); ?>
			<?php do_settings_sections( 'weiservice-advanced' ); ?>
			<?php submit_button(); ?>
		</form>
</div>
<?php
}
function weixin_robot_get_default_basic_option() {
	return array (
			'weixin_api_url' => get_option ( 'siteurl' ) . '/?weixin-api',
			'weixin_token' => 'weixin',
			'weixin_default' => '',
			'weixin_default_image'=>'',
			'weixin_crm_shop_id' => '16',
			'weixin_count' => '5' 
	// 'weixin_welcome' => "请输入关键字开始搜索！",
	// 'weixin_keyword_too_long' => '你输入的关键字太长了，系统没法处理了，请等待公众账号管理员到微信后台回复你吧。',
	// 'weixin_not_found' => '抱歉，没有找到与[keyword]相关的文章，要不你更换一下关键字，可能就有结果了哦 :-)',
	// 'weixin_voice' => '系统暂时还不支持语音回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。'
		);
}
function weixin_robot_get_basic_option() {
	$weixin_robot_basic = get_option ( 'weiservice-basic' );
	
	if (! $weixin_robot_basic) {
		$defaults = weixin_robot_get_default_basic_option ();
		return wp_parse_args ( $weixin_robot_basic, $defaults );
	} else {
		return $weixin_robot_basic;
	}
}

add_action ( 'admin_init', 'weixin_robot_admin_init' );
function weixin_robot_admin_init() {
	
	/* start 基本设置 */
	register_setting ( 'weiservice-basic-group', 'weiservice-basic', 'weixin_robot_basic_validate' );
	add_settings_section ( 'weiservice-basic-section', '', '', 'weiservice-basic' );
	
	$weixin_robot_basic_settings_fields = array (
			
			array (
					'name' => 'weixin_account',
					'title' => '微信公众登录用户',
					'type' => 'text' 
			),
			array (
					'name' => 'weixin_password',
					'title' => '微信公众登录密码',
					'type' => 'text' 
			),
			array (
					'name' => 'weixin_api_url',
					'title' => '接口地址',
					'type' => 'text',
					'description' => '设置“微信公众平台接口”配置信息中的接口地址,系统自动生成，请不要修改' 
			),
			array (
					'name' => 'weixin_token',
					'title' => '微信 Token',
					'type' => 'text',
					'description' => '与微信公众平台接入设置值一致，必须为英文或者数字，长度为3到32个字符. 请妥善保管' 
			),
			array (
					'name' => 'weixin_original_account',
					'title' => '原始帐号',
					'type' => 'text',
					'description' => '微信公众帐号的原ID串，具体设置可参考微服务帮助' 
			),
			array (
					'name' => 'weixin_default_image',
					'title' => '微信图文消息默认图片',
					'type' => 'text',
					'description' => '微信图文消息默认图片地址'
			),

			array (
					'name' => 'weixin_default',
					'title' => 'CRM服务地址',
					'type' => 'text' 
			),
			array (
					'name' => 'weixin_crm_shop_id',
					'title' => '分店号',
					'type' => 'text',
					'description' => '用于多店模式下，分店的标示号' 
			),
			array (
					'name' => 'weixin_count',
					'title' => '返回结果最大条数',
					'type' => 'text',
					'description' => '微信接口最多支持返回10个' 
			) 
	// ,
	// array (
	// 'name' => 'weixin_welcome',
	// 'title' => '欢迎语',
	// 'type' => 'textarea'
	// ),
	// array (
	// 'name' => 'weixin_keyword_too_long',
	// 'title' => '超过最大长度提示语',
	// 'type' => 'textarea',
	// 'description' => '设置超过最大长度提示语，留空则不回复！'
	// ),
	// array (
	// 'name' => 'weixin_not_found',
	// 'title' => '搜索结果为空提示语',
	// 'type' => 'textarea',
	// 'description' => '可以使用 [keyword] 代替相关的搜索关键字，留空则不回复！'
	// ),
	// array (
	// 'name' => 'weixin_voice',
	// 'title' => '语音回复',
	// 'type' => 'textarea',
	// 'description' => '设置语言的默认回复文本，留空则不回复！'
	// )
		);
	
	foreach ( $weixin_robot_basic_settings_fields as $field ) {
		add_settings_field ( $field ['name'], $field ['title'], 'weixin_robot_basic_settings_field_callback', 'weiservice-basic', 'weiservice-basic-section', $field );
	}
	/* end of 基本设置 */
}
function weixin_robot_basic_validate($weixin_robot_basic) {
	$current = get_option ( 'weiservice-basic' );
	
	if (! is_numeric ( $weixin_robot_basic ['weixin_crm_shop_id'] )) {
		$weixin_robot_basic ['weixin_crm_shop_id'] = $current ['weixin_crm_shop_id'];
		add_settings_error ( 'weiservice-basic', 'invalid-int', '必须为数字。' );
	}
	if (! is_numeric ( $weixin_robot_basic ['weixin_count'] )) {
		$weixin_robot_basic ['weixin_count'] = $current ['weixin_count'];
		add_settings_error ( 'weiservice-basic', 'invalid-int', '返回结果最大条数必须为数字。' );
	} elseif ($weixin_robot_basic ['weixin_count'] > 10) {
		$weixin_robot_basic ['weixin_count'] = 10;
		add_settings_error ( 'weiservice-basic', 'invalid-int', '返回结果最大条数不能超过10。' );
	}
	
	return $weixin_robot_basic;
}
function weixin_robot_basic_settings_field_callback($args) {
	$weixin_robot_basic = weixin_robot_get_basic_option ();
	$value = $weixin_robot_basic [$args ['name']];
	
	if ($args ['type'] == 'text') {
		echo '<input type="text" name="weiservice-basic[' . $args ['name'] . ']" value="' . $value . '" class="regular-text" />';
	} elseif ($args ['type'] == 'textarea') {
		echo '<textarea name="weiservice-basic[' . $args ['name'] . ']" rows="6" cols="50" class="regular-text code">' . $value . '</textarea>';
	}
	if (isset ( $args ['description'] ))
		echo '<p class="description">' . $args ['description'] . '</p>';
}

add_filter ( 'weixin_account', 'wpjam_basic_filter' );
add_filter ( 'weixin_password', 'wpjam_basic_filter' );
add_filter ( 'weixin_api_url', 'wpjam_basic_filter' );
add_filter ( 'weixin_original_account', 'wpjam_basic_filter' );
add_filter ( 'weixin_token', 'wpjam_basic_filter' );
add_filter ( 'weixin_default', 'wpjam_basic_filter' );
add_filter('weixin_default_image','wpjam_basic_filter');
// add_filter ( 'weixin_welcome', 'wpjam_basic_filter' );
// add_filter ( 'weixin_voice', 'wpjam_basic_filter' );
add_filter ( 'weixin_crm_shop_id', 'wpjam_basic_filter' );
// add_filter ( 'weixin_keyword_too_long', 'wpjam_basic_filter' );
add_filter ( 'weixin_count', 'wpjam_basic_filter' );
// add_filter ( 'weixin_not_found', 'wpjam_basic_filter' );
function wpjam_basic_filter($original) {
	$weixin_robot_basic = weixin_robot_get_basic_option ();
	
	global $wp_current_filter;
	
	// 最后一个才是当前的 filter
	$wpjam_current_filter = $wp_current_filter [count ( $wp_current_filter ) - 1];
	
	if (isset ( $weixin_robot_basic [$wpjam_current_filter] )) {
		if ($weixin_robot_basic [$wpjam_current_filter]) {
			return $weixin_robot_basic [$wpjam_current_filter];
		}
	} else {
		return $original;
	}
}
