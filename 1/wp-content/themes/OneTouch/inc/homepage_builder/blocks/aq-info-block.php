<?php
/** A simple text block **/
class AQ_Info_Block extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Info Block',
			'size' => 'span5',
		);
		
		//create the block
		parent::__construct('aq_info_block', $block_options);
	}
	
	function form($instance) {

		$defaults = array(
			'text' => '',
		);
		$instance = wp_parse_args($instance, $defaults);
		extract($instance);
		
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
            <label for="<?php echo $this->get_field_id('font_color') ?>">
                Block style:
                <?php
                $align = array(
                    'dark' => 'Dark',
                    'light'     => 'Light'
                );
                echo aq_field_select( 'font_color', $block_id, $align, $font_color );
                ?>
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

        <p class="description">
            <label for="<?php echo $this->get_field_id('link') ?>">
                Link to click (optional)
                <?php echo aq_field_input('link', $block_id, $link, $size = 'full') ?>
            </label>
        </p>
		<?php
	}
	
	function block($instance) {
		extract($instance);
        ?>
        <div class="<?php  if( $link )  echo 'clickable'; ?> info-item <?php echo $font_color; ?>" style="background-color: <?php echo $bgcolor; ?> !important; ">
            <div class="pic <?php echo $block_align; ?>">
                <img src="<?php echo $image; ?>" alt="">
            </div>
            <h6><?php echo $subtitle; ?></h6>
            <h2><?php echo htmlspecialchars_decode($title); ?></h2>
            <p><?php echo htmlspecialchars_decode($text); ?></p>
            <a class="link" href="<?php echo $link; ?>"></a>
        </div>
        <?php if( $link ) {?>

        <?php } ?>
    <?php
	}
	
}