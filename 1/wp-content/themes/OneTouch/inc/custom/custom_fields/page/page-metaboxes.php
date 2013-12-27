<?php
// Add the Meta Box
function page_custom_fields() {
    add_meta_box(
        'page_custom_fields', // $id
        'Page Options', // $title
        'show_page_custom_fields', // $callback
        'page', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'page_custom_fields');

// Field Array
$prefix = 'page_custom_';
$page_meta_custom_fields = array(
    array(
        'label' => 'Page subtitle',
        'desc'	=> 'Type here page subtitle, if you need it.',
        'id'	=> 'page_subtitle',
        'type'	=> 'text'
    ),
    array (
        'label' => 'Background image',
        'desc'	=> '',
        'id'	=> 'page_bg_image',
        'type'	=> 'image'
    ),
    array (
        'label' => 'Background color',
        'desc'	=> '',
        'id'	=> 'page_bg_color',
        'type'	=> 'colorpicker'
    ),
    array(
        'label' => 'Fixed background',
        'desc'	=> 'If is checked, page bakground is fixed',
        'id'	=> 'page_bg_fixed',
        'type'	=> 'checkbox'
    ),
    array(
        'label'=> 'Background repeat',
        'desc'  => 'Select repeat type of current page background.',
        'id'    => 'page_bg_repeat',
        'type'  => 'select',
        'options' => array (
            'one' => array (
                'label' => 'No Repeat',
                'value' => 'no-repeat'
            ),
            'two' => array (
                'label' => ' Repeat',
                'value' => 'repeat'
            ),
            'three' => array (
                'label' => 'Repeat X',
                'value' => 'repeat-x '
            ),
            'four' => array (
                'label' => 'Repeat Y',
                'value' => 'repeat-y '
            )
        )
    )
);

function show_page_custom_fields() {
    global $page_meta_custom_fields, $post;
    CF_print_metabox( $page_meta_custom_fields, $post,  basename(__FILE__) );
}

