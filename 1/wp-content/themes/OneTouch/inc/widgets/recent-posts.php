<?php
/*-----------------------------------------------------------------------------------*/
/* recent posts
/*-----------------------------------------------------------------------------------*/

class crum_recent_posts extends WP_Widget {
    var $settings = array( 'number', 'latest', 'days' );

    function crum_recent_posts () {

        /* Widget settings. */
        $widget_ops = array( 'classname' => 'recent-posts-widget', 'description' => __( 'Recent Posts widget', 'roots' ) );

        /* Widget control settings. */
        $control_ops = array( 'id_base' => 'recent_posts' );

        /* Create the widget. */
        $this->WP_Widget( 'recent_posts', __('Theme: Recent Posts', 'roots' ), $widget_ops, $control_ops );

    } // End Constructor

    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        $title = apply_filters('widget_title', $instance['title'] );
        $instance = $this->aq_enforce_defaults( $instance );
        extract( $instance, EXTR_SKIP );
        echo $before_widget;


        if ( $subtitle ) {
            echo '<div class="subtitle">';
            echo $subtitle;
            echo '</div>';
        }

        if ( $title ) {
            echo $before_title;
            echo $title;
            echo $after_title;
        } ?>

              <?php if (function_exists('crum_posts_latest')) crum_posts_latest( $thumb_sel, $number ); ?>

    <?php  echo $after_widget; }

    /*----------------------------------------
       update()
       ----------------------------------------

     * Function to update the settings from
     * the form() function.

     * Params:
     * - Array $new_instance
     * - Array $old_instance
     ----------------------------------------*/

    function update ( $new_instance, $old_instance ) {
        $new_instance = $this->aq_enforce_defaults( $new_instance );
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['subtitle'] = strip_tags( $new_instance['subtitle'] );
        $instance['thumb_sel'] = $new_instance['thumb_sel'] ;
        return $new_instance;
    } // End update()

    function aq_enforce_defaults( $instance ) {
        $defaults = $this->aq_get_settings();
        $instance = wp_parse_args( $instance, $defaults );
        $instance['number'] = intval( $instance['number'] );
        if ( $instance['number'] < 1 )
            $instance['number'] = $defaults['number'];
        return $instance;
    }

    /**
     * Provides an array of the settings with the setting name as the key and the default value as the value
     * This cannot be called get_settings() or it will override WP_Widget::get_settings()
     */
    function aq_get_settings() {
        // Set the default to a blank string
        $settings = array_fill_keys( $this->settings, '' );
        // Now set the more specific defaults
        $settings['number'] = 5;
        $settings['thumb_sel'] = 'date';
        return $settings;
    }

    /*----------------------------------------
      form()
      ----------------------------------------

       * The form on the widget control in the
       * widget administration area.

       * Make use of the get_field_id() and
       * get_field_name() function when creating
       * your form elements. This handles the confusing stuff.

       * Params:
       * - Array $instance
     ----------------------------------------*/

    function form( $instance ) {
        $instance = $this->aq_enforce_defaults( $instance );
        extract( $instance, EXTR_SKIP );
        ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','theory'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:160px" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'subtitle' ); ?>"><?php _e( 'Subtitle:','theory'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" value="<?php echo $instance['subtitle']; ?>" style="width:160px" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts:', 'crum' ); ?>
            <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
        </label>
    </p>
    <p>

      <label for="<?php echo $this->get_field_id( 'thumb_sel' ); ?>"><?php _e( 'Display date or thumb:' ); ?></label>
      <select id="<?php echo $this->get_field_id( 'thumb_sel' ); ?>" name="<?php echo $this->get_field_name( 'thumb_sel' );?>  value="<?php echo esc_attr( $thumb_sel ); ?>" >
        <option value = 'thumb' <?php if( esc_attr( $thumb_sel ) == 'thumb' ) echo 'selected'; ?>>Thumb</option>
        <option value = 'date' <?php if( esc_attr( $thumb_sel ) == 'date' ) echo 'selected'; ?>>Date</option>
      </select>

    </p>

    <?php
    } // End form()

} // End Class

/*---------------------------------------*/
/* Register the widget on `widgets_init`.
/*---------------------------------------*/

?>
<?php
/*-----------------------------------------------------------------------------------*/
/*  Latest Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'crum_posts_latest')) {
    function crum_posts_latest(  $thumb_sel, $posts = 5, $size = 20 ) {
        global $post;
        $latest = get_posts( 'ignore_sticky_posts=1&numberposts='. $posts .'&orderby=post_date&order=desc' );
        foreach($latest as $post) :
            setup_postdata($post); ?>


        <article class="mini <?php echo esc_attr( $thumb_sel );?>">
            <?php

            if( (esc_attr( $thumb_sel ) == 'thumb') && has_post_thumbnail() ){
                $thumb = get_post_thumbnail_id();
                $img_url = wp_get_attachment_url($thumb, 'thumb'); //get img URL
                $article_image = aq_resize($img_url, 60, 60, true);
                ?>

                <img class="thumb" src="<?php echo $article_image ?>" alt="<?php the_title(); ?>"/>
         <?php
            } else { ?>

          <time datetime="<?php echo get_the_time('c'); ?>">
            <span class="day"><?php echo get_the_date('d'); ?></span>
            <span class="mounth"><?php echo get_the_date('M'); ?>.</span>
            <span class="time"><?php the_time('g:i a'); ?></span>
          </time>

          <?php } ?>

          <div class="entry-content">
            <a href='<?php the_permalink() ;?>' class="title"><?php the_title(); ?></a>

            <p> <?php echo content($size) ?> ... </p>
          </div>
        </article>

        <?php endforeach;
        wp_reset_query();
    }
}

