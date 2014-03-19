<?php
/**
 * Template Name: Register Device
 * Description: The Push Notifications Page template
 */
 ?>
<?php

require_once('wp-config.php');

global $wpdb;
$apns_devices = $wpdb->prefix.'pn_apns_devices';
$apns_messages = $wpdb->prefix.'pn_apns_messages';

if ( 
	isset($_GET['task'])&&
	isset($_GET['appname']) && 
	isset($_GET['appversion']) && 
	isset($_GET['deviceuid']) &&
	isset($_GET['devicetoken']) &&
	isset($_GET['devicename']) &&
	isset($_GET['devicemodel']) && 
	isset($_GET['deviceversion']) &&
	isset($_GET['pushbadge']) && 
	isset($_GET['pushalert']) && 
	isset($_GET['pushsound'])

	){

	if ( $_GET['task'] == 'register'){

		$wpdb->replace( 
				$apns_devices, 
				array( 
					'appname'       =>  $_GET['appname'],
					'appversion'    =>  $_GET['appversion'],
					'deviceuid'     =>  $_GET['deviceuid'],
					'devicetoken'   =>  $_GET['devicetoken'],
					'devicename'    =>  $_GET['devicename'],
					'devicemodel'   =>  $_GET['devicemodel'],
					'deviceversion' =>  $_GET['deviceversion'],
					'pushbadge'     =>  $_GET['pushbadge'],
					'pushalert'     =>  $_GET['pushalert'],
					'pushsound'     =>  $_GET['pushsound']
				), 
				array( 
					'%s', 
					'%s', 
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s', 
					'%s', 
					'%s', 
					'%s'
				) 
			);

	}

}else{

	echo "Where are we? :/";

}

?>
