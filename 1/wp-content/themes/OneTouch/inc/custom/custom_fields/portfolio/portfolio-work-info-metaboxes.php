<?php

// Add the Meta Box
function portfolio_work_info_custom_fields() {
    add_meta_box(
        'portfolio_work_info_custom_fields', // $id
        'Work info', // $title
        'show_portfolio_work_info_custom_fields', // $callback
        'portfolio', // $page
        'normal', // $context
        'high'); // $priority
}

add_action('add_meta_boxes', 'portfolio_work_info_custom_fields');

// Field Array
$prefix = 'portfolio_custom_';

$portfolio_work_info_meta_custom_fields = array(
    array(
        'label' => 'Designer',
        'desc'	=> '',
        'id'	=> 'designer',
        'type'	=> 'text'
    ),
    array(
        'label' =>  'Photographer',
        'desc'	=> '',
        'id'	=> 'photographer',
        'type'	=> 'text'
    ),
    array(
        'label' =>  'Client',
        'desc'	=> '',
        'id'	=> 'client',
        'type'	=> 'text'
    ),
    array(
        'label' =>  'Wesite link',
        'desc'	=> '',
        'id'	=> 'wesite_link',
        'type'	=> 'text'
    ),

);

function show_portfolio_work_info_custom_fields() {
    global $portfolio_work_info_meta_custom_fields, $post;
    CF_print_metabox( $portfolio_work_info_meta_custom_fields, $post, basename(__FILE__) );
}

// Save the Data
function save_portfolio_work_info_custom_meta($post_id) {
    global $portfolio_work_info_meta_custom_fields;
    CF_save_metabox( $portfolio_work_info_meta_custom_fields, $post_id, basename(__FILE__) );
}
add_action('save_post', ' save_portfolio_work_info_custom_meta');