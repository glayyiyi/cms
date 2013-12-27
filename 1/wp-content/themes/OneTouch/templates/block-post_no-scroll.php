<?php
global $NHP_Options;

?>
<div class="row"><div class="fifteen columns">
<div class="wrap no-scroll">

    <?php
    if (($NHP_Options->get("horizontal_slider_type") == 'post') ||
        ($NHP_Options->get("horizontal_slider_type") == 'portfolio')
    )
        $horizontal_slider_type = $NHP_Options->get("horizontal_slider_type");
    else {
        $horizontal_slider_type = 'post';
    }

    $args = array(
        'post_type' => $horizontal_slider_type,
        'posts_per_page' => 18
    );

    if (($horizontal_slider_type == 'post') && count($NHP_Options->get("horizontal_slider_category")) > 0) {
        $args['cat'] = implode(",", $NHP_Options->get("horizontal_slider_category"));

    } elseif (($horizontal_slider_type == 'portfolio') && count($NHP_Options->get("horizontal_slider_portfolio_taxonomy")) > 0) {
        $args['project_type'] = implode(",", $NHP_Options->get("horizontal_slider_portfolio_taxonomy"));

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

        if (!$not_disp) {
            $list = '';
            $terms = get_the_terms(get_the_ID(), 'project_type');

            if (has_post_thumbnail()) {
                $thumb = get_post_thumbnail_id();
                $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
            } else {
                $img_url = get_template_directory_uri() . '/assets/img/no-image-large-min.png';

            }

            if ($post_desc) {
                $item_class_desc = 'disp';
            } else {
                $item_class_desc = 'hided';
            }

            $folio_size = '';
            if (( $box_counter % 6 == 1) || ($box_counter % 6 == 0) ) {
                $folio_size = 'large';
            }  /*
            if (($box_counter % 2 == 0) && ($count % 3 == 0)) {
                $folio_size = 'large';
            }    */

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

              <p> <?php echo content($numb) ?> </p>

            </div>
            <a href="<?php the_permalink();?>"></a>
          </div>

            <?php $count++; // Increase the count by 1 ?>
            <?php $box_counter++; // Increase the count by 1 ?>
            <?php } ?>
        <?php endwhile; // END the Wordpress Loop ?>
    <?php wp_reset_query(); // Reset the Query Loop?>
</div></div></div>


