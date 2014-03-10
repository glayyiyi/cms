<?php get_header(); ?>
<section id="zan-bodyer">
	<div class="container">
		<section class="row">
			<section id="mainstay" class="col-md-8">
				<!-- 幻灯片 -->
				<?php if(dynamic_sidebar('幻灯片')) {?>
					  <?php echo do_shortcode("[metaslider id=411]"); ?>
				<?php };?>
				<!-- 幻灯片 -->
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="article container well">
					<div class="data-article hidden-xs">
						<span class="month"><?php the_time(n月) ?></span>
						<span class="day"><?php the_time(d) ?></span>
					</div>
					<!-- PC端设备文章属性 -->
					<section class="visible-md visible-lg">
						<div class="title-article">
							<h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
						</div>
						<div class="tag-article container">
							<span class="label label-zan"><i class="fa fa-tags"></i> <?php the_category(','); ?></span>
							<span class="label label-zan"><i class="fa fa-user"></i> <?php the_author_posts_link(); ?></span>
							<span class="label label-zan"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></span>
						</div>
						<div class="content-article">					
							<?php $thumb_img = has_post_thumbnail() ? get_the_post_thumbnail( $post->
							ID, array(730, 300), array('alt' => trim(strip_tags( $post->post_title )),'title'=> trim(strip_tags( $post->post_title ))) ) : zanblog_get_thumbnail_img( 730, 300, 1);?>
							<figure class="thumbnail"><a href="<?php the_permalink() ?>"><?php echo $thumb_img;?></a></figure>								
							<div class="alert alert-zan">					
								<?php echo mb_strimwidth(strip_tags(apply_filters('the_content', $post->post_content)), 0, 250,"..."); ?>
							</div>
						</div>
						<a class="btn btn-danger pull-right read-more" href="<?php the_permalink() ?>"  title="详细阅读 <?php the_title(); ?>">阅读全文 <span class="badge"><?php comments_number( '0', '1', '%' ); ?></span></a>
					</section>
					<!-- PC端设备文章属性 -->
					<!-- 移动端设备文章属性 -->
					<section class="visible-xs  visible-sm">
						<div class="title-article">
							<h4><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
						</div>
						<p>
							<i class="fa fa-calendar"></i> <?php the_date('m-d'); ?>
							<i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?>
						</p>
						<div class="content-article">					
							<?php $thumb_img = has_post_thumbnail() ? get_the_post_thumbnail( $post->
							ID, array(730, 300), array('alt' => trim(strip_tags( $post->post_title )),'title'=> trim(strip_tags( $post->post_title ))) ) : zanblog_get_thumbnail_img( 730, 300, 1);?>
							<figure class="thumbnail"><a href="<?php the_permalink() ?>"><?php echo $thumb_img;?></a></figure>
							<div class="alert alert-zan">					
								<?php echo mb_strimwidth(strip_tags(apply_filters('the_content', $post->post_content)), 0, 150,"..."); ?>
							</div>
						</div>
						<a class="btn btn-danger pull-right read-more btn-block" href="<?php the_permalink() ?>"  title="详细阅读 <?php the_title(); ?>">阅读全文 <span class="badge"><?php comments_number( '0', '1', '%' ); ?></span></a>
					</section>
					<!-- 移动端设备文章属性 -->
				</div>
				<?php endwhile; else: ?>
				<p><?php _e('非常抱歉，没有相关文章.'); ?></p>
				<?php endif; ?>
			</section>
			<?php get_sidebar(); ?>
			<div class="col-md-8"><?php zanblog_page('auto'); ?></div>
		</section>
	</div>
</section>
<?php get_footer(); ?>
</body>
</html>