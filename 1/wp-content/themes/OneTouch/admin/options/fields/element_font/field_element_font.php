<?php
class NHP_Options_element_font extends NHP_Options{

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
        $this->field['fonts'] = array();

        $fonts = get_transient('nhp-opts-google-webfonts');
        if( !is_array(json_decode($fonts)) ){
            $fonts = @file_get_contents(NHP_OPTIONS_DIR.'fields/element_font/fonts.json');
            set_transient('nhp-opts-google-webfonts', $fonts, 60 * 60 * 24);
        }
        $this->field['fonts'] = json_decode($fonts);

        $this->field['size'] = array( 9, 30 );
        $this->field['style'] = array(
            'normal'=>'Normal',
            'italic'=>'Italic',
            'bold'=>"Bold",
            'italic_bold'=>'Italic Bold'
        );
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
        echo '<select id="'.$this->field['id'].'_size" class="typo_size">';
        echo '<option value="."> </option>';
        for( $i = $this->field['size'][0]; $i < $this->field['size'][1]; $i++ )
            echo '<option>'.$i.'px</option>';
        echo '</select>';

        echo '<select id="'.$this->field['id'].'_family" '.$class.'rows="6" class="typo_family"  >';
        echo '<option value="."> </option>';
        foreach((array)($this->field['fonts']->items) as $cut){
            echo '<option value="'.$cut->family.'" '.selected($this->value, $cut->family, false).'>'.$cut->family.'</option>';
        }
        echo '</select>';
        echo '<select id="'.$this->field['id'].'_style" class="typo_style">';
        echo '<option value="."> </option>';
        foreach($this->field['style'] as $style_value=>$style_name)
             echo '<option value="'.$style_value.'">'.$style_name.'</option>';
        echo '</select>';
        echo '<input type="hidden" name="'.$this->args['opt_name'].'['.$this->field['id'].']"  id="'.$this->field['id'].'" class="element_font_typo" value = "'.$this->value.'"/>';

        echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
    }//function

    function enqueue(){
        wp_enqueue_script(
            'nhp-opts-field-element_font-js',
            NHP_OPTIONS_URL.'fields/element_font/field_element_font.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
            time(),
            true
        );
    }//function

}//class
?>