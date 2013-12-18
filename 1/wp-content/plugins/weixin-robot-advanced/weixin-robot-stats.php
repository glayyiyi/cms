<?php 
add_action('weixin_robot','wpjam_stats_weixin_robot',10,2);
function wpjam_stats_weixin_robot($postObj,$response){
	if($response){
		weixin_robot_insert_message($postObj,$response);
	}
}

function weixin_robot_get_messages_table(){
	global $wpdb;
	return apply_filters('weixin_messages_table',$wpdb->prefix.'weixin_messages');
}

register_activation_hook( WEIXIN_ROBOT_PLUGIN_FILE,'weixin_robot_messages_create_table');
function weixin_robot_messages_create_table() {	
	global $wpdb;
 
	$weixin_messages_table = weixin_robot_get_messages_table();
	if($wpdb->get_var("show tables like '$weixin_messages_table'") != $weixin_messages_table) {
		$sql = "
		CREATE TABLE IF NOT EXISTS ".$weixin_messages_table." (
			`id` bigint(20) NOT NULL auto_increment,
			`MsgId` bigint(64) NOT NULL,
			`FromUserName` varchar(30) character set utf8 NOT NULL,
			`MsgType` varchar(10) character set utf8 NOT NULL,
			`CreateTime` int(10) NOT NULL,

			`Content` longtext character set utf8 NOT NULL,

			`PicUrl` varchar(255) character set utf8 NOT NULL,

			`Location_X` double(10,6) NOT NULL,
			`Location_Y` double(10,6) NOT NULL,
			`Scale` int(10) NOT NULL,
			`label` varchar(255) character set utf8 NOT NULL,

			`Title` text character set utf8 NOT NULL,
			`Description` longtext character set utf8 NOT NULL,
			`Url` varchar(255) character set utf8 NOT NULL,

			`Event` varchar(255) character set utf8 NOT NULL,
			`EventKey` varchar(255) character set utf8 NOT NULL,

			`Format` varchar(255) character set utf8 NOT NULL,
			`MediaId` text character set utf8 NOT NULL,
			`Recognition` text character set utf8 NOT NULL,
		 
			`Response` varchar(255) character set utf8 NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
		dbDelta($sql);
	}
}

function weixin_robot_insert_message($postObj,$Response=''){

	if(!is_object($postObj)) return 0;

	global $wpdb;

	global $wpdb,$is_IIS;
	
	$data = array(
		'MsgType'		=>	$postObj->MsgType,
		'FromUserName'	=>	$postObj->FromUserName,
		'CreateTime'	=>	$postObj->CreateTime,
		'Response'		=>	$Response,
		'MsgId'			=> '',
		'Content'		=> '',
		'PicUrl'		=> '',
		'Location_X'	=> 0,
		'Location_Y'	=> 0,
		'Scale'			=> 0,
		'Label'			=> '',
		'Title'			=> '',
		'Description'	=> '',
		'Url'			=> '',
		'Event'			=> '',
		'EventKey'		=> '',
		'Format'		=> '',
		'MediaId'		=> '',
		'Recognition'	=> '',
	);

	$msgType = $postObj->MsgType;

	if($msgType == 'text'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Content']	= $postObj->Content;
	}elseif($msgType == 'image'){
		$data['MsgId']		= $postObj->MsgId;
		$data['PicUrl']		= $postObj->PicUrl;
	}elseif($msgType == 'location'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Location_X']	= $postObj->Location_X;
		$data['Location_Y']	= $postObj->Location_Y;
		$data['Scale']		= $postObj->Scale;
		$data['Label']		= $postObj->Label;
	}elseif($msgType == 'link'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Title']		= $postObj->Title;
		$data['Description']= $postObj->Description;
		$data['Url']		= $postObj->Url;
	}elseif($msgType == 'event'){
		$data['Event']		= $postObj->Event;
		if($data['Event'] == 'LOCATION'){
			$data['Location_X']	= $postObj->Latitude;
			$data['Location_Y']	= $postObj->Longitude;
		}
		$data['EventKey']	= $postObj->EventKey;
	}elseif($msgType == 'voice'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Format']		= $postObj->Format;
		$data['MediaId']	= $postObj->MediaId;
		$data['Recognition']= $postObj->Recognition;
	}

	$weixin_messages_table = weixin_robot_get_messages_table();
	$wpdb->insert($weixin_messages_table,$data); 
	return $wpdb->insert_id;
}

function weixin_robot_get_message($id){
	global $wpdb;
	$weixin_messages_table = weixin_robot_get_messages_table();
	return $wpdb->get_row("SELECT * FROM {$weixin_messages_table} WHERE id=$id");
}
/*function weixin_robot_update_message($id,$Response){
	global $wpdb;
	$data = array('Response'	=>	$Response );
	$weixin_messages_table = weixin_robot_get_messages_table();
	$wpdb->update($weixin_messages_table,$data,array('id'=>$id));
}*/

add_action('admin_head','weixin_robot_stats_admin_head',999);
function weixin_robot_stats_admin_head(){
	global $plugin_page;
	if(in_array($plugin_page, array('weixin-robot-stats', 'weixin-robot-summary'))){
?>
<link rel="stylesheet" href="http://cdn.staticfile.org/morris.js/0.4.2/morris.min.css" />
<script type='text/javascript' src="http://cdn.staticfile.org/raphael/2.1.0/raphael-min.js"></script>
<script type='text/javascript' src="http://cdn.staticfile.org/morris.js/0.4.2/morris.min.js"></script>
<style type="text/css">
input[type="date"]{ background-color: #fff; border-color: #dfdfdf; border-radius: 3px; border-width: 1px; border-style: solid; color: #333; outline: 0; box-sizing: border-box; }
.widefat td { padding:4px 10px; vertical-align: middle;}
</style>
<?php
	}
}

function weixin_robot_stats_get_start_date(){
	$start_date	= (isset($_REQUEST['start_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_REQUEST['start_date']))?$_REQUEST['start_date']:'';
	if(!$start_date) $start_date=gmdate('Y-m-d',current_time('timestamp')-(60*60*24*30));
	return $start_date;
}

function weixin_robot_stats_get_end_date(){
	$end_date 	= (isset($_REQUEST['end_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_REQUEST['end_date']))?$_REQUEST['end_date']:'';
	if(!$end_date) $end_date=gmdate('Y-m-d',current_time('timestamp'));
	return $end_date;
}

function weixin_robot_stats_get_type(){
	return isset($_REQUEST['type'])?$_REQUEST['type']:'';
}

function weixin_robot_stats_header(){
	global $plugin_page;
	$start_date	= weixin_robot_stats_get_start_date();
	$end_date 	= weixin_robot_stats_get_end_date();
	?>
	<div class="tablenav">
    <div class="alignleft actions">
        <form method="get" action="admin.php" target="_self" id="export-filter" style="float:left;">
        	<input type="hidden" name="page" value="<?php echo $plugin_page;?>" />
            日期:
            <input type="date" name="start_date" id="start_date" value="<?php echo esc_attr($start_date);?>" size="11" />
            -
            <input type="date" name="end_date" id="end_date" value="<?php echo esc_attr($end_date);?>" size="11" />
            <?php /*<select name="type">
			<?php foreach ($types as $key => $value) {?>
				<option value="<?php echo $key; ?>"<?php if($type == $key) echo ' selected="selected"';?>><?php echo $value;?></option>
			<?php }?>
			</select>*/?>
            <input type="submit" value="　显示　" class="button-secondary" name="">
        </form>
    </div>
	</div>
    <h3><?php echo $start_date,' - ',$end_date; ?> 汇总数据：</h3>
<?php
}

function weixin_robot_stats_get_types(){
	return array(
		'total'			=>'所有类型',
		'text'			=>'文本消息', 
		'event'			=>'事件消息', 
		'subscribe'		=>'用户订阅', 
		'unsubscribe'	=>'取消订阅', 
		'location'		=>'位置消息', 
		'image'			=>'图片消息', 
		'link'			=>'链接消息', 
		'voice'			=>'语音消息'
	);
}

function weixin_robot_get_response_types(){
	$response_types = array(
		'total'			=> '所有类型',
		'advanced'		=> '高级回复',
		'welcome'		=> '欢迎语',
		'tag'			=> '标签最新日志',
		'cat'			=> '分类最新日志',
		'custom-text'	=> '自定义文本回复',
		'custom-img'	=> '自定义图文回复',
		'query'			=> '搜索查询回复',
		'too-long'		=> '关键字太长',
		'not-found'		=> '没有匹配内容',
		'voice'			=> '语音自动回复',
		'loction'		=> '位置自动回复',
		'image'			=> '图片自动回复',
		'enter-reply'	=> '进入微信回复'
	);

	return apply_filters('weixin_response_types',$response_types);
}

function weixin_robot_stats_page() {
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div> 
		<h2>消息统计分析</h2>
		<?php
		global $wpdb, $plugin_page;

		$start_date	= weixin_robot_stats_get_start_date();
		$end_date 	= weixin_robot_stats_get_end_date();
		$end_time	= $end_date.' 23:59:59';

		$type = weixin_robot_stats_get_type();
		if(!$type) $type = 'total';

		$types = weixin_robot_stats_get_types();
		
		$weixin_messages_table = weixin_robot_get_messages_table();

		$where = 'CreateTime > '.strtotime($start_date).' AND CreateTime < '.strtotime($end_time);
		$sum = "
		SUM(case when MsgType='text' then 1 else 0 end) as text,
		SUM(case when MsgType='event' AND Event!='subscribe' AND Event!='unsubscribe' then 1 else 0 end) as event, 
		SUM(case when MsgType='event' AND Event='subscribe' then 1 else 0 end) as subscribe, 
		SUM(case when MsgType='event' AND Event='unsubscribe' then 1 else 0 end) as unsubscribe,
		SUM(case when MsgType='location' then 1 else 0 end) as location, 
		SUM(case when MsgType='image' then 1 else 0 end) as image, 
		SUM(case when MsgType='link' then 1 else 0 end) as link, 
		SUM(case when MsgType='voice' then 1 else 0 end) as voice
		";

		weixin_robot_stats_header();

		$sql = "SELECT {$sum} FROM {$weixin_messages_table} WHERE {$where}";

		$count = $wpdb->get_row($sql);

		?>
		<div style="display:table;">

			<div style="display: table-row;">

				<div id="total-chart" style="display: table-cell; width:450px; float:left;"></div>

				<div style="display: table-cell; float:left; width:200px;">
					<table class="widefat" cellspacing="0">
						<thead>
							<tr>
								<th>类型</th>
								<th>数量</th>
							</tr>
						</thead>
						<tbody>
						<?php $data = array();?>
						<?php $alternate = '';?>
						<?php foreach ($types as $key=>$value) { $alternate = $alternate?'':'alternate';?>
							<?php if($key != 'total' && $count->$key){?>
							<?php $data []= '{"label": "'.$value.'", "value": '.$count->$key.' }'; ?>
							<tr class="<?php echo $alternate; ?>">
								<td><?php echo $value; ?></td>
								<td><?php echo $count->$key; ?></td>
							</tr>
							<?php }?>
						<?php } ?>
						<?php $data = "\n".implode(",\n", $data)."\n";?>
						</tbody>
					</table>
				</div>

			</div>

		</div>

		<script type="text/javascript">
			Morris.Donut({
			  element: 'total-chart',
			  data: [<?php echo $data;?>]
			});
		</script>

		<div style="clear:both;"></div>

		<h3>每日详细数据</h3>

		<ul class="subsubsub">
			<?php $current_page_base_url = 'admin.php?page='.$plugin_page.'&start_date='.$start_date.'&end_date='.$end_date; ?>
			<?php foreach ($types as $key=>$value) { ?>
			<li class="<?php echo $key?>"><a href="<?php echo admin_url($current_page_base_url.'&type='.$key)?>" <?php if($type == $key) {?> class="current"<?php } ?>><?php echo $value;?></a> |</li>
			<?php }?>
		</ul>

		<div style="clear:both;"></div>

		<?php

		$sql = "SELECT FROM_UNIXTIME(CreateTime, '%Y-%m-%d') as day, count(id) as total, {$sum} FROM {$weixin_messages_table} WHERE {$where} GROUP BY day ORDER BY day;";

		$counts = $wpdb->get_results($sql);

		$data = array();

		if($type == 'total'){	
			$morris_ykeys = array('total','text','event','subscribe','unsubscribe');

			$morris_labels = array();
			foreach ($morris_ykeys as $morris_ykey) {
				$morris_labels[] = $types[$morris_ykey];
			}

			foreach ($counts as $count) {
				$morris_data = '';
				foreach ($morris_ykeys as $morris_ykey) {
					$morris_data .= ', "'.$morris_ykey.'": '.$count->$morris_ykey;
				}
				$data []= '{"day": "'.$count->day.'"'.$morris_data.' }';
			}

			$morris_ykeys = "'".implode("','", $morris_ykeys)."'";
			$morris_labels = "'".implode("','", $morris_labels)."'";

		}else{
			$morris_ykeys = "'".$type."'";
			$morris_labels = "'".$types[$type]."'";

			foreach ($counts as $count) {
				$data []= '{"day": "'.$count->day.'"'.', "'.$type.'": '.$count->$type.' }';
			}
		}

		$data = "\n".implode(",\n", $data)."\n";

		?>
		
		<div id="daily-chart"></div>

		<script type="text/javascript">
			Morris.Line({
				element: 'daily-chart',
				data: [<?php echo $data;?>],
				xkey: 'day',
				ykeys: [<?php echo $morris_ykeys;?>],
				labels: [<?php echo $morris_labels;?>]
			});
		</script>
		
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th>日期</th>
				<?php foreach ($types as $key=>$value) {?>
				<th><?php echo $value;?></th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
		<?php $alternate = ''; ?>
		<?php foreach (array_reverse($counts) as $count) { $alternate = $alternate?'':'alternate';?>
			<tr class="<?php echo $alternate; ?>">
				<td><?php echo $count->day; ?></td>
				<?php foreach ($types as $key=>$value) {?>
				<td><?php echo $count->$key;?></td>
				<?php }?>
			</tr>
		<?php } ?>
		</tbody>
		</table>
		<?php
}

function weixin_robot_summary_page(){ 
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2>回复统计分析</h2>
		<?php 
		global $wpdb, $plugin_page;

		$start_date	= weixin_robot_stats_get_start_date();
		$end_date 	= weixin_robot_stats_get_end_date();
		$end_time	= $end_date.' 23:59:59';

		$response_types = weixin_robot_get_response_types();
		$response_type = isset($_GET['response_type'])?$_GET['response_type']:'total';

		$weixin_messages_table = weixin_robot_get_messages_table();

		$where = 'CreateTime > '.strtotime($start_date).' AND CreateTime < '.strtotime($end_time);

		weixin_robot_stats_header();

		$sql = "SELECT COUNT( * ) AS count, Response FROM {$weixin_messages_table} WHERE {$where} AND (MsgType ='text' OR MsgType = 'event') AND Event!='subscribe' AND Event!='unsubscribe' GROUP BY Response ORDER BY count DESC";

		$counts = $wpdb->get_results($sql);
		?>
		<div style="display:table;">

			<div style="display: table-row;">

				<div id="total-chart" style="display: table-cell; width:450px; float:left;"></div>

				<div style="display: table-cell; float:left; width:200px;">
					<table class="widefat" cellspacing="0">
						<thead>
							<tr>
								<th>回复类型</th>
								<th>数量</th>
							</tr>
						</thead>
						<tbody>
						<?php $data = array(); $i=0;?>
						<?php $alternate = '';?>
						<?php foreach ($counts as $count) { $alternate = $alternate?'':'alternate';?>
							<?php if($count->Response && isset($response_types[$count->Response])){?>
							<?php $data []= '{"label": "'.$response_types[$count->Response].'", "value": '.$count->count.' }'; $i ++; ?>
							<tr class="<?php echo $alternate;?>">
								<td><?php echo $response_types[$count->Response]; ?></td>
								<td><?php echo $count->count; ?></td>
							</tr>
							<?php }?>
						<?php } ?>
						<?php $data = "\n".implode(",\n", $data)."\n";?>
						</tbody>
					</table>
				</div>

			</div>

		</div>

		<script type="text/javascript">
			Morris.Donut({
			  element: 'total-chart',
			  data: [<?php echo $data;?>]
			});
		</script>

		<div style="clear:both;"></div>

		<h3>详细回复统计分析</h3>

		<ul class="subsubsub">
			<?php $current_page_base_url = 'admin.php?page='.$plugin_page.'&start_date='.$start_date.'&end_date='.$end_date; ?>
			<li class="<?php echo 'total'?>"><a href="<?php echo admin_url($current_page_base_url.'&response_type=total')?>" <?php if($response_type == 'total') {?> class="current"<?php } ?>>全部</a> |</li>
			<?php foreach ($counts as $count) { ?>
			<?php if($count->Response && isset($response_types[$count->Response])){?>
			<li class="<?php echo $count->Response;?>"><a href="<?php echo admin_url($current_page_base_url.'&response_type='.$count->Response)?>" <?php if($response_type == $count->Response) {?> class="current"<?php } ?>><?php echo $response_types[$count->Response];?></a> |</li>
			<?php } ?>
			<?php }?>
		</ul>

		<?php

		if($response_type == 'total'){
			$where .= " AND Response != ''";
		}else{
			$where .= " AND Response = '{$response_type}'";
		}

		$sql = "SELECT COUNT( * ) AS count, Response, MsgType, (case when Content='' then EventKey else Content end) as Content FROM {$weixin_messages_table} WHERE {$where} AND ( MsgType ='text' OR (MsgType = 'event'  AND Event!='subscribe' AND Event!='unsubscribe')) GROUP BY LOWER(Content) ORDER BY count DESC LIMIT 0 , 100";

		$sql = "SELECT COUNT( * ) AS count, Response, MsgType, Content FROM ( SELECT Response, MsgType, LOWER(Content) as Content FROM {$weixin_messages_table} WHERE {$where} AND MsgType ='text' UNION ALL SELECT Response, MsgType,  LOWER(EventKey) as Content FROM {$weixin_messages_table} WHERE {$where} AND MsgType = 'event'  AND Event!='subscribe' AND Event!='unsubscribe') as T1 GROUP BY Content ORDER BY count DESC LIMIT 0 , 100";

		
		$weixin_hot_messages = $wpdb->get_results($sql);
		?>
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th style="width:60px">排名</th>
				<th style="width:80px">数量</th>
				<th>关键词</th>
				<th style="width:150px">回复类型</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		$alternate = '';
		foreach ($weixin_hot_messages as $weixin_message) {
			if(isset($response_types[$weixin_message->Response])){
			$alternate = $alternate?'':'alternate';
			$i++;
		?>
			<tr class="<?php echo $alternate; ?>">
				<td><?php echo $i; ?></td>
				<td><?php echo $weixin_message->count; ?></td>
				<td><?php echo $weixin_message->Content; ?></td>
				<td><?php echo $response_types[$weixin_message->Response]; ?></td>
			</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
		</table>
	<?php
}

function weixin_robot_messages_page() {
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2>最新消息</h2>
		<p>下面是你公众号上最新的消息，你可以直接删除（WordPress 本地删除，公众号后台不受影响）！</p>

		<?php
		global $wpdb,$plugin_page;

		if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			if ( !wp_verify_nonce($_POST['weixin_robot_send_user_nonce'],'weixin_robot') ){
				ob_clean();
				wp_die('非法操作');
			}

			$weixin_openid	= stripslashes( trim( $_POST['weixin_openid'] ));
			$reply_id 		= stripslashes( trim( $_POST['reply_id'] ));
			$content 		= stripslashes( trim( $_POST['content'] ));

			$data = array(
				'MsgType'		=> 'manual',
				'FromUserName'	=> $weixin_openid,
				'CreateTime'	=> current_time('timestamp')-8*2400,
				'Content'		=> $content,
			);

			$weixin_messages_table = weixin_robot_get_messages_table();
			$insert_id = $wpdb->insert($weixin_messages_table,$data); 

			$wpdb->update($weixin_messages_table, array('Response'=>$wpdb->insert_id),array('id'=>$reply_id));

			$succeed_msg = weixin_rebot_sent_user($weixin_openid, $content);
		}

		$response_types = weixin_robot_get_response_types();

		$types = weixin_robot_stats_get_types();
		unset($types['subscribe']);
		unset($types['unsubscribe']);

		$types['manual'] = '需要人工回复';

		$type = weixin_robot_stats_get_type();
		if(!$type){
			$type = 'total';
		}

		$Response =  isset($_REQUEST['Response'])?$_REQUEST['Response']:'';

		$weixin_messages_table = weixin_robot_get_messages_table();

		if(isset($_GET['delete']) && isset($_GET['id']) && $_GET['id']){
			$wpdb->query("DELETE FROM $weixin_messages_table WHERE id = {$_GET['id']}");
		}

		$current_page 		= isset($_GET['paged'])?$_GET['paged']:1;
		$number_per_page	= 100;
		$start_count		= ($current_page-1)*$number_per_page;
		$limit 				= 'LIMIT '.$start_count.','.$number_per_page;

		if($type =='total'){
			$where = '';
		}elseif($type == 'manual'){
			$where = "AND Response in('not-found','too-long')";
		}else{
			$where = "AND MsgType = '{$type}'";					
		}

		if(isset($_GET['openid'])){
			$where = "AND FromUserName = '{$_GET['openid']}'";	
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$weixin_messages_table} WHERE 1=1 {$where} AND MsgType != 'manual' AND Event!= 'subscribe' AND Event != 'unsubscribe'  ORDER BY CreateTime DESC ".$limit;

		$weixin_messages = $wpdb->get_results($sql);

		$total_count	= $wpdb->get_var("SELECT FOUND_ROWS();");

		$weixin_messages_parts = array();
		foreach($weixin_messages as $weixin_message){ 
			$weixin_messages_parts[$weixin_message->FromUserName][] = $weixin_message;
		}
		?>

		<?php if(!empty($succeed_msg)){?>
		<div class="updated">
			<p><?php echo $succeed_msg;?></p>
		</div>
		<?php }?>

		<ul class="subsubsub">
		<?php foreach ($types as $key=>$value) { ?>
			<li class="<?php echo $key;?>"><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&type='.$key)?>" <?php if($type == $key) {?> class="current"<?php } ?>><?php echo $value;?></a> |</li>
		<?php }?>
		<?php /*	<li class="not-found"><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&Response=not-found')?>" <?php if($Response == 'need-manual') {?> class="current"<?php } ?>>需要回复</a></li>*/?>
		</ul>
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<?php if(weixin_robot_get_setting('weixin_advanced_api') && strpos($weixin_messages_table, 'weixin')){?>
				<th>用户</th>
				<th style="width:120px;">详细信息</th>
				<?php } ?>
				<th style="width:120px;">时间</th>
				<th style="width:60px;">类型</th>
				<th style="min-width:300px;">内容</th>
				<th style="width:100px;">回复类型</th>
				<th style="width:40px;">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		//if(!function_exists('bfi_thumb')){
		//	require_once( WEIXIN_ROBOT_PLUGIN_DIR.'/include/BFI_Thumb.php' );
		//}
		
		$alternate = '';
		foreach ($weixin_messages_parts as $weixin_openid => $weixin_messages_part) {
			if(weixin_robot_get_setting('weixin_advanced_api') && strpos($weixin_messages_table, 'weixin')){

				$weixin_user = weixin_robot_get_user($weixin_openid);
				$weixin_user_avatar = '';
				if(!empty($weixin_user['headimgurl'])){
					$weixin_user_avatar = WEIXIN_ROBOT_PLUGIN_URL.'/include/timthumb.php?src='.$weixin_user['headimgurl'];
				}
			}

			$counter = 0;
			$total = count($weixin_messages_part)+1;

			foreach ($weixin_messages_part as $weixin_message) { $counter++; $MsgType = $weixin_message->MsgType; $alternate = $alternate?'':'alternate';?>
			<tr id="<?php echo $weixin_message->id;?>" class="<?php echo $alternate; ?>">
				<?php if(weixin_robot_get_setting('weixin_advanced_api') && strpos($weixin_messages_table, 'weixin')){?>
				<?php if($counter == 1){ ?>
				<?php if($weixin_user['subscribe']){ ?>
				<td <?php if($total > 1) echo 'rowspan="'.$total.'"';?>>
				<?php if($weixin_user_avatar) {?>
					<a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&openid='.$weixin_openid)?>"><img src="<?php echo $weixin_user_avatar; ?>" width="32" /></a>
				<?php }?>
				</td>
				<td <?php if($total > 1) echo 'rowspan="'.$total.'"';?>>
					<?php echo $weixin_user['nickname']; ?><br />
					性别：<?php echo $weixin_user['sex']?'男':'女';?><br />
					地址：<?php echo $weixin_user['country'].' '.$weixin_user['province'].' '.$weixin_user['city'];?><br />
				</td>
				<?php } else{ ?>
				<td colspan="2" <?php if($total > 1) echo 'rowspan="'.$total.'"';?>>
					<span style="color:red;">*已经取消关注</span>
				</td>
				<?php } ?>
				<?php } ?>
				<?php } ?>
				<td><?php echo date('Y-m-d H:i:s',$weixin_message->CreateTime+8*60*60); ?></td>
				<td><?php echo $types[$MsgType]; ?></td>
				<td>
				<?php
				if($MsgType == 'text'){
					echo $weixin_message->Content; 
				}elseif($MsgType == 'image'){
					echo '<a href="'.$weixin_message->PicUrl.'" target="_blank"><img src="'.$weixin_message->PicUrl.'" width="100px;"></a>';
				}elseif($MsgType == 'location'){
					echo '<a href="http://ditu.google.cn/maps?q='.urlencode($weixin_message->label).'&amp;ll='.$weixin_message->Location_X.','.$weixin_message->Location_Y.'&amp;source=embed" target="_blank">'.$weixin_message->label.'</a>';
				}elseif($MsgType == 'event'){
					echo '['.$weixin_message->Event.'] '.$weixin_message->EventKey; 
				}elseif($MsgType == 'voice'){
					if($weixin_message->Recognition){
						echo '语音识别成：';
						echo $weixin_message->Recognition;
					}else{
						echo '未识别';
					}
				}else{
					echo $MsgType;
					echo '该类型的内容无法显示，请直接访问微信公众号后台进行操作！';
				}
				if(is_numeric($weixin_message->Response)){
					$weixin_reply_message = weixin_robot_get_message($weixin_message->Response);
					echo '<br /><span style="background-color:yellow; padding:2px; ">人工回复：'.$weixin_reply_message->Content.'</span>';
				}
				?>
				</td>
				<td>
					<?php 
					if(is_numeric($weixin_message->Response) ){
						echo '人工回复';
					}elseif(isset($response_types[$weixin_message->Response])){
						echo $response_types[$weixin_message->Response];	
					}
					?>
				</td>
				<td>
				<?php if(weixin_robot_get_setting('weixin_advanced_api') && strpos($weixin_messages_table, 'weixin') && (current_time('timestamp')-$weixin_message->CreateTime < (24+8)*3600) ){?>
					<?php if(is_numeric($weixin_message->Response)){ ?>
					<span>已经回复</span>
					<?php } elseif($weixin_user['subscribe']){ ?>
					<span class="reply"><a href="javascript:;" onclick="reply_to_weixin('<?php echo $weixin_openid; ?>', '<?php echo $weixin_message->id; ?>')">回复</a></span>
					<?php } ?>
					
				<?php } else {?>
					<span class="delete"><a href="<?php echo admin_url('admin.php?page=weixin-robot-messages&delete&id='.$weixin_message->id); ?>">删除</a></span>
				<?php } ?>
				</td>
			</tr>
			<?php } ?>
			<?php if(weixin_robot_get_setting('weixin_advanced_api') && strpos($weixin_messages_table, 'weixin')){?>
			<tr id='tr_<?php echo $weixin_openid?>'>
				<td colspan="5" id="td_<?php echo $weixin_openid?>">
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
		</table>
		<?php wpjam_admin_pagenavi($total_count,$number_per_page); ?>
		<?php if(weixin_robot_get_setting('weixin_advanced_api') && strpos($weixin_messages_table, 'weixin')){?>
		<form id="reply_form" style="display:none;" action="<?php echo admin_url('admin.php?page='.$plugin_page); ?>" method="POST">
			<p>
				<textarea name="content" id="content" rows="5" class="large-text code"></textarea>
			</p>
			<?php /*<p>
				<select name="reply_type" id="reply_type" >
					<option value="img">图文回复</option>
					<option value="text">文本回复</option>
				</select>
			</p>*/?>
			<input type="hidden" name="weixin_openid" id="weixin_openid" value="" />
			<input type="hidden" name="reply_id" id="reply_id" value="" />
			<?php wp_nonce_field('weixin_robot','weixin_robot_send_user_nonce'); ?>
			<p>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="回复用户">
			</p>
		</form>
		<script type="text/javascript">
			function reply_to_weixin(weixin_openid, id){
				jQuery('input#weixin_openid')[0].value = weixin_openid;
				jQuery('input#reply_id')[0].value = id;
				jQuery('#td_'+weixin_openid).append(jQuery('#reply_form'));
				//jQuery('tr#'+id).append(jQuery('#reply_form'));
				jQuery('tr#'+id).after(jQuery('#tr_'+weixin_openid));
				jQuery('#reply_form').show();
			}
		</script>
		<?php } ?>
		
<?php } ?>