<?php
if(!defined('CF_URL'))
    define('CF_URL',get_template_directory_uri().'/inc/custom/custom_fields/');

if(!defined('CF_PATH'))
    define('CF_PATH',get_template_directory().'/inc/custom/custom_fields/');

function add_custom_field_assets(){
    wp_enqueue_script('farbtastic-colorpicker', CF_URL.'/assets/colorpicker/farbtastic.js', array('jquery'));
    wp_enqueue_script('custom-fields', CF_URL.'/assets/js/custom_fields.js', array('jquery'));


    wp_register_style( 'farbtastic-colorpicker',CF_URL.'/assets/colorpicker/farbtastic.css');
    wp_enqueue_style( 'farbtastic-colorpicker' );

    wp_register_style( 'custom-fields',CF_URL.'/assets/css/custom_fields.css');
    wp_enqueue_style( 'custom-fields' );
}
add_action('admin_enqueue_scripts', 'add_custom_field_assets');


function include_custom_fields_colorpicker(){
    echo '<div id="custom-fields-colorpicker"></div>';
}

function CF_print_metabox($page_meta_custom_fields, $post, $file){
    echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce($file).'" />';
    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($page_meta_custom_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>
				<th><strong><label for="'.$field['id'].'">'.$field['label'].'</label></strong></th>
		      <td>';
        switch($field['type']) {
            case 'checkbox':
                echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','/>
                <label for="'.$field['id'].'">'.$field['desc'].'</label>';
                break;

            case 'radio':
                foreach ( $field['options'] as $option ) {
                    if( !$meta && $option['default'] )
                        $meta = $option['value'];
                    echo '<input type="radio" name="'.$field['id'].'" id="'.$option['value'].'" value="'.$option['value'].'" ',$meta == $option['value'] ? ' checked="checked"' : '',' />
				<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
                }
                break;

            case 'text':
                echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                    <br /><span class="description">'.$field['desc'].'</span>';
                break;

            case 'colorpicker':
                echo '<input type="text" class="colorpicker" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                    <br /><span class="description">'.$field['desc'].'</span>';
                include_custom_fields_colorpicker();
                break;

            case 'select':
                echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                foreach ($field['options'] as $option) {
                    echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                }
                echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                break;

            case 'taxonomy':
                $values = get_terms($field['taxonomy'],"orderby=slug&order=ASC");
                echo '<input type="hidden"  name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'">';
                echo '<select multiple="multiple" class="taxonomy-selector" data-id="'.$field['id'].'" >';
                echo '<option value="all"', !$meta ? 'selected="selected"' : '', '>All</option>';
                foreach ($values as $value) {
                    echo '<option', $meta == $value->slug ? ' selected="selected"' : '', ' value="'.$value->slug.'">'.$value->name.'</option>';
                }
                echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                break;

            case 'image':
                if( !$meta ){
                    $remove_button_style = 'display:none;';
                } else {
                    $remove_button_style = '';
                }
                echo '<input type="hidden" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                    <input type="button" class="button cf-add-image" data-target="'.$field['id'].'" value = "Add Image" /><input type="button" style = "'.$remove_button_style.'" class="button cf-remove-image" data-target="'.$field['id'].'" value = "Remove Image" />
                    <br /><div id="image-'.$field['id'].'" ><img src = "'.$meta.'" class = "CF-uploaded-image" /></div><span class="description">'.$field['desc'].'</span>';
                break;
            case 'video':
                if( !$meta ){
                    $remove_button_style = 'display:none;';
                } else {
                    $remove_button_style = '';
                }
                echo '<input type="hidden" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                    <input type="button" class="button cf-add-video" data-target="'.$field['id'].'" value = "Add Video" /><input type="button" style = "'.$remove_button_style.'" class="button cf-remove-video" data-target="'.$field['id'].'" value = "Remove Video" />
                    <br /><div id="image-'.$field['id'].'" >'.$meta.'</div><span class="description">'.$field['desc'].'</span>';
                break;
        } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table
}


function CF_save_metabox($page_meta_custom_fields, $post_id, $file) {

    // verify nonce
    /*if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], $file ))
        return $post_id;   */
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    $fields = '';
    // loop through fields and save the data
    foreach ($page_meta_custom_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }

}
