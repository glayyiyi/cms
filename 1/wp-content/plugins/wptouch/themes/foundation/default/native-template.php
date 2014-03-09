<?php
/*
	Mobile Template: Native Page
*/
include("wp-config.php");
?>


<?php if ( have_posts() ) { ?>
<div id="content">
	<div class="post-content">
		<?php the_content(); ?>
	</div>
</div>
<?php } ?>

