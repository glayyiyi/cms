
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
class JSON_API_Version_Controller{

		//echo 'INI: ' . $settings->get('db.host') . '';

	public function checkIOSVersion(){
		global $json_api;
		$ver = $_GET['version'] ;

		$settings = new Settings_INI;
		$settings->load('/opt/appstack/apache2/htdocs/mobile/install/appconfig.ini'); 
		$newVer = $settings->get('ios.version');
		if( !empty( $newVer )  && $newVer > $ver ){
			return array( "status" => "ok", "version" => $newVer );
		}
		return array( "status" => "error", "msg" => "no new version!");
	}

}



class Settings{
	var $_settings = array();
/**
    * 获取某些设置的值
    *
    * @param unknown_type $var
    * @return unknown
    */
       function get($var) {
         $var = explode('.', $var);

         $result = $this->_settings;

         foreach ($var as $key) {
                   if (!isset($result[$key])) { return false; }

                   $result = $result[$key];
         }

         return $result;
        // trigger_error ('Not yet implemented', E_USER_ERROR);//引发一个错误
       }

       function load() {
            trigger_error ('Not yet implemented', E_USER_ERROR);
       }


}
Class Settings_INI Extends Settings {
	function load ($file) {
         	if (file_exists($file) == false) {echo $file; return false; }
         	$this->_settings = parse_ini_file ($file, true);
	}
}



?>
