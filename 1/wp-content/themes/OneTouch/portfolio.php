<?php
/*
Template Name: Portfolio page TABS
*/
?>

<div class="row">

        <?php while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
        <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
        <?php endwhile; ?>

</div>

<?php global $NHP_Options; ?>
<?php get_template_part('templates/page', 'header'); ?>
<?php
    $boxed_portfolio = $NHP_Options->get('boxed_portfolio');
?>
<?php
if ($boxed_portfolio != 'full-width'){
    $content_class = 'row';
    $portfolio_page_class = "fifteen columns";
} else {
    $content_class = '';
    $portfolio_page_class = "";
}?>

<div id="content" class="<?php echo $content_class; ?>" data-boxed="<?php echo $boxed_portfolio; ?>">
<div id="portfolio-page" class="<?php echo $portfolio_page_class; ?>" data-boxed="<?php echo $boxed_portfolio; ?>">


<dl class="tabs filter">

    <?php

    // Get Portfolio Categories
    $number_per_page = ( get_post_meta($post->ID, 'portfolio_number_to_display',true)  ) ?get_post_meta($post->ID, 'portfolio_number_to_display',true)  : '16';
    $portfolio_categories = ( get_post_meta($post->ID, 'portfolio_category',true) != 'all') ?  get_post_meta($post->ID, 'portfolio_category',true) : '';

    $taxonomy = 'project_type';
    $categories = get_terms($taxonomy);
    if($portfolio_categories != ''){
        $portfolio_categories = explode(',',$portfolio_categories);
        foreach ($categories as $key=>$category){
            if( !in_array($category->slug,$portfolio_categories) ){
                unset($categories[$key]);
            }
        }
    }

    foreach ($categories as $category) {
        echo '<dd><a href="#port-tab-' . str_replace('-', '', $category->slug) . '">' . $category->name . '</a></dd>';
    }
    ?>
</dl>


<ul class="tabs-content">
    <?php

    // List the Portfolio Categories
    foreach ($categories as $category) {
        echo '<li id="port-tab-' . str_replace('-', '', $category->slug) . 'Tab" >'; ?>

             <div class="wrap">
                    <div class="scroll-box">
                      <div class="grid">

        <?php

        $args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'project_type',
                    'field' => 'slug',
                    'terms' => $category->slug
                )
            ),
            'post_type' => 'portfolio',
            'posts_per_page' => $number_per_page
        );
        $the_query = new WP_Query($args);
        $count = 1;
        $box_counter = 1;
        // The Loop
        while ($the_query->have_posts()) : $the_query->the_post();
            $list = '';
            $terms = get_the_terms(get_the_ID(), 'project_type');

            if (has_post_thumbnail()) {
                $thumb = get_post_thumbnail_id();
                $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
            } else {
                $img_url = get_template_directory_uri() . '/img/no-image-large.jpg';
            }

            global $data;
            $id = get_the_ID();
            $post_desc = get_post_meta($id, 'post_description_display', true);
            if ($post_desc) {
                $item_class_desc = 'disp';
            } else {
                $item_class_desc = 'hided';
            }

            $triple_wrapper = '';
            if ($count % 3 == 1) {
                if ($count == 1) {
                    $box_counter = 1;
                    $triple_wrapper = '<div class = "gr-box">';
                } else {
                    $box_counter++;
                    $triple_wrapper = '</div><div class = "gr-box">';
                }
            }

            $folio_size = '';
            if (($box_counter % 2 == 1) && ($count % 3 == 1)) {
                $folio_size = 'large';
            }
            if (($box_counter % 2 == 0) && ($count % 3 == 0)) {
                $folio_size = 'large';
            }

            if ($folio_size == 'large') {
                $item_class_width = 'large ' . $count . ' ' . $box_counter;
                $article_image = aq_resize($img_url, 720, 240, true); //resize & crop img
                $numb = '80';
            } else {
                $item_class_width = 'half';
                $article_image = aq_resize($img_url, 356, 240, true); //resize & crop img
                $numb = '40';
            }

            if (($count % 2) == 1) {
                $item_class_count = 'odd';
            } else {
                $item_class_count = 'even';
            }

            echo $triple_wrapper;
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

            <?php endwhile; // END the Wordpress Loop ?>
        <?php echo '</div></div></div></li>'; ?>
        <?php wp_reset_query(); // Reset the Query Loop
    }?>
</ul>


</div>
</div>
<?php
    wp_register_script('portfolio-script', ''.get_template_directory_uri().'/assets/js/portfolio.js', false, null, true);
    wp_enqueue_script('portfolio-script');
    wp_register_script('scrolling', ''.get_template_directory_uri().'/assets/js/scrolling.js', false, null, true);
    wp_enqueue_script('scrolling');
?>


