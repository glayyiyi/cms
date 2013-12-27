<?php
    $args = array(
        'order' => 'ASC',
        'post_type' => 'attachment',
        'post_parent' => $post->ID,
        'post_mime_type' => 'image',
        'post_status' => null,
        'numberposts' => -1,
    );
    $attachments = get_posts($args);
    if ($attachments) {
        echo '<div class="slide-post">';

            foreach ($attachments as $attachment) {
                $img_url =  wp_get_attachment_url($attachment->ID); //get img URL
                $article_image = aq_resize( $img_url, 1200, 999, false ); //resize & crop img
                ?>

                    <img src="<?php echo $article_image ?>" alt="<?php the_title(); ?>"/>

            <?php  }
        echo '</div>';
    }
the_excerpt('');
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".slide-post").orbit({
          fluid: true,
          directionalNav: false
        });

    });

</script>
