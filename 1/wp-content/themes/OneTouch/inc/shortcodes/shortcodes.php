<?php
function crum_button( $atts, $content = null ) {
    extract( shortcode_atts( array(
    ), $atts ) );
   return '<button>'.$content.'</button>';
}
add_shortcode('button','crum_button');

function crum_blockquote( $atts, $content = null ) {
    return '<blockquote>'.$content.'</blockquote>';
}
add_shortcode('blockquote','crum_blockquote');

function crum_divider( $atts, $content = null ) {
    return '<hr />';
}
add_shortcode('divider','crum_divider');

function crum_padding( $atts, $content = null ) {
    return '<div></div>';
}
add_shortcode('padding','crum_padding');

function crum_fancy_heading($atts, $content = null){
    return '<span class="fancy_heading">'.$content.'</span>';
}

add_shortcode('fancy_heading','crum_fancy_heading');