<?php
/*
Template Name: Posts with both sidebars
*/
?>


<?php  get_template_part('templates/page', 'header'); ?>

  <div class="row">

      <?php

$data['archive_layout'] = "3c-fixed";

if (($data['archive_layout'] == "2c-l-fixed") || ($data['archive_layout'] == "3c-fixed")) {
    get_template_part('templates/sidebar', 'left');
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

      $number_per_page = get_post_meta($post->ID, 'blog_number_to_display',true) ? get_post_meta($post->ID, 'blog_number_to_display',true) : $NHP_Options->get('posts_per_page');
      $number_per_page = ( $number_per_page ) ? $number_per_page : '12';

      $categories = ( get_post_meta($post->ID, 'blog_category',true) != 'all') ?  get_post_meta($post->ID, 'blog_category',true) : '';

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

      query_posts('post_type=post&posts_per_page='.$number_per_page.'&paged=' . $paged . $cat_query);

    get_template_part('templates/content', '');

echo '</div>';
if ($data['archive_layout'] == "3c-r-fixed") {
    get_template_part('templates/sidebar', 'left');
}

if (($data['archive_layout'] == "2c-r-fixed") || ($data['archive_layout'] == "3c-fixed") || ($data['archive_layout'] == "3c-r-fixed")) {
    get_template_part('templates/sidebar', 'right');
} ?>

</div>