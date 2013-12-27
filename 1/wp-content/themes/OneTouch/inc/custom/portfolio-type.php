<?php

add_action('init', 'portfolio_register');
  
function portfolio_register() {  
    $args = array(
        'labels' => array(
            'name' => __( 'Portfolio' ),
            'singular_name' => __( 'Project' )
        ),
        'public' => true,
        'show_ui' => true,
        'menu_position' => 10,
        'rewrite' => true,  
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions')
       );

    register_post_type( 'portfolio' , $args );  
}  

register_taxonomy("project_type", array("portfolio"), array("hierarchical" => true, "label" => "Project Types", "singular_label" => "Project Type", "rewrite" => true));

//add_action("admin_init", "portfolio_meta_box");


add_filter("manage_edit-portfolio_columns", "project_edit_columns");   
  
function project_edit_columns($columns){  
        $columns = array(  
            "cb" => "<input type=\"checkbox\" />",  
            "title" => "Project",  
            "description" => "Description",  
            "type" => "Type of Project",  
        );  
  
        return $columns;  
}  

add_action("manage_posts_custom_column",  "project_custom_columns"); 
  
function project_custom_columns($column){  
        global $post;  
        switch ($column)  
        {  
            case "description":  
                the_excerpt();  
                break;  
            case "type":  
                echo get_the_term_list($post->ID, 'project_type', '', ', ','');
                break;  
        }
}

?>