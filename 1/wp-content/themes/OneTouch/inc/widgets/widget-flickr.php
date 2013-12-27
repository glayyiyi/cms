<?php

class crum_flickr extends WP_Widget
{
    public function __construct()
    {
        $widget_ops  = array('classname' => 'Theme: Flickr Feed', 'description' => __( 'Displays your Flickr feed','theory'));
        $control_ops = array('width' => 200, 'height' => 350, 'id_base' => 'crum_flickr');
        $this->WP_Widget('crum_flickr', 'Theme: Flickr Feed', $widget_ops, $control_ops );
    }

    public function widget($args, $instance)
    {
        extract($args);

        $title = $instance['title'] ;
        $id    = $instance['id'];
        $num   = $instance['num'];

        wp_register_script('flikr_feed', get_template_directory_uri() . '/assets/js/jflickrfeed.min.js', false, null, true);
        wp_enqueue_script('flikr_feed');

        echo $before_widget;
        echo '<div id="flickr" class="flikr clearfix">';

        if ($title)
            echo $before_title . $title . $after_title;

		if ($num && !empty($id)) 
		{
        ?>
    		</div>
            <script type="text/javascript">
            <!--
            jQuery(document).ready(function() {
                jQuery('#flickr').jflickrfeed({
                    limit: <?=$num;?>,
                    qstrings: {
                        id: '<?=$id;?>'
                    },
                    itemTemplate:
                    '<a rel="colorbox" class="th zoom" href="{{image}}" title="{{title}}">' +
                    '<img src="{{image_q}}"/></a>'
                }, function(data) {
                    jQuery('#flickr a').colorbox();
                });
            });
            // -->
            </script>
		<?php 
		}
		else
		{
		   _e('Please enter correct Flickr settings in widget panel', 'theory');
		}
		echo $after_widget;
    }

    public function update($new_instance, $old_instance) 
    {
        $instance = $old_instance;
        
        $instance['title'] = trim(strip_tags($new_instance['title']));
        $instance['num']   = trim(strip_tags($new_instance['num']));
        $instance['id']    = trim(strip_tags($new_instance['id']));
        
        return $instance;
    }
    
    public function form($instance) 
    {
        $defaults = array('title' => 'Flickr Photos', 'id'=>'31472375@N06', 'num' => '6');
		$instance = wp_parse_args($instance, $defaults ); 
    ?>
        <p>
    		<label for="<?= $this->get_field_id('title'); ?>"><?php _e('Title:', 'theory'); ?></label>
    		<input id="<?= $this->get_field_id('title'); ?>" name="<?= $this->get_field_name('title'); ?>" value="<?= $instance['title']; ?>" style="width:160px" />
    	</p>

    	<p>
			<label for="<?= $this->get_field_id('id'); ?>"><?php _e('ID:','theory'); ?></label>
			<input id="<?= $this->get_field_id('id'); ?>" name="<?= $this->get_field_name('id'); ?>" value="<?= $instance['id']; ?>" style="width:160px" />
		</p>
    
		<p>
			<label for="<?= $this->get_field_id('num'); ?>"><?php _e('Number of photos:','theory'); ?></label>
			<input id="<?= $this->get_field_id('num'); ?>" name="<?= $this->get_field_name('num'); ?>" value="<?= $instance['num']; ?>" style="width:40px" />
		</p>
        <?php
    }
}