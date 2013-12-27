<?php
/** A simple text block **/
class AQ_Info_Button_Block extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Info Block Button',
			'size' => 'span5',
		);
		
		//create the block
		parent::__construct('aq_info_button_block', $block_options);
	}
	
	function form($instance) {

		$defaults = array(
			'text' => '',
		);
		$instance = wp_parse_args($instance, $defaults);
		extract( $instance );
		
		?>
        <p class="description">
            <label for="<?php echo $this->get_field_id('image') ?>">
                Upload image
                <?php echo aq_field_upload('image', $block_id, $image) ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('block_align') ?>">
                Image align:
                <?php
                $align = array(
                    'al-left' => 'Left',
                    'al-center' => 'Center',
                    'al-right'  => 'Right'
                );
                echo aq_field_select( 'block_align', $block_id, $align, $block_align );
                ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('bgcolor') ?>">
                Background color (optional)
                <?php echo aq_field_color_picker('bgcolor', $block_id, $bgcolor, $size = 'full') ?>
            </label>
        </p>
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
            <label for="<?php echo $this->get_field_id('text') ?>">
                Text:
                <?php echo aq_field_textarea('text', $block_id, $text, $size = 'full') ?>
            </label>
        </p>
        <hr>
        <p class="description">
            <label for="<?php echo $this->get_field_id('button_text') ?>">
                Button text:
                <?php echo aq_field_input('button_text', $block_id, $button_text, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('link') ?>">
                Button link (optional)
                <?php echo aq_field_input('link', $block_id, $link, $size = 'full') ?>
            </label>
        </p>
		<?php
	}
	
	function block($instance) {
		extract($instance); ?>
        <div class="info-item-alt <?php echo $font_color; ?>" style="background-color: <?php echo $bgcolor; ?>; ">
            <div class="pic <?php echo $block_align; ?>">
                <img src="<?php echo $image; ?>" alt="">
            </div>
            <div class="inner-text">
                <h6><?php echo $subtitle; ?></h6>
                <h2><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h2>
                <p><?php echo htmlspecialchars_decode($text); ?></p>
            </div>
            <div class="al-center">
                <a class="button" href="<?php echo $link; ?>"><?php echo $button_text; ?></a>
            </div>
        </div>

    <?php
	}
	
}