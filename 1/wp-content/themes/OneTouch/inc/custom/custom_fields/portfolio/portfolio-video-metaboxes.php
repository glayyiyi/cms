<?php

// Add the Meta Box
function portfolio_custom_fields() {
    add_meta_box(
        'portfolio_custom_fields', // $id
        'Portfolio Video', // $title
        'show_portfolio_custom_fields', // $callback
        'portfolio', // $page
        'normal', // $context
        'high'); // $priority
}

add_action('add_meta_boxes', 'portfolio_custom_fields');

// Field Array
$prefix = 'portfolio_custom_';
$portfolio_meta_custom_fields = array(
    array(
        'label' => 'YouTube video ID',
        'desc'	=> '',
        'id'	=> 'folio_youtube_video_url',
        'type'	=> 'text'
    ),
    array(
        'label' =>  'Vimeo video ID',
        'desc'	=> '',
        'id'	=> 'folio_vimeo_video_url',
        'type'	=> 'text'
    ),
    array(
        'label' =>  'Self hosted video file in mp4 format',
        'desc'	=> '',
        'id'	=> 'folio_self_hosted_mp4',
        'type'	=> 'video'
    ),
    array(
        'label' =>  'Self hosted video file in webM format',
        'desc'	=> '',
        'id'	=> 'folio_self_hosted_webm',
        'type'	=> 'video'
    ),

);


function show_portfolio_custom_fields() {
    global $portfolio_meta_custom_fields, $post;
    CF_print_metabox( $portfolio_meta_custom_fields, $post, basename(__FILE__) );
}

// Save the Data
function save_portfolio_custom_meta($post_id) {
    global $portfolio_meta_custom_fields;
    CF_save_metabox( $portfolio_meta_custom_fields, $post_id, basename(__FILE__) );
}
add_action('save_post', 'save_portfolio_custom_meta');

