<div class="social-share">

    <?php global $NHP_Options;
if( $NHP_Options->get("custom_share_code") !='') {
    $NHP_Options->show("custom_share_code");
} else { ?>

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
  <div id="pinitbutton">
    <a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php if(function_exists('the_post_thumbnail')) echo wp_get_attachment_url(get_post_thumbnail_id()); ?>&description=<?php echo get_the_title(); ?>" class="pin-it-button" count-layout="horizontal">Pin It</a>
    <script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
  </div>

    <?php } ?>

</div>


