<?php
if (!function_exists('register_button')){
function register_button( $buttons ){
   array_push( $buttons, "|", "qode_shortcodes" );
   return $buttons;
}
}

if (!function_exists('add_plugin')){
function add_plugin( $plugin_array ) {
   $plugin_array['qode_shortcodes'] = get_template_directory_uri() . '/includes/qode_shortcodes.js';
   return $plugin_array;
}
}

if (!function_exists('qode_shortcodes_button')){
function qode_shortcodes_button(){
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
      return;
   }

   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'add_plugin' );
      add_filter( 'mce_buttons', 'register_button' );
   }
}
}
add_action('init', 'qode_shortcodes_button');


if (!function_exists('no_wpautop')) {
function no_wpautop($content){ 
    $content = do_shortcode( shortcode_unautop($content) ); 
    $content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );
    return $content;
}
}
if (!function_exists('num_shortcodes')){
function num_shortcodes($content){ 
    $columns = substr_count( $content, '[pricing_cell' );
	return $columns;
}
}

/* Accordion shortcode */

if (!function_exists('accordion')) {
function accordion($atts, $content = null) {
	extract(shortcode_atts(array("accordion_type"=>""), $atts));
	return "<div class='accordion_holder $accordion_type clearfix'>" . no_wpautop($content) . "</div>";
}
}
add_shortcode('accordion', 'accordion');

/* Accordion item shortcode */

if (!function_exists('accordion_item')) {
function accordion_item($atts, $content = null) {

	extract(shortcode_atts(array("caption"=>"","title_color"=>""), $atts));
	return "<h3 style='color: ".$title_color.";'><span><span class='control-pm'></span></span>".$caption."</h3><div class='accordion_content'><div class='accordion_content_inner'>" . no_wpautop($content) . "</div></div>";
}
}
add_shortcode('accordion_item', 'accordion_item');

/* Action shortcode */

if (!function_exists('action')) {
function action($atts, $content = null) {
	extract(shortcode_atts(array("background_color"=>"","border"=>"","border_color"=>""), $atts));
	$html =  "<div class='call_to_action";
	if($border == "no"){
		$html .= " without_border' style='background-color: ".$background_color.";'>" . no_wpautop($content) . "</div>";
	} else {
		$html .= "' style='background-color: ".$background_color."; border-color: ".$border_color.";'>" . no_wpautop($content) . "</div>";
	}
    return $html;
}
}
add_shortcode('action', 'action');

/* Blockquote shortcode */

if (!function_exists('blockquote')) {
function blockquote($atts, $content = null) {
	$html = ""; 
  extract(shortcode_atts(array("width"=>""), $atts));
	$html .= "<blockquote"; 
	if($width > 0){
		$html .= " style=width:$width%;";
	}
	$html .= "><span class='blockquote_mark'></span>".no_wpautop($content)."</blockquote>";
  return $html;
}
}
add_shortcode('blockquote', 'blockquote');

/* Buttons shortcode */

if (!function_exists('button')) {
function button($atts, $content = null) {
	global $qode_options_satellite;
	$html = "";
	extract(shortcode_atts(array("size"=>"","border"=>"","border_color"=>"","shadow"=>"","color"=>"","background_color"=>"","font_size"=>"","line_height"=>"","font_style"=>"","font_weight"=>"","text"=>"Button","link"=>"http://qodeinteractive.com/","target"=>"_self"), $atts));
	if($target == ""){
		$target = "_self";
	}
    $html .=  '<a href="'.$link.'" target="'.$target.'" class="button '.$size.' '.$border.' '.$shadow.'" style="';
		if($color != ""){
			$html .= 'color: '.$color.'; ';
		}
		if($background_color != ""){
			$html .= 'background-color: '.$background_color.'; ';
		}
		if($font_size != ""){
			$html .= 'font-size: '.$font_size.'px; ';
		}
		if($line_height != ""){
			$html .= 'line-height: '.$line_height.'px; ';
		}
		if($font_style != ""){
			$html .= 'font-style: '.$font_style.'; ';
		}
		if($font_weight != ""){
			$html .= 'font-weight: '.$font_weight.'; ';
		}
		if($border_color != ""){
			$html .= 'border-color: '.$border_color.'; ';
		}
		$html .= '">'.$text.'</a>';

    return $html;
}
}
add_shortcode('button', 'button');

/* Three columns wrap shortcode */

if (!function_exists('three_col_col1')) {
function three_col_col1($atts, $content = null) {
    return '<div class="three_columns clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('three_col_col1', 'three_col_col1');

if (!function_exists('three_col_col2')) {
function three_col_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('three_col_col2', 'three_col_col2');

if (!function_exists('three_col_col3')) {
function three_col_col3($atts, $content = null) {
    return '<div class="column3"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('three_col_col3', 'three_col_col3');

/* Four columns wrap shortcode */

if (!function_exists('four_col_col1')) {
function four_col_col1($atts, $content = null) {
    return '<div class="four_columns clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('four_col_col1', 'four_col_col1');

if (!function_exists('four_col_col2')) {
function four_col_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('four_col_col2', 'four_col_col2');

if (!function_exists('four_col_col3')) {
function four_col_col3($atts, $content = null) {
    return '<div class="column3"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('four_col_col3', 'four_col_col3');

if (!function_exists('four_col_col4')) {
function four_col_col4($atts, $content = null) {
    return '<div class="column4"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('four_col_col4', 'four_col_col4');

/* Two columns wrap shortcode */

if (!function_exists('two_col_50_50_col1')) {
function two_col_50_50_col1($atts, $content = null) {
    return '<div class="two_columns_50_50 clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('two_col_50_50_col1', 'two_col_50_50_col1');

if (!function_exists('two_col_50_50_col2')) {
function two_col_50_50_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('two_col_50_50_col2', 'two_col_50_50_col2');

/* Two columns nested wrap shortcode */

if (!function_exists('two_col_50_50_nested_col1')) {
function two_col_50_50_nested_col1($atts, $content = null) {
    return '<div class="two_columns_50_50 clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('two_col_50_50_nested_col1', 'two_col_50_50_nested_col1');

if (!function_exists('two_col_50_50_nested_col2')) {
function two_col_50_50_nested_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('two_col_50_50_nested_col2', 'two_col_50_50_nested_col2');

/* Two columns 66_33 wrap shortcode */

if (!function_exists('two_col_66_33_col1')) {
function two_col_66_33_col1($atts, $content = null) {
    return '<div class="two_columns_66_33 clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('two_col_66_33_col1', 'two_col_66_33_col1');

if (!function_exists('two_col_66_33_col2')) {
function two_col_66_33_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('two_col_66_33_col2', 'two_col_66_33_col2');

/* Two columns 33_66 wrap shortcode */

if (!function_exists('two_col_33_66_col1')) {
function two_col_33_66_col1($atts, $content = null) {
    return '<div class="two_columns_33_66 clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('two_col_33_66_col1', 'two_col_33_66_col1');

if (!function_exists('two_col_33_66_col2')) {
function two_col_33_66_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('two_col_33_66_col2', 'two_col_33_66_col2');

/* Two columns 75_25 wrap shortcode */

if (!function_exists('two_col_75_25_col1')) {
function two_col_75_25_col1($atts, $content = null) {
    return '<div class="two_columns_75_25 clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('two_col_75_25_col1', 'two_col_75_25_col1');

if (!function_exists('two_col_75_25_col2')) {
function two_col_75_25_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('two_col_75_25_col2', 'two_col_75_25_col2');

/* Two columns 25_75 wrap shortcode */

if (!function_exists('two_col_25_75_col1')) {
function two_col_25_75_col1($atts, $content = null) {
    return '<div class="two_columns_25_75 clearfix"><div class="column1"><div class="column_inner">' . do_shortcode($content) . '</div></div>';
}
}
add_shortcode('two_col_25_75_col1', 'two_col_25_75_col1');

if (!function_exists('two_col_25_75_col2')) {
function two_col_25_75_col2($atts, $content = null) {
    return '<div class="column2"><div class="column_inner">' . do_shortcode($content) . '</div></div></div>';
}
}
add_shortcode('two_col_25_75_col2', 'two_col_25_75_col2');

/* Counter shortcode */

if (!function_exists('counter')) {
function counter($atts, $content = null) {
		extract(shortcode_atts(array("type"=>"","position"=>"","digit"=>"","font_size"=>"","font_color"=>""), $atts));
    $html = "";  
		$html .=  '<div class="counter_holder '.$position.'"><span class="counter '.$type.'"';
		if($font_color != "" || $font_size != ""){
			$html .= 'style="color:'.$font_color.'; font-size:'.$font_size.'px; height:'.$font_size.'px; line-height:'.$font_size.'px;"';
		} 
		$html .= '>'.$digit.'</span>'.no_wpautop($content).'</div>';
    return $html;
}
}
add_shortcode('counter', 'counter');

/* Custom font shortcode */

if (!function_exists('custom_font')) {
function custom_font($atts, $content = null) {
	extract(shortcode_atts(array("font_family"=>"","font_size"=>"","line_height"=>"","font_style"=>"","font_weight"=>"","color"=>"","text_decoration"=>"","background_color"=>"","padding"=>"","margin"=>""), $atts));
    $html = '';  
	$html .= '<span class="custom_font_holder" style="';
	if($font_family != ""){
		$html .= 'font-family: '.$font_family.';';
	}
	if($font_size != ""){
		$html .= ' font-size: '.$font_size.'px;';
	}
	if($line_height != ""){
		$html .= ' line-height: '.$line_height.'px;';
	}
	if($font_style != ""){
		$html .= ' font-style: '.$font_style.';';
	}
	if($font_weight != ""){
		$html .= ' font-weight: '.$font_weight.';';
	}
	if($color != ""){
		$html .= ' color: '.$color.';';
	}
	if($text_decoration != ""){
		$html .= ' text-decoration: '.$text_decoration.';';
	}
	if($background_color != ""){
		$html .= ' background-color: '.$background_color.';';
	}
	if($padding != ""){
		$html .= ' padding: '.$padding.';';
	}
	if($margin != ""){
		$html .= ' margin: '.$margin.';';
	}
	$html .= '">'.no_wpautop($content).'</span>';
    return $html;
}
}
add_shortcode('custom_font', 'custom_font');

/* Dropcaps shortcode */

if (!function_exists('dropcaps')) {
function dropcaps($atts, $content = null) {
	extract(shortcode_atts(array("type"=>"","color"=>"","background_color"=>"","border_color"=>""), $atts));
	$html = "<span class='dropcap ".$type."' style='";
	if($background_color != ""){
		$html .= "background-color: $background_color;";
	}
	if($color != ""){
		$html .= " color: $color;";
	}
	if($border_color != ""){
		$html .= " border-color: $border_color;";
	}
	$html .= "'>" . no_wpautop($content)  . "</span>";
	
	return $html;
}
}
add_shortcode('dropcaps', 'dropcaps');

/* Elements Animation shortcode */

if (!function_exists('elements_animation')) {
function elements_animation($atts, $content = null) {
	extract(shortcode_atts(array("animation_type"=>""), $atts));
	return "<div class='$animation_type'><div>" . no_wpautop($content) . "</div></div>";
}
}
add_shortcode('elements_animation', 'elements_animation');

/* Element fade in shortcode */

if (!function_exists('element_fade_in')) {
function element_fade_in($atts, $content = null) {
	extract(shortcode_atts(array("transition_delay" => ""), $atts));
	
	$html = "";  
	$html .=  '<div class="element_fade_in" ';
	if($transition_delay){
		$html .= 'style="transition-delay:'.$transition_delay.'s;"';
	}
	$html .= '>'. do_shortcode($content) . '</div>';

	return $html;
}
}
add_shortcode('element_fade_in', 'element_fade_in');

/* Highlights shortcode */

if (!function_exists('highlight')) {
function highlight($atts, $content = null) {
	extract(shortcode_atts(array("color"=>"","background_color"=>""), $atts));
	$html =  "<span class='highlight'";
	if($color != "" || $background_color != ""){
		$html .= " style='color: ".$color."; background-color:".$background_color.";'";
	}
	$html .= ">" . $content . "</span>";  
    return $html;
}
}
add_shortcode('highlight', 'highlight');

/* Icons shortcode */

if (!function_exists('icon')) {
function icon($atts, $content = null) {
    extract(shortcode_atts(array("type"=>"","style"=>"","icon"=>"","border_color"=>"","background_color"=>"","link"=>"","target"=>""), $atts));
    if($target == ""){
    	$target = "_self";
    }
    $html = "";  
	$html .=  '<span class="icon_holder '.$type.'"';
	if($border_color != ""){
		$html .= ' style="border-color: '.$border_color.';"';
	}
	$html .= '><span class="icon_inner"';
	if($background_color != ""){
		$html .= ' style="background-color: '.$background_color.';"';
	}
	if($link != ""){
		$html .= '><a href="'.$link.'" target="'.$target.'"><span class="icon '.$style.' '.$icon.'"></span></a></span></span>';
	} else {
		$html .= '><span class="icon '.$style.' '.$icon.'"></span></span></span>';
	}
    return $html;
}
}
add_shortcode('icon', 'icon');

/* Icon with title shortcode */

if (!function_exists('icon_with_title')) {
function icon_with_title($atts, $content = null) {
    extract(shortcode_atts(array("type"=>"","style"=>"","icon"=>"","border_color"=>"","background_color"=>"","title"=>"","font_size"=>"","color"=>"","link"=>"","target"=>""), $atts));
    if($target == ""){
    	$target = "_self";
    }
    $html = ""; 
	$html .=  '<span class="icon_with_title '.$type.'">';
	if($link != ""){
		$html .= '<a href="'.$link.'" target="'.$target.'">';
	}
	$html .= '<span class="icon_holder '.$type.'"';
	if($border_color != ""){
		$html .= ' style="border-color: '.$border_color.';"';
	}
	$html .= '><span class="icon_inner"';
	if($background_color != ""){
		$html .= ' style="background-color: '.$background_color.';"';
	}
	$html .= '><span class="icon '.$style.' '.$icon.'"></span></span></span><span class="icon_title" style="color: '.$color.'; font-size: '.$font_size.'px;">'.$title.'</span>';
	if($link != ""){
		$html .= '</a>';
	}
	$html .= '</span>';
    return $html;
}
}
add_shortcode('icon_with_title', 'icon_with_title');

/* Image with text shortcode */

if (!function_exists('image_with_text')) {
function image_with_text($atts, $content = null) {
	extract(shortcode_atts(array("image_link" =>"","image_title"=>"","lightbox"=>"","link"=>"","target"=>""), $atts));
	$html =  "<div class='image_with_text'>";

	if ($lightbox == "yes"){
		$html .= "<a href='".$image_link."' title='".$image_title."' data-rel='prettyPhoto[lightbox]'><img src='".$image_link."' alt=''></a><div class='image_with_text_content'>".no_wpautop($content)."</div></div>";
	} else if($link != ""){
		$html .= "<a href='".$link."' target='".$target."' title='".$image_title."'><img src='".$image_link."' alt=''></a><div class='image_with_text_content'>".no_wpautop($content)."</div></div>";
	} else {
		$html .= "<img src='".$image_link."' alt=''><div class='image_with_text_content'>".no_wpautop($content)."</div></div>";
	}
	
	return $html;
}
}
add_shortcode('image_with_text', 'image_with_text');

/* Image With Text Over shortcode */

if (!function_exists('image_with_text_over')) {
function image_with_text_over($atts, $content = null) {
	extract(shortcode_atts(array("title" => "", "image_link" => ""), $atts));
	return "<div class='image_with_text_over'><div class='shader'></div><img src='$image_link' alt='' /><div class='text'><table><tr><td><div class='caption'>".$title."</div></td></tr></table><table><tr><td><div class='desc'>".no_wpautop($content)."</div></td></tr></table></div></div>";
}
}
add_shortcode('image_with_text_over', 'image_with_text_over');

/* Latest post shortcode */

if (!function_exists('latest_post')) {
function latest_post($atts, $content = null) {
  	$html = ""; 
	extract(shortcode_atts(array("post_number"=>"","order_by"=>"","order"=>"","category"=>"","text_length"=>""), $atts));
	
	$q = new WP_Query( 
	   array('orderby'=>$order_by,'order'=>$order,'posts_per_page'=>$post_number,'category_name'=>$category) 
	);		

	$html .= "<div class='latest_post_holder'><ul>";

		while($q->have_posts()) : $q->the_post();
			$html .= '<li class="';
			if($post_number == 2){
				$html .= 'two';
			} else if($post_number == 3){
				$html .= 'three';
			} else if($post_number == 4){
				$html .= 'four';
			} else if($post_number == 5){
				$html .= 'five';
			}

			if($text_length > 0){
				$html .= '"><div class="latest_post"><a href="'.get_permalink().'">'.get_the_post_thumbnail(get_the_id(),'full').'</a><div class="latest_post_text"><h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>'.'<p>'.substr(get_the_excerpt(), 0, intval($text_length)).'...</p><span>'.get_post_time('d M Y').'</span></div></div>';
				$html .= '<div class="latest_post_bottom"><div class="latest_post_like">';
				if(function_exists('qode_like')){
					$html .= qode_like_latest_posts() . '</div>';
					$html .= do_shortcode('[social_share]');	
				}
				$html .= '</div></li>';
			} else {
				$html .= '"><div class="latest_post"><a href="'.get_permalink().'">'.get_the_post_thumbnail(get_the_id(),'full').'</a><div class="latest_post_text"><h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3><span>'.get_post_time('d M Y').'</span></div></div>';
				$html .= '<div class="latest_post_bottom"><div class="latest_post_like">';
				if(function_exists('qode_like')){
					$html .= qode_like_latest_posts() . '</div>';
					$html .= do_shortcode('[social_share]');	
				}
				$html .= '</div></li>';
			}
		
		endwhile;
		wp_reset_query();

	$html .= "</ul></div>";
	return $html;	
}
}
add_shortcode('latest_post', 'latest_post');

/* Line graph shortcode */

if (!function_exists('line_graph')) {
function line_graph($atts, $content = null) {
	global $qode_options_infographer;
	extract(shortcode_atts(array("type" => "rounded", "custom_color" => "", "labels" => "", "width" => "750", "height" => "350", "scale_steps" => "6", "scale_step_width" => "20"), $atts));
	$id = mt_rand(1000, 9999);
	if($type == "rounded"){
		$bezierCurve = "true";
	}else{
		$bezierCurve = "false";
	}
	
	$id = mt_rand(1000, 9999);
	$html = "<div class='line_graf_holder'><div class='line_graf'><canvas id='lineGraph".$id."' height='".$height."' width='".$width."'></canvas></div><div class='line_graf_legend'><ul>";
	$line_graph_array = explode(";", $content);
	for ($i = 0 ; $i < count($line_graph_array) ; $i = $i + 1){
		$line_graph_el = explode(",", $line_graph_array[$i]);
		$html .=  "<li><div class='color_holder' style='background-color: ".trim($line_graph_el[0]).";'></div><p style='color: ".$custom_color.";'>".trim($line_graph_el[1])."</p></li>";   
	}
	$html .=  "</ul></div></div><script>var lineGraph".$id." = {labels : [";
	$line_graph_labels_array = explode(",", $labels);
	for ($i = 0 ; $i < count($line_graph_labels_array) ; $i = $i + 1){
		if ($i > 0) $html .= ",";
		$html .=  '"'.$line_graph_labels_array[$i].'"';
	}
	$html .= "],";
	$html .= "datasets : [";
	$line_graph_array = explode(";", $content);
	for ($i = 0 ; $i < count($line_graph_array) ; $i = $i + 1){
		$line_graph_el = explode(",", $line_graph_array[$i]);
		if ($i > 0) $html .= ",";
		$values = "";
		for ($j = 2 ; $j < count($line_graph_el) ; $j = $j + 1){
			if ($j > 2) $values .= ",";
			$values .= $line_graph_el[$j];
		}
		$color = hex2rgb(trim($line_graph_el[0]));
		$html .=  "{fillColor: 'rgba(".$color[0].",".$color[1].",".$color[2].",0.7)',data:[".$values."]}";   
	}
	if(!empty($qode_options_infographer['text_fontsize'])){
		$text_fontsize = $qode_options_infographer['text_fontsize'];
	}else{
		$text_fontsize = 15;
	}
	if(!empty($qode_options_infographer['text_color']) && $custom_color == ""){
		$text_color = $qode_options_infographer['text_color'];
	} else if(empty($qode_options_infographer['text_color']) && $custom_color != ""){
		$text_color = $custom_color;
	} else if(!empty($qode_options_infographer['text_color']) && $custom_color != ""){
		$text_color = $custom_color;
	}else{
		$text_color = '#626262';
	}
	$html .= "]};
			var \$j = jQuery.noConflict();
			\$j(document).ready(function() {
				if(\$j('.touch .no_delay').length){
					new Chart(document.getElementById('lineGraph".$id."').getContext('2d')).Line(lineGraph".$id.",{scaleOverride : true,
					scaleStepWidth : ".$scale_step_width.",
					scaleSteps : ".$scale_steps.",
					bezierCurve : ".$bezierCurve.",
					pointDot : false,
					scaleLineColor: '#000000',
					scaleFontColor : '".$text_color."',
					scaleFontSize : ".$text_fontsize.",
					scaleGridLineColor : '#e1e1e1',
					datasetStroke : false,
					datasetStrokeWidth : 0,
					animationSteps : 120,});
				}else{
					\$j('#lineGraph".$id."').appear(function() {
						new Chart(document.getElementById('lineGraph".$id."').getContext('2d')).Line(lineGraph".$id.",{scaleOverride : true,
						scaleStepWidth : ".$scale_step_width.",
						scaleSteps : ".$scale_steps.",
						bezierCurve : ".$bezierCurve.",
						pointDot : false,
						scaleLineColor: '#000000',
						scaleFontColor : '".$text_color."',
						scaleFontSize : ".$text_fontsize.",
						scaleGridLineColor : '#e1e1e1',
						datasetStroke : false,
						datasetStrokeWidth : 0,
						animationSteps : 120,});
					},{accX: 0, accY: -200});
				}						
			});
		</script>";
	return $html;
}
}
add_shortcode('line_graph', 'line_graph');

/* Message shortcode */

if (!function_exists('message')) {
function message($atts, $content = null) {
	global $qode_options_satellite;
  	$html = ""; 
	extract(shortcode_atts(array("background_color"=>"","arrow"=>"","border_color"=>""), $atts));
	$html .= "<div class='message' style='";
	if($background_color != "" || $border_color != ""){
		$html .= 'background-color: '.$background_color.'; border-color: '.$border_color.';';
	}
	
	$html .= "'><a href='#' class='close ".$arrow."'></a>" .no_wpautop($content) . "</div>";
	return $html;
}
}
add_shortcode('message', 'message');

/* Ordered List shortcode */

if (!function_exists('ordered_list')) {
function ordered_list($atts, $content = null) {
    $html =  "<div class=ordered>" . $content . "</div>";  
    return $html;
}
}
add_shortcode('ordered_list', 'ordered_list');

/* Paralax shortcode */

if (!function_exists('parallax')) {
function parallax($atts, $content = null) {
	$html = "";
	$html .= "<section class='parallax'>";
	$html .= no_wpautop($content);
	$html .= "</section>";
	return $html;
}
}
add_shortcode('parallax', 'parallax');

if (!function_exists('parallax_section')) {
function parallax_section($atts, $content = null) {
	extract(shortcode_atts(array("id" => "", "height"=>"300", "title" => "...", "content_position"=>""), $atts));
	$parallaxes = get_post_meta(get_the_ID(), "qode_parallaxes", true);
	$html = "";
	
	foreach($parallaxes as $parallax) 
	{	
		if($parallax['imageid'] == $id) 
			{
			$html .= '<section id="'.$parallax['imageid'].'" style="background-image:url('. $parallax['parimg'] .'); background-color:'. $parallax['parcolor'] .';" data-height="' . $height . '" data-title="' . $title . '">';
			$html .= '<div class="parallax_content '.$content_position.'">';
			$html .= no_wpautop($content);
			$html .= '</div>';
			$html .= '</section>';
		}			
	}
	return $html;
}
}
add_shortcode('parallax_section', 'parallax_section');

/* Pie Chart shortcode */

if (!function_exists('pie_chart')) {
function pie_chart($atts, $content = null) {
	extract(shortcode_atts(array("title"=>"","title_color"=>"","percent"=>"100","percentage_color"=>"","active_color"=>"","noactive_color"=>"","line_width"=>""), $atts));
    $html =  "<div class='pie_chart_holder'><div class='percentage' data-percent='".$percent."' data-linewidth='".$line_width."' data-active='".$active_color."' data-noactive='".$noactive_color."' style='color: ".$percentage_color.";'><span class='tocounter'>".$percent."</span>%</div>";

    if(empty($title) && (empty($content) || $content == null || $content == "")){
    	$html .= "</div>"; 
    } else {
    	$html .= "<div class='pie_chart_text'><h3 style='color: ".$title_color.";' >".$title."</h3>" . no_wpautop($content) . "</div></div>";
    }
    return $html;
}
}
add_shortcode('pie_chart', 'pie_chart');

/* Pie chart 2 shortcode */

if (!function_exists('pie_chart2')) {
function pie_chart2($atts, $content = null) {
	extract(shortcode_atts(array("width" => "180", "height" => "180", "color" => ""), $atts));
    $id = mt_rand(1000, 9999);
    $html = "<div class='pie_graf_holder'><div class='pie_graf'><canvas id='pie".$id."' height='".$height."' width='".$width."'></canvas></div><div class='pie_graf_legend'><ul>";
    $pie_chart_array = explode(";", $content);
    for ($i = 0 ; $i < count($pie_chart_array) ; $i = $i + 1){
    	$pie_chart_el = explode(",", $pie_chart_array[$i]);
		$html .= "<li><div class='color_holder' style='background-color: ".trim($pie_chart_el[1]).";'></div><p style='color: ".$color.";'>".trim($pie_chart_el[2])."</p></li>";   
    }
    $html .= "</ul></div></div><script>var pie".$id." = [";
    $pie_chart_array = explode(";", $content);
    for ($i = 0 ; $i < count($pie_chart_array) ; $i = $i + 1){
    	$pie_chart_el = explode(",", $pie_chart_array[$i]);
    	if ($i > 0) $html .= ",";
		$html .= "{value: ".trim($pie_chart_el[0]).",color:'".trim($pie_chart_el[1])."'}";   
    }
    $html .= "];
		var \$j = jQuery.noConflict();
		\$j(document).ready(function() {
			if(\$j('.touch .no_delay').length){
				new Chart(document.getElementById('pie".$id."').getContext('2d')).Pie(pie".$id.",{segmentStrokeColor : 'transparent',});
			}else{
				\$j('#pie".$id."').appear(function() {
					new Chart(document.getElementById('pie".$id."').getContext('2d')).Pie(pie".$id.",{segmentStrokeColor : 'transparent',});
				},{accX: 0, accY: -200});
			}
		});
	</script>";
    return $html;
}
}
add_shortcode('pie_chart2', 'pie_chart2');


/* Pie chart 3 shortcode */

if (!function_exists('pie_chart3')) {
function pie_chart3($atts, $content = null) {
    extract(shortcode_atts(array("width" => "180", "height" => "180", "color" => ""), $atts));
    $id = mt_rand(1000, 9999);
    $html = "<div class='pie_graf_holder'><div class='pie_graf'><canvas id='pie".$id."' height='".$height."' width='".$width."'></canvas></div><div class='pie_graf_legend'><ul>";
    $pie_chart_array = explode(";", $content);
    for ($i = 0 ; $i < count($pie_chart_array) ; $i = $i + 1){
    	$pie_chart_el = explode(",", $pie_chart_array[$i]);
		$html .= "<li><div class='color_holder' style='background-color: ".trim($pie_chart_el[1]).";'></div><p style='color: ".$color.";'>".trim($pie_chart_el[2])."</p></li>";   
    }
    $html .= "</ul></div></div><script>var pie".$id." = [";
    $pie_chart_array = explode(";", $content);
    for ($i = 0 ; $i < count($pie_chart_array) ; $i = $i + 1){
    	$pie_chart_el = explode(",", $pie_chart_array[$i]);
    	if ($i > 0) $html .= ",";
			$html .= "{value: ".trim($pie_chart_el[0]).",color:'".trim($pie_chart_el[1])."'}";   
    }
    $html .= "];
		var \$j = jQuery.noConflict();
		\$j(document).ready(function() {
			if(\$j('.touch .no_delay').length){
				new Chart(document.getElementById('pie".$id."').getContext('2d')).Doughnut(pie".$id.",{segmentStrokeColor : 'transparent',});
			}else{
				\$j('#pie".$id."').appear(function() {
					new Chart(document.getElementById('pie".$id."').getContext('2d')).Doughnut(pie".$id.",{segmentStrokeColor : 'transparent',});
				},{accX: 0, accY: -200});
			}							
		});
	</script>";
    return $html;
}
}
add_shortcode('pie_chart3', 'pie_chart3');

/* Portfolio shortcode */

if (!function_exists('portfolio_list')) {
function portfolio_list($atts, $content = null) {
	global $wp_query;
	global $portfolio_project_id;
	$html = "";
	extract(shortcode_atts(array("type" => "standard", "columns" => "3", "order_by" => "menu_order" , "order" => "ASC" , "number"=>"-1", "filter"=>'no', "lightbox"=>'yes', "category"=>"", "selected_projects"=>"", "show_load_more" => "yes"), $atts));
	
	$html .= "<div class='projects_holder_outer'>";
	if($filter == "yes" && $category == "" && $selected_projects == ""){
		
		$html .= "<div class='filter_holder'><ul><li class='filter' data-filter='all'><span>". __('All','qode') ."</span></li>";
				if ($category == "") {
					$args = array(
						'parent'  => 0
					);
					$portfolio_categories = get_terms( 'portfolio_category',$args);
				} else {
					$top_category = get_term_by('slug',$category,'portfolio_category');
					$term_id = '';
					if (isset($top_category->term_id)) $term_id = $top_category->term_id;
					$args = array(
						'parent' => $term_id
					);
					$portfolio_categories = get_terms( 'portfolio_category',$args);
				}
				foreach($portfolio_categories as $portfolio_category) {
					$html .= "<li class='filter' data-filter='$portfolio_category->slug'><span>$portfolio_category->name</span>";
					$args = array(
						'child_of' => $portfolio_category->term_id
					);
					
					$html .= '</li>';
				}
		$html .= "</ul></div>";
	}
	$_type_class = '';
	if($type == "hover_text"){
		$_type_class = " hover_text";
	}

	$html .= "<div class='projects_holder clearfix v$columns$_type_class'>\n";
	if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
	elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
	else { $paged = 1; }
	if ($category == "") {
		$args = array(
			'post_type'=> 'portfolio_page',
			'orderby' => $order_by,
			'order' => $order,
			'posts_per_page' => $number,
			'paged' => $paged
		);
	} else {
		$args = array(
			'post_type'=> 'portfolio_page',
			'portfolio_category' => $category,
			'orderby' => $order_by,
			'order' => $order,
			'posts_per_page' => $number,
			'paged' => $paged
		);
	}
	$project_ids = null;
	if ($selected_projects != "") {
		$project_ids = explode(",",$selected_projects);
		$args['post__in'] = $project_ids;
	}
	query_posts( $args );
	if ( have_posts() ) : while ( have_posts() ) : the_post(); 
	$terms = wp_get_post_terms(get_the_ID(),'portfolio_category');
	$html .= "<article class='mix ";
	foreach($terms as $term) {
		$html .= "$term->slug ";
	}

    $title = get_the_title();
    $featured_image_array = wp_get_attachment_image_src( get_post_thumbnail_id(), 'single-post-thumbnail' ); //original size  
    $large_image = $featured_image_array[0];
    $slug_list_ = "pretty_photo_gallery";

	$html .="'>";
	
	$html .= "<div class='image_holder'>";
	$html .= "<span class='image'>";
	$html .= get_the_post_thumbnail(get_the_ID(),'full');
	$html .= "</span>";
	$html .= "<span class='text_holder'>";
		$html .= "<span class='text_outer'>";
			$html .= "<span class='text_inner'>";
				if($type == "hover_text") {
					$html .= '<h5><a href="'. get_permalink() . '">'.get_the_title().'</a></h5>';
					$html .= '<h6>';
					$k=1;
					foreach($terms as $term) {
						$html .= "$term->name";
						if(count($terms) != $k){
							$html .= ', ';
						}
					$k++;
					}
					$html .= '</h6>';
				}
				if($lightbox == "yes"){
					$html .= "<a class='lightbox' title='" . $title . "' href='" . $large_image . "' data-rel='prettyPhoto[".$slug_list_."]'><span></span></a>";
				}
			$html .= "<a class='preview' href='". get_permalink() ."'><span></span></a>";
				if($type == "hover_text") {
				$html .= "<span class='portfolio_like'>";
					$portfolio_project_id = get_the_ID();
					if(function_exists('qode_like_portfolio_list')){
						$html .= qode_like_portfolio_list();
					}
					$html .= "</span>";
				}
			$html .= "</span>";
		$html .= "</span>";
	$html .= "</span>";
	$html .= "</div>";
	if($type != "hover_text") {
	$html .= "<div class='portfolio_description'>";
	$html .= '<h5><a href="'. get_permalink() . '">'.get_the_title().'</a></h5>';
	$html .= '<h6>';
	$k=1;
	foreach($terms as $term) {
		$html .= "$term->name";
		if(count($terms) != $k){
			$html .= ', ';
		}
	$k++;
	}
	$html .= '</h6>';
	$html .= "<div class='portfolio_like'>";
	$portfolio_project_id = get_the_ID();
	if(function_exists('qode_like_portfolio_list')){
		$html .= qode_like_portfolio_list();
	}
	$html .= "</div>";
	$html .= "</div>";
	}
	$html .= "</article>\n";
						
	endwhile; 
	
	$i = 1;
	while ($i <= $columns) {
		$i++;
		if($columns != 1){	
			$html .= "<div class='filler'></div>\n";
		}
	}
	
	else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.','qode'); ?></p>
	<?php endif; 	
	
	
	$html .= "</div>";
	if(get_next_posts_link()) {
		if($show_load_more == "yes" || $show_load_more == ""){
			$html .= '<div class="portfolio_paging"><span rel="'. $wp_query->max_num_pages .'" class="load_more">'. get_next_posts_link(__('Load More','qode')) . '</span></div>';
		}
	}
	$html .= "</div>";
	wp_reset_query();	
    return $html;
}
}
add_shortcode('portfolio_list', 'portfolio_list');

/* Pricing table shortcode */

if (!function_exists('pricing_table')) {
function pricing_table($atts, $content = null) {
  $html = ""; 
	extract(shortcode_atts(array(), $atts));
    	$html .=  "<div class='price_tables clearfix";
		$html .= "'>" . no_wpautop($content) . "</div>";  
    return $html;
}
}
add_shortcode('pricing_table', 'pricing_table');

/* Pricing table column shortcode */

if (!function_exists('pricing_column')) {
function pricing_column($atts, $content = null) {
  $html = ""; 
	extract(shortcode_atts(array("type"=>"","title"=>'',"price" => "0","currency"=>"$","price_period"=>"/mo","link"=>"","target"=>"","button_text"=>"Buy Now","active"=>""), $atts));
	if($target == ""){
		$target = "_self";
	}
	$html .=  "<div class='price_table ".$type."'><div class='price_table_inner";
	if($active == "yes"){
		$html .= " active'>";
	} else {
		$html .= "'>";
	}
	$html .= "<ul><li class='prices'><div class='price_in_table'><sup class='value'>".$currency."</sup><span class='price'>".$price."</span><sub class='mark'>".$price_period."</sub></div></li><li class='cell table_title'>$title</li>" . no_wpautop($content) . "<li class='price_button'><a class='button' href='$link' target='$target'>".$button_text."</a></li></ul></div></div>";
    return $html;
}
}
add_shortcode('pricing_column', 'pricing_column');

/* Pricing table cell shortcode */

if (!function_exists('pricing_cell')) {
function pricing_cell($atts, $content = null) {
	extract(shortcode_atts(array(), $atts));
    $html =  "<li class='cell'>" . no_wpautop($content) . "</li>"; 
	return $html;
}
}
add_shortcode('pricing_cell', 'pricing_cell');

/* Progress bars horizontal shortcode */

if (!function_exists('progress_bars')) {
function progress_bars($atts, $content = null) {
	extract(shortcode_atts(array(), $atts));
    $html =  "<div class='progress_bars'>" . no_wpautop($content) . "</div>";  
    return $html;
}
}
add_shortcode('progress_bars', 'progress_bars');

/* Progress bar horizontal shortcode */

if (!function_exists('progress_bar')) {
function progress_bar($atts, $content = null) {
	extract(shortcode_atts(array("title"=>"","percent"=>"100","color"=>"","percent_color"=>"","active_background_color"=>"","noactive_background_color"=>"","height"=>""), $atts));
	$html =  "<div class='progress_bar'><span class='progress_title'><h4 style='color: ".$color.";'>$title</h4></span><span class='progress_number' style='color: ".$percent_color.";'><span>".$percent."</span>%</span> <div class='progress_content_outer' style='height: ".$height."px; background-color: ".$noactive_background_color.";'><div data-percentage='$percent' class='progress_content' style='height: ".$height."px; background: ".$active_background_color.";'></div></div></div>";
    return $html;
}
}
add_shortcode('progress_bar', 'progress_bar');

/* Progress bars icon shortcode */

if (!function_exists('progress_bar_icon')) {
function progress_bar_icon($atts, $content = null) {
	extract(shortcode_atts(array("icons_number" => "","active_number" => "","type"=>"","style"=>"","icon" => "","border_color"=>"","background_color"=>""), $atts));
    $html =  "<div class='progress_bars_with_image_holder'><div class='progress_bars_with_image'><div class='progress_bars_with_image_content ".$icon." clearfix' data-number='".$active_number."'>";
	$i = 0;
	while ($i < $icons_number) {
		$html .= "<div class='bar ".$type." ".$style."' style='background-color: ".$border_color.";'><div class='bar_noactive' style='background-color: ".$background_color.";'>&nbsp;</div><div class='bar_active' style='background-color: ".$background_color.";'>&nbsp;</div></div>";
		$i++;
	}
    $html .= "</div></div></div>";
    return $html;
}
}
add_shortcode('progress_bar_icon', 'progress_bar_icon');

/* Separator shortcode */

if (!function_exists('separator')) {
function separator($atts, $content = null) {
    extract(shortcode_atts(array("type"=>"","color"=>"","thickness"=>"","up"=>"","down"=>""), $atts));
		$html =  '<div style="';
		if($up != ""){
		$html .= "margin-top:". $up ."px;";
		}
		if($down != ""){
		$html .= "margin-bottom:". $down ."px;"; 
		}
		if($color != ""){
		$html .= "background-color: ". $color .";";
		}
		if($thickness != ""){
		$html .= "height:". $thickness ."px;";
		}
		$html .= '" class="separator '.$type.'"></div>';  
		
    return $html;
}
}
add_shortcode('separator', 'separator');

/* Social Icons shortcode */

if (!function_exists('social_icons')) {
function social_icons($atts, $content = null) {
    extract(shortcode_atts(array("style"=>"","background_color"=>"","border_color"=>""), $atts));
    $html = ""; 
    $html .=  "<ul class='social_menu ".$style."'>";  
    $social_icons_array = explode(",", $content);
    for ($i = 0 ; $i < count($social_icons_array) ; $i = $i + 2){
		$html .=  "<li class='".trim($social_icons_array[$i])."'><a href='".trim($social_icons_array[$i + 1])."' target='_blank' style='border-color: ".$border_color."; background-color: ".$background_color.";'><span>" . trim($social_icons_array[$i]) . "</span></a></li>";   
    }
    $html .=  "</ul>";
    return $html;
}
}
add_shortcode('social_icons', 'social_icons');

/* Social Share shortcode */

if (!function_exists('social_share')) {
function social_share($atts, $content = null) {
	global $qode_options_satellite;
	if(isset($qode_options_satellite['twitter_via']) && !empty($qode_options_satellite['twitter_via'])) {
		$twitter_via = " via " . $qode_options_satellite['twitter_via'];
	} else {
		$twitter_via = 	"";
	}
    $html = "";  
	if(isset($qode_options_satellite['enable_social_share']) && $qode_options_satellite['enable_social_share'] == "yes") { 
		$post_type = get_post_type();
		if(isset($qode_options_satellite["post_types_names_$post_type"])) {
			if($qode_options_satellite["post_types_names_$post_type"] == $post_type) {
			if($post_type == "portfolio_page") {
				$html .= '<div class="portfolio_share">';
			} elseif($post_type == "post") {
				$html .= '<div class="blog_share">';
			} elseif($post_type == "page") {
				$html .= '<div class="page_share">';
			}
				$html .= '<span class="social_share_holder">';
				$html .= '<span class="social_share_icon">'.  __('Share','qode') .'</span>';
					$html .= '<div class="social_share_dropdown"><ul><li><span class="share_title">'.  __('Share on','qode') .'</span></li>';
					if(isset($qode_options_satellite['enable_facebook_share']) &&  $qode_options_satellite['enable_facebook_share'] == "yes") { 
						$html .= '<li class="facebook_share">';
						$html .= '<a href="#" onclick="window.open(\'http://www.facebook.com/sharer.php?s=100&amp;p[title]=' . get_the_title() . '&amp;p[summary]=' . get_the_excerpt() . '&amp;p[url]=' . urlencode(get_permalink()) . '&amp;&p[images][0]=';
						if(function_exists('the_post_thumbnail')) {
							$html .=  wp_get_attachment_url(get_post_thumbnail_id());
						}
						$html .='\', \'sharer\', \'toolbar=0,status=0,width=620,height=280\');" href="javascript: void(0)">';
						if(!empty($qode_options_satellite['facebook_icon'])) {
							$html .= '<img src="' . $qode_options_satellite["facebook_icon"] . '" />';
						} else { 
							$html .= '<span class="social_image"><span class="social_image_inner"></span></span>';
						} 
						$html .= "<span class='share_text'>" . __('Like','qode') . "</span>";
						$html .= "</a>";
						$html .= "</li>";
						} 
						if($qode_options_satellite['enable_google_plus'] == "yes") { 
							$html .= '<li  class="google_share">';
							$html .= '<a href="#" onclick="popUp=window.open(\'https://plus.google.com/share?url=' . urlencode(get_permalink()) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false">';
									if(!empty($qode_options_satellite['google_plus_icon'])) { 
										$html .= '<img src="' . $qode_options_satellite['google_plus_icon'] . '" />';
									} else { 
										$html .= '<span class="social_image"><span class="social_image_inner"></span></span>';
									 } 
									$html .= "<span class='share_text'>" . __('Plus','qode') . "</span>";
								$html .= "</a>";
							$html .= "</li>";
						 }
						if($qode_options_satellite['enable_twitter_share'] == "yes") { 
							$html .= '<li class="twitter_share">';
							$html .= '<a href="#" onclick="popUp=window.open(\'http://twitter.com/share?url=' . urlencode(get_permalink()) . '&text=' . urlencode(the_excerpt_max_charlength(mb_strlen(get_permalink())) . $twitter_via) . '&count=horiztonal\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false;" target="_blank" rel="nofollow">';
									if(!empty($qode_options_satellite['twitter_icon'])) { 
										$html .= '<img src="' . $qode_options_satellite["twitter_icon"] . '" />';
									 } else { 
										$html .= '<span class="social_image"><span class="social_image_inner"></span></span>';
									 }
									$html .= "<span class='share_text'>" . __('Tweet','qode') . "</span>";
								$html .= "</a>";
							$html .= "</li>";
						 } 
						$html .= "</ul></div>";
				$html .= "</span>";
				if($post_type == "portfolio_page" || $post_type == "post" || $post_type == "page") {
					$html .= '</div>';
				}
			} 
		}  
	}
    return $html;
}
}
add_shortcode('social_share', 'social_share');

/* Tabs shortcode */

if (!function_exists('tabs')) {
function tabs( $atts, $content = null ) {
  $html = ""; 
	extract(shortcode_atts(array(), $atts));
	$html .= '<div class="tabs '.(isset($atts['type'])?$atts['type']:'').'">';
	$html .= '<ul class="tabs-nav">';
	$key = array_search((isset($atts['type'])?$atts['type']:''),$atts);
		if($key!==false){
			unset($atts[$key]);
	}
	foreach ($atts as $key => $tab) {
		if(stripos($key, "tabid") !== false){
			$html .= '<li><a href="#' . $key . '">' . $tab . '</a></li>';
		}
	}
	$html .= '</ul>';
	$html .= '<div class="tabs-container">';
	$html .= no_wpautop($content) .'</div></div>';
	return $html;
}
}
add_shortcode('tabs', 'tabs');

/* Tab shortcode */

if (!function_exists('tab')) {
function tab( $atts, $content = null ) {
  $html = ""; 
	extract(shortcode_atts(array(), $atts));
	$html .= '<div id="tabid' . $atts['id'] . '" class="tab-content">' . no_wpautop($content) .'</div>';
	return $html;
}
}
add_shortcode('tab', 'tab');

/* Table shortcode */

if (!function_exists('table')) {
function table($atts, $content = null) {
  $html = ""; 
	extract(shortcode_atts(array(), $atts));
    $html .=  "<table class='standard_table'><tbody>" . no_wpautop($content) . "</tbody></table>";  
    return $html;
}
}
add_shortcode('table', 'table');

/* Table row shortcode */

if (!function_exists('table_row')) {
function table_row($atts, $content = null) {
	extract(shortcode_atts(array(), $atts));
    $html =  "<tr>" . no_wpautop($content) . "</tr>";  
    return $html;
}
}
add_shortcode('table_row', 'table_row');

/* Table head cell shortcode */

if (!function_exists('table_cell_head')) {
function table_cell_head($atts, $content = null) {
	extract(shortcode_atts(array(), $atts));
    $html =  "<th><h5>" . no_wpautop($content) . "</h5></th>";  
    return $html;
}
}
add_shortcode('table_cell_head', 'table_cell_head');

/* Table body cell shortcode */

if (!function_exists('table_cell_body')) {
function table_cell_body($atts, $content = null) {
	extract(shortcode_atts(array(), $atts));
    $html =  "<td>" . no_wpautop($content) . "</td>";  
    return $html;
}
}
add_shortcode('table_cell_body', 'table_cell_body');

/* Testimonials shortcode */

if (!function_exists('testimonials')) {
function testimonials( $atts, $content = null ) {
  	$html = ""; 
	extract(shortcode_atts(array("type"=>""), $atts));
	$html .= '<div class="testimonials '.$type.'"><div class="testimonial_container">'.no_wpautop($content).'</div><ul class="testimonial_nav">';
	$key = array_search((isset($atts['type'])?$atts['type']:''),$atts);
		if($key!==false){
			unset($atts[$key]);
	}
	foreach ($atts as $key => $testimonial) {
		if(stripos($key, "testimonialimagelink") !== false){
			$html .= '<li><a href="#' . $key . '"><span class="testimonial_image_holder"><img src="'.$testimonial.'" alt=""/></span></a></li>';
		}
	}
	$html .= '</ul></div>';
	return $html;
}
}
add_shortcode('testimonials', 'testimonials');

/* Testimonial shortcode */

if (!function_exists('testimonial')) {
function testimonial( $atts, $content = null ) {
  $html = ""; 
	extract(shortcode_atts(array("name"=>"","name_color"=>"","background_color"=>"","border_color"=>""), $atts));
	$html .= '<div id="testimonialimagelink' . $atts['id'] . '" class="testimonial_content"><div class="testimonial_text_holder"><div class="testimonial_text_inner"';
	if($background_color != '' || $border_color != ''){
		$html .= ' style="background-color: '.$background_color.'; border-color: '.$border_color.';"';
	}
	$html .= '>'.no_wpautop($content).'<span class="testimonial_name"';
	if($name_color != ''){
		$html .= ' style="color: '.$name_color.';"';
	}
	$html .= '>'.$name.'</span></div><span class="transparent_arrow"></span><span class="testimonial_arrow"';
	if($background_color != '' || $border_color != ''){
		$html .= ' style="background-color: '.$background_color.'; border-color: '.$border_color.';"';
	}
	$html .= '></span></div></div>';
	return $html;
}
}
add_shortcode('testimonial', 'testimonial');

/* Unordered List shortcode */

if (!function_exists('unordered_list')) {
function unordered_list($atts, $content = null) {
    extract(shortcode_atts(array("style"=>"","animate"=>""), $atts));
    $html =  "<div class='list $style";
    if($animate == "yes"){
    	$html .= " animate_list'>" . $content . "</div>";	
    } else {
    	$html .= "'>" . $content . "</div>";
   	}
    return $html;
}
}
add_shortcode('unordered_list', 'unordered_list');

/* Video shortcode */

if (!function_exists('video')) {
function video($atts, $content = null) {
    $html = ""; 
	extract(shortcode_atts(array("type"=>"youtube","id"=>"","height"=>"") , $atts));	
	$html .= "<div class='video_holder'>"; 
	if($type == 'youtube'){
		$html .= '<iframe title="YouTube video player" height="' . $height . '" src="http://www.youtube.com/embed/' . $id . '?wmode=transparent" wmode="Opaque" frameborder="0" allowfullscreen></iframe>';
	}elseif($type == 'vimeo'){
		$html .= '<iframe src="http://player.vimeo.com/video/' . $id . '" height="' . $height . '" frameborder="0"></iframe>';
	}
	$html .= "</div>"; 
	return $html;
}
}
add_shortcode('video', 'video');