<?php
require ("LeaWeiXinClient.php");

if (! function_exists ( 'initNewWeAccount' )) :
	function initNewWeAccount() {
		// global $weAccount;
		// echo '======初始化新weAccount======';
		$current = get_option ( 'weiservice-basic' );
		$userName = $current ['weixin_account'];
		$password = $current ['weixin_password'];
		// $userName = 'ilovelife100';
		// $password = '0a97052f3305c8d6dc75933b71631387';
		//$weixinRootUrl = 'https://mp.weixin.qq.com';
		$weAccount = new WeAccount ( $userName, $password);
		return $weAccount;
	}




endif;

if (! function_exists ( 'get_post_excerpt' )) {
	function get_post_excerpt($post, $width = 320) {
		$post_excerpt = strip_tags ( $post->post_excerpt);
		if (! $post_excerpt) {
			$post_excerpt = mb_strimwidth ( strip_tags ( do_shortcode ( $post->post_content )), 0, $width, '...', 'utf-8' );
		}
		return $post_excerpt;
	}
}

if (! function_exists ( 'get_post_first_image' )) {
	function get_post_first_image($post_content) {
		preg_match_all ( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post_content, $matches );
		if ($matches) {
			return $matches [1] [0];
		} else {
			return false;
		}
	}
}

if (! function_exists ( 'get_post_first_audio' )) {
	function get_post_first_audio($post_content) {
		$pattern = "/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](([^\"']+\.mp3))['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]+)<\/a>/i";
		preg_match_all ( $pattern, $post_content, $matches );
		// print_r ( $matches );
		$pat2 = '|\[audio.*?mp3=[\'"](.*?)[\'"].*?\]|i';
		preg_match_all ( $pat2, $post_content, $matches2 );
		// print_r ( $matches2 );
		if ($matches2 [1] [0]) {
			return $matches2 [1] [0];
		} else if ($matches [2] [0]) {
			return $matches [2] [0];
		} else {
			return false;
		}
	}
}

/**
 * 微信公共平台的私有接口
 * 思路: 模拟登录, 再去调用私有web api
 *
 * 功能: 发送信息, 批量发送(未测试), 得到用户信息, 得到最近信息, 解析用户信息(fakeId)
 *
 * @author life lifephp@gmail.com https://github.com/lealife/WeiXin-Private-API
 *        
 *         参考了gitHub微信的api: https://github.com/zscorpio/weChat, 在此基础上作了修改和完善
 *         (该接口经测试不能用, 因为webToken的问题, 还有cookie生成的问题, 本接口已修复这些问题)
 */
class WeAccount {
	private $token; // 公共平台申请时填写的token
	private $account;
	private $password;
	
	// 每次登录后将cookie, webToken缓存起来, 调用其它api时直接使用
	// 注: webToken与token不一样, webToken是指每次登录后动态生成的token, 供难证用户是否登录而用
	//private $cookiePath; // 保存cookie的文件路径
	//private $webTokenPath; // 保存webToken的文路径
	                       
	// 缓存的值
	private $webToken; // 登录后每个链接后都要加token
	private $cookie;
	private $lea;
	
	// 构造函数
	public function __construct($userName, $password) {
		// if(!$config) {
		// exit("error");
		// }
		//$G_ROOT = dirname(__FILE__);
		// 配置初始化
		$this->account = $userName;
		$this->password = $password;
		
		//$this->cookiePath = $G_ROOT. '/cache/cookie'; // cookie缓存文件路径
		//$this->webTokenPath= $G_ROOT. '/cache/webToken'; // webToken缓存文件路径
		
		$this->lea = new LeaWeiXinClient ();
		
		// 读取cookie, webToken
		$this->getCookieAndWebToken ();
	}
	
	// 登录, 并获取cookie, webToken
	
	/**
	 * 模拟登录获取cookie和webToken
	 */
	public function login() {
		$url = "https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN";
		$post ["username"] = $this->account;
		$post ["pwd"] = md5 ( $this->password );
		$post ["f"] = "json";
		//echo '======log in=========';
		$re = $this->lea->submit ( $url, $post );
		//var_dump($re ['body']);
		$data = json_decode ($re ['body'], true );
		
		//print_r($post);
		//print_r($data);
		if ( $data['ErrCode'] != 0) {
			switch ($data ['ErrCode']) {
				case "-1" :
					$msg = "系统错误，请稍候再试。";
					break;
				case "-2" :
					$msg = "微信公众帐号或密码错误。";
					break;
				case "-3" :
					$msg = "微信公众帐号密码错误，请重新输入。";
					break;
				case "-4" :
					$msg = "不存在该微信公众帐户。";
					break;
				case "-5" :
					$msg = "您的微信公众号目前处于访问受限状态。";
					break;
				case "-6" :
					$msg = "登录受限制，需要输入验证码，稍后再试！";
					break;
				case "-7" :
					$msg = "此微信公众号已绑定私人微信号，不可用于公众平台登录。";
					break;
				case "-8" :
					$msg = "微信公众帐号登录邮箱已存在。";
					break;
				case "-200" :
					$msg = "因您的微信公众号频繁提交虚假资料，该帐号被拒绝登录。";
					break;
				case "-94" :
					$msg = "请使用微信公众帐号邮箱登陆。";
					break;
				case "10" :
					$msg = "该公众会议号已经过期，无法再登录使用。";
					break;
				default :
					$msg = "未知的返回。";
			}
			echo "\n" . '微信登陆错误:' . $msg;
			return false;
		}
		
		// 保存cookie
		$this->cookie = $re ['cookie'];
		//file_put_contents ( $this->cookiePath, $this->cookie );
		if ( add_option( 'weixin_cookie', $this->cookie ) ) {
		
			echo get_option( 'weixin_cookie' );
		
		} else {
		
			update_option( 'weixin_cookie', $this->cookie );
		}
		
		
		// 得到token
		$this->getWebToken ( $re ['body'] );
		
		return true;
	}
	
	/**
	 * 登录后从结果中解析出webToken
	 * 
	 * @param [String] $logonRet        	
	 * @return [Boolen]
	 */
	private function getWebToken($logonRet) {
		$logonRet = json_decode ( $logonRet, true );
		$msg = $logonRet ["ErrMsg"]; // /cgi-bin/indexpage?t=wxm-index&lang=zh_CN&token=1455899896
		$msgArr = explode ( "&token=", $msg );
		if (count ( $msgArr ) != 2) {
			return false;
		} else {
			$this->webToken = $msgArr [1];
			//file_put_contents ( $this->webTokenPath, $this->webToken );
			if ( add_option( 'weixin_token', $this->webToken ) ) {
			
				echo get_option( 'weixin_token' );
			
			} else {
			
				update_option( 'weixin_token', $this->webToken );
			}
			return true;
		}
	}
	
	/**
	 * 从缓存中得到cookie和webToken
	 * 
	 * @return [type]
	 */
	public function getCookieAndWebToken() {
		//$this->cookie = file_get_contents ( $this->cookiePath );
		//$this->webToken = file_get_contents ( $this->webTokenPath );
		$this->cookie =get_option( 'weixin_cookie' );
		$this->webToken =get_option( 'weixin_token' );
		
		// 如果有缓存信息, 则验证下有没有过时, 此时只需要访问一个api即可判断
		if ($this->cookie && $this->webToken) {
			$re = $this->getUserInfo ( 1 );
			//echo '======getCookieAndWebToken===';
			//var_dump($re);
			if (is_array ( $re )) {
				return true;
			}
		} 
			
		return $this->login ();
		
	}
	
	// 其它API, 发送, 获取用户信息
	
	/**
	 * 主动发消息
	 * 
	 * @param string $id
	 *        	用户的fakeid
	 * @param string $content
	 *        	发送的内容
	 * @return [type] [description]
	 */
	public function send($id, $content) {
		$post = array ();
		$post ['tofakeid'] = $id;
		$post ['type'] = 1;
		$post ['content'] = $content;
		$post ['ajax'] = 1;
		$url = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&token={$this->webToken}";
		$re = $this->lea->submit ( $url, $post, $this->cookie );
		return json_decode ( $re ['body'],true);
	}
	
	/**
	 * 批量发送
	 * 
	 * @param [array] $ids
	 *        	用户的fakeid集合
	 * @param [type] $content
	 *        	[description]
	 * @return [type] [description]
	 */
	public function batSend($ids, $content) {
		$result = array ();
		foreach ( $ids as $id ) {
			$result [$id] = $this->send ( $id, $content );
		}
		return $result;
	}
	
	/**
	 * 发送图片
	 * 
	 * @param int $fakeId
	 *        	[description]
	 * @param int $fileId
	 *        	图片ID
	 * @return [type] [description]
	 */
	public function sendImage($fakeId, $fileId) {
		$post = array ();
		$post ['tofakeid'] = $fakeId;
		$post ['type'] = 2;
		$post ['fid'] = $post ['fileId'] = $fileId; // 图片ID
		$post ['error'] = false;
		$post ['ajax'] = 1;
		$post ['token'] = $this->webToken;
		
		$url = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN";
		$re = $this->lea->submit ( $url, $post, $this->cookie );
		
		return json_decode ( $re ['body'],true );
	}
	
	/**
	 * 获取用户的信息
	 * 
	 * @param string $fakeId
	 *        	用户的fakeId
	 * @return [type] [description]
	 */
	public function getUserInfo($fakeId) {
		$url = "https://mp.weixin.qq.com/cgi-bin/getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&token={$this->webToken}&fakeid=$fakeId";
		$re = $this->lea->submit ( $url, array (), $this->cookie );
		$result = json_decode ( $re ['body'], true );
// 		if (! $result) {
// 			$this->login ();
// 		}
		return $result;
	}
	
	/*
	 * 得到最近发来的信息 [0] => Array ( [id] => 189 [type] => 1 [fileId] => 0 [hasReply] => 0 [fakeId] => 1477341521 [nickName] => lealife [remarkName] => [dateTime] => 1374253963 ) [ok]
	 */
	public function getLatestMsgs($page = 0) {
		// frommsgid是最新一条的msgid
		$frommsgid = 100000;
		$offset = 50 * $page;
		// $url = "https://mp.weixin.qq.com/cgi-bin/getmessage?t=ajax-message&lang=zh_CN&count=50&timeline=&day=&star=&frommsgid=$frommsgid&cgi=getmessage&offset=$offset";
		$url = "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&count=999999&day=7&offset={$offset}&token={$this->webToken}&lang=zh_CN";
		$re = $this->lea->get ( $url, $this->cookie );
		// print_r($re['body']);
		
		// 解析得到数据
		// list : ({"msg_item":[{"id":}, {}]})
		$match = array ();
		preg_match ( '/["\' ]msg_item["\' ]:\[{(.+?)}\]/', $re ['body'], $match );
		if (count ( $match ) != 2) {
			return "";
		}
		
		$match [1] = "[{" . $match [1] . "}]";
		
		return json_decode ( $match [1], true );
	}
	
	// 解析用户信息
	// 当有用户发送信息后, 如何得到用户的fakeId?
	// 1. 从web上得到最近发送的信息
	// 2. 将用户发送的信息与web上发送的信息进行对比, 如果内容和时间都正确, 那肯定是该用户
	// 实践发现, 时间可能会不对, 相隔1-2s或10多秒也有可能, 此时如果内容相同就断定是该用户
	// 如果用户在时间相隔很短的情况况下输入同样的内容很可能会出错, 此时可以这样解决: 提示用户输入一些随机数.
	
	/**
	 * 通过时间 和 内容 双重判断用户
	 * 
	 * @param [type] $createTime        	
	 * @param [type] $content        	
	 * @return [type]
	 */
	public function getLatestMsgByCreateTimeAndContent($createTime, $content) {
		$lMsgs = $this->getLatestMsgs ( 0 );
		
		// 最先的数据在前面
		
		$contentMatchedMsg = array ();
		foreach ( $lMsgs as $msg ) {
			// 仅仅时间符合
			if ($msg ['dateTime'] == $createTime) {
				// 内容+时间都符合
				if ($msg ['content'] == $content) {
					return $msg;
				}
				
				// 仅仅是内容符合
			} else if ($msg ['content'] == $content) {
				$contentMatchedMsg [] = $msg;
			}
		}
		
		// 最后, 没有匹配到的数据, 内容符合, 而时间不符
		// 返回最新的一条
		if ($contentMatchedMsg) {
			return $contentMatchedMsg [0];
		}
		
		return false;
	}
	function getRecentMessages($fakeid) {
		$post = array (
				'fromfakeid' => $fakeid,
				'token' => $this->webToken,
				'opcode' => 1,
				'ajax' => 1 
		);
		
		$url = "https://mp.weixin.qq.com/cgi-bin/singlemsgpage?count=200&t=ajax-single-getnewmsg&lang=zh_CN";
		$re = $this->lea->submit ( $url, $post, $this->cookie );
		// return json_decode($re['body']);
		$result = json_decode ( $re ['body'], true );
		
		// if (! $result) {
		// return false;
		// }
		return $result;
	}
	/**
	 * 将用户放入制定的分组
	 *
	 * @param array $fakeidsList        	
	 * @param string $groupid        	
	 * @return boolean 放入是否成功
	 */
	function putIntoGroup($fakeidsList, $groupid) {
		if (! empty ( $fakeidsList )) {
			$fakeidsListString = "";
			if (is_array ( $fakeidsList )) {
				foreach ( $fakeidsList as $value ) {
					$fakeidsListString .= $value . "|";
				}
			} else {
				$fakeidsListString = $fakeidsList;
			}
			$post = array (
					'contacttype' => $groupid,
					'tofakeidlist' => $fakeidsListString,
					'token' => $this->webToken,
					'ajax' => 1 
			);
			
			$url = "https://mp.weixin.qq.com/cgi-bin/modifycontacts?action=modifycontacts&t=ajax-putinto-group";
			$re = $this->lea->submit ( $url, $post, $this->cookie );
			//print_r ( "===========将用户放入制定的分组==========" );
			//print_r ( $post );
			//print_r ( $re );
			return json_decode ( $re ['body'],true );
		}
		return false;
	}
	function getAndUpdateUserInfo($openId) {
		if ($openId) {
			// For Debug
			// echo '=========open_id==========' . $openId;
			// $fromUsername = $openId . time ();
			$fromUsername = $openId;
			$user = null;
			$user_obj = get_user_by ( 'login', $fromUsername );
			if ($user_obj) {
				$user = $user_obj->to_array ();
				// Add additional custom fields
				foreach ( _get_additional_user_keys ( $user_obj ) as $key ) {
					$user [$key] = get_user_meta ( $user ['ID'], $key, true );
				}
				
				// Escape data pulled from DB.
				$user = add_magic_quotes ( $user );
				// echo '========get user==========';
				// print_r ( $user );
			}
			if ($user && $user ['user_url'] && $user ['user_nicename']) {
				$fakeid = substr ( trim ( $user ['user_url'] ), 7 ); // 去除前面固定的http://
				                                                     // echo '================从旧用户找到fakeId========' . $fakeid;
				$userInfo = array (
						'FakeId' => $fakeid,
						'Username' => $user ['user_nicename'],
						'NickName' => $user ['nickname'] 
				);
				// $userInfo = $this->get_weixin_user_info ( $fakeid );
				return $userInfo;
			}
			if (! $user) {
				$userdata = array (
						'user_login' => $fromUsername,
						'user_pass' => "1234",
						'user_email' => $fromUsername . "@appcn100.com" 
				)
				;
				$user_id = wp_insert_user ( $userdata );
				if ($user_id) {
					// echo ('注册新用户成功，用户ID：' . $user_id);
					$user_obj = get_user_by ( 'id', $user_id );
					$user = $user_obj->to_array ();
				}
			}
			return false;
		} else
			return false;
	}
	function getAndUpdateUserInfoWithMatchedFakeId($openId) {
		if ($openId) {
			// For Debug
			// echo '=========open_id==========' . $openId;
			// $fromUsername = $openId . time ();
			$fromUsername = $openId;
			$user = null;
			$user_obj = get_user_by ( 'login', $fromUsername );
			if ($user_obj) {
				$user = $user_obj->to_array ();
				// Add additional custom fields
				foreach ( _get_additional_user_keys ( $user_obj ) as $key ) {
					$user [$key] = get_user_meta ( $user ['ID'], $key, true );
				}
				
				// Escape data pulled from DB.
				$user = add_magic_quotes ( $user );
				// echo '========get user==========';
				// print_r ( $user );
			}
			if ($user && $user ['user_url'] && $user ['user_nicename']) {
				$fakeid = substr ( trim ( $user ['user_url'] ), 7 ); // 去除前面固定的http://
				                                                     // echo '================从旧用户找到fakeId========' . $fakeid;
				$userInfo = array (
						'FakeId' => $fakeid,
						'Username' => $user ['user_nicename'],
						'NickName' => $user ['nickname'] 
				);
				// $userInfo = $this->get_weixin_user_info ( $fakeid );
				return $userInfo;
			}
			if (! $user) {
				$userdata = array (
						'user_login' => $fromUsername,
						'user_pass' => "1234",
						'user_email' => $fromUsername . "@appcn100.com" 
				)
				;
				$user_id = wp_insert_user ( $userdata );
				if ($user_id) {
					// echo ('注册新用户成功，用户ID：' . $user_id);
					$user_obj = get_user_by ( 'id', $user_id );
					$user = $user_obj->to_array ();
				}
			}
			
			echo ',未分组好友';
			//$msgFriendList = $this->getLatestMsgs ();
			$msgFriendList =$this->account_weixin_userlist();
			//var_dump($msgFriendList);
			if ($msgFriendList) {
				
				// $openIdsub = substr ( md5 ( $openId ), 0, 16 );
				$openIdsub = $openId;
				//print_r ( '============$openId Sub:' . $openIdsub);
				foreach ( $msgFriendList as $value ) {
					//$fakeid = $value ['fakeid'];
					$fakeid = $value;
					print_r ( ',' . $fakeid );
					$recentMessages = $this->getRecentMessages ( $fakeid );
					// print_r($recentMessages);
					foreach ( $recentMessages as $messageValue ) {
						// print_r ( '=============message content:' . $messageValue ['content']);
						if (false !== strpos ( strtolower ( $messageValue ['content'] ), strtolower ( $openIdsub ) )) {
							//echo ',找到匹配的fake_id' . $fakeid;
							// $this->putIntoGroup ( $fakeid, 2 );
							// $user = get_user_by ( 'login', $openId );
							$userInfo = $this->getUserInfo ( $fakeid );
							if ($userInfo && $user) {
								$email_prefix = empty ( $fakeid ) ? $fromUsername : $fakeid;
								// print_r ( $user );
								$userdata = array (
										'ID' => $user ['ID'],
										'user_nicename' => $userInfo ['Username'],
										'nickname' => $userInfo ['NickName'],
										'display_name' => $userInfo ['NickName'],
										'user_email' => $email_prefix . "@appcn100.com",
										'user_url' => $fakeid,
										'wechat' => $userInfo ['Username'] 
								
								// 'role'=>"subscriber"
																);
								$user_id = wp_update_user ( $userdata );
								//if ($user_id)
									//$this->putIntoGroup ( $fakeid, 2 );
								// echo ('更新成功，用户ID：' . $user_id);
							}
							return $userInfo;
						}
					}
				}
			}
			return false;
		} else
			return false;
	}
	
	function account_weixin_userlist($groupid = 0, $psize = 200) {
		$url = 'https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize='.$psize.'&pageidx=0&type=0&groupid='.$groupid.'&token='.$this->webToken.'&lang=zh_CN';
		$re = $this->lea->get ( $url, $this->cookie );
		//echo '获取用户列表';
		//var_dump( $re ['body']);
		// 解析得到数据
		$match = array ();
		$match1 = array ();
		preg_match ( '/["\' ]contacts["\' ]:\[{(.+?)}\]/', $re ['body'], $match );
		
		if (count ( $match ) != 2) {
			return "";
		}
		$strTmp = "[{" . $match [1] . "}]";
		//var_dump( $strTmp);
		//return json_decode ( $match [1], true );
		
		//echo '========matche1======='.$strTmp;
		preg_match_all ( '/"id":(\d+?),/',$strTmp, $match1 );
		//var_dump($match1);
		return $match1 [1];
		
	}
	
}
