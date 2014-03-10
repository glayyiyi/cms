<?php
/**
 * Zanblog functions and definitions.
 *
 * @package Yeahzan
 * @subpackage Zanblog
 * @since Zanblog 2.0.7
 */

/**
 * Adds theme-options.
 */
require_once(TEMPLATEPATH . '/theme-options.php');

/**
 * Adds shortcodes.
 */
require_once(TEMPLATEPATH . '/shortcodes.php');

/**
 * Zanblog setup & register funtions.
 *
 * @since Zanblog 2.0.0
 *
 * @return void
 */
function zanblog_setup() {

    // Set thumbnail backend.
    add_theme_support('post-thumbnails');

    /**
     * Add new class to custom submenu.
     *
     * @since Zanblog 2.0.0
     *
     * @return void
     */
    class ZanblogMenu extends Walker_Nav_Menu {

      function start_lvl(&$output, $depth, $args = array()) {
        $indent = str_repeat("\t", $depth);
        
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";

        return $output;
      }
    }
}
add_action('after_setup_theme', 'zanblog_setup');

/**
 * Enqueues scripts and styles for front end.
 *
 * 引入js和css
 *
 * @since Zanblog 2.0.0
 *
 * @return void
 */
function zanblog_scripts_styles() {
  // Add Bootstrap JavaScript file.
  wp_enqueue_script( 'bootstrap-script', get_template_directory_uri() . '/ui/js/bootstrap.js', array('jquery'), '3.0.0');

  // Add icheck JavaScript file for iffixed.
  wp_enqueue_script( 'icheck-script', get_template_directory_uri() . '/ui/js/jquery.icheck.js', array('jquery'));

  // Add zanblog JavaScript file.
  wp_enqueue_script( 'zanblog-script', get_template_directory_uri() . '/ui/js/zanblog.js', array('jquery'), '2.0.7');
  
  // Add custom JavaScript file.
  wp_enqueue_script( 'custom-script', get_template_directory_uri() . '/ui/js/custom.js', array('jquery'), '2.0.7');

  // Add respond JavaScript file.
  wp_enqueue_script( 'custom-script', get_template_directory_uri() . '/ui/js/respond.min.js', array('jquery'), '2.0.7');

  // Add zanblog main css.
  wp_enqueue_style( 'zanblog-style', get_stylesheet_uri(), array(), '2.0.7');

  // Add Bootstrap css.
  wp_enqueue_style( 'bootstrap-style', get_template_directory_uri() . '/ui/css/bootstrap.css', array(), '3.0.0');

  // Add FontAwesome css.
  wp_enqueue_style( 'fontawesome-style', get_template_directory_uri() . '/ui/font-awesome/css/font-awesome.min.css', array(), '4.0.1');

  // Add iCheck css.
  wp_enqueue_style( 'icheck-style', get_template_directory_uri() . '/ui/css/flat/red.css', array());

  // Add Custom css.
  wp_enqueue_style( 'custom-style', get_template_directory_uri() . '/ui/css/custom.css', array(), '2.0.7');
}
add_action('wp_enqueue_scripts', 'zanblog_scripts_styles');

/**
 * Registers zanblog widget areas.
 *
 * 侧边栏小工具
 *
 * @since Zanblog 2.0.0
 *
 * @return void
 */
function zanblog_widgets_init() {
  register_sidebar(array(
    'name'          => '幻灯片',
    'before_widget' => '<figure class="slide">',
    'after_widget'  => '</figure>'
  ));

  register_sidebar(array(
    'name'          => '搜索框',
    'before_widget' => '<div class="search visible-lg">',
    'after_widget'  => '</div>'
  ));

  register_sidebar(array(
    'name'          => '侧边栏',
    'before_widget' => '',
    'after_widget'  => ''
  ));

}
add_action('widgets_init', 'zanblog_widgets_init');

/**
 * Add nav menu active class.
 *
 * @since Zanblog 2.0.0
 *
 * @return the li want to add custom class
 */
// function special_nav_class($classes, $item) {
//   if( in_array('current_page_item', $classes)) {
//          $classes[] = 'active ';
//   }
//   return $classes;
// }
// add_filter('nav_menu_css_class', 'special_nav_class', 10, 2);

/**
 * Get the thumbnail of article.
 *
 * 获得特色图像
 *
 * @since Zanblog 2.0.0
 *
 * @return false
 */
function zanblog_get_thumbnail_img($width, $height, $sizeTag) {   
  global $post, $posts;   
  
  $first_img = '';
     
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    
  $first_img = '<img src="' . $matches[1][0] . '" width="' . $width . '" height="' . $height . '" alt="' . $post->post_title . '"/>';  
    
  return false;
}

/**
 * Get the most comments articles.
 *
 * 最热文章
 *
 * @since Zanblog 2.0.0
 *
 * @return The most comments articles.
 */
function zanblog_get_most_comments($posts_num, $strim_width, $days) {
  global $wpdb;

  $sql = "SELECT ID , post_title , comment_count
          FROM $wpdb->posts
         WHERE post_type = 'post' AND TO_DAYS(now()) - TO_DAYS(post_date) < $days
     AND ($wpdb->posts.`post_status` = 'publish' OR $wpdb->posts.`post_status` = 'inherit')
         ORDER BY comment_count DESC LIMIT 0 , $posts_num ";

  $posts = $wpdb->get_results($sql);

  foreach ($posts as $post){
    $output .= "\n<li class=\"list-group-item\"><a href= \"" . get_permalink($post->ID) . "\" rel=\"bookmark\" title=\"" . $post->post_title . "\" >" . mb_strimwidth($post->post_title, 0, $strim_width) . "</a><span class=\"badge\">" . $post->comment_count . "</span></li>";
  }

  return $output;
} 

/**
 * Cut the string.
 *
 * 截取字符串
 *
 * @since Zanblog 2.0.0
 *
 * @return the cut string.
 */
function zanblog_cut_string($string, $sublen, $start = 0, $code = 'UTF-8') {
     if($code == 'UTF-8') {
         $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
         preg_match_all($pa, $string, $t_string);
         if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)) . "...";
         return join('', array_slice($t_string[0], $start, $sublen));
     } else {
         $start = $start * 2;
         $sublen = $sublen * 2;
         $strlen = strlen($string);
         $tmpstr = '';

         for($i = 0; $i < $strlen; $i++) {
             if($i >= $start && $i < ($start + $sublen)) {
                 if(ord(substr($string, $i, 1)) > 129) $tmpstr .= substr($string, $i, 2);
                 else $tmpstr .= substr($string, $i, 1);
             } 
             if(ord(substr($string, $i, 1)) > 129) $i++;
        }
             if(strlen($tmpstr) < $strlen ) $tmpstr .= "...";
             return $tmpstr;
    }
}

/**
 * Pagination funtion with two trigger method.
 *
 * 分页功能（异步加载或自然分页）
 *
 * @since Zanblog 2.0.0
 *
 * @return void.
 */
function zanblog_page($trigger){   

  global $posts_per_page, $paged;
  $my_query = new WP_Query($query_string ."&posts_per_page=-1");   
  $total_posts = $my_query->post_count;

  if(empty($paged)) $paged = 1;  

  $prev = $paged - 1;
  $next = $paged + 1;   
  $range = 4; // only edit this if you want to show more page-links   
  $showitems = ($range * 2)+1;   
    
  $pages = ceil($total_posts/$posts_per_page);

  // Judge tigger method (auto or manual).
  if($trigger == 'auto') {
    echo "<a id='load-more' class='btn btn-zan btn-block' load-data='努力加载中...' href='" . get_pagenum_link($next) . "'><i></i> <attr>加载更多</attr></a>";

  } elseif($trigger == "manual") {
      if(1 != $pages){   
        echo "<ul class='pagination pull-right'>";   
        echo ($paged > 2 && $paged+$range+1 > $pages && $showitems < $pages)? "<li><a href='".get_pagenum_link(1)."'>首页</a></li>":"";   
        echo ($paged > 1 && $showitems < $pages)? "<li><a href='".get_pagenum_link($prev)."'>上一页</a></li>":"";   
        
        for ($i=1; $i <= $pages; $i++){   
          if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){   
            echo ($paged == $i)? "<li class='active'><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>":"<li><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>";   
          }   
        }   
          
        echo ($paged < $pages && $showitems < $pages) ? "<li><a href='".get_pagenum_link($next)."'>下一页</a></li>" :"";   
        echo ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) ? "<li><a href='".get_pagenum_link($pages)."'>末页</a></li>":"";   
        echo "</ul>\n";   
      }

  } else {
      echo "<div class='alert alert-danger'><i class='icon-warning-sign'></i> 请输入正确的触发值（auto或者manual）</div>";
  }
} 

/**
 * Latest comments list function.
 *
 * 文章评论
 *
 * @since Zanblog 2.0.0
 *
 * @return the latest comments list.
 */
function zanblog_latest_comments_list($list_number, $avatar_size, $cut_length) {
  global $wpdb;

  $sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type,comment_author_url,comment_author_email, comment_content AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND comment_author != '佚站互联' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT $list_number" ;
  
  $comments = $wpdb->get_results($sql);
  
  foreach ($comments as $comment) {
    $output .= "\n<li class=\"list-group-item\">".get_avatar( $comment, $avatar_size )."<span class=\"comment-log\"> <a href=\"" . get_permalink($comment->ID). "#ds-thread\" title=\"on " .$comment->post_title . "\">" . zanblog_cut_string(strip_tags($comment->com_excerpt), $cut_length)."&nbsp;</a></span></li>";
  }

  $output = convert_smilies($output);

  return $output;
}


/**
 * Change tag cloud to colorful.
 *
 * 彩色云标签
 *
 * @since Zanblog 2.0.0
 *
 * @return tags.
 */
function zanblog_color_cloud($text) { 
  $text = preg_replace_callback('|<a (.+?)>|i', 'zanblog_color_cloud_callback', $text); 
  return $text; 
} 

function zanblog_color_cloud_callback($matches) { 
  $text = $matches[1]; 
  $color = dechex(rand(0,16777215)); 
  $pattern = '/style=(\'|\")(.*)(\'|\")/i'; 
  $text = preg_replace($pattern, "style=\"color:#{$color};$2;\"", $text); 

  return "<a $text>"; 
}
add_filter('wp_tag_cloud', 'zanblog_color_cloud', 1);

/**
 *   article archives
 *
 *   文章存档函数
 *
 * @since Zanblog 2.0.5
 *
 * @return archives.
 */
function zanblog_archives_list() {
    if( !$output = get_option('zanblog_archives_list') ){
        $output = '<div id="archives">';      
        $the_query = new WP_Query( 'posts_per_page=-1&ignore_sticky_posts=1' ); //update: 加上忽略置顶文章
        $year=0; $mon=0; $i=0; $j=0;
        while ( $the_query->have_posts() ) : $the_query->the_post();
        $year_tmp = get_the_time('Y');
        $mon_tmp = get_the_time('m');
        $y=$year; $m=$mon;
        if ($mon != $mon_tmp && $mon > 0) $output .= '</div></div></div>';
        if ($year != $year_tmp && $year > 0) $output .= '</div>';
        if ($year != $year_tmp) {
            $year = $year_tmp;
            $output .= '<h3 class="al_year">'. $year .' 年</h3><div class="panel-group" id="accordion">'; //输出年份
        }
        if ($mon != $mon_tmp) {
            $mon = $mon_tmp;
            $output .= '<div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$year. $mon .'">
                                        '. $mon .' 月</a></h4></div>
                            <div id="collapse'.$year. $mon .'" class="panel-collapse collapse">
                                <div class="panel-body">'; //输出月份
        }
        $output .= '<p>'. get_the_time('d日: ') .'<a class="archivesPostList" href="'. get_permalink() .'">'. get_the_title() .'</a> <span class="badge">'. get_comments_number('0', '1', '%') .'</span></p>';//输出文章日期和标题

        endwhile;
        wp_reset_postdata();
        $output .= '</div></div></div></div></div>';
        update_option('zanblog_archives_list', $output);
    }
    echo $output;
}
function clear_zal_cache() {
    update_option('zanblog_archives_list', ''); // 清空 存档
}
add_action('save_post', 'clear_zal_cache'); // 新发表文章/修改文章时

/**
 * post views
 *
 * 浏览量统计
 *
 * @since Zanblog 2.0.5
 *
 * @return views.
 */

function zanblog_set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{


      $count++;
      update_post_meta($postID, $count_key, $count);

    }
}
function zanblog_get_post_views($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}

/**
 * link manager
 *
 * 开启链接管理功能（包括友情链接）
 *
 * @since Zanblog 2.0.5
 */
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

?>