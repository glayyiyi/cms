	
<?php 
global $qode_options_satellite;

?>
	<div class="column_inner">
		<aside>
			<?php	
			$sidebar = "";
			
			if(get_post_meta($id, 'qode_choose-sidebar', true) != ""){
				$sidebar = get_post_meta($id, 'qode_choose-sidebar', true);
			}else{
				if (is_singular("post")) {
					if($qode_options_satellite['blog_single_sidebar_custom_display'] != ""){
						$sidebar = $qode_options_satellite['blog_single_sidebar_custom_display'];
					}else{
						$sidebar = "Sidebar";
					}
				} else {
					$sidebar = "Sidebar Page";
					
				}
			}
			?>
				
			<?php if(function_exists('dynamic_sidebar') && dynamic_sidebar($sidebar)) : 
			endif;  ?>
		</aside>
	</div>
