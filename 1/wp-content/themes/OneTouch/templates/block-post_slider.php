<?php
global $NHP_Options;

if($NHP_Options->get('boxed_post_slider') == 1 || $NHP_Options->get('boxed_post_slider') == 2 ){
?>

<?php
}
?>
        <div class="wrap" <?php echo ($display_title)?'style="height:600px; padding-top:55px;"':''; ?>>
            <div class="scroll-box" data-boxed="<?php echo $NHP_Options->get('boxed_post_slider'); ?>">
                <div class = "dragger">
                <div class="grid">
                    <?php
                    if (($NHP_Options->get("horizontal_slider_type") == 'post') ||
                        ($NHP_Options->get("horizontal_slider_type") == 'portfolio') )
                        $horizontal_slider_type = $NHP_Options->get("horizontal_slider_type");
                    else {
                        $horizontal_slider_type = 'post';
                    }

                    $args = array(
                        'post_type' =>  $horizontal_slider_type,
                        'posts_per_page' => 18
                    );

                    if(($horizontal_slider_type == 'post') && count($NHP_Options->get("horizontal_slider_category")) > 0) {
                        $args['cat'] = implode(",",$NHP_Options->get("horizontal_slider_category"));
                    } elseif(($horizontal_slider_type == 'portfolio') && count($NHP_Options->get("horizontal_slider_portfolio_taxonomy")) > 0) {
                        $args['project_type'] = implode(",",$NHP_Options->get("horizontal_slider_portfolio_taxonomy"));
                    }

                    $the_query = new WP_Query($args);
                    $count = 1;
                    $box_counter = 1;
                    // The Loop
                    while ($the_query->have_posts()) : $the_query->the_post();

                        global $data;
                        $id = get_the_ID();
                        $post_desc = get_post_meta($id, 'post_description_display', true);

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

                            if ($post_desc == 'opened'){
                                $item_class_desc = 'hided';
                            }   else {
                                $item_class_desc = 'disp';
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
                            <div class="item <?php echo $item_class_width . ' ' .$item_class_count;  ?>">
                                <img src="<?php echo $article_image ?>" style="margin:0 0;" alt="<?php the_title();?>" title="<?php the_title();?>" >
                                <div class="description <?php echo $item_class_desc ?>">
                                    <time><?php echo get_the_date(); ?> <?php echo get_the_time('', $post->ID); ?></time>
                                    <h4><?php the_title(); ?></h4>

                                    <p> <?php echo content($numb) ?> </p>

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


            <script type="text/javascript">
                jQuery(document).ready(function() {
                    var countElements = jQuery(".scroll-box .grid .gr-box").size();
                    jQuery(".scroll-box .grid").width(countElements*728);

                    var scrollbox = jQuery(".scroll-box");
                    var indent = ( jQuery(window).width() - jQuery(".fifteen.columns>.wrap").width() ) / 2;

                    setBoxedSlider();

                    var animateTime = 1,
                            offsetStep = 5;

                    scrollWrapper = jQuery('.scroll-box');
                    scrollContent = jQuery('.scroll-box .grid');

                    //event handling for buttons "left", "right"
                    jQuery('.bttL')
                            .mousedown(function() {
                                scrollContent.data('loop', true).loopingAnimation(jQuery(this), jQuery(this).is('.bttR') );
                            })
                            .bind("mouseup mouseout", function(){
                                //scrollContent.data('loop', false).stop();
                            });

                    jQuery.fn.loopingAnimation = function(el, dir){
                        if(this.data('loop')){
                            var sign = (dir) ? '-=' : '+=';
                            this.animate({ marginLeft: sign + offsetStep + 'px' }, animateTime, function(){ jQuery(this).loopingAnimation(el,dir) });
                        }
                        return false;
                    };
                    //jQuery('.scroll-box').tinyscrollbar({ axis: 'x'});

                });

                jQuery(window).resize(function(){
                    setBoxedSlider();
                    setBoxedSlider();
                });

                function setBoxedSlider(){
                    scrollbox = jQuery(".scroll-box");

                    if(scrollbox.data("boxed") == "3"){
                        var marginLeft = jQuery('.fifteen.columns').width();
                        marginLeft = (jQuery(window).width() - marginLeft)/2 - 9;

                        scrollbox.width(jQuery(window).width() );

                        if(marginLeft > 0)
                            scrollbox.closest(".wrap").css("margin-left",(-marginLeft)+"px");
                        scrollbox.closest(".wrap").width(jQuery(window).width());
                    }
                    else if(scrollbox.data("boxed") == "1"){
                        scrollbox.closest(".wrap").css("width","100%");
                        scrollbox.css("width","100%");
                    }
                    else if(scrollbox.data("boxed") == "2") {

                        scrollbox.closest(".wrap").css("width","100%");
                        scrollbox.css("width","100%");
                        var indent = jQuery(window).width() - jQuery(".fifteen").width();

                        scrollbox.width(jQuery(".fifteen").width() + indent/2 + 9);
                    }
                    scrollbox.getNiceScroll().resize();
                }
    </script>
        </div>
<?php
global $NHP_Options;
if($NHP_Options->get('boxed_post_slider') == 1){
    ?>

<?php }?>
