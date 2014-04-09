<?php
/*
Plugin Name: 果子助手
Description: 果子助手
Author: 爱普世纪科技有限公司
Author URI: http://www.appcn100.com
Version: 1.0
*/
ob_start();

require dirname( __FILE__ ) . '/wp-gz-referral.php';
require dirname( __FILE__ ) . '/wp-gz-user-level.php';
require dirname( __FILE__ ) . '/wp-gz-agency-level.php';
require dirname( __FILE__ ) . '/wp-gz-creds-list.php';

define( 'GZ_THIS',          __FILE__ );
define( 'GZ_ROOT_DIR',      plugin_dir_path( GZ_THIS ) );

require_once( GZ_ROOT_DIR . 'wp-gz-shortcodes.php' );
require_once( GZ_ROOT_DIR . 'wp-gz-functions.php' );

add_filter('wp_authenticate_user', function($user) {
    if (get_user_meta($user->ID, 'user_flag', true) != 'inactive') {
        return $user;
    }
    return new WP_Error('1','账号未启用');
}, 10, 2);

?>    
