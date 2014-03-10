<?php get_header(); ?>
<section id="zan-bodyer">
	<div class="container">
		<section class="row">
			<section id="mainstay" class="col-md-8">
				<div id="ie-warning" class="alert alert-danger fade in">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<i class="fa fa-warning"></i> 请注意，Zanblog并不支持低于IE8的浏览器，为了获得最佳效果，请下载最新的浏览器，推荐下载 <a href="http://www.google.cn/intl/zh-CN/chrome/" target="_blank"><i class="fa fa-compass"></i> Chrome</a>
				</div>
				<div class="well fade in">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					欢迎使用由 <a target="_blank" href="http://www.yeahzan.com/">佚站互联</a> 提供的 <strong>Zanblog主题</strong>，如有问题可以查看问题板块中的内容或者加入Zanblog官方群：223133969 <i class="fa fa-smile-o"></i> 
				</div>
				<!-- 幻灯片-->
				<?php if(dynamic_sidebar('幻灯片')) {?>
				<?php };?> 
				<!-- 幻灯片-->
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="article well clearfix">
					<?php if( is_sticky() ) echo '<i class="fa fa-bookmark article-stick visible-md visible-lg"></i>';?>
					<div class="data-article hidden-xs">
						<span class="month"><?php the_time(n月) ?></span>
						<span class="day"><?php the_time(d) ?></span>
					</div>
					<!-- PC端设备文章属性 -->
					<section class="visible-md visible-lg">
						<div class="title-article">
							<h1><a href="<?php the_permalink() ?>">
								<?php the_title(); ?>
							</a></h1>
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
							<i class="fa fa-calendar"></i> <?php the_time('n'); ?>-<?php the_time('d'); ?>
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
