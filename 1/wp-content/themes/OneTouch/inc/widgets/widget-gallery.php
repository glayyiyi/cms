<?php



class Gallery_Widget extends WP_Widget {



    public function __construct() {

        parent::__construct(

            'gallery_widget', // Base ID

            'Theme:Gallery', // Name

            array( 'description' => __( 'Random works', 'Theme:Random portfolio works' ), ) // Args

        );

    }



    public function form( $instance ) {

        if ( isset( $instance[ 'title' ] ) ) {

            $title = $instance[ 'title' ];

        } else {

            $title = __( 'Random work', 'gallery' );

        }
        if ( isset( $instance[ 'subtitle' ] ) ) {

            $subtitle = $instance[ 'subtitle' ];
        }



        if ( isset( $instance[ 'image_number' ] ) ) {

            $image_number = $instance[ 'image_number' ];

        } else {

            $image_number = 4;

        }
        if ( isset( $instance[ 'width' ] ) ) {

            $width = $instance[ 'width' ];

        } else {

            $width = 300;

        }
        if ( isset( $instance[ 'height' ] ) ) {

            $height = $instance[ 'height' ];

        } else {

            $height = 180;

        }



?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'subtitle' ); ?>"><?php _e( 'Subtitle:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" />
    </p>

    <p>

      <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width(px):' ); ?></label>

      <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" />

    </p>
    <p>

      <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height(px):' ); ?></label>

      <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" />

    </p>


        <p>

            <label for="<?php echo $this->get_field_id( 'image_number' ); ?>"><?php _e( 'Images number:' ); ?></label>

            <input class="widefat" id="<?php echo $this->get_field_id( 'image_number' ); ?>" name="<?php echo $this->get_field_name( 'image_number' ); ?>" type="text" value="<?php echo esc_attr( $image_number ); ?>" />

        </p>

        <?php

    }



    public function update( $new_instance, $old_instance ) {

        $instance = array();

        $instance['title'] = strip_tags( $new_instance['title'] );

        $instance['subtitle'] = strip_tags( $new_instance['subtitle'] );

        $instance['image_number'] = strip_tags( $new_instance['image_number'] );

        $instance['width'] = strip_tags( $new_instance['width'] );

        $instance['height'] = strip_tags( $new_instance['height'] );

        return $instance;

    }



    public function widget( $args, $instance ) {

        extract( $args );

            $title = apply_filters( 'widget_title', $instance['title'] );

            $subtitle = $instance['subtitle'];

            $width = $instance['width'];

            $height = $instance['height'];

            $image_number = $instance['image_number'];

        ?>



    <?php echo $before_widget;

        if ( $subtitle ) {
            echo '<div class="subtitle">';
            echo $subtitle;
            echo '</div>';
        }

        if ( ! empty( $title ) )

            echo $before_title . $title . $after_title; ?>



        <?php

            $args = array(

                'post_type' => 'portfolio',

                'posts_per_page' => $image_number,

                'orderby' => 'rand'

            );

            $the_query = new WP_Query($args);





            // The Loop

            while ($the_query->have_posts()) : $the_query->the_post();



                if(has_post_thumbnail()) {

                    $thumb = get_post_thumbnail_id();

                    $img_url = wp_get_attachment_url( $thumb,'full'); //get img URL



                    $article_image = aq_resize( $img_url, $width, $height, true ); //resize & crop img ?>

                <a href="<?php the_permalink();?>" title="<?php the_title();?>"><img src="<?php echo $article_image ?>" alt=""></a>

                <?php    }
                endwhile; ?>



    <?php

        echo $after_widget;

    }



}

