<?php

// Add the Meta Box
function page_blog_custom_fields() {
    add_meta_box(
        'page_blog_custom_fields', // $id
        'Blog Options', // $title
        'show_page_blog_custom_fields', // $callback
        'page', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'page_blog_custom_fields');

// Field Array
$page_blog_meta_custom_fields = array(
    array(
        'label' => 'Blog Category',
        'desc'	=> 'Select blog category',
        'id'	=> 'blog_category',
        'type'	=> 'taxonomy',
        'taxonomy' => 'category',
    ),
    array (
        'label' => 'Number of posts ot display',
        'desc'	=> '',
        'id'	=> 'blog_number_to_display',
        'type'	=> 'text'
    ),
    array (
        'label' => 'Height of embedded videos',
        'desc'	=> '',
        'id'	=> 'height_embedd_videos',
        'type'	=> 'text'
    ),
);

function show_page_blog_custom_fields() {
    global $page_blog_meta_custom_fields, $post;
    CF_print_metabox( $page_blog_meta_custom_fields, $post,  basename(__FILE__) );
}


