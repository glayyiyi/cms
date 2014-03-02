<?php

// add custom menu fields to menu
add_filter( 'wp_setup_nav_menu_item', 'qode_add_custom_nav_fields' );

// save menu custom fields
add_action( 'wp_update_nav_menu_item', 'qode_update_custom_nav_fields', 10, 3 );

// edit menu walker
add_filter( 'wp_edit_nav_menu_walker', 'qode_edit_walker', 10, 2 );


function qode_add_custom_nav_fields( $menu_item ) {
	
	$menu_item->nolink = get_post_meta( $menu_item->ID, '_menu_item_nolink', true );
	$menu_item->hide = get_post_meta( $menu_item->ID, '_menu_item_hide', true );
	$menu_item->recentpost = get_post_meta( $menu_item->ID, '_menu_item_recentpost', true );
	$menu_item->numberofposts = get_post_meta( $menu_item->ID, '_menu_item_numberofposts', true );
	$menu_item->featuredpostscategory = get_post_meta( $menu_item->ID, '_menu_item_featuredpostscategory', true );
	$menu_item->type_menu = get_post_meta( $menu_item->ID, '_menu_item_type_menu', true );
	$menu_item->icon = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
	return $menu_item;
   
}

/**
 * Save menu custom fields
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function qode_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
		
		$check = array('nolink', 'hide', 'recentpost', 'numberofposts', 'featuredpostscategory', 'type_menu', 'icon');
			
		foreach ( $check as $key )
		{
			if(!isset($_POST['menu-item-'.$key][$menu_item_db_id]))
			{
				$_POST['menu-item-'.$key][$menu_item_db_id] = "";
			}
			
			$value = $_POST['menu-item-'.$key][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_menu_item_'.$key, $value );
		}
		

}

/**
 * Define new Walker edit
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function qode_edit_walker($walker,$menu_id) {

	return 'Walker_Nav_Menu_Edit_Custom';
		
}

include_once('edit_custom_walker.php');


/* Custom WP_NAV_MENU function for top navigation */

if (!class_exists('qode_type1_walker_nav_menu')) {
	class qode_type1_walker_nav_menu extends Walker_Nav_Menu {
		
	// add classes to ul sub-menus
		function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
			{
					$id_field = $this->db_fields['id'];
					if ( is_object( $args[0] ) ) {
							$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
					}
					return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
			}

		function start_lvl( &$output, $depth = 0, $args = array() ) {
			
			$indent = str_repeat("\t", $depth);
			if($depth == 0){
				$out_div = '<div class="second"><div class="inner">';
			}else{
				$out_div = '';
			}
			
			// build html
			$output .= "\n" . $indent . $out_div  .'<ul>' . "\n";
		}
		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			
			if($depth == 0){
				$out_div_close = '</div></div>';
			}else{
				$out_div_close = '';
			}
			
			$output .= "$indent</ul>". $out_div_close ."\n";
		}

		// add main/sub classes to li's and links
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;
			global $qode_options_satellite;
			$sub = "";
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
			if($depth==0 && $args->has_children) : 
				$sub = ' has_sub';
			endif;
			if($depth==1 && $args->has_children) : 
				$sub = 'sub';
			endif;
			
			
			$active = "";
			
			// depth dependent classes
			if ((($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0)) && ($qode_options_satellite['page_transitions'] == "0")):
				
					$active = 'active';
				
			endif;
		
			
			// passed classes
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			
			$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
			
			//menu type class
			$menu_type = "";
			if($depth==0){
				if($item->type_menu == "wide"){
					$menu_type = " wide";
				}elseif($item->type_menu == "wide_icons"){
					$menu_type = " wide icons";
				}else{
					$menu_type = " narrow";
				}
			}
			
			
			// build html
			$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $class_names . ' ' . $active . $sub . $menu_type .'">';
			
			$current_a = "";
			// link attributes
			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			if (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0) ):
			$current_a .= ' current ';
			endif;
			$attributes .= ' class="'. $current_a . '"';
			$item_output = $args->before;
			if($item->hide == ""){
				if($item->nolink == ""){
					$item_output .= '<a'. $attributes .'>';
					if($item->icon != ""){$icon = $item->icon; } else{ $icon = "blank"; }
					$item_output .= '<i class="menu_icon '.$icon.'"></i>';
				} else{
					$item_output .= '<h5>';
				}
				$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
				$item_output .= $args->link_after;
				if($item->nolink == ""){
					$item_output .= '</a>';
				}else{
					$item_output .= '</h5>';
				}
				
				if($item->recentpost != "" && $depth == 1){
					$item_output .="<div class='flexslider'><ul class='slides'>";
					$post_per_page = "-1";
					if($item->numberofposts != ""){
						$post_per_page = $item->numberofposts;
					}
					$cat = "";
					if($item->featuredpostscategory != ""){
						 $cat= $item->featuredpostscategory;
					}
					$q = new WP_Query( 
					   array('posts_per_page'=>$post_per_page, 'orderby'=>'date', 'order'=>'DESC', 'cat'=> $cat)
					);	
					while($q->have_posts()) : $q->the_post();
						$item_output .="<li>";
							$item_output .= "<a href='" . get_permalink() . "'>" . get_the_post_thumbnail(get_the_id(),'menu-featured-post') . "</a>";
							$item_output .="<h5><a href='" . get_permalink() . "'>". get_the_title(get_the_id()) ."</a></h5>";
							$item_output .="<span class='menu_recent_post'>". get_the_time('d M');
							$num_comments = get_comments_number();
							if ( comments_open() ) {
								if ( $num_comments == 0 ) {
									$comments = __('No Comments','qode');
								} elseif ( $num_comments > 1 ) {
									$comments = $num_comments . __(' Comments','qode');
								} else {
									$comments = __('1 Comment', 'qode');
								}
								$item_output .= ' <a href="' . get_comments_link() .'">'. $comments.'</a>';
							} else {
								$item_output .= __('Comments are off for this post.');
							}
							$item_output .= "</span>";
						$item_output .="</li>";
					endwhile;
					
					$item_output .="</ul></div>";
					wp_reset_query();
				}
			}
			$item_output .= $args->after;
			
		
			
			// build html
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
		
	}
}


/* Custom WP_NAV_MENU function for mobile navigation */

if (!class_exists('qode_type2_walker_nav_menu')) {
	class qode_type2_walker_nav_menu extends Walker_Nav_Menu {
		
	// add classes to ul sub-menus
		function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
			{
					$id_field = $this->db_fields['id'];
					if ( is_object( $args[0] ) ) {
							$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
					}
					return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
			}

		function start_lvl( &$output, $depth = 0, $args = array() ) {
			
			$indent = str_repeat("\t", $depth);
			
			// build html
			$output .= "\n" . $indent  .'<ul class="sub_menu">' . "\n";
		}
		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
		
			$output .= "$indent</ul>" ."\n";
		}

		// add main/sub classes to li's and links
		 function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;
			global $qode_options_satellite;
			$sub = "";
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
			if(($depth >=0 && $args->has_children) || ($depth >=0 && $item->recentpost != "")) : 
				$sub = ' has_sub';
			endif;
			
			$active = "";
			
			// depth dependent classes
			if ((($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0)) && ($qode_options_satellite['page_transitions'] == "0")):
				
					$active = 'active';
				
			endif;
		
			
			// passed classes
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			
			$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
			
			// build html
			$output .= $indent . '<li id="mobile-menu-item-'. $item->ID . '" class="' . $class_names . ' ' . $active . $sub .'">';
			
			$current_a = "";
			// link attributes
			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			if (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0) ):
			$current_a .= ' current ';
			endif;
			
			$attributes .= ' class="'. $current_a . '"';
			$item_output = $args->before;
			if($item->hide == ""){
				if($item->nolink == ""){
					$item_output .= '<a'. $attributes .'>';
				}else{
					$item_output .= '<h5>';
				}
				$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
				$item_output .= $args->link_after;
				if($item->nolink == ""){
					$item_output .= '<span class="mobile_arrow"></span></a>';
				}else{
					$item_output .= '<span class="mobile_arrow"></span></h5>';
				}
				
				if($item->recentpost != "" && $depth == 1){
					$item_output .="<ul class='sub_menu'>";
					$post_per_page = "-1";
					if($item->numberofposts != ""){
						$post_per_page = $item->numberofposts;
					}
					$cat = "";
					if($item->featuredpostscategory != ""){
						 $cat= $item->featuredpostscategory;
					}
					$q = new WP_Query( 
					   array('posts_per_page'=>$post_per_page, 'orderby'=>'date', 'order'=>'DESC', 'cat'=> $cat)
					);	
					while($q->have_posts()) : $q->the_post();
						$item_output .="<li>";
							$item_output .="<a href='" . get_permalink() . "'><span>". get_the_title(get_the_id()) ."</span></a>";
						$item_output .="</li>";
					endwhile;
					
					$item_output .="</ul>";
					wp_reset_query();
				}
			}
			$item_output .= $args->after;
			
			// build html
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
		
	}
}
