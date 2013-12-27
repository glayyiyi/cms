<?php

function add_sidebar_select(){
    add_meta_box(
        'sidebar_select',
        'Select widget sidebars',
        'show_sidebar_select',
        'page',
        'side',
        'default'
    );

}


add_action('add_meta_boxes', 'add_sidebar_select');


$sidebar_variants = array();
$custom_meta_fields = array();

function create_sidebar_metabox_vars(){
    global $sidebar_variants, $custom_meta_fields;
    $sidebar_variants['None'] = array(
        'label' => 'Default',
        'value' => 'none'
    );
    $prefix = 'sidebar_';
    $options = get_option(NHPOPTIONS.'sidebars');
    if ( is_array ($options) )
        foreach($options as $name=>$position){
            $id = str_replace(' ', '-', $name );
            $sidebar_variants[$id] = array(
                'label' => $name,
                'value' => $id
            );
        }


    $custom_meta_fields = array(
        array(
            'label'=> 'Sidebar 1',
            'desc'  => 'Select sidebar.',
            'id'    => $prefix.'1',
            'type'  => 'select',
            'options' => $sidebar_variants,

        ),
        array(
            'label'=> 'Sidebar 2',
            'desc'  => 'Select sidebar.',
            'id'    => $prefix.'2',
            'type'  => 'select',
            'options' => $sidebar_variants,

        ),
    );
}

add_action('init', 'create_sidebar_metabox_vars');




function show_sidebar_select() {
    global $custom_meta_fields, $post;

    echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($custom_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
        switch($field['type']) {
            case 'select':
                echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                foreach ($field['options'] as $option) {
                    echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                }
                echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                break;
        } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table
}

function save_custom_meta_sidebar($post_id) {
    global $custom_meta_fields;
    // verify nonce
    if (isset($_POST['custom_meta_box_nonce']) && !wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ( isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    // loop through fields and save the data
    foreach ($custom_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = isset($_POST[$field['id']])?$_POST[$field['id']]:'';
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'save_custom_meta_sidebar');