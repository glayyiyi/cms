<?php
/**
 * Scripts and stylesheets
 */

function roots_scripts() {

  global $NHP_Options;

  wp_enqueue_style('roots_bootstrap', get_template_directory_uri() . '/assets/css/foundation.min.css', false, null);
  wp_enqueue_style('roots_app', get_template_directory_uri() . '/assets/css/app.css', false, null);

    if ($NHP_Options->get("responsive_mode")!='off'){
        wp_enqueue_style('responsive_css', get_template_directory_uri() . '/assets/css/responsive.css', false, null);
    }

  wp_enqueue_style('colorbox_css', get_template_directory_uri() . '/assets/css/colorbox.css', false, null);

  // Load style.css from child theme
  if (is_child_theme()) {
    wp_enqueue_style('roots_child', get_stylesheet_uri(), false, null);
  }


  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

    wp_register_script('modernizr', get_template_directory_uri() . '/assets/js/modernizr.foundation.js', false, null, false);
    wp_register_script('foundation', get_template_directory_uri() . '/assets/js/foundation.min.js', false, null, true);
    wp_register_script('plugins', get_template_directory_uri() . '/assets/js/jquery.plugins.min.js', false, null, false);
    wp_register_script('isotope', get_template_directory_uri() . '/assets/js/jquery.isotope.js', false, null, true);
    wp_register_script('nicescroll', get_template_directory_uri() . '/assets/js/jquery.nicescroll.js', false, null, true);
    wp_register_script('jquery.colorbox', get_template_directory_uri() . '/assets/js/jquery.colorbox.js', false, null, true);
    wp_register_script('jflickrfeed', get_template_directory_uri() . '/assets/js/jflickrfeed.min.js', false, null, true);


    //Grid scripts


    wp_register_script('masonry', ''.get_template_directory_uri().'/assets/js/jquery.masonry.min.js', false, null, true);
    wp_register_script('mousewheel', ''.get_template_directory_uri().'/assets/js/jquery.mousewheel.js', false, null, true);



    wp_register_script('site', get_template_directory_uri() . '/assets/js/site.js', false, null, true);

    wp_register_script('main', get_template_directory_uri() . '/assets/js/app.js', false, null, false);

    if (is_page_template('page-contacts.php')) {
        wp_register_script('map', ''.get_template_directory_uri().'/assets/js/gmap3.min.js', false, array('jquery'), true);
        wp_enqueue_script('map');
    }

    wp_enqueue_script('mousewheel');

    wp_enqueue_script('isotope');
    wp_enqueue_script('gallery');
    wp_enqueue_script('modernizr');

    wp_enqueue_script('easing');
    wp_enqueue_script('nicescroll');
    wp_enqueue_script('jquery.colorbox');
    wp_enqueue_script('main');
    wp_enqueue_script('site');
    wp_enqueue_script('foundation');
}

add_action('wp_enqueue_scripts', 'roots_scripts', 100);
