<?php
/** A simple text block **/
class AQ_Page_7_Block extends AQ_Page_Block {

    //set and create block
    function __construct() {
        $block_options = array(
            'name' => 'Content Medium',
            'size' => 'span6',
        );

        //create the block
        parent::__construct('aq_page_7_block', $block_options );
    }
}