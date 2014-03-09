<?php
/*
	Mobile Template: Native Page
*/
?>

	
<?php 
do_action( 'wptouch_pre_head' );
//wp_head();
do_action( 'wptouch_post_head' );
wptouch_the_post(); 
?>
	<div class="<?php wptouch_post_classes(); ?>">
	<div class="post-head-area">
		<h2 class="post-title heading-font"><?php the_title(); ?></h2>
	</div>
	<?php wptouch_the_content() ; ?>
</div>


