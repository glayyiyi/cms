<?php

class crum_unwrapped extends WP_Widget 
{
    public function __construct()
    {
        $widget_ops  = array('classname' => 'Theme:Unwrapped Text', 'description' => __( 'Displays arbritrary text of HTML just like the standard Text widget, but this one does not include the header bar and wrapper style - just a blank canvas for content','theory'));
        $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'crum_unwrapped');
        $this->WP_Widget('crum_unwrapped', 'Theme:Unwrapped Text', $widget_ops, $control_ops);
    }

    public function widget($args, $instance)
    {

        extract($args);

        echo $before_widget;
            echo '<div class="unwrapped">' . $instance['text'] . '</div>';
        echo $after_widget;
    }

    public function update($new_instance, $old_instance)
    {
        $instance         = $old_instance;
        $instance['text'] = trim($new_instance['text']);
        return $instance;
    }

    public function form($instance)
    {
        $defaults = array('text' => '');
        $instance = wp_parse_args($instance, $defaults);
?>
        <p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e( 'Text/HTML','theory'); ?></label><br />
            <textarea rows="20" cols="75" id="<?= $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>">
                <?php echo $instance['text']; ?></textarea>
		</p>
        <?php
    }
}