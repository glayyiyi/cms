<?php
$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
if ( file_exists( $root.'/wp-load.php' ) ) {
    require_once( $root.'/wp-load.php' );
} elseif ( file_exists( $root.'/wp-config.php' ) ) {
    require_once( $root.'/wp-config.php' );
}
header('Content-type: application/x-javascript');

?>

var $j = jQuery.noConflict();

$j(document).ready(function() {
	"use strict";

	<?php echo $qode_options_satellite['custom_js']; ?>
});
