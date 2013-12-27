<?php
/** A simple text block **/
class AQ_Mini_Gallery_Block extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Mini Gallery',
			'size' => 'span5',
		);
		
		//create the block
		parent::__construct('aq_mini_gallery_block', $block_options);
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
        $portfolio_taxonomies = get_terms( 'project_type', 'orderby=none&hide_empty');

        $portfolio_cats = array();
        $portfolio_cats['all'] = "All";
        foreach ($portfolio_taxonomies as $portfolio_taxonomy ){
            $portfolio_cats[$portfolio_taxonomy->slug] = $portfolio_taxonomy->name;
        }
        return $portfolio_cats;
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
           $category = '&project_type = '.implode(",", (array)$category_select);

        }

        query_posts('post_type=portfolio&posts_per_page=4'.$orderby.$category);

        global $NHP_Options; ?>
    <div>
        <div class="build-block-title">
            <h6><?php echo $subtitle;?></h6>
            <h2><a><?php echo $title; ?></a></h2>
        </div>
        <div class="small-gallery clearing-container">
            <?php
        $i = 0;
        while (have_posts()) : the_post();
            if (has_post_thumbnail()) {
                $thumb = get_post_thumbnail_id();
                $img_url = wp_get_attachment_url($thumb, 'full');
            }
            $img_width = ( ($i == 1) || ($i == 2) ) ? 252 : 124;
            $img_height = 124;
         ?>

        <div class="item">
            <div class="pic"><img src="<?php echo isset($img_url) ? aq_resize ($img_url, $img_width, $img_height, true ) : ''; ?>" alt="<?php the_title();?>" title="<?php the_title();?>"></div>
            <div class="description"></div>
            <a href="<?php the_permalink();?>"></a>
        </div>

            <?php
            $i++;
            endwhile; ?>

        </div>
    </div>
        <?php wp_reset_query();
	}
	
}