<?php
/**
 * Roots functions
 */

if (!defined('__DIR__')) { define('__DIR__', dirname(__FILE__)); }

require_once locate_template('/inc/includes.php');   
require_once locate_template('/inc/themes.php');   

function roots_setup() {

  // Make theme available for translation
  load_theme_textdomain('roots', get_template_directory() . '/lang');

  // Register wp_nav_menu() menus (http://codex.wordpress.org/Function_Reference/register_nav_menus)
  register_nav_menus(array(
    'primary_navigation' => __('Primary Navigation', 'roots'),
  ));

    // Register wp_nav_menu() menus (http://codex.wordpress.org/Function_Reference/register_nav_menus)
    register_nav_menus(array(
        'primary_navigation' => __('Primary Navigation', 'roots'),
    ));


  // Add post thumbnails (http://codex.wordpress.org/Post_Thumbnails)
  add_theme_support('post-thumbnails');


  // Add post formats (http://codex.wordpress.org/Post_Formats)
  add_theme_support('post-formats', array('gallery', 'link', 'image', 'quote', 'video', 'audio'));

  // Tell the TinyMCE editor to use a custom stylesheet
  add_editor_style('assets/css/editor-style.css');

}

add_action('after_setup_theme', 'roots_setup');


add_post_type_support('page', 'excerpt');

add_action( 'admin_notices', 'is_options_css_writable' );
function is_options_css_writable(){
    if( !is_writable( locate_template('/css/options.css') ) )
        echo '<div class="updated"><p>css/options.css rights is not enough</p></div>';
}


add_filter( 'the_category', 'add_nofollow_cat' );
function add_nofollow_cat( $text ) {
    $text = str_replace('rel="category tag"', "", $text); return $text;
}

add_filter('widget_text', 'do_shortcode');

function limit_words($string, $word_limit) {

    // creates an array of words from $string (this will be our excerpt)
    // explode divides the excerpt up by using a space character

    $words = explode(' ', $string);

    // this next bit chops the $words array and sticks it back together
    // starting at the first word '0' and ending at the $word_limit
    // the $word_limit which is passed in the function will be the number
    // of words we want to use
    // implode glues the chopped up array back together using a space character

    return implode(' ', array_slice($words, 0, $word_limit));

}


add_filter( 'wp_get_attachment_image', 'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );

function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

// show admin bar only for admins
if (!current_user_can('manage_options')) {
    add_filter('show_admin_bar', '__return_false');
}
// show admin bar only for admins and editors
if (!current_user_can('edit_posts')) {
    add_filter('show_admin_bar', '__return_false');
}

function template_admin_head(){
    echo '<script>template_uri = "'.get_template_directory_uri().'";</script>';
    echo '<script>base_url = "'.home_url().'";</script>';
}
add_action('admin_head', 'template_admin_head');

/*
 * Woocommerce init
 */

add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

function the_slug() {
    $post_data = get_page($page->ID, ARRAY_A);
    $slug = $post_data['post_name'];
    return $slug;
}