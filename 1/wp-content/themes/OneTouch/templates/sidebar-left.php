<aside class="four columns" id="left-sidebar">
    <?php
    global $post_id;
    if( !isset($post_id))
        $post_id = $post->ID;
    if( !add_user_sidebar( $post_id, 'sidebar_2' ) )
        dynamic_sidebar('sidebar-left'); ?>
</aside>