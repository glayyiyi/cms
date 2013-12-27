<?php
/*
* Custom param types
*/

function tile_size_settings_field($settings, $value) {
    $dependency = vc_generate_dependencies_attributes($settings);
    return '<div class="tile_size" data-name="'.$settings["param_name"].'">'
               .'<div class="size active" data-size="mini"><img src="'.get_template_directory_uri().'/inc/vc-tile/assets/img/single-size.png'.'" /></div>'
               .'<div class="size" data-size="double"><img src="'.get_template_directory_uri().'/inc/vc-tile/assets/img/double-size.png'.'" /></div>'
               .'<input name="'.$settings['param_name']
               .'" class="wpb_vc_param_value wpb-textinput '
               .$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'
               .$value.'" ' . $dependency . '/>'
               .'</div>';
}
if(function_exists('add_shortcode_param'))
    add_shortcode_param('tile_size', 'tile_size_settings_field',get_template_directory_uri().'/inc/vc-tile/tile_size/tile_size.js');

