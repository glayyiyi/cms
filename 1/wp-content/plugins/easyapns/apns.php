<?php
#################################################################################
## Developed by Manifest Interactive, LLC                                      ##
## http://www.manifestinteractive.com                                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
##                                                                             ##
## THIS SOFTWARE IS PROVIDED BY MANIFEST INTERACTIVE 'AS IS' AND ANY           ##
## EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE         ##
## IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR          ##
## PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL MANIFEST INTERACTIVE BE          ##
## LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR         ##
## CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF        ##
## SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR             ##
## BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,       ##
## WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE        ##
## OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,           ##
## EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
## Authors of file: Peter Schmalfeldt & John Kramlich                          ##
#################################################################################

/**
 * @category Apple Push Notification Service using PHP & MySQL
 * @package EasyAPNs
 * @author Peter Schmalfeldt <manifestinteractive@gmail.com>
 * @author John Kramlich <me@johnkramlich.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link http://code.google.com/p/easyapns/
 */

/*
	Plugin Name: EasyAPNs
	Plugin URI: http://www.easyapns.com
	Description: Adaptation of the Easy APNS open source project for WordPress. EasyAPNs is an Apple Push Notification Service, developed by <strong>John Kramlich</strong> and <strong>JPeter Schmalfeldt</strong>.
	Author: Ludovic LECERF
	Version: 0.94
	Author URI: http://www.ludoviclecerf.com/blog/2011/04/18/apple-easyapns-pour-wordpress-un-plugin-de-notifications-push-sur-iphone-ipad
	License: Apache License, Version 2.0
*/

if (!defined('EASYAPNS_VERSION')) define('EASYAPNS_VERSION', '0.94');
if (!defined('EASYAPNS_PLUGIN_URL')) define('EASYAPNS_PLUGIN_URL', plugin_dir_url( __FILE__ ));

global $wpdb;

$table_name = $wpdb->prefix . "apns_messages";
$installed_ver = get_option( "easyapns_version" );
	
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name OR $installed_ver != EASYAPNS_VERSION) {
	include 'sql/apns_wp.php';
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	update_option("easyapns_version", EASYAPNS_VERSION);
}

function get_real_time() {
	$addHours = get_option('easyapns_serverstime');
	if ($addHours) {
		$operator = substr(trim($addHours), 0, 1);
		$addHours = intval(substr(trim($addHours), 1, strlen($addHours)))*60*60;
		if ($operator == '-') $addHours = -1*$addHours;
	} else {
		$addHours = 0;
	}
	return $addHours;
}

function processQueue_activation($oncetime = false) {	
	if (!$oncetime) {
		wp_schedule_event(time()+10, 'hourly', 'processQueue_hourly');
	} else {
		wp_schedule_single_event($realTime+10, 'processQueue_hourly');
	}
}

function processQueue_deactivation() {
	wp_clear_scheduled_hook('processQueue_hourly');
}

function processQueue_action() {
	require_once('php/classes/class_APNS.php');
	require_once('php/classes/class_DbConnect.php');
	$db = new DbConnect();
	$db->show_errors();
	$args = array('task' => null);
	$datas = array(
		'sandboxCertificate' => get_option('easyapns_devpempath'),
		'certificate' => get_option('easyapns_prodpempath'),
		'logPath' => get_option('easyapns_logpath'),
		'logMaxSize' => get_option('easyapns_logmaxsize')
	);
	$apns = new APNS($db, $args, null, null, $datas);
	$apns->processQueue();	
}

function easyapns_admin_init() {
    
    if ( get_option('easyapns_appname') == null ) {
        
        function easyapns_appname_warning() {
            echo "<div id='easyapns-warning' class='updated fade'><p><strong>".sprintf(__('EasyAPNS %s needs to be configured.', 'easyapns_context'), EASYAPNS_VERSION) ."</strong> ".sprintf(__('Please go to <a href="%s">EasyAPNS admin menu</a> to configure your app name.', 'easyapns_context'), get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config')."</p></div>";
        }
        add_action('admin_notices', 'easyapns_appname_warning'); 
        
        return; 
    } else {
    	add_meta_box( 
	        'easyapns_id',
	        __( 'Push Notifications', 'easyapns_context' ),
	        'easyapns_boxcontent',
	        'post',
	        'side',
	        'high'
	    );
    }
}

function easyapns_boxcontent() {

  wp_nonce_field( plugin_basename(__FILE__), 'easyapns_nonce' );
  
  $selected = '';
  if (get_option('easyapns_processallpostsbydefault') == 'true') $selected = ' selected="selected"';

  echo '<label for="easyapns_activate">';
       _e("Activate Apple Push Notifications for this post ? ", 'easyapns_context' );
  echo '</label><br/><br/>';
  echo '<select id="easyapns_activate" name="easyapns_activate"><option value="0">'.__("No", 'easyapns_context' ).'</option><option value="1"'.$selected.'>'.__("Yes", 'easyapns_context' ).'</option></select>';
}

function apns_start($post_ID) {

	global $wpdb;
	
	$appname = get_option('easyapns_appname');
	
	if ( !wp_verify_nonce( $_POST['easyapns_nonce'], plugin_basename(__FILE__) ) OR !intval($_POST['easyapns_activate']) OR !$appname)
		return $post_ID;

	// AUTOLOAD CLASS OBJECTS... YOU CAN USE INCLUDES IF YOU PREFER
	require_once('php/classes/class_APNS.php');
	require_once('php/classes/class_DbConnect.php');
	
	// CREATE DATABASE OBJECT ( MAKE SURE TO CHANGE LOGIN INFO IN CLASS FILE )
	$db = new DbConnect();
	$db->show_errors();
	
	// FETCH $_GET OR CRON ARGUMENTS TO AUTOMATE TASKS
	$args = (!empty($_GET)) ? $_GET:array('task'=>$argv[1]);
	
	// CREATE APNS OBJECT, WITH DATABASE OBJECT AND ARGUMENTS
	$datas = array(
		'sandboxCertificate' => get_option('easyapns_devpempath'),
		'certificate' => get_option('easyapns_prodpempath'),
		'logPath' => get_option('easyapns_logpath'),
		'logMaxSize' => get_option('easyapns_logmaxsize')
	);
	$apns = new APNS($db, $args, null, null, $datas);
	
        $sql = "SELECT `".$wpdb->prefix."apns_devices`.`pid` FROM `".$wpdb->prefix."apns_devices` WHERE `".$wpdb->prefix."apns_devices`.`appname` = '$appname'";
        $query = $wpdb->get_results($sql, OBJECT);

        if($query)
        {
            foreach ($query as $object)
            {
                $id = intval($object->pid);
                $apns->newMessage($id);
                $apns->addMessageBadge(1);
                $apns->addMessageAlert(substr(get_the_title($post_ID), 0, 255));
                $apns->addMessageCustom('post_ID', $post_ID);
                $apns->queueMessage();                
            }
            if (get_option('easyapns_processqueueafterpost') == 'true') processQueue_activation(true);
        }
        
    return $post_ID;
}

if (is_admin()) {
	register_activation_hook(__FILE__, 'processQueue_activation');
	register_deactivation_hook(__FILE__, 'processQueue_deactivation');
	add_action('publish_post', 'apns_start');
	add_action('admin_init', 'easyapns_admin_init', 1);
	add_filter('query_vars', 'easyapns_query_vars');
	add_action('admin_menu', 'easyapns_config_page');
}

add_action('parse_request', 'easyapns_parse_request');

/* REGISTER A DEVICE */

function easyapns_parse_request($wp) {
    // only process requests with "index.php?easyapns=register"
    if (isset($_GET['easyapns'])
            && $_GET['easyapns'] == 'register') {
		
		require_once('php/classes/class_APNS.php');
		require_once('php/classes/class_DbConnect.php');
		
		$db = new DbConnect();
		$args = (!empty($_GET)) ? $_GET : array('task' => null);
		$datas = array(
			'sandboxCertificate' => get_option('easyapns_devpempath'),
			'certificate' => get_option('easyapns_prodpempath'),
			'logPath' => get_option('easyapns_logpath'),
			'logMaxSize' => get_option('easyapns_logmaxsize')
		);
		$apns = new APNS($db, $args, null, null, $datas);
        wp_die('Device registered!', __('Success'));
    }
}

function easyapns_query_vars($vars) {
    $vars[] = 'easyapns';
    $vars[] = 'task';
    $vars[] = 'appname';
    $vars[] = 'appversion';
	$vars[] = 'deviceuid';
    $vars[] = 'devicetoken';
	$vars[] = 'devicemodel';
    $vars[] = 'devicename';
    $vars[] = 'deviceversion';
    $vars[] = 'pushbadge';
    $vars[] = 'pushalert';
	$vars[] = 'pushsound';

    return $vars;
}

/* ADMIN SECTION */

function easyapns_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('options-general.php', __("EasyAPNs", 'easyapns_context'), __("EasyAPNs", 'easyapns_context'), 'manage_options', 'easyapns-config', 'easyapns_config');
}

function easyapns_config() {
	
	// Post Action
	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

		if ( isset( $_POST['easyapns_appname'] ) )
				update_option( 'easyapns_appname', $_POST['easyapns_appname'] );
				
		if ( isset( $_POST['easyapns_devpempath'] ) )
				update_option( 'easyapns_devpempath', $_POST['easyapns_devpempath'] );
				
		if ( isset( $_POST['easyapns_logpath'] ) )
				update_option( 'easyapns_logpath', $_POST['easyapns_logpath'] );
				
		if ( isset( $_POST['easyapns_logmaxsize'] ) )
				update_option( 'easyapns_logmaxsize', $_POST['easyapns_logmaxsize'] );
				
		if ( isset( $_POST['easyapns_prodpempath'] ) )
				update_option( 'easyapns_prodpempath', $_POST['easyapns_prodpempath'] );
				
		if ( isset( $_POST['easyapns_serverstime'] ) AND $_POST['easyapns_serverstime'] != get_option('easyapns_serverstime') AND (strstr($_POST['easyapns_serverstime'], '+') OR strstr($_POST['easyapns_serverstime'], '-'))) {				
				update_option( 'easyapns_serverstime', $_POST['easyapns_serverstime'] );
				processQueue_deactivation();
				processQueue_activation();
		}
	
		if ( isset( $_POST['easyapns_processqueueafterpost'] ) ) {
				update_option( 'easyapns_processqueueafterpost', 'true' );
		} else {
				update_option( 'easyapns_processqueueafterpost', 'false' );
		}
		if ( isset( $_POST['easyapns_processallpostsbydefault'] ) ) {
				update_option( 'easyapns_processallpostsbydefault', 'true' );
		} else {
				update_option( 'easyapns_processallpostsbydefault', 'false' );
		}
		
	}
			
	// Default Options
	if ( !get_option('easyapns_devpempath') ) update_option( 'easyapns_devpempath', '/usr/local/apns/apns-dev.pem' );
	if ( !get_option('easyapns_prodpempath') ) update_option( 'easyapns_prodpempath', '/usr/local/apns/apns.pem' );
	if ( !get_option('easyapns_logpath') ) update_option( 'easyapns_logpath', '/usr/local/apns/apns.log' );
	if ( !get_option('easyapns_logmaxsize') ) update_option( 'easyapns_logmaxsize', 1048576 );
	if ( !get_option('easyapns_serverstime') ) update_option( 'easyapns_serverstime', '+0' );
	if ( !get_option('easyapns_processqueueafterpost') ) update_option( 'easyapns_processqueueafterpost', 'false' );
	if ( !get_option('easyapns_processallpostsbydefault') ) update_option( 'easyapns_processallpostsbydefault', 'true' );
	
	global $wpdb;

	if ((isset($_GET['deviceid']) && !empty($_GET['deviceid'])) && (isset($_GET['dev']) && !empty($_GET['dev']))) {
		
		if ($_GET['dev'] == 'production') {
			$_GET['dev'] = 'sandbox';
		} else {
			$_GET['dev'] = 'production';
		}
		
		if ($wpdb->query("UPDATE {$wpdb->prefix}apns_devices SET development = '{$_GET['dev']}' WHERE pid = {$_GET['deviceid']};")) {
			echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.').'</strong></p></div>';
		}
	}
	
	$orderby = 'pid';
	if (isset($_GET['orderby']) && !empty($_GET['orderby'])) {
		$orderby = addslashes(strip_tags($_GET['orderby']));
	}
	
	$order = 'ASC';
	if (isset($_GET['order']) && !empty($_GET['order'])) {
		$order = addslashes(strip_tags($_GET['order']));
	}

	$devices = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}apns_devices ORDER BY $orderby $order;", OBJECT);
			
	?>
	
	<?php if ( !empty($_POST['submit'] ) ) : ?>
		<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
	<?php endif; ?>
	<style type="text/css">#easyapns-config input {width:300px;margin-right:10px;}</style>
	<div class="wrap">
		<h2><?php _e("EasyAPNs Configuration", 'easyapns_context'); ?></h2>
		<p><?php _e("When a post is published, the Payload is added to the queue. Every hour, the Payload will be sent to Apple Devices that were registered in the database. You don't need to configure a cron job, a WP-Cron is already active.", 'easyapns_context'); ?></p>
		<p><strong><?php _e("Note : Your iPhone AppDelegate.m need to register for remote notifications with an url. This url must have the GET parameter (easyapns=register), it will end up looking something like:", 'easyapns_context'); ?></strong>
		<pre style="width:100%;word-wrap:break-word;overflow:auto;">http://www.yourWordpressSite.com/index.php?easyapns=register&task=register&appname=transeet&appversion=1.0&deviceuid=e48884a7a1fef6977cee39a3f75849be250f20e9&devicetoken=9c7508139bd468e8064fa01eab50875533427d84ec5055ce2c478de3fdgafd15&devicemodel=iPhone&devicename=tester&deviceversion=1.5&pushbadge=enabled&pushalert=enabled&pushsound=enabled</pre></p>
		<p>Look at:</p>
		<ul>
			<li>1. <a href="<?php echo EASYAPNS_PLUGIN_URL.'delegate/Delegate.m'; ?>" target="_blank">Delegate.m example for XCode</a></li>
			<li>2. <a href="<?php echo EASYAPNS_PLUGIN_URL.'delegate/AppCelerator.js'; ?>" target="_blank">App.js example for AppCelerator</a></li>
		</ul><br/>
		<p>To use the <strong>development certificate</strong>, you need to put the value `sandbox` in front of your dev device in the `wp_apns_devices`.`development` table,<br/><strong>OR</strong> you can check your device in the table at bottom of this page.</p>
		<br/><hr/>
		<h3>Configuration</h3>
		<form action="" method="post" id="easyapns-config">
			<p><label><input name="easyapns_appname" id="easyapns_appname" type="text" value="<?php if ( get_option('easyapns_appname') ) echo get_option('easyapns_appname'); ?>" /> <?php _e('Your Application Name <em>(examples: com.company.blog or blogcompany)</em>', 'easyapns_context'); ?></label></p>
			<p><label><input name="easyapns_prodpempath" id="easyapns_prodpempath" type="text" value="<?php if ( get_option('easyapns_prodpempath') ) echo get_option('easyapns_prodpempath'); ?>" /> <?php _e('Absolute path to your Production Certificate (.pem file)', 'easyapns_context'); ?></label></p>
			<p><label><input name="easyapns_devpempath" id="easyapns_devpempath" type="text" value="<?php if ( get_option('easyapns_devpempath') ) echo get_option('easyapns_devpempath'); ?>" /> <?php _e('Absolute path to your Development Certificate (.pem file)', 'easyapns_context'); ?></label></p>
			<p><label><input name="easyapns_logpath" id="easyapns_logpath" type="text" value="<?php if ( get_option('easyapns_logpath') ) echo get_option('easyapns_logpath'); ?>" /> <?php _e('Absolute path to your log file (for APNS errors)', 'easyapns_context'); ?></label></p>
			<p><label><input name="easyapns_logmaxsize" id="easyapns_logmaxsize" type="text" value="<?php if ( get_option('easyapns_logmaxsize') ) echo get_option('easyapns_logmaxsize'); ?>" /> <?php _e('Max files size of log before it is truncated. 1048576 = 1MB.', 'easyapns_context'); ?></label></p>
			<p><label><input name="easyapns_processqueueafterpost" id="easyapns_processqueueafterpost" type="checkbox" style="width:auto;" value="1"<?php if ( get_option('easyapns_processqueueafterpost') == 'true') echo ' checked="checked"'; ?> /> <?php _e('Process queue directly when a post is published (you just need to activate the Push Notifications in the Right Panel of the post editor). If the box is unchecked, the queue will be processed hourly, but not directly after the post publication.', 'easyapns_context'); ?></label></p>
			<p><label><input name="easyapns_processallpostsbydefault" id="easyapns_processallpostsbydefault" type="checkbox" style="width:auto;" value="1"<?php if ( get_option('easyapns_processallpostsbydefault') == 'true') echo ' checked="checked"'; ?> /> <?php _e('If this box is checked, the Push Notifications are activated for all posts (in the Post Editor panel).', 'easyapns_context'); ?></label></p>
			<div style="background-color:#EEE;border-color:#DDD;padding:5px 10px;font-size:10px;">
				<p><em style="font-size:12px;">Server's Time : <?php echo date(get_option('date_format').' '.get_option('time_format').' s \s', strtotime(get_option('easyapns_serverstime').' hours')); ?></em>&nbsp;&nbsp;&nbsp;<label style="font-size:12px"><input name="easyapns_serverstime" id="easyapns_serverstime" type="text" style="width:30px" value="<?php if ( get_option('easyapns_serverstime') ) echo get_option('easyapns_serverstime'); ?>" /> <?php _e('Add / remove hour(s) to adjust the hour <em>(example: +1, -2)</em>.', 'easyapns_context'); ?></label>
				<br/><em style="font-size:12px;">Next Scheduled : <?php $nextSchedule = wp_next_scheduled('processQueue_hourly')+get_real_time(); echo date(get_option('date_format').' '.get_option('time_format').' s \s', $nextSchedule); ?></em><br/></p>
			</div>
			<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>
		</form>
		<br/><hr/>
		<h3>Devices</h3>
		<?php if (!empty($devices)) : ?>
		<table class="wp-list-table widefat fixed users" cellspacing="0" id="tabledevices">
			<thead>
				<tr>
					<th scope="col" id="id" class="column-id sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'pid' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="width: 50px;">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=pid&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'pid' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Id</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="username" class="manage-column column-username sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'appname' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="width:100px">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=appname&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'appname' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>App Name</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="username" class="manage-column column-username sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'deviceuid' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=deviceuid&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'deviceuid' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Device UID</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="name" class="manage-column column-name sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'devicename' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=devicename&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'devicename' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Device Name</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="name" class="manage-column column-name sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'devicemodel' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="width:120px">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=devicemodel&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'devicemodel' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Device Model</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="name" class="manage-column column-name sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'deviceversion' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="width:140px">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=deviceversion&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'deviceversion' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Device Version</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="name" class="manage-column column-name sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'development' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=development&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'development' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Development</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="name" class="manage-column column-name sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'status' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="width:80px">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=status&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'status' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Status</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="name" class="manage-column column-name sortable <?php $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'created' && $_GET['order'] == 'DESC')?'asc':'desc'; echo $order; ?>" style="">
						<a href="<?php echo get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&orderby=created&amp;order='; $order = (isset($_GET['orderby']) && $_GET['orderby'] == 'created' && $_GET['order'] == 'DESC')?'ASC':'DESC'; echo $order.'#tabledevices'; ?>"><span>Created</span><span class="sorting-indicator"></span></a>
					</th>
				</tr>
			</thead>
			<tbody id="the-list" class="list:user">
				<?php foreach ($devices as $device) : ?>
				<tr id="user-1" class="alternate">
					<td><?php echo $device->pid; ?></td>
					<td><?php echo $device->appname; ?></td>
					<td><?php echo $device->deviceuid; ?></td>
					<td><?php echo $device->devicename; ?></td>
					<td><?php echo $device->devicemodel; ?></td>
					<td><?php echo $device->deviceversion; ?></td>
					<td><?php echo $device->development; ?> - <?php if ($device->development != 'sandbox') : echo '<a href="'.get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&deviceid='.$device->pid.'&dev='.$device->development.'">Use for dev</a>'; else : echo '<a href="'.get_bloginfo('url').'/wp-admin/options-general.php?page=easyapns-config&deviceid='.$device->pid.'&dev='.$device->development.'">Use for prod</a>'; endif; ?></td>
					<td><?php echo $device->status; ?></td>
					<td><?php echo $device->created; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : echo '<p>No registered device.</p>'; endif; ?>
		<br/><hr/>
		<h3>Any problem ?</h3>
		<p>If you have a problem, just put a comment on the french blog : <a href="http://www.ludoviclecerf.com/blog/2011/04/18/apple-easyapns-pour-wordpress-un-plugin-de-notifications-push-sur-iphone-ipad" target="_blank">Ludovic LECERF</a>.</p>
	</div>
	
	<?php
}

?>