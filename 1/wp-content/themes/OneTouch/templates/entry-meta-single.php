<div class="dopinfo-inner dopinfo">

    <span class="entry-date">
         <?php echo get_the_date(); ?> <?php the_time('g:i a'); ?>
    </span>
    <span class="delim"></span>

    <span class="byline author vcard"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></span>

    <span class="delim"></span> <span class="tags"><?php the_category(', '); ?></span>

    <span class="delim"></span>  <?php comments_popup_link(__('Leave a comment', 'twentyten'), __('1 Comment', 'twentyten'), __('% Comments', 'twentyten')); ?>

</div>