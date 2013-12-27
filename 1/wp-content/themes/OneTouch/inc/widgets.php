<?php

function crum_widgets_init() {
  // Register widgetized areas
  register_sidebar(array(
        'name'          => __('Left Sidebar', 'roots'),
        'id'            => 'sidebar-left',
        'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
    register_sidebar(array(
        'name'          => __('Right Sidebar', 'roots'),
        'id'            => 'sidebar-right',
        'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));

  register_sidebar(array(
    'name'          => __('Footer-col1', 'roots'),
    'id'            => 'sidebar-footer-col1',
    'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
    'after_widget'  => '</div></section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ));
    register_sidebar(array(
        'name'          => __('Footer-col2', 'roots'),
        'id'            => 'sidebar-footer-col2',
        'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
    register_sidebar(array(
        'name'          => __('Footer-col3', 'roots'),
        'id'            => 'sidebar-footer-col3',
        'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Sidebar for shop', 'roots'),
        'id'            => 'shop-sidebar',
        'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));

    $sidebars = get_option(NHPOPTIONS.'sidebars');
    if( is_array( $sidebars ) )
        foreach( $sidebars as $name => $position )
            register_sidebar(array(
                'name' => __( $name , 'roots'),
                'id' =>str_replace(' ', '-', strtolower($name)),
                'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
                'after_widget' => '</div></section>',
                'before_title' => '<h3>',
                'after_title' => '</h3>',
            ));

}
add_action('widgets_init', 'crum_widgets_init');

$widget_path = locate_template('/inc/widgets/');

$widgets = array(
    'inc/widgets/widget-tabs.php',
    'inc/widgets/recent-posts.php',
    'inc/widgets/widget-tweets.php',
    'inc/widgets/widget-flickr.php',
    'inc/widgets/text-subtitle.php',
    'inc/widgets/tags-subtitle.php',
    'inc/widgets/search-subtitle.php',
    'inc/widgets/widget-contact.php',
    'inc/widgets/category-tiled.php',
    'inc/widgets/widget-facebook.php',
    'inc/widgets/widget-gallery.php',
);

// Allow child themes/plugins to add widgets to be loaded.
$widgets = apply_filters( 'crum_widgets', $widgets );

foreach ( $widgets as $w ) {
    locate_template( $w, true );
}

//CUSTOM WIDGETS
add_action("widgets_init", "load_widgets");
function load_widgets()
{
    register_widget('crum_latest_tweets');
    register_widget('crum_recent_posts');
    register_widget('crum_flickr');
    register_widget('Crum_Widget_Tabs');
    register_widget('crum_text_subtitle');
    register_widget('crum_search_subtitle');
    register_widget('crum_tags_subtitle');
    register_widget('crum_contact');
    register_widget('crum_tile_categories');
    register_widget('Facebook_Widget');
    register_widget('Gallery_Widget');
}

function add_user_sidebar( $id, $meta ){
    $sidebar = get_post_meta($id, $meta, $single = true);
    if ( ( $sidebar ) &&  function_exists('dynamic_sidebar') )
        return  dynamic_sidebar( $sidebar );
}

