<aside class="four columns" id="right-sidebar">
    <?php
    global $post_id;
    if( !isset($post_id))
        $post_id = $post->ID;
    if( !add_user_sidebar( $post_id, 'sidebar_1' ) )
        dynamic_sidebar('sidebar-right');?>
  </aside>
