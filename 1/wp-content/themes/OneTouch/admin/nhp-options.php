<?php

/*
 * 
 * Require the framework class before doing anything else, so we can use the defined urls and dirs
 * Also if running on windows you may have url problems, which can be fixed by defining the framework url first
 *
 */
//define('NHP_OPTIONS_URL', site_url('path the options folder'));

$theme_version = '';

if( function_exists( 'wp_get_theme' ) ) {
    if( is_child_theme() ) {
        $temp_obj = wp_get_theme();
        $theme_obj = wp_get_theme( $temp_obj->get('Template') );
    } else {
        $theme_obj = wp_get_theme();
    }

    $theme_version = $theme_obj->get('Version');
    $theme_name = $theme_obj->get('Name');
    $theme_uri = $theme_obj->get('ThemeURI');
    $author_uri = $theme_obj->get('AuthorURI');
} else {
    $theme_data = get_theme_data(get_stylesheet_directory_uri().'/style.css' );
    $theme_version = $theme_data['Version'];
    $theme_name = $theme_data['Name'];
    $theme_uri = $theme_data['ThemeURI'];
    $author_uri = $theme_data['AuthorURI'];
}

define( 'NHPOPTIONS', $theme_name.'_hhp-options' );


if(!class_exists('NHP_Options')){
    require_once( dirname( __FILE__ ) . '/options/options.php' );
    require_once( dirname( __FILE__ ) . '/function.ajax.php' );
    require_once( dirname( __FILE__ ) . '/function.get.options.php' );
}


/*
 * 
 * Custom function for filtering the sections array given by theme, good for child themes to override or add to the sections.
 * Simply include this function in the child themes functions.php file.
 *
 * NOTE: the defined constansts for urls, and dir will NOT be available at this point in a child theme, so you must use
 * get_template_directory_uri() if you want to use any of the built in icons
 *
 */
function add_another_section($sections){

    //$sections = array();
    $sections[] = array(
        'title' => __('A Section added by hook', 'roots'),
        'desc' => __('<p class="description">This is a section created by adding a filter to the sections array, great to allow child themes, to add/remove sections from the options.</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => trailingslashit(get_template_directory_uri()).'options/img/glyphicons/glyphicons_062_attach.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array()
    );

    return $sections;

}//function
//add_filter('nhp-opts-sections-twenty_eleven', 'add_another_section');


/*
 * 
 * Custom function for filtering the args array given by theme, good for child themes to override or add to the args array.
 *
 */
function change_framework_args($args){

    //$args['dev_mode'] = false;

    return $args;

}//function
//add_filter('nhp-opts-args-twenty_eleven', 'change_framework_args');

/*
 * This is the meat of creating the optons page
 *
 * Override some of the default values, uncomment the args and change the values
 * - no $args are required, but there there to be over ridden if needed.
 *
 *
 */

function setup_framework_options(){
    $args = array();

//Set it to dev mode to view the class settings/info in the form - default is false
    $args['dev_mode'] = false;

//google api key MUST BE DEFINED IF YOU WANT TO USE GOOGLE WEBFONTS
//$args['google_api_key'] = '***';

//Remove the default stylesheet? make sure you enqueue another one all the page will look whack!
//$args['stylesheet_override'] = true;

//Add HTML before the form
    $args['intro_text'] = __('<p>Panel to configure theme options. If you have any questions or suggestions, please write on our forum <a href="http://sup.crumina.net"/>sup.crumina.net</a></p>', 'roots');

//Setup custom links in the footer for share icons
    $args['share_icons']['twitter'] = array(
        'link' => 'http://twitter.com/Crumina',
        'title' => 'Folow me on Twitter',
        'img' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_322_twitter.png'
    );
    $args['share_icons']['linked_in'] = array(
        'link' => 'http://sup.crumina.net/',
        'title' => 'Support forum',
        'img' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_049_star.png'
    );

//Choose to disable the import/export feature
//$args['show_import_export'] = false;

//Choose a custom option name for your theme options, the default is the theme name in lowercase with spaces replaced by underscores
    $args['opt_name'] = 'one_touch';

    define('NHP_OPT_NAME', $args['opt_name']);

//Custom menu icon
//$args['menu_icon'] = '';

//Custom menu title for options page - default is "Options"
    $args['menu_title'] = __('Theme Options', 'roots');

//Custom Page Title for options page - default is "Options"
    $args['page_title'] = __('One Touch  Theme Options', 'roots');

//Custom page slug for options page (wp-admin/themes.php?page=***) - default is "nhp_theme_options"
    $args['page_slug'] = 'nhp_theme_options';

//Custom page capability - default is set to "manage_options"
//$args['page_cap'] = 'manage_options';

//page type - "menu" (adds a top menu section) or "submenu" (adds a submenu) - default is set to "menu"
//$args['page_type'] = 'submenu';

//parent menu - default is set to "themes.php" (Appearance)
//the list of available parent menus is available here: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
//$args['page_parent'] = 'themes.php';

//custom page location - default 100 - must be unique or will override other items
    $args['page_position'] = 3;

//Custom page icon class (used to override the page icon next to heading)
//$args['page_icon'] = 'icon-themes';

//Want to disable the sections showing as a submenu in the admin? uncomment this line
//$args['allow_sub_menu'] = false;

//Set ANY custom page help tabs - displayed using the new help tab API, show in order of definition		
    $args['help_tabs'][] = array(
        'id' => 'nhp-opts-1',
        'title' => __('Theme Information 1', 'roots'),
        'content' => __('<p>This is the tab content, HTML is allowed.</p>', 'roots')
    );
    $args['help_tabs'][] = array(
        'id' => 'nhp-opts-2',
        'title' => __('Theme Information 2', 'roots'),
        'content' => __('<p>This is the tab content, HTML is allowed.</p>', 'roots')
    );

    $font_size_options = array(
        '.' => '',
        '9' => '9px',
        '10' => '10px',
        '11' => '11px',
        '12' => '12px',
        '13' => '13px',
        '14' => '14px',
        '16' => '16px',
        '18' => '18px',
        '20' => '20px',
        '22' => '22px',
        '24' => '24px',
        '32' => '32px',
        '40' => '40px',
    );

//Set the Help Sidebar for the options page - no sidebar by default										
    $args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'roots');

    $portfolio_taxonomies = get_terms('project_type', 'orderby=none&hide_empty');

    $portfolio_cats = array();
    foreach ($portfolio_taxonomies as $portfolio_taxonomy ){
        $portfolio_cats[$portfolio_taxonomy->slug] = $portfolio_taxonomy->name;
    }

    $sections = array();


    $sections[] = array(
        'title' => __('Main Options', 'roots'),
        'desc' => __('<p class="description">Main options of site</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_023_cogwheels.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'custom_favicon',
                'type' => 'upload',
                'title' => __('Favicon', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select a 16px X 16px image from the file location on your computer and upload it as a favicon of your site', 'roots')
            ),
            array(
                'id' => 'custom_logo_upload',
                'type' => 'upload',
                'title' => __('Header Logo', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select an image from the file location on your computer and upload it as a header logotype', 'roots'),
                'std'  => 'http://theme.crumina.net/onetouch/wp-content/uploads/2012/12/metro.png',
            ),
            array(
                'id' => 'custom_footer_logo_upload',
                'type' => 'upload',
                'title' => __('Footer Logo', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select an image from the file location on your computer and upload it as a footer logotype', 'roots'),
                'std'  =>'http://theme.crumina.net/onetouch/wp-content/uploads/2012/12/logo.png',
            ),

            array(
                'id' => 'responsive_mode',
                'type' => 'button_set',
                'title' => __('Responsive mode', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Enable or disable Responsive CSS', 'roots'),
                'options' => array('off' => 'Off', 'on' => 'On'),
                'std' => 'on'
            ),

            array(
                'id' => 'type_posts_show',
                'type' => 'select',
                'title' => __('Post', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('With this option you may choose the way your posts will be viewed.', 'roots'),
                'options' => array('excert' => 'Excerpts','full_post' => 'Full Post',),//Must provide key => value pairs for select options
                'std' => 'excert'
            ),

            array(
                'id' => 'main_menu_style',
                'type' => 'select',
                'title' => __('Top submenu style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('With this option you may choose a style of your submenu.', 'roots'),
                'options' => array('horisontal' => 'Row', 'vertical' => 'Column',), //Must provide key => value pairs for select options
                'std' => 'horisontal'
            ),

            array(
                'id' => 'google_analytics',
                'type' => 'textarea',
                'title' => __('Tracking Code', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Generate your tracking code at Google Analytics Service and insert it here. ', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => ''
            ),
            array(
                'id' => 'custom_css',
                'type' => 'textarea',
                'title' => __('Custom CSS', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('You may add any other styles for your theme to this field.', 'roots'),
                'std' => ''
            ),

        ),
    );

    $sections[] = array(
        'title' => __('Inner page options', 'roots'),
        'desc' => __('<p class="description">Parameters for inner pages (social share etc)</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL . 'img/glyphicons/glyphicons_144_folder_open.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'post_share_button',
                'type' => 'button_set',
                'title' => __('Social share buttons', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('With this option you may activate or deactivate social share buttons.', 'roots'),
                'options' => array('0' => 'Off','1' => 'On'),
                'std' => '1'// 1 = on | 0 = off
            ),
            array(
                'id' => 'post_share_place',
                'type' => 'select',
                'title' => __('Social share buttons placement', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('This option enables you to choose a place for your social share buttons on page.', 'roots'),
                'options' => array('bottom'=>'After post','top' => 'Before post','both' => 'Before and after post'),
                'std' => 'bottom'
            ),
            array(
                'id' => 'custom_share_code',
                'type' => 'textarea',
                'title' => __('Custom share code', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('You may add any other social share buttons to this field.', 'roots'),
            ),

            array(
                'id' => 'autor_box_disp',
                'type' => 'button_set',
                'title' => __('Author Info', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('This option enables you to insert information about the author of the post.', 'roots'),
                'options' => array('0' => 'Off','1' => 'On'),
                'std' => '1'// 1 = on | 0 = off
            ),

            array(
                'id' => 'pagination_style',
                'type' => 'button_set', //the field type
                'title' => __('Pagination type', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('This option enables you to choose pagination type.', 'roots'),
                'options' => array('1' => __('Prev/next butt.', 'roots'), '2' => __('Pages list', 'roots')),
                'std' => '1'//this should be the key as defined above
            ),

            array(
                'id' => 'masonry_posts_per_page',
                'type' => 'text',
                'title' => __('Number of blog posts placed masonry', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('You may put here a number of posts placed on blog masonry.'),
                'validate' => 'numeric',
                'std' => '18'
            ),
            array(
                'id' => 'posts_per_page',
                'type' => 'text',
                'title' => __('Number of posts in standard blog style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('You may put here a number of posts placed on blog of a standard type.', 'roots'),
                'validate' => 'numeric',
                'std' => '5'
            ),
            array(
                'id' => 'post_thumbnails_width',
                'type' => 'text',
                'title' => __('Post thumbnail width (in px)', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'numeric',
                'std' => '1200'
            ),
            array(
                'id' => 'post_thumbnails_height',
                'type' => 'text',
                'title' => __('Post  thumbnail height (in px)', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'numeric',
                'std' => '300',
            ),
            array(
                'id' => 'portfolio_page_select',
                'type' => 'pages_select',
                'title' => __('Portfolio page', 'crum'),
                'sub_desc' => __('', 'crum'),
                'desc' => __('Please select main portfolio page (for proper urls)', 'crum'),
                'args' => array()//uses get_pages
            ),
            array(
                'id' => 'post_inner_header',
                'type' => 'button_set',
                'title' => __('Post info', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('It is information about the post (time and date of creation, author, comments on the post).', 'roots'),
                'options' => array('1' => __('On', 'roots'), '0' => __('Off', 'roots')),
                'std' => '0'//this should be the key as defined above
            ),
            array(
                'id' => 'boxed_portfolio',
                'type' => 'select',
                'title' => __('Portfolio style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array('boxed' => 'Boxed','left-boxed' => 'Left Boxed', 'full-width'=>"Full Width"),
                'std' => 'left-boxed',
            ),
            array(
                'id' => 'portfolio_single_style',
                'type' => 'button_set', //the field type
                'title' => __('Portfolio text location', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' =>array(
                    'left'=>'to the right',
                    'full'=>'below the image',
                ),
                'std' => 'left',
            ),
            array(
                'id' => 'portfolio_single_slider',
                'type' => 'button_set', //the field type
                'title' => __('Portfolio image display', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' =>array(
                    'slider'=>'Slider',
                    'full'=>'Items',
                ),
                'std' => 'slider',
            ),

        ),
    );


    $sections[] = array(
        'title' => __('Styling Options', 'roots'),
        'desc' => __('<p class="description">Style parameters of body and footer</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_234_brush.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'main_menu_position',
                'type' => 'button_set', //the field type
                'title' => __('Main menu alignment', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' =>array(
                    'left'=>'Left',
                    'right'=>'Right',
                ),
                'std' => 'right',
            ),
            array(
                'id' => 'site_boxed',
                'type' => 'select_hide_show_opts',
                'title' => __('Body layout', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array('0' => 'Full width', '1' => 'Boxed'),
                'std' => '0',// 1 = on | 0 = off,
                'options_to_show' => array(
                    '0'=>"body_wrapper_bg_color,body_wrapper_bg_image,body_wrapper_custom_repeat,",
                    "1"=>""
                ),
            ),

            array(
                'id' => 'main_site_color',
                'type' => 'color',
                'title' => __('Main site color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Color of buttons, tabs, scrolling line etc.', 'roots'),
                'std' => '#57bae8'
            ),
            array(
                'id' => 'secondary_site_color',
                'type' => 'color',
                'title' => __('Secondary site color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Color of inactive or hovered elements', 'roots'),
                'std' => '#50a9d2'
            ),

            //Body wrapper
            array(
                'id' => 'body_wrapper_bg_color',
                'type' => 'color',
                'title' => __('Body background color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select background color.', 'roots'),
                'std' => '#FFFFFF'
            ),
            array(
                'id' => 'body_wrapper_bg_image',
                'type' => 'upload',
                'title' => __('Custom background image', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Upload your own background image or pattern.', 'roots')
            ),
            array(
                'id' => 'body_wrapper_custom_repeat',
                'type' => 'select',
                'title' => __('Background image repeat', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select type background image repeat', 'roots'),
                'options' => array('repeat-y' => 'vertically','repeat-x' => 'horizontally','no-repeat' => 'no-repeat', 'repeat' => 'both vertically and horizontally', ),//Must provide key => value pairs for select options
                'std' => 'repeat'
            ),


            //Body wrapper
            array(
                'id' => 'body_bg_color',
                'type' => 'color',
                'title' => __('Body background color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select background color.', 'roots'),
                'std' => '#FFFFFF'
            ),
            array(
                'id' => 'body_bg_image',
                'type' => 'upload',
                'title' => __('Custom background image', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Upload your own background image or pattern.', 'roots')
            ),
            array(
                'id' => 'body_custom_repeat',
                'type' => 'select',
                'title' => __('Background image repeat', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select type background image repeat', 'roots'),
                'options' => array('repeat-y' => 'vertically','repeat-x' => 'horizontally','no-repeat' => 'no-repeat', 'repeat' => 'both vertically and horizontally', ),//Must provide key => value pairs for select options
                'std' => ''
            ),
            array(
                'id' => 'body_bg_fixed',
                'type' => 'button_set',
                'title' => __('Fixed body background', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array('0' => 'Off','1' => 'On'),
                'std' => '0'// 1 = on | 0 = off
            ),
            array(
                'id' => 'footer_bg_color',
                'type' => 'color',
                'title' => __('Footer background color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select footer background color. ', 'roots'),
                'std' => ''
            ),
            array(
                'id' => 'footer_font_color',
                'type' => 'color',
                'title' => __('Footer font color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select footer font color.', 'roots'),
                'std' => ''
            ),
            array(
                'id' => 'footer_bg_image',
                'type' => 'upload',
                'title' => __('Custom footer background image', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Upload your own footer background image or pattern.', 'roots')
            ),
            array(
                'id' => 'footer_custom_repeat',
                'type' => 'select',
                'title' => __('Footer background image repeat', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select type background image repeat', 'roots'),
                'options' => array('repeat-y' => 'vertically','repeat-x' => 'horizontally','no-repeat' => 'no-repeat', 'repeat' => 'both vertically and horizontally', ),//Must provide key => value pairs for select options
                'std' => ''
            ),
            array(
                'id' => 'main_link_color',
                'type' => 'color',
                'title' => __('Basic links color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select basic links color.', 'roots'),
                'std' => ''
            ),
            array(
                'id' => 'main_link_color_hover',
                'type' => 'color',
                'title' => __('Basic links color when hovered', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('select basic links color on hovering', 'roots'),
                'std' => ''
            ),


        ),
    );

    $sections[] = array(
        'title' => __('Main slider options', 'roots'),
        'desc' => __('<p class="description">Metro-style slider options</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_050_link.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'boxed_post_slider',
                'type' => 'select',
                'title' => __('Main slider style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select main slider show type.', 'roots'),
                'options' => array('1' => 'Boxed','2' => 'Left Boxed','3' => 'Full Width','4' => 'No scrolling'),//Must provide key => value pairs for select options
                'std' => '1'
            ),
            array(
                'id' => 'disable_slider_scroll',
                'type' => 'button_set',
                'title' => __('Disable slider scroll', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('disable horizontal slider scroll on mouse scroll wrap', 'roots'),
                'options' => array('1' => 'On','0' => 'Off'),
                'std' => '0'
            ),
            array(
                'id' => 'horizontal_slider_type',
                'type' => 'select',
                'title' => __('Main slider items', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select posts or portfolio to be shown on the main slider.', 'roots'),
                'options' => array('post' => 'Posts','portfolio' => 'Portfolio',),//Must provide key => value pairs for select options
                'std' => 'post'
            ),
            array(
                'id' => 'slider_posts_number',
                'type' => 'select',
                'title' => __('Number of posts to display', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select number of posts to be displayed on the main slider.', 'roots'),
                'options' => array(3=>3,6=>6,9=>9,12=>12,15=>15,18=>18,21=>21,24=>24,27=>27,30=>30),
                'std' => 18
            ),
            array(
                'id' => 'main_slider_item_icon',
                'type' => 'upload',
                'title' => __('Icon of the main slider item', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Upload icon for the items in the main slider.', 'roots')
            ),
            array(
                'id' => 'even_slider_elements_bgcolor',
                'type' => 'color',
                'title' => __('Even elements substrate color of a hover box', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select color of a hover box substrate.', 'roots'),
                'std' => ''
            ),
            array(
                'id' => 'even_slider_elements_textcolor',
                'type' => 'color',
                'title' => __('Even elements text color of a hover box', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select color of a hover box text.', 'roots'),
                'std' => ''
            ),
            array(
                'id' => 'even_slider_elements_datecolor',
                'type' => 'color',
                'title' => __('Even elements date color of a hover box', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select color of a hover box date.', 'roots'),
                'std' => ''
            ),
            array(
                'id' => 'odd_slider_elements_bgcolor',
                'type' => 'color',
                'title' => __('Odd elements substrate color of a hover box', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select color of a hover box substrate.', 'roots'),
                'std' => ''
            ),

            array(
                'id' => 'odd_slider_elements_textcolor',
                'type' => 'color',
                'title' => __('Odd elements text color of a hover box ', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select color of a hover box text.', 'roots'),
                'std' => ''
            ),

            array(
                'id' => 'odd_slider_elements_datecolor',
                'type' => 'color',
                'title' => __('Odd elements date color of a hover box', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select color of a hover box date.', 'roots'),
                'std' => ''
            ),
        )
    );


    $sections[] = array(
        'title' => __('Footer options', 'roots'),
        'desc' => __('<p class="description">Footer options</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_050_link.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'footer_display',
                'type' => 'select_hide_show_opts',
                'title' => __('Footer display', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select the view of footer', 'roots'),
                'std' => 'blocks',
                'options_to_show'=>array(
                    'blocks'=>"",
                    "copyright"=>"footer_title_description_text,footer_subtitle_description_text,footer_description_text,footer_contacts_text"),
                'options'=>array(
                    'blocks'=>'show company information',
                    'copyright'=>'Show copyright'
                ),
                'std' => 'blocks',
            ),



            array(
                'id' => 'footer_title_description_text',
                'type' => 'text',
                'title' => __('Title "About company"', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => 'About company'
            ),
            array(
                'id' => 'footer_subtitle_description_text',
                'type' => 'text',
                'title' => __('Subtitle "About company"', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => 'IT IS REALLY INTERESTING'
            ),
            array(
                'id' => 'footer_description_text',
                'type' => 'textarea',
                'title' => __('Text "About company"', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => 'I’m not sure anyone saw this coming, but now that it’s here, it makes good sense: parents have smartphones. Some of their'
            ),
            array(
                'id' => 'footer_contacts_text',
                'type' => 'textarea',
                'title' => __('Contact information', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post,
                'std' => 'Address:   123456 Street Name, Los Angeles <br> Phone:   (1800) 765-4321'
            ),

            array(
                'id' => 'copyright_footer',
                'type' => 'text',
                'title' => __('Show copyright', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Fill in the copyright text.', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => 'My copyright info &copyr; 2013'
            ),

            array(
                'id' => 'menu_in_footer',
                'type' => 'button_set',
                'title' => __('Display menu in footer?', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array('1' => 'On','0' => 'Off'),
                'std' => '0'
            ),

            array(
                'id' => 'footer_menu_select',
                'type' => 'menu_select',
                'title' => __('Footer links menu', 'roots'),
                'sub_desc' => __('Select one to display in footer', 'roots'),
                'desc' => __('This option enables you to create your own footer links menus by going to “Appearance>Menus”.', 'roots'),
                //'args' => array()//uses wp_get_nav_menus
            ),

            array(
                'id' => 'footer_color_style',
                'type' => 'button_set',
                'title' => __('Footer background style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array('light' => 'White','dark' => 'Dark'),
                'std' => 'dark'
            ),

        )
    );


    $sections[] = array(
        'title' => __('Contact page options', 'roots'),
        'desc' => __('<p class="description">Contact page options</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_024_parents.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'text_contact_page',
                'type' => 'editor',
                'title' => __('Text on top', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('This is the description field, again good for additional info.', 'roots'),
            ),
            array(
                'id' => 'map_address',
                'type' => 'text',
                'title' => __('Address on Google Map ', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Fill in your address to be shown on Google map.', 'roots'),
                'std' =>'London, Downing street, 10'
            ),
            array(
                'id' => 'faces_on_contact_menu',
                'type' => 'button_set',
                'title' => __('Show pictures on menu item “Contacts”', 'roots'),
                'sub_desc' => __('', 'roots'),
            'desc' => __('Choose this option if you want pictures to be shown on your "Contacts" menu item.', 'roots'),
                'options' => array('0' => 'Off','1' => 'On'),
                'std' => '1'// 1 = on | 0 = off
            ),
            array(
                'id' => 'antispam_way',
                'type' => 'select_hide_show_opts',
                'title' => __('Use default key', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select the way of anti spam protection', 'roots'),
                'std' => 'About company',
                'options_to_show'=>array(
                    'recaptcha'=>"antispam_question,antispam_answer",
                    "question"=>"public_key_recaptcha,private_key_recaptcha,default_keys_recaptcha"),
                'options'=>array(
                    'recaptcha'=>'Recaptcha',
                    'question'=>'Controll question'
                ),
                'std' => 'question',
            ),
            array(
                'id' => 'antispam_question',
                'type' => 'text',
                'title' => __('Type the antispam question', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Antispam question will protect you from spamers', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => 'How many legs does elephant have?'
            ),
            array(
                'id' => 'antispam_answer',
                'type' => 'text',
                'title' => __('Type the answer for antispam question', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Antispam question will protect you from spamers', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' =>'4'
            ),
            array(
                'id' => 'public_key_recaptcha',
                'type' => 'text',
                'title' => __('Insert your public reCaptcha key', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'no_html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => ''
            ),
            array(
                'id' => 'private_key_recaptcha',
                'type' => 'text',
                'title' => __('Insert your rivate reCaptcha key', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'validate' => 'no_html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => ''
            ),
            array(
                'id' => 'default_keys_recaptcha',
                'type' => 'checkbox',
                'title' => __('Use default keys', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'std' => '1'
            ),

        ),
    );

    $sections[] = array(
        'title' => __('Unlimited sidebars', 'roots'),
        'desc' => __('<p class="description">Add or delete your own sidebars</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_154_show_big_thumbnails.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'custom_sidebars',
                'type' => 'custom_sidebars',
                'title' => __('Custom sidebar name', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('You may create a custom sidebar to be displayed on a single page.', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => ''
            ),
        ),
    );

    $sections[] = array(
        'title' => __('Social accounts', 'roots'),
        'desc' => __('<p class="description">Type links for social accounts</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_088_adress_book.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'soc_ico_panel',
                'type' => 'button_set',
                'title' => __('Social icons panel', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('You can enable or disable panel with social icons', 'roots'),
                'options' => array('off' => 'Off','on' => 'On'),
                'std' => 'on'// 1 = on | 0 = off
            ),
            array(
                'id' => 'title_panel_text',
                'type' => 'text',
                'title' => __('Social icons panel title', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('You can change or delete text near social panel icon', 'roots'),
                'std' => 'Social icons'
            ),
            array(
                'id' => 'expand_panel_text',
                'type' => 'text',
                'title' => __('Expand panel text', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Text that displayed when social panel closed', 'roots'),
                'std' => 'Close panel'
            ),
            array(
                'id' => 'close_panel_text',
                'type' => 'text',
                'title' => __('Close panel text', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Text that displayed when social panel opened', 'roots'),
                'std' => 'Expand panel'
            ),

            array(
                'id' => 'expand_social_icons',
                'type' => 'button_set',
                'title' => __('Expand social icons', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Display social icons panel when site loaded', 'roots'),
                'options' => array('0' => 'Closed','1' => 'Opened'),
                'std' => '0'// 1 = on | 0 = off
            ),
            array(
                'id' => 'fb_link',
                'type' => 'text',
                'title' => __('Facebook link', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://facebook.com'
            ),
            array(
                'id' => 'tw_link',
                'type' => 'text',
                'title' => __('Twitter link', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://twitter.com'
            ),
            array(
                'id' => 'fl_link',
                'type' => 'text',
                'title' => __('Flickr link', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://flickr.com'
            ),
            array(
                'id' => 'vi_link',
                'type' => 'text',
                'title' => __('Vimeo link', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://vimeo.com'
            ),
            array(
                'id' => 'lf_link',
                'type' => 'text',
                'title' => __('Last FM link', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://lastfm.com'
            ),
            array(
                'id' => 'dr_link',
                'type' => 'text',
                'title' => __('Dribble link', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://dribble.com'
            ),
            array(
                'id' => 'yt_link',
                'type' => 'text',
                'title' => __('YouTube', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://youtube.com'
            ),
            array(
                'id' => 'ms_link',
                'type' => 'text',
                'title' => __('Microsoft ID', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'https://accountservices.passport.net/'
            ),
            array(
                'id' => 'li_link',
                'type' => 'text',
                'title' => __('LinkedIN', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://linkedin.com'
            ),
            array(
                'id' => 'gp_link',
                'type' => 'text',
                'title' => __('Google +', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'https://accounts.google.com/'
            ),
            array(
                'id' => 'pi_link',
                'type' => 'text',
                'title' => __('Picasa', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://picasa.com'
            ),
            array(
                'id' => 'pt_link',
                'type' => 'text',
                'title' => __('Pinterest', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://pinterest.com'
            ),
            array(
                'id' => 'wp_link',
                'type' => 'text',
                'title' => __('Wordpress', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://wordpress.com'
            ),
            array(
                'id' => 'db_link',
                'type' => 'text',
                'title' => __('Dropbox', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste link to your account', 'roots'),
                'validate' => 'url',
                'std' => 'http://dropbox.com'
            ),
            array(
                'id' => 'rss_link',
                'type' => 'text',
                'title' => __('RSS', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Paste alternative link to Rss', 'roots'),
                'std' => ''
            ),
        ),
    );
    
    $sections[] = array(
        'title' => __('Blocks Manager', 'roots'),
        'desc' => __('<p class="description">Setup and configure blocks</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_153_more_windows.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'block_manager',
                'type' => 'block_manager',
                'title' => __('Blocks Manager', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Add new and configure blocks', 'roots'),
                'std' => '{+Director page+:{+id+:+director-page+,+color+:++,+bgimage+:++,+page+:+director+,+title+:++,+subtitle+:++},+Post Slider+:{+id+:+post-slider+,+color+:++,+bgimage+:++,+page+:+post_slider+,+title+:++,+subtitle+:++},+Recent Projects+:{+id+:+recent-projects+,+color+:++,+bgimage+:++,+page+:+recent_projects+,+title+:+Recent Projects Title+,+subtitle+:+Recent Projects Subtitle+}}'
            ),
        ),
    );

    $sections[] = array(
        'title' => __('Home Settings', 'roots'),
        'desc' => __('<p class="description">Set blocks position, enable or disable them.</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_020_home.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'homepage_title_description_text',
                'type' => 'textarea',
                'title' => __('Homepage title of description text', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Type here title of the description in homepage', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => 'About company'
            ),
            array(
                'id' => 'homepage_subtitle_description_text',
                'type' => 'textarea',
                'title' => __('Homepage subtitle of description text', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Type here subtitle of the description in homepage', 'roots'),
                'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                'std' => 'About company'
            ),
            array(
                'id' => 'homepage_blocks',
                'type' => 'sorter',
                'title' => __('Blocks Sorter', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Drag and drop your blocks', 'roots'),
                'std' => '{+enabled+:[+Post Slider+,+Director page+,+Recent Projects+],+disabled+:[+Demo block+]}'
            ),
        ),
    );


    $sections[] = array(
        'title' => __('Typography', 'roots'),
        'desc' => __('<p class="description">Typography settings</p>', 'roots'),
        //all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
        //You dont have to though, leave it blank for default.
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_100_font.png',
        //Lets leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id' => 'body_font_size',
                'type' => 'select',
                'title' => __('Body main font size', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select font size.', 'roots'),
                'std' => '#4D4D4D',
                'options' => $font_size_options,
            ),
            array(
                'id' => 'body_font_color',
                'type' => 'color',
                'title' => __('Body main font color', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('Select font color.', 'roots'),
                'std' => '#4D4D4D'
            ),
            array(
                'id' => 'h1_typo',
                'type' => 'element_font',
                'title' => __('H1 elements style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'std' => '',
                'selector' =>'',
            ),
            array(
                'id' => 'h1_color',
                'type' => 'color',
                'title' => __('H1 elements color', 'roots'),
                'sub_desc' => __('Click on field to choose color', 'roots'),
                'std' => '',

            ),
            array(
                'id' => 'h2_typo',
                'type' => 'element_font',
                'title' => __('H2 elements style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),

                'std' => '',
                'selector' =>'',
            ),
            array(
                'id' => 'h2_color',
                'type' => 'color',
                'title' => __('H2 elements color', 'roots'),
                'sub_desc' => __('Click on field to choose color', 'roots'),

                'std' => ''
            ),
            array(
                'id' => 'h3_typo',
                'type' => 'element_font',
                'title' => __('H3 elements style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),

                'std' => '',
                'selector' =>'',
            ),
            array(
                'id' => 'h3_color',
                'type' => 'color',
                'title' => __('H3 elements color', 'roots'),
                'sub_desc' => __('Click on field to choose color', 'roots'),

                'std' => ''
            ),
            array(
                'id' => 'h4_typo',
                'type' => 'element_font',
                'title' => __('H4 elements style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),

                'std' => '',
                'selector' =>'',
            ),
            array(
                'id' => 'h4_color',
                'type' => 'color',
                'title' => __('H4 elements color', 'roots'),
                'sub_desc' => __('Click on field to choose color', 'roots'),

                'std' => ''
            ),
            array(
                'id' => 'h5_typo',
                'type' => 'element_font',
                'title' => __('H5 elements style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),
                'std' => '',
                'selector' =>'',
            ),
            array(
                'id' => 'h5_color',
                'type' => 'color',
                'title' => __('H5 elements color', 'roots'),
                'sub_desc' => __('Click on field to choose color', 'roots'),

                'std' => ''
            ),
            array(
                'id' => 'h6_typo',
                'type' => 'element_font',
                'title' => __('H6 elements style', 'roots'),
                'sub_desc' => __('', 'roots'),
                'desc' => __('', 'roots'),

                'std' => '',
                'selector' =>'',
            ),
            array(
                'id' => 'h6_color',
                'type' => 'color',
                'title' => __('H6 elements color', 'roots'),

                'sub_desc' => __('Click on field to choose color', 'roots'),

                'std' => ''
            ),
        ),
    );

    $sections[] = array(
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_157_show_lines.png',
        'title' => __('Layouts Settings', 'roots'),
        'desc' => __('<p class="description">Configure layouts of different pages</p>', 'roots'),
        'fields' => array(
            array(
                'id' => 'pages_layout',
                'type' => 'radio_img',
                'title' => __('Single pages layout', 'roots'),
                'sub_desc' => __('Select one type of layout for single pages', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array(
                    '1col-fixed' => array('title' => 'No sidebars', 'img' => NHP_OPTIONS_URL.'img/1col.png'),
                    '2c-l-fixed' => array('title' => 'Sidebar on left', 'img' => NHP_OPTIONS_URL.'img/2cl.png'),
                    '2c-r-fixed' => array('title' => 'Sidebar on right', 'img' => NHP_OPTIONS_URL.'img/2cr.png'),
                    '3c-l-fixed' => array('title' => '2 left sidebars', 'img' => NHP_OPTIONS_URL.'img/3cl.png'),
                    '3c-fixed' => array('title' => 'Sidebar on either side', 'img' => NHP_OPTIONS_URL.'img/3cc.png'),
                    '3c-r-fixed' => array('title' => '2 right sidebars', 'img' => NHP_OPTIONS_URL.'img/3cr.png'),
                ),//Must provide key => value(array:title|img) pairs for radio options
                'std' => '1col-fixed'
            ),
            array(
                'id' => 'archive_layout',
                'type' => 'radio_img',
                'title' => __('Archive Pages Layout', 'roots'),
                'sub_desc' => __('Select one type of layout for archive pages', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array(
                    '1col-fixed' => array('title' => 'No sidebars', 'img' => NHP_OPTIONS_URL.'img/1col.png'),
                    '2c-l-fixed' => array('title' => 'Sidebar on left', 'img' => NHP_OPTIONS_URL.'img/2cl.png'),
                    '2c-r-fixed' => array('title' => 'Sidebar on right', 'img' => NHP_OPTIONS_URL.'img/2cr.png'),
                    '3c-l-fixed' => array('title' => '2 left sidebars', 'img' => NHP_OPTIONS_URL.'img/3cl.png'),
                    '3c-fixed' => array('title' => 'Sidebar on either side', 'img' => NHP_OPTIONS_URL.'img/3cc.png'),
                    '3c-r-fixed' => array('title' => '2 right sidebars', 'img' => NHP_OPTIONS_URL.'img/3cr.png'),
                ),//Must provide key => value(array:title|img) pairs for radio options
                'std' => '2c-l-fixed'
            ),
            array(
                'id' => 'single_layout',
                'type' => 'radio_img',
                'title' => __('Single posts layout', 'roots'),
                'sub_desc' => __('Select one type of layout for single posts', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array(
                    '1col-fixed' => array('title' => 'No sidebars', 'img' => NHP_OPTIONS_URL.'img/1col.png'),
                    '2c-l-fixed' => array('title' => 'Sidebar on left', 'img' => NHP_OPTIONS_URL.'img/2cl.png'),
                    '2c-r-fixed' => array('title' => 'Sidebar on right', 'img' => NHP_OPTIONS_URL.'img/2cr.png'),
                    '3c-l-fixed' => array('title' => '2 left sidebars', 'img' => NHP_OPTIONS_URL.'img/3cl.png'),
                    '3c-fixed' => array('title' => 'Sidebar on either side', 'img' => NHP_OPTIONS_URL.'img/3cc.png'),
                    '3c-r-fixed' => array('title' => '2 right sidebars', 'img' => NHP_OPTIONS_URL.'img/3cr.png'),
                ),//Must provide key => value(array:title|img) pairs for radio options
                'std' => '2c-l-fixed'
            ),
            array(
                'id' => 'search_layout',
                'type' => 'radio_img',
                'title' => __('Search results layout', 'roots'),
                'sub_desc' => __('Select one type of layout for search results', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array(
                    '1col-fixed' => array('title' => 'No sidebars', 'img' => NHP_OPTIONS_URL.'img/1col.png'),
                    '2c-l-fixed' => array('title' => 'Sidebar on left', 'img' => NHP_OPTIONS_URL.'img/2cl.png'),
                    '2c-r-fixed' => array('title' => 'Sidebar on right', 'img' => NHP_OPTIONS_URL.'img/2cr.png'),
                    '3c-l-fixed' => array('title' => '2 left sidebars', 'img' => NHP_OPTIONS_URL.'img/3cl.png'),
                    '3c-fixed' => array('title' => 'Sidebar on either side', 'img' => NHP_OPTIONS_URL.'img/3cc.png'),
                    '3c-r-fixed' => array('title' => '2 right sidebars', 'img' => NHP_OPTIONS_URL.'img/3cr.png'),
                ),//Must provide key => value(array:title|img) pairs for radio options
                'std' => '2c-l-fixed'
            ),
            array(
                'id' => '404_layout',
                'type' => 'radio_img',
                'title' => __('404 Page Layout', 'roots'),
                'sub_desc' => __('Select one of layouts for 404 page', 'roots'),
                'desc' => __('', 'roots'),
                'options' => array(
                    '1col-fixed' => array('title' => 'No sidebars', 'img' => NHP_OPTIONS_URL.'img/1col.png'),
                    '2c-l-fixed' => array('title' => 'Sidebar on left', 'img' => NHP_OPTIONS_URL.'img/2cl.png'),
                    '2c-r-fixed' => array('title' => 'Sidebar on right', 'img' => NHP_OPTIONS_URL.'img/2cr.png'),
                    '3c-l-fixed' => array('title' => '2 left sidebars', 'img' => NHP_OPTIONS_URL.'img/3cl.png'),
                '3c-fixed' => array('title' => 'Sidebar on either side', 'img' => NHP_OPTIONS_URL.'img/3cc.png'),
                    '3c-r-fixed' => array('title' => '2 right sidebars', 'img' => NHP_OPTIONS_URL.'img/3cr.png'),
                ),//Must provide key => value(array:title|img) pairs for radio options
                'std' => '2c-l-fixed'
            )
        ),
    );
    //var_dump(get_option($args['opt_name']));

    $tabs = array();

    if (function_exists('wp_get_theme')){
        $theme_data = wp_get_theme();
        $theme_uri = $theme_data->get('ThemeURI');
        $description = $theme_data->get('Description');
        $author = $theme_data->get('Author');
        $version = $theme_data->get('Version');
        $tags = $theme_data->get('Tags');
    }else{
        $theme_data = get_theme_data(trailingslashit(get_stylesheet_directory()).'style.css');
        $theme_uri = $theme_data['URI'];
        $description = $theme_data['Description'];
        $author = $theme_data['Author'];
        $version = $theme_data['Version'];
        $tags = $theme_data['Tags'];
    }

    $theme_info = '<div class="nhp-opts-section-desc">';
    $theme_info .= '<p class="nhp-opts-theme-data description theme-uri">'.__('<strong>Theme URL:</strong> ', 'roots').'<a href="'.$theme_uri.'" target="_blank">'.$theme_uri.'</a></p>';
    $theme_info .= '<p class="nhp-opts-theme-data description theme-author">'.__('<strong>Author:</strong> ', 'roots').$author.'</p>';
    $theme_info .= '<p class="nhp-opts-theme-data description theme-version">'.__('<strong>Version:</strong> ', 'roots').$version.'</p>';
    $theme_info .= '<p class="nhp-opts-theme-data description theme-description">'.$description.'</p>';
    $theme_info .= '<p class="nhp-opts-theme-data description theme-tags">'.__('<strong>Tags:</strong> ', 'roots').implode(', ', (array)$tags).'</p>';
    $theme_info .= '</div>';



    $tabs['theme_info'] = array(
        'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_195_circle_info.png',
        'title' => __('Theme Information', 'roots'),
        'content' => $theme_info
    );

    if(file_exists(trailingslashit(get_stylesheet_directory()).'README.html')){
        $tabs['theme_docs'] = array(
            'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_071_book.png',
            'title' => __('Documentation', 'roots'),
            'content' => nl2br(file_get_contents(trailingslashit(get_stylesheet_directory()).'README.html'))
        );
    }//if

    global $NHP_Options;
    $NHP_Options = new NHP_Options($sections, $args, $tabs);

}//function
add_action('init', 'setup_framework_options', 0);

/*
 * 
 * Custom function for the callback referenced above
 *
 */
function my_custom_field($field, $value){
    print_r($field);
    print_r($value);

}//function

/*
 * 
 * Custom function for the callback validation referenced above
 *
 */
function validate_callback_function($field, $value, $existing_value){

    $error = false;
    $value =  'just testing';
    /*
    do your validation

    if(something){
        $value = $value;
    }elseif(somthing else){
        $error = true;
        $value = $existing_value;
        $field['msg'] = 'your custom error message';
    }
    */

    $return['value'] = $value;
    if($error == true){
        $return['error'] = $field;
    }
    return $return;

}//function
