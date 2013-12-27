<?php
// Add the Meta Box
function page_portfolio_custom_fields() {
    add_meta_box(
        'page_portfolio_custom_fields', // $id
        'Portfolio Options', // $title
        'show_page_portfolio_custom_fields', // $callback
        'page', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'page_portfolio_custom_fields');

// Field Array
$page_portfolio_meta_custom_fields = array(
    array(
        'label' => 'Portfolio Category',
        'desc'	=> 'Select portfolio category',
        'id'	=> 'portfolio_category',
        'type'	=> 'taxonomy',
        'taxonomy' => 'project_type',
    ),
    array (
        'label' => 'Number of items ot display',
        'desc'	=> '',
        'id'	=> 'portfolio_number_to_display',
        'type'	=> 'text'
    ),
);

function show_page_portfolio_custom_fields() {
    global $page_portfolio_meta_custom_fields, $post;
    CF_print_metabox( $page_portfolio_meta_custom_fields, $post,  basename(__FILE__) );
}


