<?php
class NHP_Options_custom_sidebars extends NHP_Options{
	
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
	function render(){

		$class = (isset($this->field['class']))?'class="'.$this->field['class'].'" ':'';
		
        echo '<input style="width:180px;" type="text" placeholder="Type new sidebar name" class="new_widget_name" id="new_sidebar_'.$this->field['id'].'"> ';
		echo '<a class="button-primary new_widget_add" data-name="new_sidebar_'.$this->field['id'].'">Add sidebar</a><div></div>';
        $sidebars = get_option(NHPOPTIONS.'sidebars');
        $i = 1;
        ?>
        <div class="sidebar-list" style="margin-top: 40px">
            <strong>Created sidebars:</strong>
        <?php
            if(is_array($sidebars)){
                foreach ($sidebars as  $name => $sidebar): ?>
                        <div class="sidebar_<?php echo $name; ?>">
                        <span style="width:170px; margin:10px 5px 5px 0;display: inline-block"><?php echo $name; ?></span>
                        <a class="button-secondary delete_widget_sidebar" data-sidebar ="<?php echo $name;?>" data-name="new_sidebar_'<?php $this->field['id']; ?>'">Delete sidebar</a> <br>
                        </div>
                <?php
                    $i++;
                endforeach;
            }    ?>
        </div>



        <?php
        echo (isset($this->field['desc']) && !empty($this->field['desc']))?'&nbsp;&nbsp;<span class="description">'.$this->field['desc'].'</span>':'';

	}//function
	
	
	
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
			'nhp-opts-field-custom_sidebars-js',
			NHP_OPTIONS_URL.'fields/custom_sidebars/field_custom_sidebars.js',
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
			time(),
			true
		);

		
	}//function
	
}//class



?>