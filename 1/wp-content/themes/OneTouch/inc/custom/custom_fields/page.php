<?php
require_once 'page/page-metaboxes.php';
require_once 'page/page-portfolio-metaboxes.php';
require_once 'page/page-blog-metaboxes.php';
require_once 'page/sidebar-metaboxes.php';

// Save the Data
function save_page_custom_meta($post_id) {
    global $page_meta_custom_fields, $custom_meta_fields,$page_portfolio_meta_custom_fields,$page_blog_meta_custom_fields;
    $array_for_save = array();
    $array_for_save = array_merge( (array)$page_meta_custom_fields, (array)$custom_meta_fields );
    $array_for_save = array_merge( (array)$array_for_save, (array)$page_portfolio_meta_custom_fields );
    $array_for_save = array_merge( (array)$array_for_save, (array)$page_blog_meta_custom_fields );
    CF_save_metabox($array_for_save, $post_id, basename(__FILE__) );
}

add_action('save_post', 'save_page_custom_meta');

