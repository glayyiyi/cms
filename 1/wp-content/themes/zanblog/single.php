<?php get_header(); ?>
<div id="zan-bodyer" style="padding-top: 30px;">
	<div class="container">
		<section class="row">
			<div class="col-md-8">	
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php zanblog_set_post_views(get_the_ID()); ?>
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
					<!-- 大型设备文章属性 -->
					<div class="hidden-xs">
						<div class="title-article">
							<h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
						</div>
						<div class="tag-article container">
							<span class="label label-zan"><i class="fa fa-tags"></i> <?php the_time(n . '-' .d); ?></span>
							<span class="label label-zan"><i class="fa fa-tags"></i> <?php the_category(','); ?></span>
							<span class="label label-zan"><i class="fa fa-user"></i> <?php the_author_posts_link(); ?></span>
							<span class="label label-zan"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></span>
						</div>
					</div>
					<!-- 大型设备文章属性 -->
					<!-- 小型设备文章属性 -->
					<div class="visible-xs">
						<div class="title-article">
							<h4><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
						</div>
						<p>
							<i class="fa fa-calendar"></i> <?php the_time('n'); ?>-<?php the_time('d'); ?>
							<i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?>
						</p>
					</div>
					<!-- 小型设备文章属性 -->
					<div class="centent-article">
							<?php $thumb_img = has_post_thumbnail() ? get_the_post_thumbnail( $post->
							ID, array(730, 300), array('alt' => trim(strip_tags( $post->post_title )),'title'=> trim(strip_tags( $post->post_title ))) ) : zanblog_get_thumbnail_img( 730, 300, 1);?>
							<figure class="thumbnail hidden-xs"><?php echo $thumb_img;?></figure>							                 
						<p>
							<?php the_content(); ?>
						</p>
						<!-- 分页 -->
						<div clas="zan-page bs-example">
					        <ul class="pager">
								<li class="previous"><?php previous_post_link('%link', '上一篇', TRUE); ?></li>
								<li class="next"><?php next_post_link('%link', '下一篇', TRUE); ?></li>
							</ul>
				        </div>
				        <!-- 分页 -->
						<!-- 文章版权信息 -->
						<div class="copyright alert alert-success">
							<p>版权属于：<a href="http://www.yeahzan.com">佚站互联</a> - <a href="http://www.yeahzan.com">杭州网站建设</a></p>
							<p>原文地址：<a href="<?php the_permalink() ?>"><?php the_permalink() ?></a></p>
							<p>转载时必须以链接形式注明原始出处及本声明。</p>
						</div>
						<!-- 文章版权信息 -->
						<!-- Baidu Button BEGIN -->
						<div id="bdshare" class="bdshare_b pull-right clearfix" style="line-height: 12px;">
						<img src="http://bdimg.share.baidu.com/static/images/type-button-1.jpg?cdnversion=20120831" />
						<a class="shareCount"></a>
						</div>
						<script type="text/javascript" id="bdshare_js" data="type=button&amp;uid=6553914" ></script>
						<script type="text/javascript" id="bdshell_js"></script>
						<script type="text/javascript">
						document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000);
						</script>
						<!-- Baidu Button END -->
					</div>				
				</article>
				<?php endwhile; else: ?>
				<p>
					<?php _e('抱歉，该页面不存在！'); ?>
				</p>
				<?php endif; ?>
				<!-- 相关文章 -->
		        <div id="post-related" class="bs-example visible-md visible-lg">
					<div class="row">
						<div class="alert alert-danger related-title">您可能也喜欢:</div>
			            <?php $post_tags = wp_get_post_tags($post->ID);
			            if ($post_tags) {

			            foreach ($post_tags as $tag) 
			            {
			            	$tag_list[] .= $tag->term_id;
			            }

			            $post_tag = $tag_list[ mt_rand(0, count($tag_list) - 1) ];					            
			            $args = array(
				            'tag__in' => array($post_tag),
				            'category__not_in' => array(NULL), 
				            'post__not_in' => array($post->ID),
				            'showposts' => 3, 
				            'caller_get_posts' => 1
			            );
			            query_posts($args);

			            if (have_posts()) : 
			            while (have_posts()) : the_post(); update_post_caches($posts); ?>					            
			            <div class="col-md-4">
			                <div class="thumbnail">
			                    <div class="caption">
									<p class="post-related-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
									<p class="post-related-content"><?php echo mb_strimwidth(strip_tags(apply_filters('the_content', $post->post_content)), 0, 150,"..."); ?></p>
									<p><a class="btn btn-danger pull-right read-more" href="<?php the_permalink() ?>"  title="详细阅读 <?php the_title(); ?>">阅读全文</a></p>							
								</div>
			                </div>					                
			            </div>
			            <?php endwhile; else : ?>
			            <div>暂无相关产品</div>
			            <?php endif; wp_reset_query(); } ?>
					</div>
				</div>
				<?php comments_template(); ?> 									
			</div>
			<?php get_sidebar(); ?>
		</section>
	</div>
</div>
<?php get_footer(); ?>
</body>
</html>