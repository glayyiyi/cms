<?php
class NHP_Options_block_manager extends NHP_Options{
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since NHP_Options 1.0
	*/
	function __construct($field = array(), $value ='', $parent){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
		
	}//function
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since NHP_Options 1.0
	*/
	function render() {
		$class = (isset($this->field['class']))?'class="'.$this->field['class'].'" ':'';
        echo '<input id="new_block_name" type="text" placeholder="New block name" /> <a href="#" class="button button-primary" id="add_block">Add Block</a><br>';
        echo '<em id="new-block-validation-error"></em>';?>
        <script>
            pages =  '<?php
                $pages = $this->return_pages_array();
                foreach ($pages as $slug=>$title){
                    $title = str_replace("'", "", "$title");
                    echo '<option value="'.$slug.'">'.$title.'</option>';
                };
            ?>';
        </script>
        <div id="colorpicker"></div>
        <div id="block-list">

        </div>
        <?php
        echo '<input type="hidden" id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" value="'.$this->value.'" class="'.$class.'" />';
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'&nbsp;&nbsp;<span class="description">'.$this->field['desc'].'</span>':'';
		
	}//function


    function return_pages_array(){
        $args = array("post_type"=>"page",
                      "numberposts"=>-1);
        $pages = get_posts( $args );
        $pages_array = array();
        $pages_array['special-1'] = "--Blocks---------";
        $pages_array['post_slider'] = "Horizontal Slider";
        $pages_array['recent_projects'] = "Recent Projects";
        $pages_array['special-2'] = "--Pages----------";
        if( is_array($pages) ){
            foreach($pages as $page){
                $pages_array[$page->post_name] = $page->post_title;
            }
        }

        return (array)$pages_array;
    }
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since NHP_Options 1.0
	*/
	function enqueue(){
		
		wp_enqueue_style('nhp-opts-jquery-ui-css');

		wp_enqueue_script(
			'nhp-opts-field-block_manager-js',
			NHP_OPTIONS_URL.'fields/block_manager/field_block_manager.js',
			array('jquery', 'farbtastic'),
			time(),
			true
		);
        wp_enqueue_style('nhp-opts-field-block_manager-style', NHP_OPTIONS_URL.'fields/block_manager/field_block_manager.css', false, null);
		
	}//function
	
}//class
?>