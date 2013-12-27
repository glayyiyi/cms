<?php
/** A simple text block **/
class AQ_Horizontal_Slider_Block extends AQ_Block {

    //set and create block
    function __construct() {
        $block_options = array(
            'name' => 'Horizontal Slider',
            'size' => '',
        );

        //create the block
        parent::__construct('aq_horizontal_slider_block', $block_options);
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
        <div class="row"><div class="fifteen columns"><div class="text-block">
            <?php
                $page = get_post($page_select);
                echo $page->post_content;
            ?>
        </div></div></div>
        <?php
    }

}