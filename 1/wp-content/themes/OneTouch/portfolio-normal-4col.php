<?php
/*
Template Name: Portfolio simple 4 columns
*/
?>

<?php get_template_part('templates/page', 'header'); ?>

<div class="row">
  <div id="portfolio-page" class="fifteen columns">

      <div class="row">
          <?php while (have_posts()) : the_post(); ?>
          <?php the_content(); ?>
          <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
          <?php endwhile; ?>
      </div>

    <div class="col-4-folio clearing-container">


        <?php
        if ( is_front_page() ) {
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        } else {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }
        $number_per_page = ( get_post_meta($post->ID, 'portfolio_number_to_display',true)  ) ?get_post_meta($post->ID, 'portfolio_number_to_display',true)  : '16';
        $portfolio_categories = ( get_post_meta($post->ID, 'portfolio_category',true) != 'all') ?  get_post_meta($post->ID, 'portfolio_category',true) : '';

        $args = array(
            'post_type' => 'portfolio',
            'posts_per_page' => $number_per_page,
            'paged' => $paged,
            'tax_query' => array(
                array(
                    'taxonomy' => 'project_type',
                    'terms' => array( $portfolio_categories ),
                    'field' => 'slug',
                    'include_children' => false,
                    'operator' => 'IN'
                )
            )
        );


        $the_query = new WP_Query( $args );
        ?>


        <?php if (!have_posts()) : ?>
      <div class="alert alert-block fade in">
        <a class="close" data-dismiss="alert">&times;</a>

        <p><?php _e('Sorry, no results were found.', 'roots'); ?></p>
      </div>
        <?php get_search_form(); ?>
        <?php endif; ?>

        <?php $count = '1';

        while ( $the_query->have_posts() ) : $the_query->the_post();

            if (has_post_thumbnail()) {
                $thumb = get_post_thumbnail_id();
                $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                $article_image = aq_resize($img_url, 400, 400, true);
            }
            if (($count % 2) == 1) {
                $item_class_count = 'odd';
            } else {
                $item_class_count = 'even';
            }
            $count ++;
            ?>



          <div class="portfolio-item item <?php echo $item_class_count?>">
            <img src="<?php echo $article_image ?>" style="margin:0 0;" alt="<?php the_title();?>" title="<?php the_title();?>">

            <div class="description">
              <div class="title">
                <time datetime="<?php echo get_the_time('c'); ?>" ><?php echo get_the_date(); ?></time>
                <h4><?php the_title(); ?></h4>
              </div>

              <p> <?php echo content(30) ?> </p>
            </div>
            <a href="<?php the_permalink();?>"></a>
          </div>

            <?php endwhile;  ?>
    </div>
      <?php if ($wp_query->max_num_pages > 1) : ?>

      <?php if ($NHP_Options->get('pagination_style') == '2') { ?>

          <nav id="post-nav" class="page-numb">
              <?php if (function_exists('wp_corenavi')) wp_corenavi(); ?>
          </nav>

          <?php } else { ?>

          <nav id="post-nav" class="page-nav">
              <?php next_posts_link(__('<span>Older</span>', 'roots')); ?>
              <?php previous_posts_link(__('<span>Newer</span>', 'roots')); ?>
          </nav>

          <?php } endif; wp_reset_postdata(); ?>


  </div>
</div>