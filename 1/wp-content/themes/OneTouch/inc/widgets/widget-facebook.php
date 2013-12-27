<?php
class Facebook_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct(

            'facebook_widget', // Base ID

            'Theme: Facebook widget', // Name

            array( 'description' => __( 'Cool Facebook Widget', 'Theme:Facebook widget' ), ) // Args

        );

    }




    public function form( $instance ) {

        if ( isset( $instance[ 'title' ] ) ) {

            $title = $instance[ 'title' ];

        } else {

            $title = __( 'Facebook widget', 'roots' );

        }
        if ( isset( $instance[ 'subtitle' ] ) ) {

            $subtitle = $instance[ 'subtitle' ];
        }



        if ( isset( $instance[ 'width' ] ) ) {

            $width = $instance[ 'width' ];

        } else {

            $width = 300;

        }



        if( isset($instance[ 'color' ] ) ){

            $color = $instance[ 'color' ];

        } else {

            $color = 'dark';

        }



        if( isset($instance[ 'stream' ] ) ){

            $stream = $instance[ 'stream' ];

        } else {

            $stream = 'false';

        }



        if( isset($instance[ 'faces' ] ) ){

            $faces = $instance[ 'faces' ];

        } else {

            $faces = 'true';

        }



        if( isset($instance[ 'url' ] ) ){
            $url = $instance[ 'url' ];
        } else {
            $url = 'platform';
        }



        if( isset($instance[ 'header' ] ) ){
            $header = $instance[ 'header' ];
        } else {
            $header = 'false';
        }

        if( isset($instance[ 'header' ] ) ){
            $css = $instance[ 'css' ];
        }


        if( isset($instance[ 'border' ] ) ){
            $border = $instance[ 'border' ];
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

            <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Facebook Name: ( ex: envato )' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />

        </p>

        <p>

            <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width(px):' ); ?></label>

            <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" />

        </p>
    <!--

    <p>

        <label for="<?php echo $this->get_field_id( 'border' ); ?>"><?php _e( 'Border color:' ); ?></label>

        <input class="widefat" id="<?php echo $this->get_field_id( 'border' ); ?>" name="<?php echo $this->get_field_name( 'border' ); ?>" type="text" value="<?php echo esc_attr( $border ); ?>" />

    </p>

        <p>

            <label for="<?php echo $this->get_field_id( 'color' ); ?>"><?php _e( 'Color scheme:' ); ?></label>

            <select id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' );?>  value="<?php echo esc_attr( $color ); ?>" >

                <option value ='light' <?php if( esc_attr( $color ) == 'light' ) echo 'selected'; ?>>Light</option>

                <option value = 'dark' <?php if( esc_attr( $color ) == 'dark' ) echo 'selected'; ?>>Dark</option>

            </select>



        </p>-->

        <p>

        <label for="<?php echo $this->get_field_id( 'stream' ); ?>"><?php _e( 'Show stream:' ); ?></label>

        <select id="<?php echo $this->get_field_id( 'stream' ); ?>" name="<?php echo $this->get_field_name( 'stream' );?>  value="<?php echo esc_attr( $stream ); ?>" >

            <option value ='true' <?php if( esc_attr( $stream ) == 'true' ) echo 'selected'; ?>>Yes</option>

            <option value = 'false' <?php if( esc_attr( $stream ) == 'false' ) echo 'selected'; ?>>No</option>

        </select>

        </p>

        <p>

            <label for="<?php echo $this->get_field_id( 'faces' ); ?>"><?php _e( 'Show faces:' ); ?></label>

            <select id="<?php echo $this->get_field_id( 'faces' ); ?>" name="<?php echo $this->get_field_name( 'faces' );?>  value="<?php echo esc_attr( $faces ); ?>" >

                <option value ='true' <?php if( esc_attr( $faces ) == 'true' ) echo 'selected'; ?>>Yes</option>

                <option value = 'false' <?php if( esc_attr( $faces ) == 'false' ) echo 'selected'; ?>>No</option>

            </select>

        </p>

        <p>

            <label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Show header:' ); ?></label>

            <select id="<?php echo $this->get_field_id( 'header' ); ?>" name="<?php echo $this->get_field_name( 'header' );?>  value="<?php echo esc_attr( $header ); ?>" >

            <option value ='true' <?php if( esc_attr( $header ) == 'true' ) echo 'selected'; ?>>Yes</option>

            <option value = 'false' <?php if( esc_attr( $header ) == 'false' ) echo 'selected'; ?>>No</option>

         </select>

        </p>



        <?php

    }



    public function update( $new_instance, $old_instance ) {

        $instance = array();

        $instance['title'] = strip_tags( $new_instance['title'] );

        $instance['subtitle'] = strip_tags( $new_instance['subtitle'] );

        $instance['color'] = strip_tags( $new_instance['color'] );

        $instance['stream'] = strip_tags( $new_instance['stream'] );

        $instance['width'] = strip_tags( $new_instance['width'] );

        $instance['faces'] = strip_tags( $new_instance['faces'] );

        $instance['url'] = strip_tags( $new_instance['url'] );

        $instance['header'] = strip_tags( $new_instance['header'] );

        $instance['border'] = strip_tags( $new_instance['border'] );

        $instance['css'] = strip_tags( $new_instance['css'] );


        return $instance;

    }



    public function widget( $args, $instance ) {

        extract( $args );

            $title = apply_filters( 'widget_title', $instance['title'] );

            $subtitle = $instance['subtitle'];

            $width = $instance['width'];

            $color = $instance['color'];

            $stream = $instance['stream'];

            $faces = $instance['faces'];

            $url = $instance['url'];

            $header = $instance['header'];

            $border = $instance['border'];

            $css = $instance['css'];



        ?>


        <?php echo $before_widget;

        if ( $subtitle ) {
            echo '<div class="subtitle">';
            echo $subtitle;
            echo '</div>';
        }

                if ( ! empty( $title ) )

            echo $before_title . $title . $after_title; ?>


    <style type="text/css">

    <?php  if ($css !=''){
         echo $css;
    }?>

        .facebookOuter {
            background-color: transparent;
            padding-bottom: 5px;
            border: 1px solid #687f8e;
        }
        .facebookInner {
            height:275px;
            border:none;
            background: transparent;
        }
    </style>

    <div class="facebookOuter">
        <div class="facebookInner">
            <div class="fb-like-box"
                 data-width="<?php echo $width; ?>" data-height="255"
                 data-href="http://www.facebook.com/<?php echo $url; ?>"
                 data-colorscheme="dark"
                 data-border-color="#4e616d" data-show-faces="<?php echo $faces; ?>"
                 data-stream="<?php echo $stream; ?>" data-header="<?php echo $header; ?>">
            </div>
        </div>
    </div>

    <div id="fb-root"></div>

    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>

    <?php
      echo $after_widget;

    }

}

