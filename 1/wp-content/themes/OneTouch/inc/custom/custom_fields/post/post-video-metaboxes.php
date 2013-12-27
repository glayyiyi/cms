<?php



// Add the Meta Box
function post_video_custom_fields() {
    add_meta_box(
        'post_video_custom_fields', // $id
        'Post Video', // $title
        'show_post_video_custom_fields', // $callback
        'post', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'post_video_custom_fields',9);

// Field Array
$prefix = 'post_custom_';
$post_video_meta_custom_fields = array(
    array(
        'label' => 'Use self hosted videos? ',
        'desc'	=> '',
        'id'	=> 'self_hosted_videos',
        'type'	=> 'checkbox'
    ),
    array(
        'label' =>  'Self hosted video file in mp4 format',
        'desc'	=> '',
        'id'	=> 'post_video_mp4',
        'type'	=> 'video'
    ),
    array(
        'label' =>  'Video file in WebM format',
        'desc'	=> '',
        'id'	=> 'post_video_webm',
        'type'	=> 'video'
    ),
);

function show_post_video_custom_fields() {
    global $post_video_meta_custom_fields, $post;
    CF_print_metabox( $post_video_meta_custom_fields, $post, basename(__FILE__) );
}
