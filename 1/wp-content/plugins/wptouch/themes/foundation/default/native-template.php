<?php
/*
	Mobile Template: Native Page
*/
?>


<?php if ( have_posts() ) { ?>
<div id="content">
	<div class="post-content">
	<?php get_the_content(); ?>
	</div>
</div>
<?php } ?>

