<?php
/*
	Mobile Template: Native Page
*/
?>



<div id="content">
		<?php while ( wptouch_have_posts() ) { ?>
		
			<?php wptouch_the_post(); ?>

			<div class="<?php wptouch_post_classes(); ?>">
				<?php wptouch_the_content(); ?>
			</div>

		<?php } ?>
	</div> <!-- content -->


