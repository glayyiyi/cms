<?php
/**
 * Duplicated and tweaked WP core Categories widget class
 */
class crum_tile_categories extends WP_Widget {

    function __construct() {
        $widget_ops = array( 'classname' => 'category-widget', 'description' => __( "A list of categories, with slightly tweaked output.", 'roots'  ) );
        parent::__construct( 'categories_custom', __( 'Theme: Tiled categories list', 'roots' ), $widget_ops );
    }

    function widget( $args, $instance ) {
        extract( $args );

        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories Custom', 'roots'  ) : $instance['title'], $instance, $this->id_base);

        if ( isset( $instance[ 'subtitle' ] ) ) {

            $subtitle = $instance[ 'subtitle' ];
        }

        echo $before_widget;

        if ( $subtitle ) {
            echo '<div class="subtitle">';
            echo $subtitle;
            echo '</div>';
        }

        if ( $title )
            echo $before_title . $title . $after_title;
        ?>

    <div class="tile-category-list clearing-container">


        <?php
        $args = array(
            'hierarchical' => 0,
            'type' => 'post' 
        );
        $categories = get_categories( $args );

        $i = 1;
            foreach($categories as $category){ ?>
              <div class="tile category <?php if ((($i % 4)== 2) || (($i % 4)== 3)) { ?>even <?php } else { ?>odd <?php } echo 'item-'.$i  ?> ">
                <a href="<?php echo $category_link = get_category_link( $category->cat_ID ); ?>"></a>
                <div class="text-mini-left">
                  <?php echo $category->name; ?>
                </div>
                <span class="count"><?php echo $category->count; ?></span>
              </div>
                <?php
                $i++;
                } ?>
    </div>
    <?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['subtitle'] = strip_tags( $new_instance['subtitle'] );
        $instance['count'] = 1;

        return $instance;
    }

    function form( $instance ) {
        //Defaults
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = esc_attr( $instance['title'] );
        $subtitle = $instance['subtitle'];
        $count = true;
        ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Subtitle:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
    </p>

    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> disabled="disabled" />
    <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts', 'roots'  ); ?></label><br />
    <?php
    }

}