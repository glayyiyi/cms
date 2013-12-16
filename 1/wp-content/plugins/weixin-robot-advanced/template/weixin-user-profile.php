<?php
if(!is_weixin()){
	wp_die('请在微信中访问该页！');
}
$query_id		= $_GET['weixin_user_id'];
$weixin_openid 	= weixin_robot_user_get_openid($query_id);

if($weixin_openid == false){
	wp_die('非法访问！');
}

if(isset($_GET['update'])){
	if(isset($_POST['update'])  && wp_verify_nonce( $_POST['weixin_user_act'], 'weixin_user')){
		
		$weixin_user_new = array(	
			//'nickname'	=> trim(wp_strip_all_tags($_POST['nickname'])),
			'name'		=> trim(wp_strip_all_tags($_POST['name'])), 
			'address' 	=> trim(wp_strip_all_tags($_POST['address'])), 
			'phone'		=> trim(wp_strip_all_tags($_POST['phone']))
		);

		weixin_robot_update_user($weixin_openid,$weixin_user_new);

		$success= "ok";
	}
}else{
	global $wpdb;
	$weixin_credits_table = weixin_robot_credits_table();
	$weixin_credits = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$weixin_credits_table} WHERE weixin_openid=%s ORDER BY id DESC LIMIT 0,30;",$weixin_openid));
}

$weixin_user = weixin_robot_get_user($weixin_openid);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>用户中心</title>
	<meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
    ul{padding-left: 0;}
    li{margin-left: 20px;}
    input, textarea {font-size: large;}
    textarea {width: 95%;}
    th,td{border-top:1px solid #ccc;}
    </style>
<?php //wp_head();?>
</head>
<body>
		
<div class="content">
<?php if(isset($_GET['update'])){ ?>
	<?php if(isset($success) && $success == 'ok') { ?><p>修改成功 </p><?php } ?>
	<p>提交或者修改以下信息，当你获奖的是以便于兑换奖品。 </p>
	<form action="" method="post" id="details_form">

		<?php wp_nonce_field('weixin_user','weixin_user_act'); ?>

		<?php /*
		<p>
			<label for="nickname">昵称：</label><br />
			<input type="text" class="form-fields" name="nickname" id="nickname" value="<?php echo $weixin_user['nickname']?>">
		</p>
		*/?>

		<p>
			<label for="contact">姓名：</label><br />
			<input type="text" class="form-fields" name="name" id="name" value="<?php echo $weixin_user['name']?>">
		</p>

		<p>
			<label for="mobile">手机：</label><br />
			<input type="text" class="form-fields" name="phone" id="phone" value="<?php echo $weixin_user['phone']?>">
		</p>

		<p>
			<label for="address">地址：</label><br />
			<textarea class="form-fields" name="address" id="address" rows="3"><?php echo $weixin_user['address']?></textarea>
		</p>

		<p>
			<input type="submit" name="update" value="编辑" >
		</p>

	</form>
<?php }elseif(isset($_GET['credit_rule'])){ ?>
	<p><strong>积分规则：</strong></p>
	<ul>
		<li>签到：				<?php echo weixin_robot_get_setting('weixin_checkin_credit');?>分</li>
		<?php /*<li>发送文章给好友：		<?php echo weixin_robot_get_setting('weixin_SendAppMessage_credit');?>分</li>
		<li>分享文章到朋友圈：		<?php echo weixin_robot_get_setting('weixin_ShareTimeline_credit');?>分</li>
		<li>分享文章到腾讯微博：	<?php echo weixin_robot_get_setting('weixin_ShareWeibo_credit');?>分</li>*/?>
		<li>每天最多：			<?php echo weixin_robot_get_setting('weixin_day_credit_limit');?>分</li>
	</ul>
<?php } else {  ?>
	<p><strong>你现在共有 <?php echo weixin_robot_get_credit($weixin_openid); ?> 积分</strong>：</p>
	<ul>
		<li><a href="<?php echo home_url('?weixin_user_profile&weixin_user_id='.$query_id.'&credit_rule=1')?>">查看积分规则</a></li>
		<li><a href="<?php echo home_url('?weixin_user_profile&weixin_user_id='.$query_id.'&update=1')?>">修改个人资料</a></li>
	</ul>
	
	<p><strong>积分历史</strong>：</p>
	<table cellspacing="0" cellpadding="6" width="98%">
		<thead>
			<tr><th>操作</th><th width="20%">积分</th><th width="20%">新增</th></tr>
		</thead>
		<tbody>
		<?php foreach ($weixin_credits as $weixin_credit) { ?>
			<tr><td><?php echo $weixin_credit->note; ?></td><td><?php echo $weixin_credit->credit; ?></td><td><?php echo $weixin_credit->credit_change; ?></td></tr>
			<?php /*<td><?php if($weixin_credit->limit){echo '每日'.DAY_CREDIT_LIMIT.'分上限';}; ?></td> */?>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>
</div>

<?php //the_footer(); ?>
</body>
</html>