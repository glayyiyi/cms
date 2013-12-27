<?php
/** A simple text block **/
class AQ_Call2act_Block extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Call to Action',
			'size' => 'span12',
		);
		
		//create the block
		parent::__construct('aq_call2act_block', $block_options);
	}
	
	function form($instance) {

		$defaults = array(
			'text' => '',
		);
		$instance = wp_parse_args($instance, $defaults);
		extract($instance);
		
		?>
        <p class="description">
            <label for="<?php echo $this->get_field_id('button_title') ?>">
                Button Title :
                <?php echo aq_field_input('button_title', $block_id, $button_title, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('button_href') ?>">
                Button link :
                <?php echo aq_field_input('button_href', $block_id, $button_href, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('block_align') ?>">
                Align :
                <?php
                $align = array('Left', 'Right');
                echo aq_field_select( 'block_align', $block_id, $align, $block_align );
                ?>
            </label>
        </p>
		<p class="description">
			<label for="<?php echo $this->get_field_id('main_text') ?>">
				Main Text (optional)
				<?php echo aq_field_input('main_text', $block_id, $main_text, $size = 'full') ?>
			</label>
		</p>
		
		<p class="description">
			<label for="<?php echo $this->get_field_id('secondary_text') ?>">
				Secondary Text (optional)
				<?php echo aq_field_input('secondary_text', $block_id, $secondary_text, $size = 'full') ?>
			</label>
		</p>
		
		<?php
	}

    /* block header */
    function before_block($instance) {
        extract($instance);
        $size_converting = array(
            'span12' => 'fifteen',
            'span8'  => 'eleven',
            'span6'  => 'seven',
            'span5'  => 'five',
            'span3'  => 'four'
        );
        $column_class = $first ? 'aq-first' : '';

        echo '<div id="aq-block-'.$number.'" class="aq-block clearing-container aq-block-'.$id_base.' '.$size_converting[$size].' columns '.$column_class.' cf">';
    }
	
	function block($instance) {
		extract($instance);?>
        <div class="call-to <?php echo ((bool)$block_align)?'al-right':'al-left'; ?> clearing-container">
            <a class="button large" href="<?php echo $button_href; ?>"><?php echo $button_title; ?></a>
            <h2><?php echo htmlspecialchars_decode($main_text); ?></h2>
            <h5><?php echo htmlspecialchars_decode($secondary_text); ?></h5>
        </div>
    <?php
	}
	
}