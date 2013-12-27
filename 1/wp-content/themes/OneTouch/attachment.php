
<div id="content" class="row">

    <div class="fifteen columns">

        <article>
            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

            <article <?php post_class() ?> id="post-<?php the_ID(); ?>">

                <div class="entry">

                    <div class="entry-attachment">

                        <?php if ( wp_attachment_is_image() ) :
                        $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
                        foreach ( $attachments as $k => $attachment ) {
                            if ( $attachment->ID == $post->ID )
                                break;
                        }
                        $k++;
                        // If there is more than 1 image attachment in a gallery
                        if ( count( $attachments ) > 1 ) {
                            if ( isset( $attachments[ $k ] ) )
                                // get the URL of the next image attachment
                                $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
                            else
                                // or get the URL of the first image attachment
                                $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
                        } else {
                            // or, if there's only 1 image attachment, get the URL of the image
                            $next_attachment_url = wp_get_attachment_url();
                        }
                        ?>
                        <div class="attachment" style="text-align: center; padding: 20px 0">
                            <a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment" class="zoom">

                            <?php
                            $attachment_width  = apply_filters( 'attachment_width', 1180 );
                            $attachment_height = apply_filters( 'attachment_height', 1180 );
                            echo wp_get_attachment_image( $post->ID, array( $attachment_width, $attachment_height ) ); // filterable image width with, essentially, no limit for image height.
                            ?>
                            </a>

                            <div class="nav-previous">
                                <?php previous_image_link( false, '&nbsp;' ); ?>
                            </div>
                            <div class="nav-next">
                                <?php next_image_link( false, '&nbsp;' ); ?>
                            </div>

                        </div>

                        <?php endif; ?>
                    </div><!-- .entry-attachment -->

                    <div class="entry-caption"  style="text-align: center;">

                        <?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?>

                    </div>

                </div><!-- entry -->

            </article><!-- post -->

            <?php //comments_template(); ?>

            <?php endwhile; // end of the loop. ?>
        </article>

    </div>
</div>