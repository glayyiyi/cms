<?php
/** A simple text block **/
class AQ_Page_11_Block extends AQ_Page_Block {

    //set and create block
    function __construct() {
        $block_options = array(
            'name' => 'Content Big',
            'size' => 'span8',
        );

        //create the block
        parent::__construct('aq_page_11_block', $block_options);
    }
}