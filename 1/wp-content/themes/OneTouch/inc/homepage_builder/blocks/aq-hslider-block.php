<?php
/** A simple text block **/
class AQ_HSlider_Block extends AQ_Block {

    //set and create block
    function __construct($type, $block_options) {
        parent::__construct($type, $block_options);
    }

    function form($instance) {

        $defaults = array(
            'text' => '',
        );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance);

        ?>

    <?php
    }

    function block($instance) {
        extract($instance);   ?>
    <?php
        require_once locate_template('templates/block-post_slider.php');
        ?>
    <?php
    }

}