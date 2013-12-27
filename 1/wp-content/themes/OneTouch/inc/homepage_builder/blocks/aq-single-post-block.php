<?php
/** A simple text block **/
class AQ_Single_Post_Block extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Single Post',
			'size' => 'span5',
		);
		
		//create the block
		parent::__construct('aq_single_post_block', $block_options);
	}
	
	function form($instance) {

		$defaults = array(
			'text' => '',
		);
		$instance = wp_parse_args($instance, $defaults);
		extract( $instance );
		
		?>

        <p class="description">
            <label for="<?php echo $this->get_field_id('title') ?>">
                Title:
                <?php echo aq_field_input('title', $block_id, $title, $size = 'full') ?>
            </label>
        </p>
		<p class="description">
			<label for="<?php echo $this->get_field_id('subtitle') ?>">
				Subtitle:
				<?php echo aq_field_input('subtitle', $block_id, $subtitle, $size = 'full') ?>
			</label>
		</p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('category_select') ?>">
                Select category to display :
                <?php
                $categories = $this->get_categories();
                echo aq_field_multiselect( 'category_select', $block_id, $categories, $category_select );
                ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('category_select') ?>">
                Sort Items:
                <?php
                $post_select_vars = array('Last', 'Random');
                echo aq_field_select( 'random_post', $block_id, $post_select_vars, $random_post );
                ?>
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

    function update($new_instance, $old_instance) {
        return $new_instance;
    }


    function block($instance) {
		extract($instance);
        if( (bool)$random_post ) {
            $orderby = '&orderby=rand';
        } else{
            $orderby = '&orderby=date&order=ASC';
        }

        if( !in_array('all', (array)$category_select) ) {
           $category = '&cat='.implode(",", (array)$category_select);

        }

        query_posts('post_type=post&posts_per_page=1'.$orderby.$category);
        global $NHP_Options; ?>
        <div class="build-block-title">
            <h6><?php echo $subtitle; ?></h6>
            <h2><a href=""><?php echo $title; ?></a></h2>
        </div>
        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <time class="updated" datetime="<?php echo get_the_time('c'); ?>" pubdate>
                    <span class="day"><?php echo get_the_date('d'); ?></span>
                    <span class="mounth"><?php echo get_the_date('M'); ?>.</span>
                    <span class="time"><?php the_time('g:i a'); ?></span>
                </time>

                <header>
                    <?php get_template_part('templates/entry-meta'); ?>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                </header>

                <?php

                if ( has_post_format( 'video' ) || has_post_format( 'gallery' )) {

                } else {


                    if (has_post_thumbnail()) {
                        $thumb = get_post_thumbnail_id();
                        $img_url = wp_get_attachment_url($thumb, 'full'); //get img URL
                        if ($NHP_Options->get('post_thumbnails_width') !='' && $NHP_Options->get('post_thumbnails_height') !=''){
                            $article_image = aq_resize($img_url, $NHP_Options->get('post_thumbnails_width'), $NHP_Options->get('post_thumbnails_height'), true);
                        } else {
                            $article_image = aq_resize($img_url, 1200, 500, true);
                        }
                        ?>
                        <div class="entry-thumb">
                            <a href="<?php the_permalink(); ?>"><img src="<?php echo $article_image ?>" alt="<?php the_title(); ?>"/></a>
                        </div>
                        <?php }}?>

                <div class="entry-content">

                    <?php global $data;  echo $data['type_posts_show'];
                if ($data['type_posts_show'] == 'full_post') {
                    the_content('');
                } else {
                    if ( has_post_format( 'gallery' )) {
                        get_template_part('templates/post', 'gallery');
                    }if ( has_post_format( 'link' )) {
                        get_template_part('templates/post', 'link');
                    }if ( has_post_format( 'image' )) {
                        get_template_part('templates/post', 'image');
                    }if ( has_post_format( 'quote' )) {
                        get_template_part('templates/post', 'quote');
                    }if ( has_post_format( 'video' )) {
                        get_template_part('templates/post', 'video');
                    }if ( has_post_format( 'audio' )) {
                        get_template_part('templates/post', 'audio');
                    }else {
                        $format = get_post_format();
                        if ( false === $format ) {
                            echo '<p>';
                            the_excerpt('');
                            echo '</p>';
                        }
                    }
                } ?>
                </div>

            </article>
            <?php endwhile; ?>
        <?php wp_reset_query();
	}
	
}