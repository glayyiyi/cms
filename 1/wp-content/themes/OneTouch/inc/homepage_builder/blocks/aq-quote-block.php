<?php
/** A simple text block **/
class AQ_Quote_Block extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Quote Block',
			'size' => 'span5',
		);
		
		//create the block
		parent::__construct('aq_quote_block', $block_options);
	}
	
	function form($instance) {

		$defaults = array(
			'text' => '',
		);
		$instance = wp_parse_args($instance, $defaults);
		extract( $instance );
		
		?>

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
            <label for="<?php echo $this->get_field_id('author_post') ?>">
                Author subtitle:
                <?php echo aq_field_input('author_post', $block_id, $author_post, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('author_name') ?>">
                Author name:
                <?php echo aq_field_input('author_name', $block_id, $author_name, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('link') ?>">
                Link (optional)
                <?php echo aq_field_input('link', $block_id, $link, $size = 'full') ?>
            </label>
        </p>
		<?php
	}
	
	function block($instance) {
		extract($instance); ?>
        <div class="quote-block">
            <div class="build-block-title">
                <h6><?php echo $subtitle ?></h6>
                <h2><a href="<?php echo $link; ?>"><?php echo $title ?></a></h2>
            </div>

            <div class="quote-text">
                <?php echo html_entity_decode($text); ?>
            </div>

            <div class="quote-autor">
                <h6><?php echo $author_post; ?></h6>
                <h3><a href=""><?php echo $author_name; ?></a></h3>
            </div>
        </div>

    <?php
	}
	
}