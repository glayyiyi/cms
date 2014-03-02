<?php 
$blog_hide_comments = "";
if (isset($qode_options_satellite['blog_hide_comments'])) {
	$blog_hide_comments = $qode_options_satellite['blog_hide_comments'];
}

?>
<article class="standard">
	<div class="post_info">
		<div class="inner">
			<div class="post_date">
				<span class="date"><?php the_time('d'); ?></span>
				<span class="month"><?php the_time('M'); ?></span>
			</div>
		</div>
	</div>
	<div class="post_content_holder">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="post_image">
				<div class="inner">
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail('blog-type-1'); ?>
					</a>
				</div>
			</div>
		<?php } ?>
		<div class="post_text">
			<div class="inner">
				<span class="top_info"><?php _e('Posted by','qode'); ?> <span><?php the_author(); ?></span> <?php _e('in','qode'); ?> <?php the_category(', '); ?></span>
				<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
				<div class="info_bottom">
					<?php if($blog_hide_comments != "yes"){ ?>
						<a  class="comments" href="<?php comments_link(); ?>"><?php comments_number( __('no comment','qode'), '1 '.__('comment','qode'), '% '.__('comments','qode') ); ?></a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</article>