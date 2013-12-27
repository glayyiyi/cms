<?php

function add_sidebar(){
    if($_POST['name'] != '')   {
        $sidebars = get_option(NHPOPTIONS.'sidebars');
        $sidebars[ $_POST['name'] ] = '';
        update_option( NHPOPTIONS.'sidebars', $sidebars );
    }

}
add_action('wp_ajax_add_sidebar', 'add_sidebar');
add_action('wp_ajax_nopriv_add_sidebar', 'add_sidebar');


function delete_sidebar(){
    $sidebars = get_option(NHPOPTIONS.'sidebars');
    unset($sidebars[ $_POST['name'] ] );
    update_option( NHPOPTIONS.'sidebars', $sidebars );

}

add_action('wp_ajax_delete_sidebar', 'delete_sidebar');
?>