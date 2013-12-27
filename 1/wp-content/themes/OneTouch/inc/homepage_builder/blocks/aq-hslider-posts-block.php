<?php
/** A simple text block **/
class AQ_HSlider_Posts_Block extends AQ_Block {

    protected $taxonomy;
    private $post_type;
    //set and create block
    function __construct(
        $block_options = array(
            'name' => 'Slider Posts',
            'size' => 'span12',
            'post_type' => 'post',
            'taxonomy'  => 'cat',
            'block_type' => 'aq_hslider_posts_block',
        )
    ) {
        $this->taxonomy = $block_options['taxonomy'];
        $this->post_type = $block_options['post_type'];
        parent::__construct( $block_options['block_type'], $block_options );
    }

    function form($instance) {

        $defaults = array(
            'text' => '',
        );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance); ?>

        <p class="description">
            <label for="<?php echo $this->get_field_id('title') ?>">
                Title (optional)
                <?php echo aq_field_input('title', $block_id, $title, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('subtitle') ?>">
                Subtitle (optional)
                <?php echo aq_field_textarea('subtitle', $block_id, $subtitle, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('category_select') ?>">
                Select category to display:
                <?php
                $categories = $this->get_categories();
                echo aq_field_multiselect( 'category_select', $block_id, $categories, $category_select );
                ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('margin') ?>">
                Margin: (optional)
                <?php echo aq_field_input('margin', $block_id, $margin, $size = 'full') ?>
            </label>
        </p>


    <?php
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function get_categories () {
        $post_categories = get_categories( array( 'number'=>100, 'type' => 'post') );
        $categories['all'] = 'All' ;
        foreach($post_categories as $post_category){
            $categories[$post_category->term_id] = $post_category->cat_name;
        }
        return $categories;
    }

    function block($instance) {
        extract($instance);   ?>
    <?php

    global $NHP_Options;
    $display_title = ($title != '') || ($subtitle != '');
    if($display_title){ ?>

        <div class="slider-title">
            <span class="icon recent"></span>
            <h6 class="subtitle"><?php echo $subtitle; ?></h6>
            <h2 class="block-title"><?php echo $title; ?></h2>
        </div>

    <?php
    }


    if( $NHP_Options->get('boxed_post_slider') == 4 ){
        $this->non_scrolled_slider($category_select);
    }
    else {
        $this->scrolled_slider($category_select);
    }

    }

    function scrolled_slider ($category_select) {
        global $NHP_Options;
     
	 if ($NHP_Options->get('main_site_color')!=''){
            $drag_color = $NHP_Options->get('main_site_color');
        } else {
            $drag_color = '#52b6e5';
        }

        ?>
        <div class="wrap"  style="margin:<?php echo $margin.';';?> ">
            <div class="scroll-box" id="<?php if ($NHP_Options->get('disable_slider_scroll')) {echo 'no-scrolling-sider';} ?>" data-color="<?php echo $drag_color; ?>" data-boxed="<?php $NHP_Options->show('boxed_post_slider'); ?>">
                <div class = "dragger">
                    <div class="grid">
                        <?php

                        if ($this->post_type == "portfolio"){
                            $crum_post_per_page = $NHP_Options->get('slider_posts_number');
                        } else {
                            $crum_post_per_page = $NHP_Options->get('slider_posts_number');
                        }


                        $args = array(
                            'post_type' => $this->post_type,
                            'posts_per_page' => $crum_post_per_page
                        );
                        if( !in_array('all', (array)$category_select) ){
                            $args[$this->taxonomy] = implode( ",", (array)$category_select );
                        }

                        $the_query = new WP_Query($args);
                        $count = 1;
                        $box_counter = 1;
                        // The Loop
                        while ($the_query->have_posts()) : $the_query->the_post();


                            global $data;
                            $id = get_the_ID();
                            $post_desc = get_post_meta($id, 'post_description_display', true);
                            $post_desc = ( $post_desc ) ? $post_desc : 'show' ;

                            $not_disp = get_post_meta($id, 'display_post_in_slider', true);

                            if(!$not_disp){
                                $list = '';
                                $terms = get_the_terms( get_the_ID(), 'project_type' );

                                if (has_post_thumbnail()) {
                                    $thumb = get_post_thumbnail_id();
                                    $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                                } else {
                                    $img_url = get_template_directory_uri() . '/assets/img/no-image-large.png';

                                }

                                if ($post_desc !='hide'){
                                    $item_class_desc = 'disp';
                                }   else {
                                    $item_class_desc = 'hided';
                                }

                                $triple_wrapper = '';
                                if( $count%3 == 1 ){
                                    if($count == 1) {
                                        $box_counter = 1;
                                        $triple_wrapper = '<div class = "gr-box">';
                                    }
                                    else {
                                        $box_counter++;
                                        $triple_wrapper =  '</div><div class = "gr-box">';
                                    }
                                }

                                $folio_size = '';
                                if ( ($box_counter%2 == 1) && ($count %3 == 1) ) {
                                    $folio_size = 'large';
                                }
                                if ( ($box_counter%2 == 0) && ($count %3 == 0) ) {
                                    $folio_size = 'large';
                                }

                                if ($folio_size == 'large'){
                                    $item_class_width = 'large '.$count.' '.$box_counter;
                                    if($img_url != get_template_directory_uri() . '/assets/img/no-image-large.png')
                                        $article_image = aq_resize($img_url, 720, 240, true); //resize & crop img
                                    else {
                                        $article_image = $img_url;
                                    }
                                    $numb = '80';

                                }else {
                                    $item_class_width = 'half';
                                    if($img_url != get_template_directory_uri() . '/assets/img/no-image-large.png')
                                        $article_image = aq_resize($img_url, 356, 240, true); //resize & crop img
                                    else {
                                        $article_image = get_template_directory_uri() . '/assets/img/no-image-large-small.png';
                                    }
                                    $numb = '40';
                                }

                                if (($count %2) == 1) {
                                    $item_class_count = 'odd';
                                }else {
                                    $item_class_count = 'even';
                                }

                                echo $triple_wrapper;
                                ?>
                                <div class="item <?php echo $item_class_width . ' ' .$item_class_count .' item-'.$count;  ?>">
                                    <img src="<?php echo $article_image ?>" style="margin:0 0;" alt="<?php the_title();?>" title="<?php the_title();?>" >
                                    <div class="description <?php echo $item_class_desc ?>">
                                        <time><?php echo get_the_date(); ?> <?php echo get_the_time('', $post->ID); ?></time>
                                        <h4><?php the_title(); ?></h4>

                                        <p> <?php echo wp_trim_words( get_the_excerpt() , $num_words = $numb, $more = null ); ?> </p>

                                    </div>
                                    <a href="<?php the_permalink();?>"></a>
                                </div>

                                <?php  $count++; // Increase the count by 1 ?>
                                <?php } ?>
                            <?php endwhile; // END the Wordpress Loop ?>
                        <?php echo '</div>'; ?>
                        <?php wp_reset_query(); // Reset the Query Loop?>
                    </div>
                </div>
            </div>
            <?php
            wp_register_script('scrolling', ''.get_template_directory_uri().'/assets/js/scrolling.js', false, null, true);
            wp_enqueue_script('scrolling');
            ?>
        </div>
    <?php
    }

    function non_scrolled_slider($category_select){
        global $NHP_Options;
        ?>
        <div class="wrap no-scroll">

        <?php
         if ($this->post_type == "portfolio"){
             $crum_post_per_page = $NHP_Options->get('slider_posts_number');
         } else {
             $crum_post_per_page = $NHP_Options->get('slider_posts_number');
         }


            $args = array(
                'post_type' => $this->post_type,
                'posts_per_page' => $crum_post_per_page
            );

            if( !in_array('all', (array)$category_select) ){
                $args[$this->taxonomy] = implode( ",", (array)$category_select );
            }

            $the_query = new WP_Query($args);
            $count = 1;
            $box_counter = 1;
            // The Loop
            while ($the_query->have_posts()) : $the_query->the_post();

                global $data;
                $id = get_the_ID();

                $post_desc = get_post_meta($id, 'post_description_display', true);

                $post_desc = ( $post_desc ) ? $post_desc : 'show' ;

                if (!$not_disp) {
                    $list = '';
                    $terms = get_the_terms(get_the_ID(), 'project_type');

                    if (has_post_thumbnail()) {
                        $thumb = get_post_thumbnail_id();
                        $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                    } else {
                        $img_url = get_template_directory_uri() . '/assets/img/no-image-large-min.png';

                    }

                    if ($post_desc !='hide'){
                        $item_class_desc = 'disp';
                    }   else {
                        $item_class_desc = 'hided';
                    }

                    $folio_size = '';
                    if (( $box_counter % 6 == 1) || ($box_counter % 6 == 0) ) {
                        $folio_size = 'large';
                    }

                    if ($folio_size == 'large') {
                        $item_class_width = 'large ' . $count . ' ' . $box_counter;
                        if ($img_url != get_template_directory_uri() . '/assets/img/no-image-large-min.png')
                            $article_image = aq_resize($img_url, 585, 290, true); //resize & crop img
                        else {
                            $article_image = $img_url;
                        }
                        $numb = '80';

                    } else {
                        $item_class_width = 'half';
                        if ($img_url != get_template_directory_uri() . '/assets/img/no-image-large-min.png')
                            $article_image = aq_resize($img_url, 291, 290, true); //resize & crop img
                        else {
                            $article_image = get_template_directory_uri() . '/assets/img/no-image-large-small-min.png';
                        }
                        $numb = '40';
                    }

                    if (($count % 2) == 1) {
                        $item_class_count = 'odd';
                    } else {
                        $item_class_count = 'even';
                    }
                    ?>
                    <div class="item <?php echo $item_class_width . ' ' . $item_class_count;  ?>">
                        <img src="<?php echo $article_image ?>" style="margin:0 0;" alt="<?php the_title();?>" title="<?php the_title();?>">

                        <div class="description <?php echo $item_class_desc ?>">
                            <time><?php echo get_the_date(); ?> <?php echo get_the_time('', $post->ID); ?></time>
                            <h4><?php the_title(); ?></h4>

                            <p> <?php echo wp_trim_words( get_the_excerpt() , $num_words = $numb, $more = null ); ?> </p>

                        </div>
                        <a href="<?php the_permalink();?>"></a>
                    </div>

                    <?php $count++; // Increase the count by 1 ?>
                    <?php $box_counter++; // Increase the count by 1 ?>
                    <?php } ?>
                <?php endwhile; // END the Wordpress Loop ?>
            <?php wp_reset_query(); // Reset the Query Loop?>
        </div>

    <?php
    }

}