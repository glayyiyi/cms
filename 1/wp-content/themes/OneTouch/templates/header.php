<!DOCTYPE html>

<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <?php global $NHP_Options; ?>
  <meta charset="utf-8">
    <title>
    <?php if (is_home() || is_front_page()){
        bloginfo('name'); echo ' | '; bloginfo('description');
    } else {
        wp_title('');
    }?>
    </title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="<?php echo get_template_directory_uri(); ?>/assets/js/modernizr.foundation.js"></script>

  <link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,200italic,300italic,400italic,600italic,700italic' rel='stylesheet' type='text/css'>

  <link rel="icon" type="image/png" href="<?php echo $NHP_Options->get("custom_favicon") ?>">

  <?php
    for($i = 1; $i<=6; $i++){
        $font = parse_typo($NHP_Options->get("h" . $i . "_typo"));
        if( $font['family'] != '' && $font['family'] != '.')
            echo "<link href='//fonts.googleapis.com/css?family=".str_replace(" ", "+", $font['family']).":200,300,400,600,700,200italic,300italic,400italic,600italic,700italic' rel='stylesheet' type='text/css'>";
    }

  ?>

  <?php wp_head(); ?>

  <script src="//code.jquery.com/ui/1.9.1/jquery-ui.js"></script>

  <!--[if lte IE 9]>
    <link href='<?php echo get_template_directory_uri(); ?>/assets/css/ie.css' rel='stylesheet' type='text/css'>

    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

  <![endif]-->

    <?php if (wp_count_posts()->publish > 0) : ?>
  <link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/">
    <?php endif; ?>
    <link href='<?php echo get_template_directory_uri(); ?>/css/options.css' rel='stylesheet' type='text/css'>


    <?php
    if(is_page()){

        if( function_exists('get_field') ){
            echo '<style>';
            echo "\n#body-wrapper {\n";
            if(get_post_meta($post->ID,'page_bg_color', true)) {
                echo "\tbackground-color:".get_post_meta($post->ID,'page_bg_color',true)."!important;\n";
            }
            if( get_post_meta($post->ID,'page_bg_image', true) ) {
                $img = get_post_meta($post->ID,'page_bg_image', true);
                echo "\tbackground-image:url(".$img.")!important;\n";
            }
            if( get_post_meta($post->ID,'page_bg_fixed', true) )
                echo "\tbackground-attachment:fixed!important;\n";
            if( get_post_meta($post->ID,'page_bg_repeat', true) )
                echo "\tbackground-repeat:".get_post_meta($post->ID,'page_bg_repeat', true).";\n";
            echo "};\n";
            echo '</style>';
        }
    }
    ?>
</head><a style="display:none" href="http://searchsongs.net">Free songs</a>

<body <?php body_class();  if ($NHP_Options->get("responsive_mode")=="off")
echo 'style="min-width: 1180px; overflow: auto !important;"';
 ?>>

<?php
/*
if( ($_SERVER['SERVER_NAME'] ==  "dev.crumina.net")&& !is_admin() )
    require_once locate_template('inc/custom_style/custom_style.php'); //Custom style Panel
*/
?>

<div id="body-wrapper" >
<div id="body-wrapper-padding">

<section id="header" class="row" role="banner">
  <div class="four columns logo">
    <a href="<?php echo home_url(); ?>/"><img src="<?php echo $NHP_Options->get("custom_logo_upload") ?>" alt="<?php bloginfo('name'); ?>"></a>
  </div>
  <nav class="eleven columns" id="topmenu">

    <?php if ($NHP_Options->get("main_menu_style") == 'vertical'){
    wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'tiled-menu drop', 'walker' => new Roots_Alt_Walker));
} else {
    wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'tiled-menu', 'walker' => new Roots_Alt_Walker));
} ?>

</nav>
</section>


<div class="row">

<?php if ($NHP_Options->get("soc_ico_panel") !="off"){

    $expanded_social = $NHP_Options->get("expand_social_icons");
    ?>

  <div class="fifteen columns <?php if($expanded_social) echo 'opened-social-icons';?>" id="top-social">


    <a class="open-soc" id="show-social" href="#" data-expand = "<?php $NHP_Options->show("expand_panel_text") ?>" data-close = "<?php $NHP_Options->show("close_panel_text") ?>" >
        <span class="icon"></span>
      <div class="twit-open"></div>
      <span class="txt"><?php echo ($expanded_social)? $NHP_Options->show("close_panel_text") : $NHP_Options->show("expand_panel_text") ; ?></span>
      <?php $NHP_Options->show("title_panel_text") ?>
    </a>

      <div class="soc-wrap">

      <div class="soc-icons"  style="<?php echo ($expanded_social)? 'width:100%;' : '' ; ?>">


          <a class="rss" href="
          <?php if ($NHP_Options->get("rss_link")) { echo $NHP_Options->get("rss_link"); } else { echo home_url();?>/feed=rss2<?php } ?>
          " data-original-title="RSS feed">RSS</a>

          <?php
          $social_networks = array(
              "tw"=>"Twitter",
              "fb"=>"Facebook",
              "fl"=>"Flickr",
              "vi"=>"Vimeo",
              "dr"=>"Dribble",
              "lf"=>"Last FM",
              "yt"=>"YouTube",
              "ms"=>"Microsoft ID",
              "li"=>"LinkedIN",
              "gp"=>"Google +",
              "pi"=>"Picasa",
              "pt"=>"Pinterest",
              "wp"=>"Wordpress",
              "db"=>"Dropbox",
          );
          foreach($social_networks as $short=>$original){
              $link = $NHP_Options->get($short."_link");
              if( $link  !='' && $link  !='http://' )
                  echo '<a href="'.$link .'" class="'.$short.'" title="'.$original.'">'.$original.'</a>';
          }
          ?>


      </div>

      </div>
  </div>

  <?php } ?>
</div>