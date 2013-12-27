<?php
/** A simple text block **/
class AQ_HSlider_Portfolio_Block extends AQ_HSlider_Posts_Block {

    //set and create block
    function __construct(
            $block_options = array(
                'name' => 'Slider Portfolio',
                'size' => 'span12',
                'post_type' => 'portfolio',
                'taxonomy' =>  'project_type',
                'block_type' => 'aq_hslider_portfolio_block',
            )
        ) {

        parent::__construct( $block_options );
    }

    function get_categories () {
        $portfolio_taxonomies = get_terms( $this->taxonomy, 'orderby=none&hide_empty');

        $portfolio_cats = array();
        $portfolio_cats['all'] = "All";
        foreach ($portfolio_taxonomies as $portfolio_taxonomy ){
            $portfolio_cats[$portfolio_taxonomy->slug] = $portfolio_taxonomy->name;
        }
        return $portfolio_cats;
    }
}