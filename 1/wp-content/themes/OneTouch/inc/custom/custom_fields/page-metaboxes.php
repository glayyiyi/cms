<?php
require_once ("common.php");

// Add the Meta Box
function page_custom_fields() {
    add_meta_box(
        'page_custom_fields', // $id
        'Page Options', // $title
        'show_page_custom_fields', // $callback
        'page', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'page_custom_fields');

// Field Array
$prefix = 'page_custom_';
$page_meta_custom_fields = array(
    array(
        'label' => 'Page subtitle',
        'desc'	=> 'Type here page subtitle, if you need it.',
        'id'	=> 'page_subtitle',
        'type'	=> 'text'
    ),
    array (
        'label' => 'Background image',
        'desc'	=> '',
        'id'	=> 'page_bg_image',
        'type'	=> 'image'
    ),
    array (
        'label' => 'Background color',
        'desc'	=> '',
        'id'	=> 'page_bg_color',
        'type'	=> 'colorpicker'
    ),
    array(
        'label' => 'Fixed background',
        'desc'	=> 'If is checked, page bakground is fixed',
        'id'	=> 'page_bg_fixed',
        'type'	=> 'checkbox'
    ),
    array(
        'label'=> 'Background repeat',
        'desc'  => 'Select repeat type of current page background.',
        'id'    => 'page_bg_repeat',
        'type'  => 'select',
        'options' => array (
            'one' => array (
                'label' => 'No Repeat',
                'value' => 'no-repeat'
            ),
            'two' => array (
                'label' => ' Repeat',
                'value' => 'repeat'
            ),
            'three' => array (
                'label' => 'Repeat X',
                'value' => 'repeat-x '
            ),
            'four' => array (
                'label' => 'Repeat Y',
                'value' => 'repeat-y '
            )
        )
    )
);

function show_page_custom_fields() {
    global $page_meta_custom_fields, $post;
    // Use nonce for verification
    echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
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
            break;

            case 'select':
                echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                foreach ($field['options'] as $option) {
                    echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
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
        } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table
    include_custom_fields_colorpicker();
}


// Save the Data
function save_page_custom_meta($post_id) {
    global $page_meta_custom_fields;
    // verify nonce
    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
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
    // loop through fields and save the data
    foreach ($page_meta_custom_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}

add_action('save_post', 'save_page_custom_meta');