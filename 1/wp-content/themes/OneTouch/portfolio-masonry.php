<?php
/*
Template Name: Portfolio page Masonry Blocks
*/
?>

<?php get_template_part('templates/page', 'header'); ?>

<div id="content" class="row">
    <div id="portfolio-page" class="fifteen columns">

        <div class="row">
            <?php while (have_posts()) : the_post(); ?>
            <?php the_content(); ?>
            <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
            <?php endwhile; ?>
        </div>


        <!-- Fullscreen jQuery template -->

        <?php if (!have_posts()) : ?>
        <div class="alert alert-block fade in">
            <a class="close" data-dismiss="alert">&times;</a>

            <p><?php _e('Sorry, no results were found.', 'roots'); ?></p>
        </div>
        <?php get_search_form(); ?>
        <?php endif; ?>

        <div id="container" class="masonry-items">

            <?php

            $number_per_page = get_post_meta($post->ID, 'portfolio_number_to_display', true) ? get_post_meta($post->ID, 'portfolio_number_to_display', true) : $NHP_Options->get('masonry_posts_per_page');

            $number_per_page = ( get_post_meta($post->ID, 'portfolio_number_to_display',true)  ) ?get_post_meta($post->ID, 'portfolio_number_to_display',true)  : '12';
            $portfolio_categories = ( get_post_meta($post->ID, 'portfolio_category',true) != 'all') ?  get_post_meta($post->ID, 'portfolio_category',true) : '';

            if ( is_front_page() ) {
                $paged = (get_query_var('page')) ? get_query_var('page') : 1;
            } else {
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            }

            query_posts('post_type=portfolio&posts_per_page='.$number_per_page.'&project_type='.$portfolio_categories.'&paged=' . $paged);

            while (have_posts()) : the_post();

                if (has_post_thumbnail()) {
                    $thumb = get_post_thumbnail_id();
                    $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                    $article_image = aq_resize($img_url, 285, 500, false); //resize & crop img
                    ?>

    <div class="item block withimg" data-bgimage="<?php echo $img_url ?>">
        <div class="pic"><img src="<?php echo $article_image ?>" alt="<?php the_title(); ?>"></div>
                    <?php } else { ?>
    <div class="item block without" data-bgimage="<?php echo $img_url ?>">
 <?php } ?>


                <a href="<?php the_permalink(); ?>" class="more_link"></a>

                <div class="description">


                    <div class="title">
                        <time><?php echo get_the_date(); ?></time>
                        <h4><?php the_title(); ?></h4>
                    </div>
                </div>
  </div>

              <?php endwhile; // END the Wordpress Loop ?>

        </div><!-- container -->

            <?php if ($NHP_Options->get('pagination_style') == '2') { ?>

            <nav id="post-nav" class="page-numb">
                <?php if (function_exists('wp_corenavi')) wp_corenavi(); ?>
            </nav>

            <?php } else { ?>

            <nav id="post-nav" class="page-nav">
                <?php next_posts_link(__('<span>Older</span>', 'roots')); ?>
                <?php previous_posts_link(__('<span>Newer</span>', 'roots')); ?>
            </nav>

            <?php }  ?>

            <?php wp_reset_query(); // Reset the Query Loop?>



        <?php
            wp_enqueue_style('grid', get_template_directory_uri() . '/assets/css/grid.css', false, null);

            wp_enqueue_script('masonry');
            wp_enqueue_script('easing');
        ?>

            <script type="text/javascript">
                jQuery(document).ready(function () {


                    var $container = jQuery('#container');

                    $container.imagesLoaded(function () {
                        $container.masonry({
                            itemSelector:'div.block'
                        });
                    });


                });
            </script>


        </div>
    </div>




