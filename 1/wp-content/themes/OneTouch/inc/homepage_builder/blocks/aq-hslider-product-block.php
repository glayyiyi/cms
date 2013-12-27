<?php
/** A simple text block **/
class AQ_HSlider_Product_Block extends AQ_HSlider_Portfolio_Block {

    //set and create block
    function __construct() {
        $block_options = array(
            'name' => 'Slider Product',
            'size' => 'span12',
            'block_type' => 'aq_hslider_product_block',
            'post_type' => 'product',
            'taxonomy' =>  'product_cat'
        );

        //create the block
        parent::__construct( $block_options );
    }
}