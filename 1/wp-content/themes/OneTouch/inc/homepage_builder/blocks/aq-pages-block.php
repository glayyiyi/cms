<?php
/** A simple text block **/
class AQ_Page_Block extends AQ_Block {

    //set and create block
    function __construct($format, $block_options) {
        //create the block
        parent::__construct($format, $block_options);
    }

    function form($instance) {

        $defaults = array(
            'text' => '',
        );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance);

        ?>
    <p class="description">
        <label for="<?php echo $this->get_field_id('text') ?>">
            Paste your shortcode here or select page below:
            <?php echo aq_field_input('shortcode', $block_id, $shortcode, $size = 'full') ?>
        </label>
    </p>
    <hr />
        Or select page:
    <p class="description">
        <label for="<?php echo $this->get_field_id('page_select') ?>">
            List of pages:
            <?php
                $pages_options = $this->get_pages();
            ?>
            <?php echo aq_field_select( 'page_select', $block_id, $pages_options, $page_select ); ?>
        </label>
    </p>

    <hr />
    Or
    <p class="description">
        <label for="<?php echo $this->get_field_id('show_blog') ?>">
            Display blog instead page or shortcode :
            <?php echo aq_field_hide_element_checkbox('show_blog', $block_id, $show_blog, array($this->get_field_id('category_select'), $this->get_field_id('post_number') ) ); ?>
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
        <label for="<?php echo $this->get_field_id('post_number') ?>"  <?php if ( !$show_blog ) echo 'style = "display: none;"';?> >
            Type number of posts to display:
            <?php echo aq_field_input('post_number', $block_id, $post_number, $size = 'full') ?>
        </label>
    </p>
    <?php
    }

    function get_categories () {
        $post_categories = get_categories( array( 'number'=>100, 'type' => 'post') );
        $categories['all'] = 'All' ;
        foreach($post_categories as $post_category){
            $categories[$post_category->term_id] = $post_category->cat_name;
        }
        return $categories;
    }

    function get_pages(){
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
        return $pages_options;
    }

    function block($instance) {
        extract($instance);
        if( $shortcode != '' ) {
            echo do_shortcode($shortcode);
        } elseif ( $show_blog ) {
            $this->show_blog($post_number, $category_select);
        } else {
            $this->show_page($instance);
        }
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function show_page($instance){
        extract($instance);        ?>
    <div class="<?php echo ($border)?'text-block':'';?>">
        <?php
        $page = get_post($page_select);
        echo do_shortcode($page->post_content);
        ?>
    </div>

    <?php
        /*}*/
    }

    function show_blog($post_number, $category_select){
        global $NHP_Options;
        if ( is_front_page() ) {
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        } else {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }
        $category_select = implode(",",(array)$category_select);
        query_posts('post_type=post&posts_per_page='.$post_number.'&paged=' . $paged.'&cat='.$category_select);
        get_template_part('templates/content', '');
        wp_reset_query();
    }

}