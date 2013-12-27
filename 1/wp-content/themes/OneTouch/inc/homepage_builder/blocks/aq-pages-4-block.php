<?php
/** A simple text block **/
class AQ_Page_4_Block extends AQ_Page_Block {

    //set and create block
    function __construct() {
        $block_options = array(
            'name' => 'Content Small',
            'size' => 'span3',
        );

        //create the block
        parent::__construct('aq_page_4_block', $block_options);
    }

}