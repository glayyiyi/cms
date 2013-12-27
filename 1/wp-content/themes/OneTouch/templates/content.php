<?php global $NHP_Options; ?>
<div id="main">
    <?php if (!have_posts()) : ?>


  <div class="alert alert-block fade in">
    <div class="messagebox_text">
      <p> </p>
      <h2><?php _e('Sorry, no results were found.', 'roots'); ?></h2>
    </div>
  </div>

    <?php get_search_form(); ?>
    <?php endif; ?>

    <?php while (have_posts()) : the_post(); ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <time class="updated" datetime="<?php echo get_the_time('c'); ?>" pubdate>
      <span class="day"><?php echo get_the_date('d'); ?></span>
      <span class="mounth"><?php echo get_the_date('M'); ?>.</span>
      <span class="time"><?php the_time('g:i a'); ?></span>
    </time>

    <header>
        <?php get_template_part('templates/entry-meta'); ?>
      <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    </header>

      <?php

      if ( has_post_format( 'video' ) || has_post_format( 'gallery' )) {

      } else {


      if (has_post_thumbnail()) {
          $thumb = get_post_thumbnail_id();
          $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
            if ($NHP_Options->get('post_thumbnails_width') !='' && $NHP_Options->get('post_thumbnails_height') !=''){
                $article_image = aq_resize($img_url, $NHP_Options->get('post_thumbnails_width'), $NHP_Options->get('post_thumbnails_height'), true);
            } else {
                $article_image = aq_resize($img_url, 1200, 500, true);
            }
          ?>
        <div class="entry-thumb">
          <a href="<?php the_permalink(); ?>"><img src="<?php echo $article_image ?>" alt="<?php the_title(); ?>"/></a>
        </div>
          <?php }}?>

    <div class="entry-content">

        <?php global $data;  echo $data['type_posts_show']; if ($data['type_posts_show'] == 'full_post') {
            the_content('');
         } else {
        if ( has_post_format( 'gallery' )) {
            get_template_part('templates/post', 'gallery');
        }if ( has_post_format( 'link' )) {
            get_template_part('templates/post', 'link');
        }if ( has_post_format( 'image' )) {
            get_template_part('templates/post', 'image');
        }if ( has_post_format( 'quote' )) {
            get_template_part('templates/post', 'quote');
        }if ( has_post_format( 'video' )) {
            get_template_part('templates/post', 'video');
        }if ( has_post_format( 'audio' )) {
            get_template_part('templates/post', 'audio');
        }else {
            $format = get_post_format();
            if ( false === $format ) {
          echo '<p>';
            the_excerpt('');
          echo '</p>';
            }
        }}?>

    </div>

  </article>

    <?php endwhile; ?>

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

    <?php } endif; ?>

</div>




