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
	
<body class="page page-id-2810 page-template page-template-template-fullwidth-php logged-in css-videos tablet landscape ios dark-header light-body dark-post-head circles">
<div class="page-wrapper">
<div id="content">
<div class="post section post-2810 post-name-promote-introduction post-author-1 not-single page no-thumbnail show-thumbs">
	<div class="post-page-head-area bauhaus">
		<h2 class="post-title heading-font"><?php the_title(); ?></h2>
	</div>
	<div class="post-page-content">
	<?php wptouch_the_content() ; ?>
	</div>
</div>			
</div>
</div>
</body>


