<?php
class NHP_Options_sorter extends NHP_Options{

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
        echo '<div class="sorter">';
        global $NHP_Options;
        $blocks = $NHP_Options->get("block_manager");
        $blocks = (array)json_decode(str_replace("+","\"",$blocks));

        $enabled_and_disabled = (array)json_decode(str_replace("+","\"",$this->value));
        $enabled = (array)$enabled_and_disabled['enabled'];
        $disabled =  (array)$enabled_and_disabled['disabled'];
        $enabled = array_flip($enabled);
        $disabled = array_flip($disabled);

        foreach ((array)$enabled as $name=>$block){
            if(!isset($blocks[$name])){
                unset($enabled[$name]);
                echo $name.'<br>';
            }
        }

        foreach ((array)$disabled as $name=>$block){
            if(!isset($blocks[$name]))
                unset($disabled[$name]);
        }



        foreach($blocks as $name=>$block){
            if(!isset($enabled[$name]) && !isset($disabled[$name]) )
                $enabled[$name] = $name;
            //echo $name.'<br>';
        }
        ?>
    <div class="enabled-wrapper">
        <h3>Enabled</h3>
        <ul class = 'enabled'>
            <?php
            foreach((array)$enabled as $name=>$block)
                echo '<li class="widget"><div class="widget-top"><div class="widget-title"><h4>'.$name.'</h4></div></div></li>';
            ?>
        </ul>
    </div>

    <div class="disabled-wrapper">
        <h3>Disabled</h3>
        <ul class = 'disabled'>
            <?php
            foreach($disabled as $name => $current)
                echo '<li class="widget"><div class="widget-top"><div class="widget-title"><h4>'.$name.'</h4></div></div></li>';
            ?>
        </ul>
    </div>
    <?php
        echo '</div>';
        echo '<input type="hidden" id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" value="'.$this->value.'" class="'.$class.'" />';
        echo '<script>input_id="'.$this->field['id'].'"</script>';
        echo (isset($this->field['desc']) && !empty($this->field['desc']))?'&nbsp;&nbsp;<span class="description">'.$this->field['desc'].'</span>':'';

    }//function

    function enqueue(){
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script(
            'nhp-opts-field-sorter-js',
            NHP_OPTIONS_URL.'fields/sorter/field_sorter.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
            time(),
            true
        );
        wp_enqueue_style('nhp-opts-field-sorter-style', NHP_OPTIONS_URL.'fields/sorter/field_sorter.css', false, null);
    }

}//class
?>