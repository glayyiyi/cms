<?php global $NHP_Options;
    $is_full = ($NHP_Options->get("portfolio_single_style") == 'full');
?>
<div id="single-work" class="clearing-container">
<div class="<?php echo ($is_full) ? 'fifteen' : 'ten'; ?> columns">

<?php if (!post_password_required(get_the_id())) { ?>
    <?php $custom = get_post_custom($post->ID);

    if (get_post_meta($post->ID, 'folio_vimeo_video_url', true)): ?>

        <div class="video">
            <p>
            <iframe src='http://player.vimeo.com/video/<?php echo get_post_meta($post->ID, 'folio_vimeo_video_url', true); ?>?portrait=0' width='640' height='460' frameborder='0'></iframe>
            </p>
        </div>

        <?php endif;
    if (get_post_meta($post->ID, 'folio_youtube_video_url', true)): ?>

        <div class="video">
            <iframe width="640" height="460" src="http://www.youtube.com/embed/<?php echo get_post_meta($post->ID, 'folio_youtube_video_url', true); ?>?wmode=opaque" frameborder="0" class="youtube-video" allowfullscreen></iframe>
        </div>

        <?php
        if ((get_post_meta($post->ID, 'folio_self_hosted_mp4', true) != '') || (get_post_meta($post->ID, 'folio_self_hosted_webm', true) != '')) {

            if (has_post_thumbnail()) {
                $thumb = get_post_thumbnail_id();
                $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                $article_image = aq_resize($img_url, 500, 300, true); ?>

                <?php } ?>

            <link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet">
            <script src="http://vjs.zencdn.net/c/video.js"></script>


            <video id="video-post<?php the_ID();?>" class="video-js vjs-default-skin" controls
                   preload="auto"
                   width="640"
                   height="480"
                   poster="<?php echo $article_image ?>"
                   data-setup="{}">


                <?php if (get_post_meta($post->ID, 'folio_self_hosted_mp4', true)): ?>
                <source src="<?php echo get_post_meta($post->ID, 'folio_self_hosted_mp4', true) ?>" type='video/mp4'>
                <?php endif;?>
                <?php if (get_post_meta($post->ID, 'folio_self_hosted_webm"', true)): ?>
                <source src="<?php echo get_post_meta($post->ID, 'folio_self_hosted_webm"', true)  ?>" type='video/webm'>
                <?php endif;?>
            </video>


            <?php
        }

    endif;

    if ($NHP_Options->get("portfolio_single_featured") != '2') {

        echo '<div id="work-slider">';

        global $data;
        if (has_post_thumbnail()) {
            $thumb = get_post_thumbnail_id();
            $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
            $img_width = $is_full ? 1200 : 940;
            $img_height = $is_full ? 1200 : 999;
            $article_image = aq_resize($img_url, $img_width, $img_height, false); //resize & crop img
        }

        $args = array(
            'order' => 'ASC',
            'exclude' => get_post_thumbnail_id($post->ID),
            'post_type' => 'attachment',
            'post_parent' => $post->ID,
            'post_mime_type' => 'image',
            'post_status' => null,
            'numberposts' => -1,
        );
        $attachments = get_posts($args);
        $attachments_count = '0';
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $img_url = wp_get_attachment_url($attachment->ID); //get img URL

                if ($NHP_Options->get("portfolio_single_slider") == 'full') {
                    $img_height = 9999;
                } else {
                    $img_height = 1200;
                }

                $img_width = 1200;

                $article_image = aq_resize($img_url, $img_width, $img_height, false); //resize & crop img
                $attachments_count++;
                ?>

                <img src="<?php echo $article_image ?>" alt="<?php the_title(); ?>" class="thumb1"/>


                <?php
            }
        } else {
            ?>

            <img src="<?php echo $article_image ?>" alt="<?php the_title(); ?>" class="thumb2"/>


            <?php
        }
        echo '</div>';
    } ?>

</div>

    <div  class="folio-info <?php echo $is_full?'fifteen':'five'; ?> columns">

    <?php while (have_posts()) : the_post(); ?>

        <time class="updated" datetime="<?php echo get_the_time('c'); ?>" pubdate>
            <?php _e('Date', 'roots'); ?>:  <?php echo get_the_date('d.m.Y'); ?>
        </time>


      <div class="entry-content">
        <?php the_content(); ?>
      </div>
          <div class="custom-fields">
              <?php

              if(function_exists('get_field_object')){

              $fields = get_post_custom_keys( get_the_id() );
              foreach( $fields as $key=>$field ){
                  substr( $field, 0, 1);
                  if(substr( $field, 0, 1) == '_')
                      unset($fields[$key]);
              }

              $fields = array_flip($fields);
              foreach($fields as $key=>$field){
                  $fields[$key] = get_post_meta($post->ID,$key, true);
              }
              //var_dump($fields);
              if( $fields ) {
                  foreach( $fields as $field_name => $value ) {

                      $field = get_field_object($field_name, false, array('load_value' => false));

                      if (($field_name == 'display_post_in_slider')||($field_name == 'folio_vimeo_video_url')||($field_name == 'folio_self_hosted_mp4')||($field_name == 'folio_self_hosted_webm') ){} else

                      if (($field_name == 'website_link')|| ($field_name == 'wesite_link')){

                          echo '<div class="field field-link"><a href="http://'.$value.'">';
                          echo $value;
                          echo '</a>';
                          echo '</div>';
                      }else {
                          if($field['label'] != ''){
                          echo '<div class="field field-' . $field['type'] . '">';
                          echo $field['label'] . ': ';
                          echo $value;
                          echo '</div>';
                          }
                      }
                  }
              }} ?>
          </div>
    <?php endwhile; ?>
    </div>



    <?php if (has_post_thumbnail()&&($NHP_Options->get("portfolio_single_slider") != 'full')&&($attachments_count > 1)){ ?>

<script type="text/javascript">
        jQuery(window).load(function() {
            jQuery("#work-slider").orbit();
        });

</script>
    <?php
    }

    if( $NHP_Options->get("post_share_button") ) { ; ?>

      <!--social share buttons start-->
      <div class="social-single">

        <div id="plusonebutton"><g:plusone size="medium"></g:plusone></div>
        <script type="text/javascript">
          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>

        <div id="twitterbutton">
          <a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>

        <div id="likebutton">
          <iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo rawurlencode(get_permalink()); ?>&layout=button_count&show_faces=false&width=150&action=like&font=tahoma
&colorscheme=light&height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>
        </div>

      </div>
      <!--social share ends-->
        <?php } ?>
</div>
<?php } else the_content(); ?>
</div>

<?php /* comments_template('/templates/comments.php'); todo: Add as option */?>