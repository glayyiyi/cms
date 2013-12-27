<?php
if(!is_front_page()){ ?>
    <div class="row">
      <div class="fifteen columns" id="page-title">
        <a class="back" href="javascript:history.back()"></a>
        <div class="subtitle">
            <?php
            if (is_archive()) {
                echo category_description($category[0]->cat_ID);    }
            elseif (is_single()) {
                $category = get_the_category();
                echo $category[0]->category_description;
            } elseif (is_page()) {

               echo (get_post_meta( $post->ID, "page_subtitle", true ))?(get_post_meta( $post->ID, "page_subtitle", true )):'';
            } elseif (is_singular('portfolio') || is_singular('gallery')) {

            }elseif (is_404()) {
                _e('File Not Found', 'roots');
            }
            ?>
        </div>

        <?php

        ?>
        <h1 class="page-title">
            <?php
            if (is_home()) {
                if (get_option('page_for_posts', true)) {
                    echo get_the_title(get_option('page_for_posts', true));
                } else {
                    _e('Latest Posts', 'roots');
                }

            } elseif (is_archive()) {
                $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                if ($term) {
                    echo $term->name;
                } elseif (is_post_type_archive()) {
                    echo get_queried_object()->labels->name;
                } elseif (is_day()) {
                    printf(__('Daily Archives: %s', 'roots'), get_the_date());
                } elseif (is_month()) {
                    printf(__('Monthly Archives: %s', 'roots'), get_the_date('F Y'));
                } elseif (is_year()) {
                    printf(__('Yearly Archives: %s', 'roots'), get_the_date('Y'));
                } elseif (is_author()) {
                    global $post;
                    $author_id = $post->post_author;

                    $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
                    $google_profile = get_the_author_meta('google_profile', $curauth->ID);
                    if ($google_profile) {
                        printf(__('Author Archives:', 'roots'));
                        echo '<a href="' . esc_url($google_profile) . '" rel="me">' . $curauth->display_name . '</a>'; ?></a>
                        <?php } else {
                        printf(__('Author Archives: %s', 'roots'), get_the_author_meta('display_name', $author_id));
                    }

                } else {
                    single_cat_title();
                }
            } elseif (is_search()) {
                printf(__('Search Results for %s', 'roots'), get_search_query());
            } elseif (is_404()) {
                _e('File Not Found', 'roots');
            } else {
                    the_title();
            }
            ?>
        </h1>

        <div class="breadcrumbs"><?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?></div>


      </div>

      <div class="fifteen columns"><div class="line"> </div></div>

    </div>
<?php
} else {


 } ?>