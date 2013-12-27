<div class="dopinfo">

  <span class="byline author vcard"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></span>



    <?php the_tags('<span class="delim"></span> <span class="tags">', ', ', '</span>'); ?>

  <span class="delim"></span>

    <?php comments_popup_link(__('Leave a comment', 'twentyten'), __('1 Comment', 'twentyten'), __('% Comments', 'twentyten')); ?>

</div>