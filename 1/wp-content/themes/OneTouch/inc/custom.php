<?php
function wp_corenavi()
{
    global $wp_query, $wp_rewrite;
    $pages = '';
    $max = $wp_query->max_num_pages;
    if (!$current = get_query_var('paged')) $current = 1;
    $a['base'] = str_replace(999999999, '%#%', get_pagenum_link(999999999));
    $a['total'] = $max;
    $a['current'] = $current;

    $total = 0; //1 - display the text "Page N of N", 0 - not display
    $a['mid_size'] = 6; //how many links to show on the left and right of the current
    $a['end_size'] = 1; //how many links to show in the beginning and end
    $a['prev_text'] = __('Prev', 'roots'); //text of the "Previous page" link
    $a['next_text'] = __('Next', 'roots'); //text of the "Next page" link

    if ($max > 1) echo '';
    if ($total == 1 && $max > 1) $pages = '' . $current . ' of ' . $max . '' . "\r\n";
    echo $pages . paginate_links($a);
    if ($max > 1) echo '';
}

function dimox_breadcrumbs()
{

    $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter = '<span class="delim">/</span>'; // delimiter between crumbs

    $home = __('Home', 'roots'); // text for the 'Home' link
    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $before = ''; // tag before the current crumb
    $after = ''; // tag after the current crumb

    global $post;
    $homeLink = home_url();

    if (is_home() || is_front_page()) {

        if ($showOnHome == 1) echo '<a href="' . $homeLink . '">' . $home . '</a>';

    } else {

        echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

        if (is_category()) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
            echo $before . '' . single_cat_title('', false) . '' . $after;

        } elseif (is_day()) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;

        } elseif (is_month()) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;

        } elseif (is_year()) {
            echo $before . get_the_time('Y') . $after;

        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
                if ($showCurrent == 1) echo $before . get_the_title() . $after;
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                if ($showCurrent == 1) echo $before . get_the_title() . $after;
            }

        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;

        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
            echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
            if ($showCurrent == 1) echo $before . get_the_title() . $after;

        } elseif (is_page() && !$post->post_parent) {
            if ($showCurrent == 1) echo $before . get_the_title() . $after;

        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
            if ($showCurrent == 1) echo $before . get_the_title() . $after;

        } elseif (is_search()) {
            echo $before . 'Search results for "' . get_search_query() . '"' . $after;

        } elseif (is_tag()) {
            echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo $before . 'Articles posted by ' . $userdata->display_name . $after;

        } elseif (is_404()) {
            echo $before . 'Error 404' . $after;
        }

        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo ' (';
            echo __(' - Page') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo ')';
        }

    }
} // end dimox_breadcrumbs()

/*
* Gets the excerpt of a specific post ID or object
* @param - $post - object/int - the ID or object of the post to get the excerpt of
* @param - $length - int - the length of the excerpt in words
* @param - $tags - string - the allowed HTML tags. These will not be stripped out
* @param - $extra - string - text to append to the end of the excerpt
*/
function cr_excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>', $extra = '') {

    if(is_int($post)) {
        // get the post object of the passed ID
        $post = get_post($post);
    } elseif(!is_object($post)) {
        return false;
    }

    if(has_excerpt($post->ID)) {
        $the_excerpt = $post->post_excerpt;
        return apply_filters('the_content', $the_excerpt);
    } else {
        $the_excerpt = $post->post_content;
    }

    $the_excerpt = strip_shortcodes(strip_tags($the_excerpt), $tags);
    $the_excerpt = preg_split('/\b/', $the_excerpt, $length * 2+1);
    $excerpt_waste = array_pop($the_excerpt);
    $the_excerpt = implode($the_excerpt);
    $the_excerpt .= $extra;

    return apply_filters('the_content', $the_excerpt);
}