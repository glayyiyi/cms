<?php
/*
	Mobile Template: Native Page
*/
?>
<header>
<link rel='stylesheet' id='foundation_font_awesome_css-css'  href='http://www.appcn100.com/cms/iagent/wp-content/plugins/wptouch/themes/foundation/modules/font-awesome/font-awesome.min.css?ver=2.0.4' type='text/css' media='screen' />
<link rel='stylesheet' id='wptouch-parent-theme-css-css'  href='http://www.appcn100.com/cms/iagent/wp-content/plugins/wptouch/themes/foundation/default/style.css?ver=3.1.8' type='text/css' media='all' />
<style type='text/css'>
.page-wrapper { background-color: #f9f9f8; }
a { color: #2d353f; }
body, header, .wptouch-menu, #search-dropper, .date-circle { background-color: #2d353f; }
a, #slider a p:after { color: #35c4ff; }
.dots li.active, #switch .active { background-color: #35c4ff; }
.bauhaus, .wptouch-login-wrap, form#commentform button#submit { background-color: #84ac50; }

</style>
<link rel='stylesheet' id='wptouch-theme-css-css'  href='http://www.appcn100.com/cms/iagent/wp-content/plugins/wptouch/themes/bauhaus/default/style.css?ver=3.1.8' type='text/css' media='all' />
</header>
<?php 

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


