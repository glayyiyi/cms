<div class="row">
    <div class="fifteen columns">
        <div id="recent" class="section-block clearing-container">
            <span class="icon recent"></span>
            <div class="subtitle"><?php echo $homepage_block['subtitle']; ?></div>
            <h2 class="block-title"><?php echo $homepage_block['title']; ?></h2>
            <div class="sort-panel">
                <ul class="filter clearfix">
                    <li class="active">
                        <a href="javascript:void(0)" class="all"><?php _e('All', 'roots'); ?></a>
                    </li>

                    <?php
                    $i = $count = 1;
// Get Portfolio Categories
                    $taxonomy = 'project_type';
                    $categories = get_terms($taxonomy);
                    global $data;
                    $term_list = '';
// List the Portfolio Categories
                    foreach ($categories as $category) {
                        $i++;
                        // rewrite the output for each category

                        $term_list .= '<li>
                            <a href="javascript:void(0)" class="'. strtolower(preg_replace('/\s+/', '-', $category->slug)) .'">' . $category->name . '</a>
                        </li>';
                        // if count is equal to i then output blank
                        if ($count != $i)
                        { $term_list .= '';}
                        else
                        {$term_list .= '';}
                    }
                    // print out each of the categories in our new format
                    echo $term_list; ?>

                </ul>
            </div>

            <div class="works-list">
                <ul class="filterable-grid">
                    <?php
                    $args = array(
                        'post_type' => 'portfolio',
                        'posts_per_page' => 9
                    );
                    $the_query = new WP_Query($args);

                    // The Loop
                    while ($the_query->have_posts()) : $the_query->the_post();

                        $terms = get_the_terms( get_the_ID(), 'project_type' );

                        if (has_post_thumbnail()) {
                            $thumb = get_post_thumbnail_id();
                            $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                        } else {
                            $img_url = get_template_directory_uri() . '/assets/img/no-image-large.png';
                        }


                        global $data;
                        $id = get_the_ID();

                        $folio_size = '';

                        if ((($count %3) == 0)) {
                            $folio_size = 'large';
                        }

                        if ($folio_size == 'large'){
                            $item_class_width = 'large';
                            $article_image = aq_resize($img_url, 390, 180, true); //resize & crop img
                        }else {
                            $item_class_width = 'half';
                            $article_image = aq_resize($img_url, 193, 180, true); //resize & crop img
                        }

                        if (($count %2) == 1) {
                            $item_class_count = 'odd';
                        }else {
                            $item_class_count = 'even';
                        }
                        ?>


                        <li class="item" data-id="id-<?php echo $count; ?>" data-type="<?php foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } ?>">

                            <div class="<?php echo $item_class_width . ' ' .$item_class_count;  ?>">
                                <div class="pic"><img src="<?php echo $article_image ?>" style="margin:0 0;" alt="<?php the_title();?>" title="<?php the_title();?>" ></div>
                                <div class="description">
                                  <div class="title">
                                    <time datetime="<?php echo get_the_time('c'); ?>" ><?php echo get_the_date(); ?></time>
                                    <h4><?php the_title(); ?></h4>
                                </div>
                                </div>
                                <a href="<?php the_permalink();?>"></a>
                            </div>
                        </li>


                        <?php  $count++; // Increase the count by 1 ?>
                        <?php endwhile; // END the Wordpress Loop ?>
                    <?php wp_reset_query(); // Reset the Query Loop?>

                </ul>
            </div>
        </div>
    </div>
</div>
