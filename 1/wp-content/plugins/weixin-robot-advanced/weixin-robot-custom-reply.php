<?php 
register_activation_hook( WEIXIN_ROBOT_PLUGIN_FILE,'weixin_robot_custom_replies_create_table');
function weixin_robot_custom_replies_create_table() {	
	global $wpdb;
 
	$weixin_custom_replies_table = weixin_robot_get_custom_replies_table();
	if($wpdb->get_var("show tables like '$weixin_custom_replies_table'") != $weixin_custom_replies_table) {
		$sql = "
		CREATE TABLE IF NOT EXISTS " . $weixin_custom_replies_table . " (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`keyword` varchar(255) CHARACTER SET utf8 NOT NULL,
			`reply` text CHARACTER SET utf8 NOT NULL,
			`status` int(1) NOT NULL DEFAULT '1',
			`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`type` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'text',
			PRIMARY KEY (`id`),
			UNIQUE KEY `keyword` (`keyword`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
		dbDelta($sql);
	}
}

function weixin_robot_custom_reply_page(){
	global $wpdb,$weixin_robot_custom_replies,$id,$succeed_msg;

	$wpdb->show_errors();

	$weixin_custom_replies_table = weixin_robot_get_custom_replies_table();
	
	if(isset($_GET['delete']) && isset($_GET['id']) && $_GET['id']){
		$wpdb->query("DELETE FROM $weixin_custom_replies_table WHERE id = {$_GET['id']}");
		delete_transient('weixin_custom_keywords');
	}

	if(isset($_GET['edit']) && isset($_GET['id'])){
		$id = (int)$_GET['id'];	
	}

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ){

		if ( !wp_verify_nonce($_POST['weixin_robot_custom_reply_nonce'],'weixin_robot') ){
			ob_clean();
			wp_die('非法操作');
		}
		
		$data = array(
			'keyword'	=> stripslashes( trim( $_POST['keyword'] )),
			'reply'		=> stripslashes( trim( $_POST['reply'] )),
			'status'	=> isset($_POST['status'] )?1:0,
			'time'		=> stripslashes( trim( $_POST['time'] )),
			'type'		=> stripslashes( trim( $_POST['type'] ))
		);
		
		if(empty($id)){
			$wpdb->insert($weixin_custom_replies_table,$data); 
			//$id = $wpdb->insert_id;
			$succeed_msg = '添加成功';
		}else{
			$current_user = $user = wp_get_current_user();
			$wpdb->update($weixin_custom_replies_table,$data,array('id'=>$id));
			$succeed_msg = '修改成功';
		}

		delete_transient('weixin_custom_keywords');
	}
?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2 class="nav-tab-wrapper">
            <a class="nav-tab nav-tab-active" href="javascript:void();" id="tab-title-custom">自定义回复</a>
            <a class="nav-tab" href="javascript:void();" id="tab-title-builtin">内置回复</a>
        </h2>

        <p>
        	*自定义回复优先级高于内置回复。<br />
        	*可以在自定义回复中设置关键字取代内置回复关键字，然后类型选择函数，回复内容设置为对应的函数名即可。<br />
        	*只能取代完全匹配类型的内置回复关键字。
        </p>

		<?php if(!empty($succeed_msg)){?>
		<div class="updated">
			<p><?php echo $succeed_msg;?></p>
		</div>
		<?php }?>
		<?php if(!empty($err_msg)){?>
		<div class="error" style="color:red;">
			<p>错误：<?php echo $err_msg;?></p>
		</div>
		<?php }?>

		<div id="tab-custom" class="div-tab hidden" >
	    <?php weixin_robot_custom_reply_list(); ?>
		<?php weixin_robot_custom_reply_add(); ?>
	    </div>

	    <div id="tab-builtin" class="div-tab hidden">
		<?php weixin_robot_builtin_reply_list();?>
		</div>

		<?php wpjam_option_tab_script(); ?>

	</div>
<?php
}

function weixin_robot_builtin_reply_list(){
	global $plugin_page,$wpdb;
?>
	
	<?php $weixin_builtin_replies = weixin_robot_get_builtin_replies(); ?>

	<?php if($weixin_builtin_replies) { ?>
	<h3>插件或者扩展内置回复列表</h3>


	<style>.widefat td { padding:4px 10px;vertical-align: middle;}</style>
	<table class="widefat" cellspacing="0">
	<thead>
		<tr>
			<?php /*<th style="width:40px">ID</th>*/?>
			<th>关键字</th>
			<th>类型</th>
			<th>描述</th>
			<th>处理函数</th>
		</tr>
	</thead>
	<tbody>
	<?php $alternate = '';?>
	<?php foreach($weixin_builtin_replies as $keyword => $weixin_builtin_reply){ $alternate = $alternate?'':'alternate';?>
		<tr class="<?php echo $alternate;?>">
			<td><?php echo $keyword; ?></td>
			<td><?php if($weixin_builtin_reply['type'] == 'full'){ echo '完全匹配'; }else{ echo '前缀匹配'; }; ?></td>
			<td><?php echo $weixin_builtin_reply['reply']; ?></td>
			<td><?php echo $weixin_builtin_reply['function']; ?></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
	
	
	<?php } ?>
<?php
}

function weixin_robot_custom_reply_list(){
	global $plugin_page,$wpdb;
?>
	<h3>自定义回复列表</h3>

	<?php 
		$weixin_custom_replies_table = weixin_robot_get_custom_replies_table();
		$weixin_robot_custom_replies = $wpdb->get_results("SELECT * FROM $weixin_custom_replies_table;");
	?>
	<?php if($weixin_robot_custom_replies) { ?>
	<form action="<?php echo admin_url('admin.php?page='.$plugin_page); ?>" method="POST">

		<style>.widefat td { padding:4px 10px;vertical-align: middle;}</style>
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<?php /*<th style="width:40px">ID</th>*/?>
				<th style="min-width:50px">关键字</th>
				<th>回复</th>
				<th style="width:80px">类型</th>
				<th style="width:130px">添加时间</th>
				<th style="width:50px">状态</th>
				<th style="width:70px">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $alternate = '';?>
		<?php foreach($weixin_robot_custom_replies as $weixin_robot_custom_reply){ $alternate = $alternate?'':'alternate';?>
			<tr class="<?php echo $alternate;?>">
				<?php /*<td><?php echo $weixin_robot_custom_reply->id; ?></td>*/?>
				<td><?php echo $weixin_robot_custom_reply->keyword; ?></td>
				<td><?php echo $weixin_robot_custom_reply->reply; ?></td>
				<td><?php $type = $weixin_robot_custom_reply->type; if($type == 'text'){echo '文本回复';}elseif($type == 'img'){ echo '图文回复'; } ?></td>
				<td><?php echo $weixin_robot_custom_reply->time; ?></td>
				<td><?php echo $weixin_robot_custom_reply->status?'使用中':'未使用'; ?></td>
				<td><span><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&edit&id='.$weixin_robot_custom_reply->id); ?>">编辑</a></span> | <span class="delete"><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&delete&id='.$weixin_robot_custom_reply->id); ?>">删除</a></span></td>
			</tr>
		<?php } ?>
		</tbody>
		</table>
	</form>
	<?php } else{ ?>
	
	<p>你还没有添加自定义回复，开始添加第一条自定义回复！</p>

	<?php } ?>
<?php
}

function weixin_robot_custom_reply_add(){
	global $wpdb,$id,$plugin_page;
	$weixin_custom_replies_table = weixin_robot_get_custom_replies_table();

	if(isset($id)){
		$weixin_robot_custom_reply = $wpdb->get_row($wpdb->prepare("SELECT * FROM $weixin_custom_replies_table WHERE id=%d LIMIT 1",$id));
	}else{
		$id = '';
	}

	?>
	<h3><?php echo $id?'修改':'新增';?>自定义回复 <?php if($id) { ?> <a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&add'); ?>" class="add-new-h2">新增另外一条自定义回复</a> <?php } ?></h3>

	<?php 
	$form_fields = array(
		array('name'=>'keyword',	'label'=>'关键字',	'type'=>'text',		'value'=>$id?$weixin_robot_custom_reply->keyword:'',	'description'=>'多个关键字请用英文逗号区分开，如：<code>七牛, qiniu, 七牛云存储, 七牛镜像存储</code>'),
		array('name'=>'type',		'label'=>'回复类型',	'type'=>'select',	'value'=>$id?$weixin_robot_custom_reply->type:'',		'options'=> array('text'=>'文本','img'=>'图文','function'=>'函数')),
		array('name'=>'reply',		'label'=>'回复内容',	'type'=>'textarea',	'value'=>$id?$weixin_robot_custom_reply->reply:'',		'description'=>'如果回复类型选择图文，请输入构成图文回复的单篇或者多篇日志的ID，并用英文逗号区分开，如：<code>123,234,345</code>，并且 ID 数量不要超过基本设置里面的返回结果最大条数。'),
		array('name'=>'time',		'label'=>'添加时间', 'type'=>'datetime',	'value'=>$id?$weixin_robot_custom_reply->time:current_time('mysql')),
		array('name'=>'status',		'label'=>'状态',		'type'=>'checkbox',	'value'=>'1',											'checked'=>$id?($weixin_robot_custom_reply->status?'checked':''):'checked')
	);

	?>
	<form method="post" action="<?php echo admin_url('admin.php?page='.$plugin_page.'&edit&id='.$id); ?>" enctype="multipart/form-data" id="form">
		<?php wpjam_admin_display_form_table($form_fields); ?>
		<?php wp_nonce_field('weixin_robot','weixin_robot_custom_reply_nonce'); ?>
		<input type="hidden" name="action" value="edit" />
		<p class="submit"><input class="button-primary" type="submit" value="　　<?php echo $id?'修改':'新增';?>　　" /></p>
	</form>
<?php

}

function weixin_robot_get_custom_replies_table(){
	global $wpdb;
	return $wpdb->prefix.'weixin_custom_replies';
}