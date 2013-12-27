<?php


// Add the Meta Box
function post_custom_fields() {
    add_meta_box(
        'post_custom_fields', // $id
        'Display Posts Options', // $title
        'show_post_custom_fields', // $callback
        'post', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'post_custom_fields',20);

// Field Array
$prefix = 'post_custom_';
$post_meta_custom_fields = array(
    array(
        'label' => 'Embedded video height',
        'desc'	=> 'Custom height in px',
        'id'	=> 'embedd_video_height',
        'type'	=> 'text'
    ),
    array(
        'label' => 'Not display on slider',
        'desc'	=> 'If is checked, this post is excluding from slider',
        'id'	=> 'display_post_in_slider',
        'type'	=> 'checkbox'
    ),
    array(
        'label' =>  'Select layout of current post single page.',
        'desc'	=> '',
        'id'	=> 'full_width_post',
        'type'	=> 'select',
        'options' => array (
            'one' => array (
                'label' => 'Default',
                'value'	=> 'default'
            ),
            'seven' => array (
                'label' => 'Full width',
                'value'	=> '1col-fixed'
            ),
            'two' => array (
                'label' => 'Left sidebar',
                'value'	=> '2c-l-fixed'
            ),
            'three' => array(
                'label' =>  'Right sidebar',
                'value' =>   '2c-r-fixed',
            ),
            'four' => array(
                'label' =>  'Both sidebars',
                'value' =>  '3c-fixed',
            ),
            'five' =>   array(
                'label' =>  '2 right sidebars',
                'value' =>  '3c-r-fixed',
            ),
            'six' =>array(
                'label' =>  '2 left sidebars',
                'value' =>  '3c-l-fixed',
            ),
        )
    ),
    array (
        'label' => 'Post description display',
        'desc'	=> 'How is displayed post in recent posts on main page',
        'id'	=> 'post_description_display',
        'type'	=> 'radio',
        'options' => array (
            'one' => array (
                'label' => 'Display short description',
                'value'	=> 'show',
                'default' => true,
            ),
            'two' => array (
                'label' => 'Hide description',
                'value'	=> 'hide'
            ),
        )
    )
);

function show_post_custom_fields() {
    global $post_meta_custom_fields, $post;
    CF_print_metabox( $post_meta_custom_fields, $post, basename(__FILE__) );
}
