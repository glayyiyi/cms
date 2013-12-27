<?php
global $NHP_Options;
global $data;
$id = get_the_ID();
$emb_vid_height = get_post_meta($id, 'embedd_video_height', true);

if ($emb_vid_height) {
    echo '<style> object, embed {height: '.$emb_vid_height.'px !important;} </style> ';
}


if (!have_posts()) : ?>
  <div class="alert alert-block fade in">
    <p><?php _e('Sorry, no results were found.', 'roots'); ?></p>
  </div>
    <?php get_search_form(); ?>
<?php endif; ?>


<?php while (have_posts()) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>



    <?php
    global $NHP_Options;
    if( $NHP_Options->get("post_inner_header") == '1'){

    echo '<header style = "margin-left: 0; min-height: auto">';
        get_template_part('templates/entry-meta','single');
    echo '</header>';
    }

    if( $NHP_Options->get("post_share_button") && (($NHP_Options->get("post_share_place") == 'top' )|| ($NHP_Options->get("post_share_place") == 'both' ))) { ?>

        <?php get_template_part('/templates/social', 'share'); ?>

        <?php  }

    if ($NHP_Options->get("thumb_inner_disp") == '1') {
        if (has_post_thumbnail()) {
            $thumb = get_post_thumbnail_id();
            $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
            if ($NHP_Options->get('post_thumbnails_width') != '' && $NHP_Options->get('post_thumbnails_height') != '') {
                $article_image = aq_resize($img_url, $NHP_Options->get('post_thumbnails_width'), $NHP_Options->get('post_thumbnails_height'), true);
            } else {
                $article_image = aq_resize($img_url, 1200, 500, true);
            }
            ?>
            <div class="entry-thumb">
                <a href="<?php the_permalink(); ?>"><img src="<?php echo $article_image ?>" alt="<?php the_title(); ?>"/></a>
            </div>
            <?php
        }
    }
    ?>

    <div class="entry-content">

    <?php if (has_post_format('video')) {

        get_template_part('templates/single-post', 'video');
    }?>

        <?php the_content(); ?>

    </div>

  </article>
<?php    if( $NHP_Options->get("autor_box_disp") =='1'){

        get_template_part('/templates/author', 'box');

    }
    if( $NHP_Options->get("post_share_button") && (($NHP_Options->get("post_share_place") == 'bottom' )|| ($NHP_Options->get("post_share_place") == 'both' ))) {

        get_template_part('/templates/social', 'share');

      }

    endwhile;  comments_template('/templates/comments.php'); ?>

</div>