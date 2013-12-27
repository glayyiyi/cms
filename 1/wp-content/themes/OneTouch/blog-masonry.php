<?php
/*
Template Name: Blog with Masonry Blocks
*/
?>

<?php global $NHP_Options; ?>
<?php get_template_part('templates/page', 'header'); ?>

<div class="row">
  <div class="fifteen columns">
    <div class="row">
      <?php while (have_posts()) : the_post(); ?>
      <?php the_content(); ?>
      <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
      <?php endwhile; ?>
    </div>

    <?php

      if ( is_front_page() ) {
          $paged = (get_query_var('page')) ? get_query_var('page') : 1;
      } else {
          $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      }

      $number_per_page = get_post_meta($post->ID, 'blog_number_to_display',true) ? get_post_meta($post->ID, 'blog_number_to_display',true) : $NHP_Options->get('masonry_posts_per_page');
      $number_per_page = ( $number_per_page ) ? $number_per_page : '12';

      $categories = ( get_post_meta($post->ID, 'blog_category',true) != 'all') ?  get_post_meta($post->ID, 'blog_category',true) : '';
      //$categories = $categories ? '&category='.$categories : '';
      $cat_query = '';
      if( $categories ){
          $cat_query = '&cat=';
          $categories = explode(",",$categories);
          foreach ($categories as $key=>$category){
              $category = get_category_by_slug( $category );
              $categories[$key] = $category->cat_ID ;
          }
          $cat_query .= implode( ',', $categories );
      }

    query_posts('post_type=post&posts_per_page=' . $number_per_page . '&paged=' . $paged .  $cat_query);

    get_template_part('templates/content', 'grid');
   ?>

  </div>
</div>

<?php
wp_enqueue_script('masonry');
wp_enqueue_script('easing');

wp_enqueue_style('grid', get_template_directory_uri() . '/assets/css/grid.css', false, null);
?>

<script type="text/javascript">
  jQuery(document).ready(function(){


    var $container = jQuery('#grid-posts');

    $container.imagesLoaded( function(){
      $container.masonry({
        itemSelector : 'article.mini'
      });
    });


  });
</script>