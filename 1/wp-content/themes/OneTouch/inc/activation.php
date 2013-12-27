<?php

if (is_admin() && isset($_GET['activated']) && 'themes.php' == $GLOBALS['pagenow']) {
    wp_redirect(admin_url('themes.php?page=theme_activation_options'));
    exit;
}

function roots_theme_activation_options_init()
{
    if (roots_get_theme_activation_options() === false) {
        add_option('roots_theme_activation_options', roots_get_default_theme_activation_options());
    }

    register_setting(
        'roots_activation_options',
        'roots_theme_activation_options',
        'roots_theme_activation_options_validate'
    );
}

add_action('admin_init', 'roots_theme_activation_options_init');

function roots_activation_options_page_capability($capability)
{
    return 'edit_theme_options';
}

add_filter('option_page_capability_roots_activation_options', 'roots_activation_options_page_capability');

function roots_theme_activation_options_add_page()
{
    $roots_activation_options = roots_get_theme_activation_options();
    if (!$roots_activation_options['first_run']) {
        $theme_page = add_theme_page(
            __('Theme Activation', 'roots'),
            __('Theme Activation', 'roots'),
            'edit_theme_options',
            'theme_activation_options',
            'roots_theme_activation_options_render_page'
        );
    } else {
        if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'theme_activation_options') {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
            wp_redirect(admin_url('themes.php'));
            exit;
        }
    }

}

add_action('admin_menu', 'roots_theme_activation_options_add_page', 50);

function roots_get_default_theme_activation_options()
{
    $default_theme_activation_options = array(
        'first_run' => false,
        'create_front_page' => false,
        'change_permalink_structure' => false,
        'load_custom_fields' => false,
        'change_uploads_folder' => false,
        'create_navigation_menus' => false,
        'add_pages_to_primary_navigation' => false,
    );

    return apply_filters('roots_default_theme_activation_options', $default_theme_activation_options);
}

function roots_get_theme_activation_options()
{
    return get_option('roots_theme_activation_options', roots_get_default_theme_activation_options());
}

function roots_theme_activation_options_render_page()
{
    global $wpdb;
    if(!get_option("custom_metro_menus")) {
        $option = 'a:2:{i:0;b:0;s:18:"primary-navigation";a:1:{s:5:"items";a:6:{i:42;a:3:{s:5:"color";s:7:"#ffaa31";s:7:"bgimage";s:0:"";s:4:"icon";s:74:"http://theme.crumina.net/onetouch/wp-content/uploads/2012/12/Landscape.png";}i:194;a:3:{s:5:"color";s:7:"#ffaa31";s:7:"bgimage";s:0:"";s:4:"icon";s:0:"";}i:439;a:3:{s:5:"color";s:7:"#6cbe42";s:7:"bgimage";s:71:"http://theme.crumina.net/onetouch/wp-content/uploads/2012/12/earth1.png";s:4:"icon";s:0:"";}i:527;a:3:{s:5:"color";s:7:"#cecece";s:7:"bgimage";s:97:"http://theme.crumina.net/onetouch/wp-content/uploads/2012/12/tumblr_m3yj7gRlDS1qdq19eo1_12801.jpg";s:4:"icon";s:0:"";}i:705;a:3:{s:5:"color";s:7:"#ffaa31";s:7:"bgimage";s:0:"";s:4:"icon";s:70:"http://theme.crumina.net/onetouch/wp-content/uploads/2012/12/pppp1.png";}i:973;a:3:{s:5:"color";s:7:"#57bae8";s:7:"bgimage";s:68:"http://theme.crumina.net/onetouch/wp-content/uploads/2012/12/874.jpg";s:4:"icon";s:0:"";}}}}';
        $sql = "INSERT INTO `".$wpdb->prefix."options` (`option_id`, `option_name`, `option_value`) VALUES (NULL,'option_name', '".$option."');";
    }    ?>

<div class="wrap">
    <?php screen_icon(); ?>
  <h2><?php printf(__('%s Theme Activation', 'roots'), wp_get_theme()); ?></h2>
    <?php settings_errors(); ?>
  <!--<h3 style="color:red;"><strong>Attention!!!</strong> Press "Save Changes" button now. And only after this install required plugins.</h3> -->
  <form method="post" action="options.php">

      <?php
      settings_fields('roots_activation_options');
      create_static_front_page();
      $roots_activation_options = roots_get_theme_activation_options();
      $roots_default_activation_options = roots_get_default_theme_activation_options();
      ?>

    <input type="hidden" value="1" name="roots_theme_activation_options[first_run]"/>

    <table class="form-table">

      <tr valign="top" style="display: none;">
        <th scope="row"><?php _e('Create static front page?', 'roots'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e('Create static front page?', 'roots'); ?></span></legend>
            <select name="roots_theme_activation_options[create_front_page]" id="create_front_page">
              <option value="yes" selected="selected"><?php echo _e('Yes', 'roots'); ?></option>
              <option value="no"><?php echo _e('No', 'roots'); ?></option>
            </select>
            <br/>
            <small class="description"><?php printf(__('Create a page called Home and set it to be the static front page', 'roots')); ?></small>
          </fieldset>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('Change permalink structure?', 'roots'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e('Update permalink structure?', 'roots'); ?></span>
            </legend>
            <select name="roots_theme_activation_options[change_permalink_structure]" id="change_permalink_structure">
              <option selected="selected" value="yes"><?php echo _e('Yes', 'roots'); ?></option>
              <option value="no"><?php echo _e('No', 'roots'); ?></option>
            </select>
            <br/>
            <small class="description"><?php printf(__('Change permalink structure to /&#37;postname&#37;/', 'roots')); ?></small>
          </fieldset>
        </td>
      </tr>

    <tr valign="top" style="display: none">
        <th scope="row"><?php _e('Load custom fields', 'roots'); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Load custom fields', 'roots'); ?></span>
                </legend>
                <select name="roots_theme_activation_options[load_custom_fields]" id="load_custom_fields">
                    <option value="yes"><?php echo _e('Yes', 'roots'); ?></option>
                    <option selected="selected" value="no"><?php echo _e('No', 'roots'); ?></option>
                </select>
                <br/>
                <small class="description"><?php printf(__('Load custom fields?', 'roots')); ?></small>
            </fieldset>
        </td>
    </tr>

      <tr valign="top" style="display: none;">
        <th scope="row"><?php _e('Change uploads folder?', 'roots'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e('Update uploads folder?', 'roots'); ?></span></legend>
            <select name="roots_theme_activation_options[change_uploads_folder]" id="change_uploads_folder">
              <option value="yes"><?php echo _e('Yes', 'roots'); ?></option>
              <option value="no" selected="selected" ><?php echo _e('No', 'roots'); ?></option>
            </select>
            <br/>
            <small class="description"><?php printf(__('Change uploads folder to /assets/ instead of /wp-content/uploads/', 'roots')); ?></small>
          </fieldset>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('Create navigation menu?', 'roots'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e('Create navigation menu?', 'roots'); ?></span></legend>
            <select name="roots_theme_activation_options[create_navigation_menus]" id="create_navigation_menus">
              <option selected="selected" value="yes"><?php echo _e('Yes', 'roots'); ?></option>
              <option value="no"><?php echo _e('No', 'roots'); ?></option>
            </select>
            <br/>
            <small class="description"><?php printf(__('Create the Primary Navigation menu and set the location', 'roots')); ?></small>
          </fieldset>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('Add pages to menu?', 'roots'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e('Add pages to menu?', 'roots'); ?></span></legend>
            <select name="roots_theme_activation_options[add_pages_to_primary_navigation]" id="add_pages_to_primary_navigation">
              <option value="yes"><?php echo _e('Yes', 'roots'); ?></option>
              <option selected="selected" value="no"><?php echo _e('No', 'roots'); ?></option>
            </select>
            <br/>
            <small class="description"><?php printf(__('Add all current published pages to the Primary Navigation', 'roots')); ?></small>
          </fieldset>
        </td>
      </tr>

    </table>

      <?php submit_button(); ?>
  </form>
</div>

<?php
}

function roots_theme_activation_options_validate($input)
{
    $output = $defaults = roots_get_default_theme_activation_options();

    if (isset($input['load_defaults'])) {
        if ($input['load_defaults'] === 'yes') {
            $input['load_defaults'] = true;
        }
        if ($input['load_defaults'] === 'no') {
            $input['load_defaults'] = false;
        }
        $output['load_defaults'] = $input['load_defaults'];
    }

    if (isset($input['first_run'])) {
        if ($input['first_run'] === '1') {
            $input['first_run'] = true;
        }
        $output['first_run'] = $input['first_run'];
    }

    if (isset($input['create_front_page'])) {
        if ($input['create_front_page'] === 'yes') {
            $input['create_front_page'] = true;
        }
        if ($input['create_front_page'] === 'no') {
            $input['create_front_page'] = false;
        }
        $output['create_front_page'] = $input['create_front_page'];
    }

    if (isset($input['change_permalink_structure'])) {
        if ($input['change_permalink_structure'] === 'yes') {
            $input['change_permalink_structure'] = true;
        }
        if ($input['change_permalink_structure'] === 'no') {
            $input['change_permalink_structure'] = false;
        }
        $output['change_permalink_structure'] = $input['change_permalink_structure'];
    }


    if (isset($input['load_custom_fields'])) {
        if ($input['load_custom_fields'] === 'yes') {
            $input['load_custom_fields'] = true;
        }
        if ($input['load_custom_fields'] === 'no') {
            $input['load_custom_fields'] = false;
        }
        $output['load_custom_fields'] = $input['load_custom_fields'];
    }


    if (isset($input['change_uploads_folder'])) {
        if ($input['change_uploads_folder'] === 'yes') {
            $input['change_uploads_folder'] = true;
        }
        if ($input['change_uploads_folder'] === 'no') {
            $input['change_uploads_folder'] = false;
        }
        $output['change_uploads_folder'] = $input['change_uploads_folder'];
    }

    if (isset($input['create_navigation_menus'])) {
        if ($input['create_navigation_menus'] === 'yes') {
            $input['create_navigation_menus'] = true;
        }
        if ($input['create_navigation_menus'] === 'no') {
            $input['create_navigation_menus'] = false;
        }
        $output['create_navigation_menus'] = $input['create_navigation_menus'];
    }

    if (isset($input['add_pages_to_primary_navigation'])) {
        if ($input['add_pages_to_primary_navigation'] === 'yes') {
            $input['add_pages_to_primary_navigation'] = true;
        }
        if ($input['add_pages_to_primary_navigation'] === 'no') {
            $input['add_pages_to_primary_navigation'] = false;
        }
        $output['add_pages_to_primary_navigation'] = $input['add_pages_to_primary_navigation'];
    }

    return apply_filters('roots_theme_activation_options_validate', $output, $input, $defaults);
}

function create_static_front_page() {

    $default_pages = array('Help');
    $existing_pages = get_pages();
    $temp = array();

    foreach ($existing_pages as $page) {
        $temp[] = $page->post_title;
    }

    $pages_to_create = array_diff($default_pages, $temp);
    $page_demo = <<<EOD
<div class="promo"><span class="icon info">&nbsp;</span><h2>First time setup page</h2><h5>Here we describe how to setup One Touch theme</h5></div>
<h1>Wellcome! This page will help you to setup home page. Just follow the instructions:</h1>
<hr />
<p>&nbsp;</p>
<h2><img class="alignleft" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-1.jpg" width="432" height="295" />If you want your home page is the same to our demo</h2>
<p>Just import demo content, Go to Tools-&gt;Import-&gt;Wordpress and using file uploader import content.xml file from ImportXML directory, that you’ll find in archive with theme.And go to Settings-&gt;Reading on the main admin menu. And set  <strong>Front page displays </strong>"A static page" and select page "Home". <br> &nbsp; </p>
<img class="alignnone" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-2.png" width="472" height="142" />
<p>&nbsp;</p>
<hr />
<p>&nbsp;</p>
<h2><img class="alignleft" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-3.png" width="437" height="298" />If you want to add own content</h2>
<p>If you want to add content into the horizontal slider and Recent Projects block, just add your content to Posts and Portfolio. If you need change home page layout go to Page Builder and set your settings for Homepage template</p>
<p>&nbsp;</p>
<img class="alignnone" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-4.png" width="140" height="141" />
EOD;
    foreach ($pages_to_create as $new_page_title) {
        $add_default_pages = array(
            'post_title' => $new_page_title,
            'post_content' => $page_demo,
            'post_status' => 'publish',
            'post_type' => 'page'
        );

        $result = wp_insert_post( $add_default_pages );
    }


    $home = get_page_by_title('Help');
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home->ID);

    //update_post_meta($home->ID, "_wp_page_template", "page-custom.php");

    $home_menu_order = array(
        'ID' => $home->ID,
        'menu_order' => -1
    );
    wp_update_post($home_menu_order);
}


function roots_theme_activation_action()
{
    $roots_theme_activation_options = roots_get_theme_activation_options();

    if ($roots_theme_activation_options['create_front_page']) {
        $roots_theme_activation_options['create_front_page'] = false;

        $default_pages = array('Help');
        $existing_pages = get_pages();
        $temp = array();

        foreach ($existing_pages as $page) {
            $temp[] = $page->post_title;
        }

        $pages_to_create = array_diff($default_pages, $temp);
        $page_demo = <<<EOD
<div class="promo"><span class="icon info">&nbsp;</span><h2>First time setup page</h2><h5>Here we describe how to setup One Touch theme</h5></div>
<h1>Wellcome! This page will help you to setup home page. Just follow the instructions:</h1>
<hr />
<p>&nbsp;</p>
<h2><img class="alignleft" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-1.jpg" width="432" height="295" />If you want your home page is the same to our demo</h2>
<p>Just import demo content, Go to Tools-&gt;Import-&gt;Wordpress and using file uploader import content.xml file from ImportXML directory, that you’ll find in archive with theme.And go to Settings-&gt;Reading on the main admin menu. And set  <strong>Front page displays </strong>"A static page" and select page "Home". <br> &nbsp; </p>
<img class="alignnone" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-2.png" width="472" height="142" />
<p>&nbsp;</p>
<hr />
<p>&nbsp;</p>
<h2><img class="alignleft" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-3.png" width="437" height="298" />If you want to add own content</h2>
<p>If you want to add content into the horizontal slider and Recent Projects block, just add your content to Posts and Portfolio. If you need change home page layout go to Page Builder and set your settings for Homepage template</p>
<p>&nbsp;</p>
<img class="alignnone" alt="" src="http://theme.crumina.net/promo/onetouch/help-image-4.png" width="140" height="141" />
EOD;
        foreach ($pages_to_create as $new_page_title) {
            $add_default_pages = array(
                'post_title' => $new_page_title,
                'post_content' => $page_demo,
                'post_status' => 'publish',
                'post_type' => 'page'
            );

            $result = wp_insert_post( $add_default_pages );
        }


        $home = get_page_by_title('Help');
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home->ID);

        //update_post_meta($home->ID, "_wp_page_template", "page-custom.php");

        $home_menu_order = array(
            'ID' => $home->ID,
            'menu_order' => -1
        );
        wp_update_post($home_menu_order);
    }


    if ($roots_theme_activation_options['change_permalink_structure']) {
        $roots_theme_activation_options['change_permalink_structure'] = false;

        if (get_option('permalink_structure') !== '/%postname%/') {
            update_option('permalink_structure', '/%postname%/');
        }

        global $wp_rewrite;
        $wp_rewrite->init();
        $wp_rewrite->flush_rules();
    }

    if ($roots_theme_activation_options['load_custom_fields']) {
        load_php_custom_fields();
    }

    if ($roots_theme_activation_options['change_uploads_folder']) {
        $roots_theme_activation_options['change_uploads_folder'] = false;

        update_option('uploads_use_yearmonth_folders', 0);
        update_option('upload_path', 'assets');
    }

    if (isset($roots_theme_activation_options['load_defaults'])) {
        if ($roots_theme_activation_options['load_defaults']) {
            if ((int)get_option('Restyle_Theory_is_active') == 0) {
                load_defaults();
                update_option('Restyle_Theory_is_active', 1);
            }
        }
    }


    if ($roots_theme_activation_options['create_navigation_menus']) {
        $roots_theme_activation_options['create_navigation_menus'] = false;

        $roots_nav_theme_mod = false;

        if (!has_nav_menu('primary_navigation')) {
            $primary_nav_id = wp_create_nav_menu('Primary Navigation', array('slug' => 'primary_navigation'));
            $roots_nav_theme_mod['primary_navigation'] = $primary_nav_id;
        }

        if ($roots_nav_theme_mod) {
            set_theme_mod('nav_menu_locations', $roots_nav_theme_mod);
        }
    }

    if ($roots_theme_activation_options['add_pages_to_primary_navigation']) {
        $roots_theme_activation_options['add_pages_to_primary_navigation'] = false;

        $primary_nav = wp_get_nav_menu_object('Primary Navigation');
        $primary_nav_term_id = (int)$primary_nav->term_id;
        $menu_items = wp_get_nav_menu_items($primary_nav_term_id);
        if (!$menu_items || empty($menu_items)) {
            $pages = get_pages();
            foreach ($pages as $page) {
                $item = array(
                    'menu-item-object-id' => $page->ID,
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish'
                );
                wp_update_nav_menu_item($primary_nav_term_id, 0, $item);
            }
        }
    }

    update_option('roots_theme_activation_options', $roots_theme_activation_options);
}

add_action('admin_init', 'roots_theme_activation_action');


function roots_deactivation_action()
{
    $roots_theme_activation_options = roots_get_default_theme_activation_options();
    update_option('Restyle_Theory_is_active', 0);

    update_option('roots_theme_activation_options', roots_get_default_theme_activation_options());
}

add_action('switch_theme', 'roots_deactivation_action');

function load_php_custom_fields(){
    /**
     * Activate Add-ons
     * Here you can enter your activation codes to unlock Add-ons to use in your theme.
     * Since all activation codes are multi-site licenses, you are allowed to include your key in premium themes.
     */
    function my_acf_settings( $options )
    {
        // activate add-ons
        $options['activation_codes']['repeater'] = 'XXXX-XXXX-XXXX-XXXX';
        $options['activation_codes']['options_page'] = 'XXXX-XXXX-XXXX-XXXX';
        $options['activation_codes']['flexible_content'] = 'XXXX-XXXX-XXXX-XXXX';
        $options['activation_codes']['gallery'] = 'XXXX-XXXX-XXXX-XXXX';

        // setup other options (http://www.advancedcustomfields.com/docs/filters/acf_settings/)

        return $options;

    }
    add_filter('acf_settings', 'my_acf_settings');


    /**
     * Register field groups
     * The register_field_group function accepts 1 array which holds the relevant data to register a field group
     * You may edit the array as you see fit. However, this may result in errors if the array is not compatible with ACF
     * This code must run every time the functions.php file is read
     */

    if(function_exists("register_field_group"))
    {

        register_field_group(array (
            'id' => '50e9448dab2b9',
            'title' => 'Work info',
            'fields' =>
            array (
                0 =>
                array (
                    'key' => 'field_50be429bfddd6',
                    'label' => 'Designer',
                    'name' => 'designer',
                    'type' => 'text',
                    'order_no' => 0,
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' =>
                    array (
                        'status' => 0,
                        'rules' =>
                        array (
                            0 =>
                            array (
                                'field' => 'null',
                                'operator' => '==',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'default_value' => '',
                    'formatting' => 'html',
                ),
                1 =>
                array (
                    'key' => 'field_50be42d7fddd7',
                    'label' => 'Photographer',
                    'name' => 'photographer',
                    'type' => 'text',
                    'order_no' => 1,
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' =>
                    array (
                        'status' => 0,
                        'rules' =>
                        array (
                            0 =>
                            array (
                                'field' => 'null',
                                'operator' => '==',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'default_value' => '',
                    'formatting' => 'html',
                ),
                2 =>
                array (
                    'key' => 'field_50be42eefddd8',
                    'label' => 'Client',
                    'name' => 'client',
                    'type' => 'text',
                    'order_no' => 2,
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' =>
                    array (
                        'status' => 0,
                        'rules' =>
                        array (
                            0 =>
                            array (
                                'field' => 'null',
                                'operator' => '==',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'default_value' => '',
                    'formatting' => 'html',
                ),
                3 =>
                array (
                    'key' => 'field_50be430afddd9',
                    'label' => 'Wesite link',
                    'name' => 'wesite_link',
                    'type' => 'text',
                    'order_no' => 3,
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' =>
                    array (
                        'status' => 0,
                        'rules' =>
                        array (
                            0 =>
                            array (
                                'field' => 'null',
                                'operator' => '==',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'default_value' => '',
                    'formatting' => 'html',
                ),
            ),
            'location' =>
            array (
                'rules' =>
                array (
                    0 =>
                    array (
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'portfolio',
                        'order_no' => '0',
                    ),
                ),
                'allorany' => 'all',
            ),
            'options' =>
            array (
                'position' => 'side',
                'layout' => 'default',
                'hide_on_screen' =>
                array (
                ),
            ),
            'menu_order' => 0,
        ));
    }

}



