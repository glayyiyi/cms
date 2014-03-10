<?php
	/*
	Template Name: 文章存档
	*/
?>

<?php get_header(); ?>
<div id="zan-bodyer" style="padding-top: 30px;">
	<div class="container">
		<section class="row">
			<div class="col-md-8">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article class="article container well">
					<!-- 面包屑 -->
					<div class="breadcrumb">
					    <?php 
					    	if(function_exists('bcn_display')) {
					        	echo '<i class="fa fa-home"></i> ';
					        	bcn_display();
					    	}
					    ?>
					</div>	
					<!-- 面包屑 -->
					<?php zanblog_archives_list(); ?>
				</article>
				<?php endwhile; else: ?>
				<p>
					<?php _e('抱歉，该页面不存在！'); ?>
				</p>
				<?php endif; ?>
			</div>
			<?php get_sidebar(); ?>
		</section>
	</div>
</div>
<?php get_footer(); ?>
</body>
</html>


