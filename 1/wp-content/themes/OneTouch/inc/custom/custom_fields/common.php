<?php
if(!defined('CF_URL'))
    define('CF_URL',get_template_directory_uri().'/inc/custom/custom_fields/');

if(!defined('CF_PATH'))
    define('CF_PATH',get_template_directory().'/inc/custom/custom_fields/');

function add_custom_field_assets(){
    wp_enqueue_script('farbtastic-colorpicker', CF_URL.'/assets/colorpicker/farbtastic.js', array('jquery'));
    wp_enqueue_script('custom-fields', CF_URL.'/assets/js/custom_fields.js', array('jquery'));


    wp_register_style( 'farbtastic-colorpicker',CF_URL.'/assets/colorpicker/farbtastic.css');
    wp_enqueue_style( 'farbtastic-colorpicker' );

    wp_register_style( 'custom-fields',CF_URL.'/assets/css/custom_fields.css');
    wp_enqueue_style( 'custom-fields' );
}
add_action('admin_enqueue_scripts', 'add_custom_field_assets');


function include_custom_fields_colorpicker(){
    echo '<div id="custom-fields-colorpicker"></div>';
}
