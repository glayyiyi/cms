<?php get_header(); ?>
<div class="right">
	<?php if (have_posts()): ?>
		<ul data-role="listview" data-inset="true"
		<?php jqmobile_ui('post');?>>
			<?php while (have_posts()) : the_post(); ?>
				<li <?php if(is_sticky()) {jqmobile_ui('sticky');} ?>><a
			href="<?php the_permalink() ?>"> <img
				src="<?php
			$thumb = WP_CONTENT_URL . "/plugins/weiservice/images/icon-72.png";
			$thumbnail_id = get_post_thumbnail_id ( $post->ID );
			if ($thumbnail_id) {
				$thumbs = wp_get_attachment_image_src ( $thumbnail_id, array (
						80,
						80 
				) );
				if ($thumbs [0])
					$thumb = $thumbs [0];
			} else {
				preg_match_all ( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post->post_content, $matches );
				if ($matches) {
					if ($matches [1] [0])
						$thumb = $matches [1] [0];
				}
			}
			
			echo $thumb;
			?>">
				<p class="ui-li-aside"><?php the_time('Y-m-d'); ?></p>
				<h3><?php the_title(); ?></h3>

				<p>
					<strong><?php the_author(); ?></strong>
				</p>
				<div><?php the_excerpt(); ?></div>
						<?php if (comments_open()): ?>
							<span class="ui-li-count"><?php comments_number('0', '1', '%' );?></span>
						<?php endif; ?>
					</a></li>
			<?php endwhile; ?>
		</ul>
		<?php include (TEMPLATEPATH . '/inc/nav.php' ); ?>
	<?php else: ?>
		<h2>Not Found</h2>
	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>