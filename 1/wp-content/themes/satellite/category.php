<?php get_header(); ?>
<?php 
global $wp_query;
$id = $wp_query->get_queried_object_id();

if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
else { $paged = 1; }

$sidebar = $qode_options_satellite['category_blog_sidebar'];

if(get_post_meta($id, "qode_responsive-title-image", true) != ""){
 $responsive_title_image = get_post_meta($id, "qode_responsive-title-image", true);
}else{
	$responsive_title_image = $qode_options_satellite['responsive_title_image'];
}

if(get_post_meta($id, "qode_fixed-title-image", true) != ""){
 $fixed_title_image = get_post_meta($id, "qode_fixed-title-image", true);
}else{
	$fixed_title_image = $qode_options_satellite['fixed_title_image'];
}

if(get_post_meta($id, "qode_title-image", true) != ""){
 $title_image = get_post_meta($id, "qode_title-image", true);
}else{
	$title_image = $qode_options_satellite['title_image'];
}

$blog_hide_comments = "";
if (isset($qode_options_satellite['blog_hide_comments'])) 
	$blog_hide_comments = $qode_options_satellite['blog_hide_comments'];

if(get_post_meta($id, "qode_title-height", true) != ""){
 $title_height = get_post_meta($id, "qode_title-height", true);
}else{
	$title_height = $qode_options_satellite['title_height'];
}

$title_background_color = '';
if(get_post_meta($id, "qode_page-title-background-color", true) != ""){
 $title_background_color = get_post_meta($id, "qode_page-title-background-color", true);
}else{
	$title_background_color = $qode_options_satellite['title_background_color'];
}

$show_title_image = true;
if(get_post_meta($id, "qode_show-page-title-image", true)) {
	$show_title_image = false;
}

if(isset($qode_options_satellite['blog_page_range']) && $qode_options_satellite['blog_page_range'] != ""){
	$blog_page_range = $qode_options_satellite['blog_page_range'];
} else{
	$blog_page_range = $wp_query->max_num_pages;
}

?>
	<div class="title <?php if($responsive_title_image == 'no' && $title_image != "" && $fixed_title_image == "yes" && $show_title_image == true){ echo 'has_fixed_background '; } if($responsive_title_image == 'no' && $title_image != "" && $fixed_title_image == "no" && $show_title_image == true){ echo 'has_background'; } if($responsive_title_image == 'yes' && $show_title_image == true){ echo 'with_image'; } ?>" style="<?php if($responsive_title_image == 'no' && $title_image != "" && $show_title_image == true){ echo 'background-image:url('.$title_image.');';  } if($title_height != ''){ echo 'height:'.$title_height.'px;'; } if($title_background_color != ''){ echo 'background-color:'.$title_background_color.';'; } ?>">
			<?php if($responsive_title_image == 'yes' && $title_image != "" && $show_title_image == true){ echo '<img src="'.$title_image.'" alt="title" />'; } ?>
			<?php if(!get_post_meta($id, "qode_show-page-title-text", true)) { ?>
				<div class="title_holder">
					<div class="container">
						<div class="container_inner clearfix">
							<h1><?php single_cat_title(''); ?></h1>
						</div>
					</div>
				</div>
			<?php } ?>
	</div>
	
	<?php if($qode_options_satellite['show_back_button'] == "yes") { ?>
		<a id='back_to_top' href='#'>
			<span class="icon_holder small_icon nohover"><span class="icon_inner"><span class="icon white arrow_up_in_circle">&nbsp;</span></span></span>
			<span class="icon_holder small_icon hover"><span class="icon_inner"><span class="icon white arrow_up_in_circle_fill">&nbsp;</span></span></span>
		</a>
	<?php } ?>
	
	<?php
		$revslider = get_post_meta($id, "qode_revolution-slider", true);
		if (!empty($revslider)){ ?>
			<div class="slider"><div class="slider_inner">
			<?php echo do_shortcode($revslider); ?>
			</div></div>
		<?php
		}
		?>
	
		<div class="container">
		<div class="container_inner clearfix">
				<?php if(($sidebar == "default")||($sidebar == "")) : ?>
				<?php switch ($qode_options_satellite['blog_style']) {
					case 1: ?>
							<div class="blog_holder v2">
								<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
								<?php 
									get_template_part('blog_full_image', 'loop');
								?>
								<?php endwhile; ?>
								<?php if($qode_options_satellite['pagination'] != "0") : ?>
									<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
								<?php endif; ?>
								<?php else: //If no posts are present ?>
									<div class="entry">                        
											<p><?php _e('No posts were found.', 'qode'); ?></p>    
									</div>
								<?php endif; ?>
							</div>
					 <?php	break;
					case 2: ?>
						<div class="blog_holder blog_small_layout">
							<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
								<?php 
									get_template_part('blog_small_image', 'loop');
								?>
							
							<?php endwhile; ?>
							<?php if($qode_options_satellite['pagination'] != "0") : ?>
								<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
							<?php endif; ?>
							<?php else: //If no posts are present ?>
									<div class="entry">                        
											<p><?php _e('No posts were found.', 'qode'); ?></p>    
									</div>
							<?php endif; ?>
						</div>
					<?php	break;
					case 3: ?>
						<div class="blog_holder massonary">
							<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
								<?php 
									get_template_part('blog_masonry', 'loop');
								?>
							<?php endwhile; ?>
							<?php else: //If no posts are present ?>
									<div class="entry">                        
											<p><?php _e('No posts were found.', 'qode'); ?></p>    
									</div>
							<?php endif; ?>
						</div>
						<?php if($qode_options_satellite['pagination'] != "0") : ?>
							<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
						<?php endif; ?>
					<?php	break;
						case 4: ?>
									<div class="blog_holder v2">
										<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
											<?php 
												get_template_part('blog_full_image_whole_post', 'loop');
											?>
										
										<?php endwhile; ?>
										<?php if($qode_options_satellite['pagination'] != "0") : ?>
											<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
										<?php endif; ?>
										<?php else: //If no posts are present ?>
												<div class="entry">                        
														<p><?php _e('No posts were found.', 'qode'); ?></p>    
												</div>
										<?php endif; ?>
									</div>
						<?php	break;
						
				} ?>
			<?php elseif($sidebar == "1" || $sidebar == "2"): ?>
				<div class="<?php if($sidebar == "1"):?>two_columns_66_33<?php elseif($sidebar == "2") : ?>two_columns_75_25<?php endif; ?> background_color_sidebar grid2 clearfix">
					<div class="column1">
						<div class="column_inner">
							<?php switch ($qode_options_satellite['blog_style']) {
							case 1: ?>
								<div class="blog_holder v2">
									<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
									<?php 
										get_template_part('blog_full_image', 'loop');
									?>
									<?php endwhile; ?>
									<?php if($qode_options_satellite['pagination'] != "0") : ?>
										<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
									<?php endif; ?>
									<?php else: //If no posts are present ?>
										<div class="entry">                        
												<p><?php _e('No posts were found.', 'qode'); ?></p>    
										</div>
									<?php endif; ?>
								</div>
							<?php	break;
							case 2: ?>
									<div class="blog_holder blog_small_layout">
										<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
											<?php 
												get_template_part('blog_small_image', 'loop');
											?>
										
										<?php endwhile; ?>
										<?php if($qode_options_satellite['pagination'] != "0") : ?>
											<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
										<?php endif; ?>
										<?php else: //If no posts are present ?>
												<div class="entry">                        
														<p><?php _e('No posts were found.', 'qode'); ?></p>    
												</div>
										<?php endif; ?>
									</div>
								<?php	break;
							case 3: ?>
								<div class="blog_holder massonary">
									<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
										<?php 
											get_template_part('blog_masonry', 'loop');
										?>
									<?php endwhile; ?>
									<?php else: //If no posts are present ?>
											<div class="entry">                        
													<p><?php _e('No posts were found.', 'qode'); ?></p>    
											</div>
									<?php endif; ?>
								</div>
								<?php if($qode_options_satellite['pagination'] != "0") : ?>
									<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
								<?php endif; ?>
							<?php	break;
							case 4: ?>
										<div class="blog_holder v2">
											<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
												<?php 
													get_template_part('blog_full_image_whole_post', 'loop');
												?>
											
											<?php endwhile; ?>
											<?php if($qode_options_satellite['pagination'] != "0") : ?>
												<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
											<?php endif; ?>
											<?php else: //If no posts are present ?>
													<div class="entry">                        
															<p><?php _e('No posts were found.', 'qode'); ?></p>    
													</div>
											<?php endif; ?>
										</div>
							<?php	break;
						}
						
						?>		
						</div>
					</div>
					<div class="column2">
						<?php get_sidebar(); ?>	
					</div>
				</div>
		<?php elseif($sidebar == "3" || $sidebar == "4"): ?>
				<div class="<?php if($sidebar == "3"):?>two_columns_33_66<?php elseif($sidebar == "4") : ?>two_columns_25_75<?php endif; ?> background_color_sidebar grid2 clearfix">
					<div class="column1">
					<?php get_sidebar(); ?>	
					</div>
					<div class="column2">
						<div class="column_inner">
								<?php switch ($qode_options_satellite['blog_style']) {
								case 1: ?>
									<div class="blog_holder v2">
										<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
										<?php 
											get_template_part('blog_full_image', 'loop');
										?>
										<?php endwhile; ?>
										<?php if($qode_options_satellite['pagination'] != "0") : ?>
											<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
										<?php endif; ?>
										<?php else: //If no posts are present ?>
											<div class="entry">                        
													<p><?php _e('No posts were found.', 'qode'); ?></p>    
											</div>
										<?php endif; ?>
									</div>
								 <?php	break;
								case 2: ?>
										<div class="blog_holder blog_small_layout">
											<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
												<?php 
													get_template_part('blog_small_image', 'loop');
												?>
											
											<?php endwhile; ?>
											<?php if($qode_options_satellite['pagination'] != "0") : ?>
												<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
											<?php endif; ?>
											<?php else: //If no posts are present ?>
													<div class="entry">                        
															<p><?php _e('No posts were found.', 'qode'); ?></p>    
													</div>
											<?php endif; ?>
										</div>
								<?php	break;
								case 3: ?>
									<div class="blog_holder massonary">
										<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
											<?php 
												get_template_part('blog_masonry', 'loop');
											?>
										<?php endwhile; ?>
										<?php else: //If no posts are present ?>
												<div class="entry">                        
														<p><?php _e('No posts were found.', 'qode'); ?></p>    
												</div>
										<?php endif; ?>
									</div>
									<?php if($qode_options_satellite['pagination'] != "0") : ?>
										<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
									<?php endif; ?>
								<?php	break;
								case 4: ?>
										<div class="blog_holder v2">
											<?php if(have_posts()) : while ( have_posts() ) : the_post(); ?>
												<?php 
													get_template_part('blog_full_image_whole_post', 'loop');
												?>
											
											<?php endwhile; ?>
											<?php if($qode_options_satellite['pagination'] != "0") : ?>
												<?php pagination($wp_query->max_num_pages, $blog_page_range, $paged); ?>
											<?php endif; ?>
											<?php else: //If no posts are present ?>
													<div class="entry">                        
															<p><?php _e('No posts were found.', 'qode'); ?></p>    
													</div>
											<?php endif; ?>
										</div>
								<?php	break;
								
						}
						
						?>		
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	
<?php get_footer(); ?>