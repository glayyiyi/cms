<?php

class crum_instgram_widget extends WP_Widget {

    public function __construct()
    {
        parent::WP_Widget('crum_instgram_widget', 'Crumina Instagram Widget', array('description' => 'A widget to display a users instagram feed'));
    }

    public function widget($args, $instance)
    {
        extract($args);

        $title          = $instance['title'];
        $user_id        = $instance['user_id'];
        $access_token   = $instance['access_token'];
        $picture_number = (int)$instance['picture_number'];
        $picture_size   = 'thumbnail';

        echo $before_widget;

        if ($title)
        {
            echo $before_title . $title . $after_title;
            echo '<div class="flikr clearfix" >';
        }

        $results = $this->get_instagram_data($user_id, $access_token, $picture_number);

        if(!$this->_isCurl())
        {
            _e("You need CURL support. Please ask you hosting provider to enable this module", 'crum');
        }
        elseif(empty($picture_number) || $picture_number < 0 || !is_int($picture_number))
        {
            _e("You need select number of images in widget admin panel", 'crum');
        }
        else
        {
            if(!empty($results->data))
            {
                foreach($results->data as $item)
                {
                    echo "<a href='" . $item->link . "' class='th' 'target='_blank'><img src='" . $item->images->$picture_size->url . "' alt='" . $title . "'  style='float:left;margin-left:10px;margin-top:10px;'/></a>";
                }
            }
            else
            {
                _e("You don't have any images", 'crum');
            }
        }

        echo $after_widget;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title']          = trim(strip_tags($new_instance['title']));
        $instance['access_token']   = trim(strip_tags($new_instance['access_token']));
        $instance['user_id']        = trim(strip_tags($new_instance['user_id']));
        $instance['picture_number'] = trim(strip_tags($new_instance['picture_number']));

        return $instance;
    }

    public function form($instance)
    {
        $title          = $instance['title'];
        $access_token   = $instance['access_token'];
        $user_id        = $instance['user_id'];
        $picture_number = $instance['picture_number'];
?>
		
		<p>
    		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
		<p>
    		<label for="<?php echo $this->get_field_id('user_id'); ?>"><?php _e('User ID:'); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id('user_id'); ?>" name="<?php echo $this->get_field_name('user_id'); ?>" type="text" value="<?php echo $user_id; ?>" />
		</p>
		
		<p>
    		<label for="<?php echo $this->get_field_id('access_token'); ?>"><?php _e('Access Token:'); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="text" value="<?php echo $access_token; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('picture_number'); ?>"><?php _e('Number of Images:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('picture_number'); ?>" name="<?php echo $this->get_field_name('picture_number'); ?>" type="text" value="<?php echo $picture_number; ?>" />
		</p>
		
		<p><a href="http://instagram.davidmregister.com/" target="_blank">Get Access token</a></p>
		<?php 
    }

    protected function get_instagram_data($user_id, $access_token, $number_of_images)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.instagram.com/v1/users/' . $user_id . '/media/recent/?access_token=' . $access_token . '&count=' . $number_of_images);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);
        return $result;
    }
    
    protected function _isCurl()
    {
        return (extension_loaded('curl')) ? true : false;
    }
}