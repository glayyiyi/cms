<?php

if( current_user_can( 'edit_themes' ) )
    add_action('admin_menu', 'add_menu_customizer_menu');

function add_menu_customizer_menu() {
    add_theme_page('Metro Menu Customizer', 'Metro Menu Customizer', 'read', 'menu-customizer', 'menu_customizer');
}

function menu_customizer(){
    if( current_user_can( 'edit_themes' ) ){
        add_custom_menu_assets();
        echo '<h2>Metro Menu Customizer</h2>';
        show_menu_list();
        show_menus_items_list();
    } else {
        echo "<h2>You do not have sufficient permissions to access this page.</h2>";
    }

}

function show_menu_list(){
    echo 'Select menu:<br>';
    echo '<select id="menu-list">';
    $menus = get_terms('nav_menu');
    foreach($menus as $menu){
        echo '<option value="'.$menu->slug.'">'.$menu->name.'</option>';
    }
    echo '</select>';
}

function show_menus_items_list(){
    $menus = get_terms('nav_menu');
    $menus_params = (array)get_option("custom_metro_menus");
    foreach($menus as $menu){
        echo '<ul class="metro-menu-items"  style="clear:both;" data-menu="'.$menu->slug.'">';
        $current_menu = $menus_params[$menu->slug];
        $args = array();
        $items = wp_get_nav_menu_items( $menu, $args );
        foreach($items as $item ){
            if( $item->menu_item_parent == 0 ){
                echo '<li class="metro-menu-item"><a href="" style="background-color:'.$current_menu["items"][$item->ID]["color"].'; background-image:url('.$current_menu["items"][$item->ID]["bgimage"].')";"><span class="item-title">'.$item->title.'<span><div class="tile-icon" style="background-image:url('.aq_resize($current_menu["items"][$item->ID]["icon"],110,96, true).')"></div></a>';
                echo '<div class="metro-menu-settings">Background color:<input type="text" value="'.$current_menu["items"][$item->ID]["color"].'" class="metro-item-color" data-item="'.$item->ID.'" /><br>';
                echo 'Background image:<input type="text" value="'.$current_menu["items"][$item->ID]["bgimage"].'" class="metro-item-bgimage bgimage-item-'.$item->ID.'" data-item="'.$item->ID.'" /><br><a data-item="'.$item->ID.'" class = "button-secondary remove-image">Remove image</a><br/>';
                echo 'Background icon:<input type="text" value="'.$current_menu["items"][$item->ID]["icon"].'" class="metro-item-icon icon-item-'.$item->ID.'" data-item="'.$item->ID.'" /><br><a data-item="'.$item->ID.'"  class = "button-secondary remove-icon">Remove icon</a></div></li>';
            }
        }
        echo '</ul>';
    }
    echo '<div id="metro-menu-colorpicker" ></div>';
    echo '<div class="clear"></div> ';
    echo '<br><a class="button-primary" style="float:none;" id="save_metro_menu">Save</a>';
}

function add_custom_menu_assets(){
    wp_enqueue_script('jquery');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('media-upload');

    //wp_enqueue_script('custom_menu_main');

    wp_register_script('custom_menu_main', get_template_directory_uri().'/inc/menu_customizer/assets/js/custom_menu_main.js', false, null, true);
    wp_enqueue_script('custom_menu_main');

    wp_register_script('quadratic-colorpicker-script', get_template_directory_uri().'/inc/menu_customizer/assets/colorpicker/farbtastic.js', false, null, true);
    wp_enqueue_script('quadratic-colorpicker-script');

    wp_enqueue_style('quadratic-colorpicker-style', get_template_directory_uri().'/inc/menu_customizer/assets/colorpicker/farbtastic.css', false, null);

    wp_enqueue_style('custom_menu_style', get_template_directory_uri() . '/inc/menu_customizer/assets/css/style.css', false, null);

}
add_action('wp_enqueue_scripts', 'add_custom_menu_assets');



function save_metro_menu(){
    $menu = str_replace("\\","",$_POST['menu']);
    $menu_object = json_decode($menu);
    $menu = array();

    $menu[ $menu_object->menu_slug]['items'] = array();
    foreach((array)$menu_object->menu_items as $key=>$item){
        foreach( (array)$item as $k=>$i ){
            $menu[ $menu_object->menu_slug]['items'][$key][$k] = $i;
        }
    }
    $menus = array_merge((array)get_option("custom_metro_menus"), (array)$menu);
    update_option("custom_metro_menus", $menus);
}

add_action("wp_ajax_save_metro_menu", "save_metro_menu");