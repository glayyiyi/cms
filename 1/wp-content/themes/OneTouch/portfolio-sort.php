<?php
/*
Template Name: Portfolio page With Sorting
*/
?>

<?php get_template_part('templates/page', 'header');
//Values from custom metaboxes
$portfolio_categories = array();
$number_per_page = ( get_post_meta($post->ID, 'portfolio_number_to_display',true)  ) ?get_post_meta($post->ID, 'portfolio_number_to_display',true) : '-1';
$portfolio_categories = ( get_post_meta($post->ID, 'portfolio_category',true) != 'all') ?  get_post_meta($post->ID, 'portfolio_category',true) : '';
?>

<div class="row">
  <div id="portfolio-page" class="fifteen columns">

      <div class="row">
          <?php while (have_posts()) : the_post(); ?>
          <?php the_content(); ?>
          <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
          <?php endwhile; ?>
      </div>
 
      <?php if ($portfolio_categories ==''){ ?>

    <div class="sort-panel" style="min-height:20px">
      <ul class="filter clearfix">
        <li class="active">
          <a href="javascript:void(0)" class="all">All</a>
        </li>

          <?php
          $i = $count = 1;
// Get Portfolio Categories
          $taxonomy = 'project_type';
          $categories = get_terms($taxonomy);


          global $data;
// List the Portfolio Categories
          foreach ($categories as $category) {
              $i++;
              // rewrite the output for each category
              $term_list .= '<li>
        <a href="javascript:void(0)" class="'. strtolower(preg_replace('/\s+/', '-', $category->slug)) .'">' . $category->name . '</a>
        </li>';
              // if count is equal to i then output blank
              if ($count != $i)
              { $term_list .= '';}
              else
              {$term_list .= '';}
          }
          // print out each of the categories in our new format
          echo $term_list; ?>

      </ul>
    </div>

    <div class="works-list">
      <ul class="filterable-grid">
          <?php

          $args = array(
              'post_type' => 'portfolio',
              'taxonomy' => 'project_type',
              'posts_per_page' => $number_per_page
          );


          $the_query = new WP_Query( $args );

          // The Loop
          while ($the_query->have_posts()) : $the_query->the_post();

              $terms = get_the_terms( get_the_ID(), 'project_type' );

              if (has_post_thumbnail()) {
                  $thumb = get_post_thumbnail_id();
                  $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
              } else {
                  $img_url = get_template_directory_uri() . '/img/no-image-large.jpg';
              }


              global $data;
              $id = get_the_ID();

              $folio_size = '';

              if ((($count %3) == 0)) {
                  $folio_size = 'large';
              }

              if ($folio_size == 'large'){
                  $item_class_width = 'large';
                  $article_image = aq_resize($img_url, 398, 180, true); //resize & crop img
              }else {
                  $item_class_width = 'half';
                  $article_image = aq_resize($img_url, 197, 180, true); //resize & crop img
              }

              if (($count %2) == 1) {
                  $item_class_count = 'odd';
              }else {
                  $item_class_count = 'even';
              }
              ?>


            <li class="item" data-id="id-<?php echo $count; ?>" data-type="<?php foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->slug)). ' '; } ?>">

              <div class="<?php echo $item_class_width . ' ' .$item_class_count;  ?>">
                <div class="pic"><img src="<?php echo $article_image ?>" style="margin:0 0;" alt="<?php the_title();?>" title="<?php the_title();?>" ></div>
                <div class="description">
                  <div class="title">
                    <time datetime="<?php echo get_the_time('c'); ?>" ><?php echo get_the_date(); ?></time>
                    <h4><?php the_title(); ?></h4>
                  </div>
                </div>
                <a href="<?php the_permalink();?>"></a>
              </div>
            </li>


              <?php  $count++; // Increase the count by 1 ?>
              <?php endwhile; // END the Wordpress Loop ?>
          <?php wp_reset_query(); // Reset the Query Loop?>

      </ul>
    </div>
    <?php } else { ?>

      <div class="works-list">
          <ul class="filterable-grid">
              <?php

              $args = array(
                  'post_type' => 'portfolio',
                  'posts_per_page' => $number_per_page,
                  'project_type' => $portfolio_categories
              );
              $the_query = new WP_Query($args);

              // The Loop
              while ($the_query->have_posts()) : $the_query->the_post();

                  $terms = get_the_terms( get_the_ID(), 'project_type' );

                  if (has_post_thumbnail()) {
                      $thumb = get_post_thumbnail_id();
                      $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                  } else {
                      $img_url = get_template_directory_uri() . '/img/no-image-large.jpg';
                  }


                  global $data;
                  $id = get_the_ID();

                  $folio_size = '';

                  if ((($count %3) == 0)) {
                      $folio_size = 'large';
                  }

                  if ($folio_size == 'large'){
                      $item_class_width = 'large';
                      $article_image = aq_resize($img_url, 398, 180, true); //resize & crop img
                  }else {
                      $item_class_width = 'half';
                      $article_image = aq_resize($img_url, 197, 180, true); //resize & crop img
                  }

                  if (($count %2) == 1) {
                      $item_class_count = 'odd';
                  }else {
                      $item_class_count = 'even';
                  }
                  ?>


                  <li class="item" data-id="id-<?php echo $count; ?>" data-type="<?php foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->slug)). ' '; } ?>">

                      <div class="<?php echo $item_class_width . ' ' .$item_class_count;  ?>">
                          <div class="pic"><img src="<?php echo $article_image ?>" style="margin:0 0;" alt="<?php the_title();?>" title="<?php the_title();?>" ></div>
                          <div class="description">
                              <div class="title">
                                  <time datetime="<?php echo get_the_time('c'); ?>" ><?php echo get_the_date(); ?></time>
                                  <h4><?php the_title(); ?></h4>
                              </div>
                          </div>
                          <a href="<?php the_permalink();?>"></a>
                      </div>
                  </li>


                  <?php  $count++; // Increase the count by 1 ?>
                  <?php endwhile; // END the Wordpress Loop ?>
              <?php wp_reset_query(); // Reset the Query Loop?>

          </ul>
      </div>


    <?php } ?>

  </div>
</div>

<?php
wp_register_script('qsand', ''.get_template_directory_uri().'/assets/js/jquery.quicksand.js', false, array('jquery'), true);
wp_enqueue_script('qsand');
wp_register_script('pfoto', ''.get_template_directory_uri().'/assets/js/jquery.prettyPhoto.js', false, array('jquery'), true);
wp_enqueue_script('pfoto');
?>

<script type="text/javascript">
  /*-----------------------------------------------------------------------------------

     Custom JS - All front-end jQuery

-----------------------------------------------------------------------------------*/

  jQuery(document).ready(function($) {



    function portfolio_quicksand() {

      // Setting Up Our Variables
      var $filter;
      var $container;
      var $containerClone;
      var $filterLink;
      var $filteredItems

      // Set Our Filter
      $filter = $('.filter li.active a').attr('class');

      // Set Our Filter Link
      $filterLink = $('.filter li a');

      // Set Our Container
      $container = $('ul.filterable-grid');

      // Clone Our Container
      $containerClone = $container.clone();

      // Apply our Quicksand to work on a click function
      // for each for the filter li link elements
      $filterLink.click(function(e)
      {
        // Remove the active class
        $('.filter li').removeClass('active');

        // Split each of the filter elements and override our filter
        $filter = $(this).attr('class').split(' ');

        // Apply the 'active' class to the clicked link
        $(this).parent().addClass('active');

        // If 'all' is selected, display all elements
        // else output all items referenced to the data-type
        if ($filter == 'all') {
          $filteredItems = $containerClone.find('li');
        }
        else {
          $filteredItems = $containerClone.find('li[data-type~=' + $filter + ']');
        }

        // Finally call the Quicksand function
        $container.quicksand($filteredItems,
            {
              // The Duration for animation
              duration: 450,
              // the easing effect when animation
              easing: 'easeInOutCirc'
              // height adjustment becomes dynamic

            });

        //Initalize our PrettyPhoto Script When Filtered
        $container.quicksand($filteredItems,
            function () { lightbox(); }
        );
      });
    }

    if(jQuery().quicksand) {
      portfolio_quicksand();
    }

    function lightbox() {
      // Apply PrettyPhoto to find the relation with our portfolio item
      $("a[rel^='prettyPhoto']").prettyPhoto({
        // Parameters for PrettyPhoto styling
        animationSpeed:'fast',
        slideshow:5000,
        theme:'pp_default',
        show_title:false,
        overlay_gallery: false,
        social_tools: false
      });
    }

    if(jQuery().prettyPhoto) {
      lightbox();
    }


  }); // END OF DOCUMENT
</script>

</div>