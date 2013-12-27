<?php
/*-----------------------------------------------------------------------------------*/
/* Tabbed Widget
/*-----------------------------------------------------------------------------------*/

class Crum_Widget_Tabs extends WP_Widget {
    var $settings = array( 'number', 'pop', 'latest', 'days' );

    function Crum_Widget_Tabs () {

        /* Widget settings. */
        $widget_ops = array( 'classname' => 'recent-tabs-widget', 'description' => __( 'Tabbed widget containing Popular posts, and Recent Posts', 'roots' ) );

        /* Widget control settings. */
        $control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'aq_tabs' );

        /* Create the widget. */
        $this->WP_Widget( 'aq_tabs', __('Theme: Tabbed Widget', 'roots' ), $widget_ops, $control_ops );

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

    <dl class="tabs">
      <?php if ( $header_format == 'popular-recent'): ?>

          <dd class="active"><a href="#popular">
            <img src="<?php echo get_template_directory_uri();?>/assets/img/tab-people.png" alt="">
            <span>Popular</span>
          </a></dd>
          <dd><a href="#recent">
            <img src="<?php echo get_template_directory_uri();?>/assets/img/tab-card.png" alt="">
            <span>Recent</span>
          </a></dd>

       <?php else : ?>

            <dd class="active"><a href="#recent">
                <img src="<?php echo get_template_directory_uri();?>/assets/img/tab-card.png" alt="">
                <span>Recent</span>
            </a></dd>
            <dd ><a href="#popular">
                <img src="<?php echo get_template_directory_uri();?>/assets/img/tab-people.png" alt="">
                <span>Popular</span>
            </a></dd>

       <?php endif; ?>
    </dl>
        <ul class="tabs-content">
          <li id="popularTab" <?php echo ( ( $header_format == 'popular-recent') ) ? 'class="active"' : '';   ?>>
              <?php if (function_exists('aq_widget_tabs_popular')) aq_widget_tabs_popular($thumb_sel,$number, $days); ?>
          </li>
          <li id="recentTab" <?php echo ( ( $header_format != 'popular-recent') ) ? 'class="active"' : '';   ?>>
              <?php if (function_exists('aq_widget_tabs_latest')) aq_widget_tabs_latest($thumb_sel,$number); ?>
          </li>
        </ul>

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
        $instance['header_format'] = $new_instance['header_format'] ;
        return $new_instance;
    } // End update()

    function aq_enforce_defaults( $instance ) {
        $defaults = $this->aq_get_settings();
        $instance = wp_parse_args( $instance, $defaults );
        $instance['number'] = intval( $instance['number'] );
        if ( $instance['number'] < 1 )
            $instance['number'] = $defaults['number'];
        $instance['thumb_size'] = absint( $instance['thumb_size'] );

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
        $settings['thumb_size'] = 50;
        $settings['thumb_sel'] = 'date';
        $settings['header_format'] = 'popular-recent';
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
        <label for="<?php echo $this->get_field_id( 'days' ); ?>"><?php _e( 'Popular limit (days):', 'crum' ); ?>
            <input class="widefat" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="text" value="<?php echo esc_attr( $instance['days'] ); ?>" />
        </label>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'header_format' ); ?>"><?php _e( 'Select header format:' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'header_format' ); ?>" name="<?php echo $this->get_field_name( 'header_format' );?>  value="<?php echo esc_attr( $header_format ); ?>" >
        <option value = 'popular-recent' <?php if( esc_attr( $header_format ) == 'popular-recent' ) echo 'selected'; ?>>Popular-Recent</option>
        <option value = 'recent-popular' <?php if( esc_attr( $header_format ) == 'recent-popular' ) echo 'selected'; ?>>Recent-Popular</option>
        </select>

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

add_action( 'widgets_init', create_function( '', 'return register_widget("Crum_Widget_Tabs");' ), 1 );
?>
<?php
/*-----------------------------------------------------------------------------------*/
/*  Popular Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'aq_widget_tabs_popular')) {
    function aq_widget_tabs_popular( $thumb_sel ,$posts = 5, $days = null ) {
        global $post;

        if ( $days ) {
            global $popular_days;
            $popular_days = $days;


            // Register the filtering function
            add_filter('posts_where', 'filter_where');
        }

        $popular = get_posts( array( 'suppress_filters' => false, 'ignore_sticky_posts' => 1, 'orderby' => 'comment_count', 'numberposts' => $posts) );



        foreach($popular as $post) :
        setup_postdata($post); ?>

        <article class="mini">
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

            <p> <?php echo content(12) ?> ... </p>
          </div>
        </article>

        <?php endforeach;
        wp_reset_query();
    }
}

//Create a new filtering function that will add our where clause to the query
function filter_where($where = '') {
    global $popular_days;
    //posts in the last X days
    $where .= " AND post_date > '" . date('Y-m-d', strtotime('-'.$popular_days.' days')) . "'";
    return $where;
}

/*-----------------------------------------------------------------------------------*/
/*  Latest Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'aq_widget_tabs_latest')) {
    function aq_widget_tabs_latest( $thumb_sel, $posts = 5, $size = 12 ) {
        global $post;
        $latest = get_posts( 'ignore_sticky_posts=1&numberposts='. $posts .'&orderby=post_date&order=desc' );
        foreach($latest as $post) :
            setup_postdata($post); ?>

        <article class="mini">
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

            <p> <?php echo content(12) ?> ... </p>
          </div>
        </article>

        <?php endforeach;
        wp_reset_query();
    }
}

