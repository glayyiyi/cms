<?php
/*
	Mobile Template: Native Page
*/
?>



<div id="content">
		<?php while ( wptouch_have_posts() ) { ?>
		
			<?php 
				do_action( 'wptouch_pre_head' );
	do_action( 'wptouch_post_head' );
			wptouch_the_post(); ?>

			<div class="<?php wptouch_post_classes(); ?>">
				<div class="post-head-area">
					<?php // if ( has_post_thumbnail() ) the_post_thumbnail(); ?>  
					<h2 class="post-title heading-font"><?php wptouch_the_title(); ?></h2>
				</div>
				<?php wptouch_the_content(); ?>
			</div>

		<?php } ?>
	</div> <!-- content -->


