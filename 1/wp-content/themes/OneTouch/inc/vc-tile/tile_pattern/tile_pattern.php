<?php
function tile_pattern_settings_field($settings, $value) {
    $dependency = vc_generate_dependencies_attributes($settings);
    return   '<div class="tile-pattern-container" data-name="'.$settings['param_name'].'">'
            .'<div class="tile-pattern active" data-pattern="BTL"><img src="'.get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/BTL.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="BTR"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/BTR.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="BTC"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/BTC.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TI"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TI.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TIN"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TIN.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TLD"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TLD.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TRD"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TRD.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TTL"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TTL.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TTR"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TTR.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="IBG"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/IBG.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="SIC"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/SIC.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="BIC"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/BIC.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TTLI"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TTLI.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="TTRI"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/TTRI.png" alt=""></div>'
            .'<div class="tile-pattern" data-pattern="SIN"><img src="'. get_template_directory_uri().'/inc/vc-tile/tile_pattern/img/SIN.png" alt=""></div>'
            .'<input name="'.$settings['param_name']
            .'" class="wpb_vc_param_value wpb-textinput '
            .$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'
            .$value.'" ' . $dependency . '/>'
            .'</div>';
}
if(function_exists('add_shortcode_param'))
    add_shortcode_param('tile_pattern', 'tile_pattern_settings_field',get_template_directory_uri().'/inc/vc-tile/tile_pattern/tile_pattern.js');

