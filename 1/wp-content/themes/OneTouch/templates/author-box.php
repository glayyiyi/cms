<?php
/**
 * Author Box
 *
 * Displays author box with author description and thumbnail on single posts
 *
 * @package WordPress
 * @subpackage OneTouch theme, for WordPress
 * @since OneTouch theme 1.9
 */
?>
<div class="author_info">
    <div class="author_photo">
        <?php echo get_avatar( get_the_author_id() , 60 ); ?>
    </div>
    <div class="author-description">
        <h4><?php the_author_posts_link(); ?> </h4>
        <p><?php the_author_description(); ?></p>
        <ul class="dopinfo">
            <li class="first"><a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php _e('All posts', 'roots'); ?></a></li>
            <li><a href="<?php the_author_meta('user_url'); ?>" rel="author" ><?php _e('Website', 'roots'); ?></a>   </li>
            <li><a href="mailto:<?php echo antispambot(get_the_author_email()); ?>" title="E-mail"><?php _e('Email', 'roots');?></a></li>
        </ul>
        <div class="soc-icons">
            <?php if (get_the_author_meta('twitter')) {  echo '<a class="tw" href="',the_author_meta('twitter'),'"></a>';  } ?>
            <?php if (get_the_author_meta('facebook')) {  echo '<a class="fb" href="',the_author_meta('facebook'),'"></a>';  } ?>
            <?php if (get_the_author_meta('googleplus')) {  echo '<a class="gp" href="',the_author_meta('googleplus'),'"></a>';  } ?>
            <?php if (get_the_author_meta('linkedin')) {  echo '<a class="li" href="',the_author_meta('linkedin'),'"></a>';  } ?>
            <?php if (get_the_author_meta('flickr')) {  echo '<a class="fl" href="',the_author_meta('flickr'),'"></a>';  } ?>
        </div>
    </div>

</div>