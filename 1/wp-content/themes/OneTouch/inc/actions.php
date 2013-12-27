<?php

/**
 * Add the RSS feed link in the <head> if there's posts
 */
function roots_feed_link() {
  $count = wp_count_posts('post'); if ($count->publish > 0) {
    echo "\n\t<link rel=\"alternate\" type=\"application/rss+xml\" title=\"". get_bloginfo('name') ." Feed\" href=\"". home_url() ."/feed/\">\n";
  }
}

add_action('wp_head', 'roots_feed_link', -2);


/**
 * Custom Login Logo
 */
function crum_custom_login_logo() {
global $NHP_Options;
if($NHP_Options->get("custom_logo_upload") !=''){
    $custom_logo = $NHP_Options->get("custom_logo_upload");
} else {
    $custom_logo = get_template_directory_uri() .'/assets/img/logo.png';
}

echo '<style type="text/css">
    body.login{background:#fff;}
    h1 a { background-image:url('. $custom_logo .') !important; height: 140px !important; background-size: inherit !important;} </style>';
}

add_action('login_head', 'crum_custom_login_logo');



function loginpage_custom_link() {
    return site_url();
}
add_filter('login_headerurl','loginpage_custom_link');

function change_title_on_logo() {
    return get_bloginfo( 'name' );
}
add_filter('login_headertitle', 'change_title_on_logo');


// Add/Remove Contact Methods
function add_remove_contactmethods( $contactmethods ) {

    $contactmethods['twitter'] = 'Twitter';
    $contactmethods['facebook'] = 'Facebook';
    $contactmethods['googleplus'] = 'Google Plus';
    $contactmethods['linkedin'] = 'Linked In';
    $contactmethods['flickr'] = 'Flickr';
    $contactmethods['lastfm'] = 'LastFM';
    $contactmethods['dribble'] = 'Dribble';

    // Remove Contact Methods
    unset($contactmethods['aim']);
    unset($contactmethods['yim']);
    unset($contactmethods['jabber']);

    return $contactmethods;
}
add_filter('user_contactmethods','add_remove_contactmethods',10,1);