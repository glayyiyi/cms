<div class="entry-thumb">

    <blockquote class="clearing-container">
    <?php the_content(); ?>

        <?php $post_id =  get_the_ID();
        if( get_post_meta($post_id,  "quote_author" ) ): ?>
           <div style="text-align: right"><strong><?php echo get_post_meta($post_id,  "quote_author" ); ?></strong></div>
        <?php endif;?>

    </blockquote>
</div>
