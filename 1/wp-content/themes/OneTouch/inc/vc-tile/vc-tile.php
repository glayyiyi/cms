<?php
/*********************************
 *   Visual Composer Extension   *
 *********************************/
require_once(locate_template( "/inc/vc-tile/tile_size/tile_size.php" ));
require_once(locate_template( "/inc/vc-tile/tile_pattern/tile_pattern.php" ));

function tile_generator_style() {
    wp_enqueue_style('tile_generator_style', get_template_directory_uri() . '/inc/vc-tile/assets/css/frontend.css', false, null);
}
add_action('wp_enqueue_scripts', 'tile_generator_style');
add_action('admin_enqueue_scripts', 'tile_generator_style');

function crumina_tile( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'number' => '',
        'pattern' => 'none',
        'size' => '',
        'text' => '',
        'bgcolor'=>'none',
        'icon'=>'',
        'link' => '',
        'bgimage' => '',
    ), $atts ) );

    if($icon != ''){
        $icon = wp_get_attachment_image_src( $icon, 'full' );
        $icon_width = $icon[1];
        $icon_height = $icon[2];
        $icon = $icon[0];
    }

    switch($pattern){
        case "BTC":
            $content = "<div class='text-tile text-big-center' >".$text."</div><a href='".$link."'>&nbsp;</a>";
            break;
        case "BTR":
            $content = "<div class='text-tile text-big-right' >".$text."</div><a href='".$link."'>&nbsp;</a>";
            break;
        case "BTL":
            $content = "<div class='text-tile text-big-left' >".$text."</div><a href='".$link."'>&nbsp;</a>";
            break;
        case "TLD":
            $content = "<div class='text-tile text-left-down' >".$text."</div><a href='".$link."'>&nbsp;</a>";
            break;
        case "TRD":
            $content = "<div class='text-tile text-left-down' >".$text."</div><a href='".$link."'>&nbsp;</a>";
            break;
        case "TTR":
            $content = "<div class='text-tile text-right-top' >".$text."</div><a href='".$link."'>&nbsp;</a>";
            break;
        case "TTL":
            $content = "<div class='text-tile text-left-top' >".$text."</div><a href='".$link."'>&nbsp;</a>";
            break;
        case "IBG":
            if($size == 'double'){
                $icon = aq_resize( $icon, 342, 164, true);
            }else {
                $icon = aq_resize( $icon, 164, 164, true);
            };
            $content = "<img class='ibg' src='". $icon ."' alt='' /><a href='".$link."'>&nbsp;</a>";
            break;

        case "SIC":
            $icon = aq_resize( $icon, 96, 96,true, false);

            $margin_top = (164 - $icon[1])/2;
            if($size == 'double')
                $margin_left = (332 - $icon[2])/2;
            else
                $margin_left = (164 - $icon[2])/2;
            $icon = $icon[0];
            $content = "<div style='margin-top:".$margin_top."px;  class='icon small_icon_center' ><img src='".$icon."' alt=''></div><a href='".$link."'>&nbsp;</a>";
            break;
        case "BIC":
            $icon = aq_resize( $icon, 128, 128,true, false);

            $margin_top = (164 - $icon[1])/2;
            if($size == 'double')
                $margin_left = (334 - $icon[2])/2;
            else
                $margin_left = (164 - $icon[2])/2;
            $icon = $icon[0];
            $content = "<div  style='margin-top:".$margin_top."px;' class='icon big_icon_center' ><img src='".$icon."' alt=''></div><a href='".$link."'>&nbsp;</a>";
            break;
        case "TI":
            $icon = aq_resize( $icon, 70, 70,true);
            $content = "<div class='icon icon_center' ><img src='".$icon."' alt=''></div><a href='".$link."'>&nbsp;</a>";
            $content .="<div class='text-tile text-mini-left' >".$text."</div>";
            break;
        case "TTLI":
            $icon = aq_resize( $icon, 96, 96,true, false);
            $margin_top = (164 - $icon[1])/2;
            if($size == 'double')
                $margin_left = (332 - $icon[2])/2;
            else
                $margin_left = (164 - $icon[2])/2;
            $icon = $icon[0];
            $content = "<div style='margin-top:".$margin_top."px;'  class='icon icon_left' ><img src='".$icon."' alt=''></div><a href='".$link."'>&nbsp;</a>";
            $content .="<div class='text-tile text-left-top' >".$text."</div>";
            break;
        case "TTRI":
            $icon = aq_resize( $icon, 96, 96,true, false);
            $margin_top = (164 - $icon[1])/2;
            if($size == 'double')
                $margin_left = (332 - $icon[2])/2;
            else
                $margin_left = (164 - $icon[2])/2;
            $icon = $icon[0];
            $content = "<div style='margin-top:".$margin_top."px;' class='icon icon_left' ><img src='".$icon."' alt=''></div><a href='".$link."'>&nbsp;</a>";
            $content .="<div class='text-tile text-right-top' >".$text."</div>";
            break;
        case "TIN":
            $icon = aq_resize( $icon, 96, 96,true);
            $content = "<div class='icon icon_center' ><img src='".$icon."' alt=''></div> <a href='".$link."'>&nbsp;</a>";
            $content .="<div class='text-tile text-mini-left' >".$text."</div><span class='number'>".$number."</span>";
            break;
        case "SIN":
            $icon = aq_resize( $icon, 96, 96,true, false);
            $margin_top = (164 - $icon[1])/2;
            $icon = $icon[0];
            $content = "<div style='margin-top:".$margin_top."px;' class='icon icon_center-number' ><img src='".$icon."' alt=''></div><a href='".$link."'>&nbsp;</a>";
            $content .="<div class='text-tile text-mini-number' ></div><span class='number number-center'>".$number."</span>";
            break;
        default:
            break;
    }


    $output = "<div class='tile ".$size."' style='background-color:".$bgcolor."'>".$content."</div>";
    return $output;
}
add_shortcode( 'tile', 'crumina_tile' );

if(function_exists("wpb_map"))
    //Adding shortcode to VC
    wpb_map( array(
        "name" => __("Metro Tile"),
        "base" => "tile",
        "class" => "metro-tile",
        "controls" => "full",
        "category" => __('Content'),
        'admin_enqueue_js' => array(get_template_directory_uri().'/inc/vc-tile/assets/js/vc-tile.js'),
        'admin_enqueue_css' => array(get_template_directory_uri().'/inc/vc-tile/assets/css/vc-tile.css'),
        "params" => array(
            array(
                "type" => "tile_pattern",
                "holder" => "div",
                "class" => "",
                "heading" => __("Tile pattern"),
                "param_name" => "pattern",
                "value" => 'BTL', //Default Red color
                "description" => __("")
            ),
            array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Tile background color"),
                "param_name" => "bgcolor",
                "value" => '#FF0000', //Default Red color
                "description" => __("Choose text color")
            ),
            array(
                "type" => "textfield",
                "holder" => "div",
                "class" => "",
                "heading" => __("Text ont the tile"),
                "param_name" => "text",
                "value" => __("Demo tile"),
                "description" => __("Text, will be displayed on your tile")
            ),
            array(
                "type" => "textfield",
                "holder" => "div",
                "class" => "",
                "heading" => __("Number on the tile"),
                "param_name" => "number",
                "value" => __("12"),
                "description" => __("Number on special tile style")
            ),
            array(
                "type" => "textfield",
                "holder" => "div",
                "class" => "",
                "heading" => __("Link"),
                "param_name" => "link",
                "value" => __("http://"),
                "description" => __("Link for the tile")
            ),
            array(
                "heading" => __("Upload image"),
                "type" => "attach_image",
                "param_name" => "icon",
                "description" => __("Upload image to display on the tile"),
            ),
            array(
                "type" => "tile_size",
                "holder" => "div",
                "class" => "",
                "heading" => __("Select tile size"),
                "param_name" => "size",
                "description" => __(""),
                "value"=>"mini"
            ),
        )
    ) );



