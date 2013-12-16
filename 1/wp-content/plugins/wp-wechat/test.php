<?php
//echo '开始测试';
if( !class_exists( 'WP_Http' ) )
	include_once( ABSPATH . WPINC. '/class-http.php' );


echo '开始创建菜单＝＝＝';
// $body = array(
// 'nick' => 'ozh',
// 'mood' => 'happy'
// );
$request = new WP_Http;
echo '开始请求＝＝＝';
$tkn_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx32a93cdf1b70a748&secret=2a02d2f9b545401aaac3fcbbda3a9279';
$result = $request->request ( $tkn_url );
echo '完成请求＝＝＝';
//$json = $result ['body'];
var_dump( $result );
$json_arr = json_decode ( $result, true );
$access_token = $json_arr ['access_token'];

if ($access_token) {
	$body = '{
	"button":[
	{
		"type":"click",
		"name":"今日歌曲",
		"key":"V1001_TODAY_MUSIC"
	},
	{
		"type":"view",
		"name":"歌手简介",
		"url":"http://www.qq.com/"
	},
	{
		"name":"菜单",
	"sub_button":[
	{
		"type":"click",
		"name":"hello word",
		"key":"V1001_HELLO_WORLD"
	},
	{
		"type":"click",
		"name":"赞一下我们",
		"key":"V1001_GOOD"
	}]
	}]
}';
	$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';
	$result = $request->request ( $url, array (
			'method' => 'POST',
			'body' => $body 
	) );
	print_r ( $result ['response'] );
}

exit();

//print_r($weiXin->send($testFakeId, "test"));
//$weiXin->account_weixin_userlist();

// 发送图片, 图片必须要先在公共平台中上传, 得到图片Id
//print_r($weiXin->sendImage($testFakeId, "10000000"));

// 批量发送
//print_r($weiXin->batSend(array($testFakeId, $testFakeId2), "test batSend"));

// 得到用户信息
//print_r($weiXin->getUserInfo($testFakeId));

// 得到最新消息
//print_r($weiXin->getLatestMsgs());


//$messages=$weiXin->getRecentMessages('1121610100');
//print_r($messages);

//$userInfo = $weiXin->getAndUpdateUserInfoWithMatchedFakeId ( 'o_SSsjiArWBczFVGCQiV4ugWmYUE' );
//print_r ($userInfo);

