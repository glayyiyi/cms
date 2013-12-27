<?php
add_action('init', 'gallery_register');
function gallery_register()
{
    $args = array(
        'label' => __('Gallery'),
        'singular_label' => __('Gallery'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions')
    );

    register_post_type('gallery', $args);
}

