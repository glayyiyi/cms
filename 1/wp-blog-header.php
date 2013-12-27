<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */
echo '<meta name="baidu-tc-cerfication" content="c64bfc262bc6d5387404e047286e18cd" />';
if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	require_once( dirname(__FILE__) . '/wp-load.php' );

	wp();

	require_once( ABSPATH . WPINC . '/template-loader.php' );

}
