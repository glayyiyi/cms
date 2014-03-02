<?php 
global $qode_options_satellite;
$blog_hide_comments = "";
if (isset($qode_options_satellite['blog_hide_comments'])) {
	$blog_hide_comments = $qode_options_satellite['blog_hide_comments'];
}

?>
<?php
$_post_format = get_post_format();
?>
<?php
	switch ($_post_format) {
		case "video":
?>
		<article class="video">
			<div class="post_info">
				<div class="inner">
					<div class="post_date">
						<span class="date"><?php the_time('d'); ?></span>
						<span class="month"><?php the_time('M'); ?></span>
					</div>
					<div class="blog_like">
						<?php if( function_exists('qode_like') ) qode_like(); ?>
					</div>
					<?php echo do_shortcode('[social_share]'); ?>
				</div>
			</div>
			<div class="post_content_holder">
				<div class="post_image">
					<div class="post_image_video">
						<?php $_video_type = get_post_meta(get_the_ID(), "video_format_choose", true);?>
						<?php if(get_post_meta(get_the_ID(), "video_height", true) != "") { 
							$video_height = get_post_meta(get_the_ID(), "video_height", true);
						} else {
							$video_height = 600;
						} ?>
						<?php if($_video_type == "youtube") { ?>
							<iframe height="<?php echo $video_height; ?>" src="http://www.youtube.com/embed/<?php echo get_post_meta(get_the_ID(), "video_format_link", true);  ?>?wmode=transparent" wmode="Opaque" frameborder="0" allowfullscreen></iframe>
						<?php } elseif ($_video_type == "vimeo"){ ?>
							<iframe height="<?php echo $video_height; ?>" src="http://player.vimeo.com/video/<?php echo get_post_meta(get_the_ID(), "video_format_link", true);  ?>?title=0&amp;byline=0&amp;portrait=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
						<?php } ?>
					</div>	
				</div>
				<div class="post_text">
					<div class="inner">
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<span class="post_infos"><?php _e('By','qode'); ?> <?php the_author(); ?> <?php _e('In','qode'); ?> <?php the_category(', '); ?></span>
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
						<?php if( has_tag()) { ?><div class="blog_single_tags"><?php the_tags(__('Tags > ','qode')); ?></div><?php } ?>
					</div>
				</div>
			</div>
		</article>	

<?php
		break;
		case "audio":
?>
		<article class="audio">
			<div class="post_info">
				<div class="inner">
					<div class="post_date">
						<span class="date"><?php the_time('d'); ?></span>
						<span class="month"><?php the_time('M'); ?></span>
					</div>
					<div class="blog_like">
						<?php if( function_exists('qode_like') ) qode_like(); ?>
					</div>
					<?php echo do_shortcode('[social_share]'); ?>
					
				</div>
			</div>
			<div class="post_content_holder">
				<div class="post_image">
					<audio src="<?php echo get_post_meta(get_the_ID(), "audio_link", true) ?>" controls="controls">
						<?php _e("Your browser don't support audio player","qode"); ?>
					</audio>
				</div>
				<div class="post_text">
					<div class="inner">
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<span class="post_infos"><?php _e('By','qode'); ?> <?php the_author(); ?> <?php _e('In','qode'); ?> <?php the_category(', '); ?></span>
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
						<?php if( has_tag()) { ?><div class="blog_single_tags"><?php the_tags(__('Tags > ','qode')); ?></div><?php } ?>
					</div>
				</div>
			</div>
		</article>
<?php
		break;
		case "link":
?>
			<article class="link">
				<div class="post_info">
					<div class="inner">
						<div class="post_date">
							<span class="date"><?php the_time('d'); ?></span>
							<span class="month"><?php the_time('M'); ?></span>
						</div>
						<div class="blog_like">
							<?php if( function_exists('qode_like') ) qode_like(); ?>
						</div>
						<?php echo do_shortcode('[social_share]'); ?>
					</div>
				</div>
				<div class="post_content_holder">
					<div class="link_holder">
						<div class="link_mark">
							<span></span>
						</div>
						<div class="link_text_holder">
							<div class="link_text">
								<h2><a href="<?php echo get_post_meta(get_the_ID(), "title_link", true); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>	
							</div>
						</div>
					</div>
				<div class="post_text">
					<div class="inner">
						<span class="post_infos"><?php _e('By','qode'); ?> <?php the_author(); ?> <?php _e('In','qode'); ?> <?php the_category(', '); ?></span>
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
						<?php if( has_tag()) { ?><div class="blog_single_tags"><?php the_tags(__('Tags > ','qode')); ?></div><?php } ?>
					</div>
				</div>
				</div>
			</article>
<?php
		break;
		case "gallery":
?>
			<article class="gallery">
				<div class="post_info">
					<div class="inner">
						<div class="post_date">
							<span class="date"><?php the_time('d'); ?></span>
							<span class="month"><?php the_time('M'); ?></span>
						</div>
						<div class="blog_like">
							<?php if( function_exists('qode_like') ) qode_like(); ?>
						</div>
						<?php echo do_shortcode('[social_share]'); ?>
					</div>
				</div>
				<div class="post_content_holder">
					<div class="post_image">
						<div class="flexslider">
							<ul class="slides">
								<?php
									$post_content = get_the_content();
									preg_match('/\[gallery.*ids=.(.*).\]/', $post_content, $ids);
									$array_id = explode(",", $ids[1]);
									
									foreach($array_id as $img_id){ ?>
										<li><a href="<?php the_permalink(); ?>"><?php echo wp_get_attachment_image( $img_id, 'full' ); ?></a></li>
									<?php } ?>
							</ul>
						</div>
					</div>
					<div class="post_text">
						<div class="inner">
							<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
							<span class="post_infos"><?php _e('By','qode'); ?> <?php the_author(); ?> <?php _e('In','qode'); ?> <?php the_category(', '); ?></span>
							<?php
								$content =  str_replace($ids[0], "", $post_content);
								echo do_shortcode($content);
							?>
							<?php wp_link_pages(); ?>
							<?php if( has_tag()) { ?><div class="blog_single_tags"><?php the_tags(__('Tags > ','qode')); ?></div><?php } ?>
						</div>
					</div>
				</div>
			</article>
<?php
		break;
		case "quote":
?>
			<article class="blockquote">
				<div class="post_info">
					<div class="inner">
						<div class="post_date">
							<span class="date"><?php the_time('d'); ?></span>
							<span class="month"><?php the_time('M'); ?></span>
						</div>
						<div class="blog_like">
							<?php if( function_exists('qode_like') ) qode_like(); ?>
						</div>
						<?php echo do_shortcode('[social_share]'); ?>
					</div>
				</div>
				<div class="post_content_holder">
					<div class="blockquote_holder">
						<div class="blockquote_mark">
							<span></span>
						</div>
						<div class="blockquote_text_holder">
							<div class="blockquote_text">
								<h2><?php echo get_post_meta(get_the_ID(), "quote_format", true); ?></h2>
								<span class="blockquote_author">&mdash; <?php the_title(); ?></span>	
							</div>
						</div>
					</div>
					<div class="post_text">
						<div class="inner">
							<span class="post_infos"><?php _e('By','qode'); ?> <?php the_author(); ?> <?php _e('In','qode'); ?> <?php the_category(', '); ?></span>
							<?php the_content(); ?>
							<?php wp_link_pages(); ?>
							<?php if( has_tag()) { ?><div class="blog_single_tags"><?php the_tags(__('Tags > ','qode')); ?></div><?php } ?>
						</div>
					</div>
				</div>
			</article>
<?php
		break;
		default:
?>
		<article class="standard">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post_info">
				<div class="inner">
					<div class="post_date">
						<span class="date"><?php the_time('d'); ?></span>
						<span class="month"><?php the_time('M'); ?></span>
					</div>
					<div class="blog_like">
						<?php if( function_exists('qode_like') ) qode_like(); ?>
					</div>
					<?php echo do_shortcode('[social_share]'); ?>
				</div>
			</div>
			<div class="post_content_holder">
				<?php if(get_post_meta(get_the_ID(), "qode_hide-featured-image", true) != "yes") { ?>
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="post_image">
						<div class="inner">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<?php the_post_thumbnail('full'); ?>
							</a>
						</div>
					</div>
				<?php } } ?>
				<div class="post_text<?php if (!has_post_thumbnail() ) { echo " no_image"; } ?>">
					<div class="inner">
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<span class="post_infos"><?php _e('By','qode'); ?> <?php the_author(); ?> <?php _e('In','qode'); ?> <?php the_category(', '); ?></span>  
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
						<?php if( has_tag()) { ?><div class="blog_single_tags"><?php the_tags(__('Tags > ','qode')); ?></div><?php } ?>
					</div>
				</div>
			</div>
		</div>
		</article>
<?php
}
?>		

