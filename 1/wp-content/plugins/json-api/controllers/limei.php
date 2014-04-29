
<?php
/*
   The follows is all the operation of the points.

/**
* ini例子:
* [db]
name = test
host = localhost

 */
//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//var $root1='/opt/appstack/apache2/htdocs/mobile/install/';
class JSON_API_Limei_Controller{


	public function limeiRequestPoint($uid1, $aid1, $adt1, $mac1, $udid1) {
		$url = "http://api.immob.cn/score/getscore?adu=5f9f2cc22c061b455d1437d341d4949b&uid="
			. $mac1."_".$udid1."_".$uid1
			//. "&aid=".$aid1
			. "&adt=".$adt1
			. "&pv=2.3";

		$json_ret = file_get_contents($url);
		$result = json_decode(trim($json_ret));
		$num = count($result);
		$resultArray = $result->data;

		$points= $result['data']->point;
		if( $points >= 0 ){
				update_user_meta($userid, 'limei_real_points', $points);
				$cur_points = get_user_meta($userid, 'limei_total_points', true );
				if( $points > $cur_points )
					update_user_meta($userid, 'limei_total_points', $points);
		}
		
		
	}
	public function limei_user() {
		global $json_api;
		global $wpdb;
		global $mycred;
		
		$adt = $_GET['ifa'];
		$mac = $_GET['mac'];
		$udid = $_GET['oid'];
		$userid = $_GET['userid'];
		$exist_users = get_user_by('id', $userid);

		$user_is_empty = empty($exist_users);

		if(!$user_is_empty){
			$limei_total_points = get_user_meta($userid, 'limei_total_points', true);
			$limei_real_points = get_user_meta($userid, 'limei_real_points', true);
			if(   empty( $limei_total_points ) )
			{
				update_user_meta($userid, 'limei_total_points', 0);
				update_user_meta($userid, 'limei_real_points', 0);
				return array("status" => "err" , "msg" => "first added" );
			}
			limeiRequestPoint($userid, '', $adt, $mac, $udid );
			return array("msg" =>  "ok"
			    );
		}
				return array("status" => "err" , "msg" => "nothing added" );

	}
}


?>
