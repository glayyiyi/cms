<?php
/** A simple text block **/
class AQ_Page_15_Block extends AQ_Page_Block {

    //set and create block
    function __construct() {
        $block_options = array(
            'name' => 'Content Full',
            'size' => 'span12',
        );

        //create the block
        parent::__construct('aq_page_15_block', $block_options);
    }

    function form($instance) {

        $defaults = array(
            'text' => '',
        );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance);

        ?>
        <p class="description">
            <label for="<?php echo $this->get_field_id('full_width') ?>">
                Full width block:
                <?php echo aq_field_checkbox('full_width', $block_id, $full_width); ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('text') ?>">
                Paste your shortcode here or select page below:
                <?php echo aq_field_input('shortcode', $block_id, $shortcode, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('text') ?>">
                Select page:
                <?php
                $args = array(
                    'numberposts'     => '-1',
                    'post_type'       => 'page',
                );
                $pages = get_posts( $args );
                $pages_options = array();
                foreach ($pages as $page) {
                    if($page->post_parent == 0) {
                        $pages_options[$page->ID] = $page->post_title;
                        $pages_children = get_page_children($page->ID, $pages);
                        foreach($pages_children as $page_children){
                            $pages_options[$page_children->ID] = ' - '.$page_children->post_title;
                        }
                    }
                }
                ?>
                <?php echo aq_field_select( 'page_select', $block_id, $pages_options, $page_select ); ?>
            </label>
        </p>
        <hr />
        Or
        <p class="description">
            <label for="<?php echo $this->get_field_id('show_blog') ?>">
                Display blog instead page or shortcode <?php echo $show_blog; ?>:
                <?php echo aq_field_hide_element_checkbox('show_blog', $block_id, $show_blog, array($this->get_field_id('category_select') ) ); ?>
            </label>
        </p>
        <p class="description">
            <label <?php if ( !$show_blog ) echo 'style = "display: none;"';?> for="<?php echo $this->get_field_id('category_select') ?>">
                Select category to display :
                <?php
                $categories = $this->get_categories();
                echo aq_field_multiselect( 'category_select', $block_id, $categories, $category_select );
                ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('post_number') ?>">
                Type number of posts to display:
                <?php echo aq_field_input('post_number', $block_id, $post_number, $size = 'full') ?>
            </label>
        </p>
    <?php
    }
    function before_block($instance) {
        extract($instance);
        $size_converting = array(
            'span12' => 'fifteen',
            'span8'  => 'eleven',
            'span6'  => 'seven',
            'span3'  => 'four'
        );
        $column_class = $first ? 'aq-first' : '';
        if( !$full_width ){
            echo '<div id="aq-block-'.$number.'" class="aq-block aq-block-'.$id_base.' '.$size_converting[$size].' '.$column_class.' cf">';
        } else {
            echo '</div>';
            echo '<div style = "width:100%;" id="aq-block-'.$number.'" class="aq-block aq-block-'.$id_base.'  '.$column_class.' cf">';
        }
    }

    /* block footer */
    function after_block($instance) {
        extract($instance);

        if( !$full_width ){
            echo '</div>';
        } else {
            echo '</div>';
            echo '<div class="row" >';
        }
    }
}