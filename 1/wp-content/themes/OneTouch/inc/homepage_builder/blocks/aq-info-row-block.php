<?php
/** A simple text block **/
class AQ_Info_Row_Block extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Info Row',
			'size' => 'span12',
		);
		
		//create the block
		parent::__construct('aq_info_row_block', $block_options);
	}
	
	function form($instance) {

		$defaults = array(
			'text' => '',
		);
		$instance = wp_parse_args($instance, $defaults);
		extract($instance);
		
		?>
		<p class="description">
			<label for="<?php echo $this->get_field_id('title') ?>">
				Title (optional)
				<?php echo aq_field_input('title', $block_id, $title, $size = 'full') ?>
			</label>
		</p>
		
		<p class="description">
			<label for="<?php echo $this->get_field_id('subtitle') ?>">
				Content
				<?php echo aq_field_input('subtitle', $block_id, $subtitle, $size = 'full') ?>
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
        <div class="promo">
            <span class="icon info"></span>
            <h2><?php echo htmlspecialchars_decode($title); ?></h2>
            <h5><?php echo htmlspecialchars_decode($subtitle); ?></h5>
        </div>
    <?php
	}
	
}