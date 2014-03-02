<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php 
global $qode_options_satellite;
global $wp_query;
$disable_qode_seo = "";
$seo_title = "";
if (isset($qode_options_satellite['disable_qode_seo'])) $disable_qode_seo = $qode_options_satellite['disable_qode_seo'];
if ($disable_qode_seo != "yes") {
	$seo_title = get_post_meta($wp_query->get_queried_object_id(), "qode_seo_title", true);
	$seo_description = get_post_meta($wp_query->get_queried_object_id(), "qode_seo_description", true);
	$seo_keywords = get_post_meta($wp_query->get_queried_object_id(), "qode_seo_keywords", true);
}
?>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php
	$responsiveness = "yes";
	if (isset($qode_options_satellite['responsiveness'])) $responsiveness = $qode_options_satellite['responsiveness'];
	if($responsiveness != "no"):
	?>
	<meta name=viewport content="width=device-width,initial-scale=1">
	<?php 
	endif;
	?>
	<title><?php if($seo_title) { ?><?php bloginfo('name'); ?> | <?php echo $seo_title; ?><?php } else {?><?php bloginfo('name'); ?> | <?php is_front_page() ? bloginfo('description') : wp_title(''); ?><?php } ?></title>
	<?php if ($disable_qode_seo != "yes") { ?>
	<?php if($seo_description) { ?>
	<meta name="description" content="<?php echo $seo_description; ?>">
	<?php } else if($qode_options_satellite['meta_description']){ ?>
	<meta name="description" content="<?php echo $qode_options_satellite['meta_description'] ?>">
	<?php } ?>
	<?php if($seo_keywords) { ?>
	<meta name="keywords" content="<?php echo $seo_keywords; ?>">
	<?php } else if($qode_options_satellite['meta_keywords']){ ?>
	<meta name="keywords" content="<?php echo $qode_options_satellite['meta_keywords'] ?>">
	<?php } ?>
	<?php } ?>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo $qode_options_satellite['favicon_image']; ?>">
	<!--[if gte IE 9]>
		<style type="text/css">
			.gradient {
				 filter: none;
			}
		</style>
	<![endif]-->

	<?php wp_head(); ?>
	
</head>

<body <?php body_class(); ?>>
	
	
	<?php
		$loading_animation = true;
		if (isset($qode_options_satellite['loading_animation'])){ if($qode_options_satellite['loading_animation'] == "off") { $loading_animation = false; }};
	
		if (isset($qode_options_satellite['loading_image'])){ $loading_image = $qode_options_satellite['loading_image'];}else( $loading_image =  get_template_directory_uri().'/img/ajax-loader.gif' );
	?>
	<?php if($loading_animation){ ?>
		<div class="ajax_loader"><div class="ajax_loader_1"><div class="ajax_loader_2"><img src="<?php echo $loading_image; ?>" alt="" /></div></div></div>
	<?php } ?>
	<?php 
		$enable_side_area = "yes";
		if (isset($qode_options_satellite['enable_side_area'])){ if($qode_options_satellite['enable_side_area'] == "no") { $enable_side_area = "no"; }};
	?>
	<?php if($enable_side_area != "no") { ?>
		<section class="side_menu right">
			<div class="side_menu_title">
				 <h5><?php if(isset($qode_options_satellite['side_area_title']) && $qode_options_satellite['side_area_title'] != "") { echo $qode_options_satellite['side_area_title']; } else { _e('Side Menu', 'qode'); } ?></h5>
				<a href="#" target="_self" class="close_side_menu"></a>
			</div>
			<?php dynamic_sidebar('sidearea'); ?>
		</section>
	<?php } ?>
	<div class="wrapper">
	<div class="wrapper_inner">
	<!-- Google Analytics start -->
	<?php if (isset($qode_options_satellite['google_analytics_code'])){
				if($qode_options_satellite['google_analytics_code'] != "") { 
	?>
		<script>
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo $qode_options_satellite['google_analytics_code']; ?>']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	<?php }
		}
	?>
	<!-- Google Analytics end -->
	
<?php
	$header_in_grid = true;
	if(isset($qode_options_satellite['header_in_grid'])){if ($qode_options_satellite['header_in_grid'] == "no") $header_in_grid = false;}
	
	$menu_in_center = false;
	if(isset($qode_options_satellite['menu_in_center'])){if ($qode_options_satellite['menu_in_center'] == "yes") $menu_in_center = true;}
	
?>

<header>
	<div class="header_inner clearfix">
	<?php 
		$display_header_top = "yes";
		if(isset($qode_options_satellite['header_top_area'])){
			$display_header_top = $qode_options_satellite['header_top_area'];
		}
		if (!empty($_SESSION['qode_header_top'])){
			$display_header_top = $_SESSION['qode_header_top'];
		}
		
	?>
	<?php if($display_header_top == "yes"){ ?>
		<div class="header_top clearfix">
			<?php if($header_in_grid){ ?>
				<div class="container">
					<div class="container_inner clearfix">
			<?php } ?>
					<div class="left">
						<?php	
							dynamic_sidebar('header_left'); 
						?>
					</div>
					<div class="right">
						<?php	
							dynamic_sidebar('header_right'); 
						?>
					</div>
				<?php if($header_in_grid){ ?>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<div class="header_bottom">
		<?php if($header_in_grid){ ?>
				<div class="container">
					<div class="container_inner clearfix">
			<?php } ?>
					<div class="header_inner_left">
						<div class="mobile_menu_button"><span>&nbsp;</span></div>
						<div class="logo_wrapper">
							<div class="logo">
									<a href="<?php echo home_url(); ?>/">
											<img src="<?php echo $qode_options_satellite['logo_image']; ?>" alt="Logo"/>
									</a>
							</div>
						</div>
					</div>
					<?php if($enable_side_area != "no") { ?>
						<div class="header_inner_right">
								<div class="side_menu_button_wrapper right"><div class="side_menu_button"><a href="#"></a></div></div>
						</div>
					<?php } ?>
					<nav class="main_menu drop_down <?php if(!$menu_in_center){ echo 'right';} ?>">
					<?php
						
						wp_nav_menu( array( 'theme_location' => 'top-navigation' , 
																'container'  => '', 
																'container_class' => '', 
																'menu_class' => '', 
																'menu_id' => '',
																'fallback_cb' => 'top_navigation_fallback',
																'link_before' => '<span>',
																'link_after' => '</span>',
																'walker' => new qode_type1_walker_nav_menu()
					 ));
					?>
					</nav>
					<nav class="mobile_menu">
						<?php			
							wp_nav_menu( array( 'theme_location' => 'top-navigation' , 
																	'container'  => '', 
																	'container_class' => '', 
																	'menu_class' => '', 
																	'menu_id' => '',
																	'fallback_cb' => 'top_navigation_fallback',
																	'link_before' => '<span>',
																	'link_after' => '</span>',
																	'walker' => new qode_type2_walker_nav_menu()
						 ));
						?>
					</nav>
			<?php if($header_in_grid){ ?>
					</div>
				</div>
			<?php } ?>
	</div>
	</div>
</header>
	<div class="content">
		<?php 
global $wp_query;
$id = $wp_query->get_queried_object_id();
$animation = get_post_meta($id, "qode_show-animation", true);
if (!empty($_SESSION['qode_animation']) && $animation == "")
	$animation = $_SESSION['qode_animation'];

?>
			<?php if($qode_options_satellite['page_transitions'] == "1" || $qode_options_satellite['page_transitions'] == "2" || $qode_options_satellite['page_transitions'] == "3" || $qode_options_satellite['page_transitions'] == "4" || ($animation == "updown") || ($animation == "fade") || ($animation == "updown_fade") || ($animation == "leftright")){ ?>
				<div class="meta">				
					<?php if($seo_title){ ?>
						<div class="seo_title"><?php bloginfo('name'); ?> | <?php echo $seo_title; ?></div>
					<?php } else{ ?>
						<div class="seo_title"><?php bloginfo('name'); ?> | <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></div>
					<?php } ?>
					<?php if($seo_description){ ?>
						<div class="seo_description"><?php echo $seo_description; ?></div>
					<?php } else if($qode_options_satellite['meta_description']){?>
						<div class="seo_description"><?php echo $qode_options_satellite['meta_description']; ?></div>
					<?php } ?>
					<?php if($seo_keywords){ ?>
						<div class="seo_keywords"><?php echo $seo_keywords; ?></div>
					<?php }else if($qode_options_satellite['meta_keywords']){?>
						<div class="seo_keywords"><?php echo $qode_options_satellite['meta_keywords']; ?></div>
					<?php }?>
					<span id="qode_page_id"><?php echo $wp_query->get_queried_object_id(); ?></span>
					<div class="body_classes"><?php echo implode( ',', get_body_class()); ?></div>
				</div>
			<?php } ?>
			<div class="content_inner <?php echo $animation;?> ">
				
			