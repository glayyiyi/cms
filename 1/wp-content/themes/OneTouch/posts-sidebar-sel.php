<?php
/*
Template Name: Posts with user layout selection
*/
?>

<?php
global $layout;
$layouts = array(
    "right-1"     => "right-1",
    "left-1"      => "left-1",
    "left-2"      => "left-2",
    "right-2"     => "right-2",
    "both"        => "both",
    "no-sidebars" => "no-sidebars"
);

if(isset($_GET["page_layout"])){
    $layout = $_GET["page_layout"];
    $_SESSION["page_layout".$post->ID] = $_GET["page_layout"];
} elseif( isset($_SESSION["page_layout".$post->ID]) ){
    $layout = $_SESSION["page_layout".$post->ID];
} else {
    $layout = get_field( "posts_page_layout",$post->ID );
}

if( !isset($layouts[$layout]) )
    $layout ='both';


switch($layout) {
    case "no-sidebars":
        $data['archive_layout'] = "1col-fixed";
    break;
    case "right-1":
        $data['archive_layout'] = "2c-r-fixed";
    break;
    case "left-1":
        $data['archive_layout'] = "2c-l-fixed";
    break;
    case "right-2":
        $data['archive_layout'] = "3c-r-fixed";
    break;
    case "left-2":
        $data['archive_layout'] = "3c-l-fixed";
    break;
    case "both":
        $data['archive_layout'] = "3c-fixed";
    break;
    default:
        $data['archive_layout'] = "3c-fixed";
    break;
}

?>

<?php  get_template_part('templates/page', 'header_lay'); ?>

<div class="row">

<?php  if (($data['archive_layout'] == "2c-l-fixed") || ($data['archive_layout'] == "3c-fixed")|| ($data['archive_layout'] == "3c-l-fixed")) {
    get_template_part('templates/sidebar', 'left');
}
    if ($data['archive_layout'] == "3c-l-fixed") {
        get_template_part('templates/sidebar', 'right');
    }
if (($data['archive_layout'] == "2c-l-fixed") || ($data['archive_layout'] == "2c-r-fixed")) {
    echo '<div id="content" class="eleven columns">';
} elseif (($data['archive_layout'] == "1col-fixed")) {
    echo '<div id="content" class="fifteen columns">';
} else {
    echo '<div id="content" class="seven columns">';
}

    if ( is_front_page() ) {
        $paged = (get_query_var('page')) ? get_query_var('page') : 1;
    } else {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }

    query_posts('post_type=post&posts_per_page='.$NHP_Options->get('posts_per_page').'&paged=' . $paged);

    get_template_part('templates/content', '');

    echo '</div>';
	
if ($data['archive_layout'] == "3c-r-fixed") {
    get_template_part('templates/sidebar', 'left');
}
if (($data['archive_layout'] == "2c-r-fixed") || ($data['archive_layout'] == "3c-fixed") || ($data['archive_layout'] == "3c-r-fixed")) {
    get_template_part('templates/sidebar', 'right');
} ?>

</div>