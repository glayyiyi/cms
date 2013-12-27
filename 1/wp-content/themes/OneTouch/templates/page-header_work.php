<div class="row">
  <div class="twelve columns" id="page-title">
    <a class="back" href="javascript:history.back()"></a>
    <div class="subtitle">
        <?php
        if (is_archive()) {
            echo category_description($category[0]->cat_ID);    }
        elseif (is_single()) {
            $category = get_the_category();
            echo $category[0]->category_description;
        } elseif (is_page()) {
            echo (get_field("subtitle", $post->ID))?(get_field("subtitle", $post->ID)):'';
        } elseif (is_singular('portfolio') || is_singular('gallery')) {

        }elseif (is_404()) {
            _e('File Not Found', 'roots');
        }
        ?>
    </div>
      <h1 class="page-title">
          <?php

          function get_ID_by_slug($page_slug) {
              $page = get_page_by_path($page_slug);
              if ($page) {
                  return $page->ID;
              } else {
                  return null;
              }
          }

          global $NHP_Options;
          if ($NHP_Options->get("portfolio_page_select")){
              $foli_id = $NHP_Options->get("portfolio_page_select");
          } else {
              $foli_id = get_ID_by_slug('portfolio');
          }

          $folio = get_post($foli_id);
          $slug = $folio->post_name;
          $foli_title = get_the_title($foli_id);

              the_title();
           ?>
      </h1>

      <div class="breadcrumbs">
          <a rel="v:url" property="v:title" href="<?php echo home_url(); ?>/"><?php _e('Home', 'roots') ?></a>
          <span class="delim"> / </span>
          <a rel="v:url" property="v:title" href="<?php echo home_url() . '/' . $slug; ?>/"><?php echo $foli_title; ?></a>
          <span class="delim"> / </span>
          <?php the_title(); ?>
      </div>


  </div>

    <div class="three columns">
        <?php while (have_posts()) : the_post(); ?>
        <nav class="post-nav right">
            <?php previous_post_link('%link','<span>Prev.</span>', $loop->max_num_pages); ?>
            <?php next_post_link('%link','<span>Next</span>', $loop->max_num_pages); ?>
        </nav>
        <?php endwhile; ?>
    </div>


  <div class="fifteen columns"><div class="line"> </div></div>

</div>