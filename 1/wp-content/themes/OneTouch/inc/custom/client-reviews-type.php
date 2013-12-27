<?php
add_action('init', 'client_review_register');
function client_review_register() {
    $args = array(
        'label' => __('Client Reviews'),
        'singular_label' => __('Client Reviews'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => true,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions')
    );

    register_post_type( 'client_reviews' , $args );
}


