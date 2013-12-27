<?php get_template_part('templates/page', 'header'); ?>


<div class="row">

    <?php
    $post_display = get_post_meta($post->ID, 'full_width_post', true);  ;

    if( !$post_display || $post_display == 'default' ) {
        set_layout('single', true);
    } else {
        if (($post_display == "2c-l-fixed") || ($post_display == "3c-fixed") || ($post_display == "3c-l-fixed")) {
            get_template_part('templates/sidebar', 'left');
        }
        if ($post_display == "3c-l-fixed"){
            get_template_part('templates/sidebar', 'right');
        }

        if (($post_display == "2c-l-fixed") || ($post_display == "2c-r-fixed")) {
            echo '<div id="content" class="eleven columns">';
        } elseif (($post_display == "1col-fixed")) {
            echo '<div id="content" class="fifteen columns">';
        } else {
            echo '<div id="content" class="seven columns">';
        }
    }

    get_template_part('templates/content', 'single');

    if( !$post_display || $post_display == 'default' ) {
        set_layout('single', false);
    } else {
        if ($post_display == "3c-r-fixed") {
            get_template_part('templates/sidebar', 'left');
        }
        if (($post_display == "2c-r-fixed") || ($post_display == "3c-fixed") || ($post_display == "3c-r-fixed")) {
            get_template_part('templates/sidebar', 'right');
        }
    }
    ?>
</div>

