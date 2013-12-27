<?php

require_once 'post/post-metaboxes.php';
require_once 'post/post-quote-metaboxes.php';
require_once 'post/post-video-metaboxes.php';



function save_post_custom_meta($post_id) {
    global $post_meta_custom_fields, $post_quote_meta_custom_fields, $post_video_meta_custom_fields;
    $post_params_to_save = array();
    $post_params_to_save = array_merge( (array)$post_meta_custom_fields, (array)$post_quote_meta_custom_fields );
    $post_params_to_save = array_merge( (array)$post_params_to_save, (array)$post_video_meta_custom_fields );

    CF_save_metabox( $post_params_to_save, $post_id, basename(__FILE__) );
}
add_action('save_post', 'save_post_custom_meta');