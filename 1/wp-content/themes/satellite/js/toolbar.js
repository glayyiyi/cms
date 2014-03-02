$j(window).load(function(){
	setTimeout(function(){
		$j("#panel").animate({marginLeft: "0px"});
		$j("a.open").addClass('opened');
		$j("#panel").addClass('opened-panel');
	},1000);
});


$j(document).ready(function() {
	
	$j('#panel select').sSelect();
	
	$j("#panel a.open").click(function(e){
		e.preventDefault();
		var margin_left = $j("#panel").css("margin-left");
		if (margin_left == "-208px"){
			$j("#panel").animate({marginLeft: "0px"});
			$j("#panel").addClass('opened-panel');
			$j(this).addClass('opened');
		}
		else{
			$j("#panel").animate({marginLeft: "-208px"});
			$j(this).removeClass('opened');
			$j("#panel").removeClass('opened-panel');
		}
		return false;
	});
	
	// $j('#tootlbar_header_top').change(function(){
		// if($j(this).val() != ""){
			
    	// $j.post(root+'updatesession.php', {header_top : $j(this).val()}, function(data){
					// location.reload();
			// });
		// }
	// });
	
	$j('#tootlbar_pattern').change(function(){
		jQuery('#tootlbar_pattern_css').remove();
		
		if($j(this).val() != "no"){
			$j('#tootlbar_boxed').getSetSSValue('boxed');
			$j('#tootlbar_background').getSetSSValue('no');
			$j('body').addClass('boxed');
			newSkin ="body.boxed .wrapper { \
									background-image: url('http://demo.qodeinteractive.com/satellite/demo_images/"+$j(this).val()+".png'); \
									background-position: 0px 0px; \
									background-repeat: repeat; \
								} \
							";
			jQuery('body').append('<style id="tootlbar_pattern_css" type="text/css">'+newSkin+'</style>'); 
			
		}else{
			newSkin ="body { \
									background-image: none; \
								} \
							";
			jQuery('body').append('<style id="tootlbar_pattern_css" type="text/css">'+newSkin+'</style>'); 
		}
	});
	
	$j('#tootlbar_background').change(function(){
	
	jQuery('#tootlbar_background_css').remove();
		if($j(this).val() != "no"){
			$j('#tootlbar_boxed').getSetSSValue('boxed');
			$j('#tootlbar_pattern').getSetSSValue('no');
			$j('body').addClass('boxed');
			newSkin ="body.boxed .wrapper { \
									background-image: url('http://demo.qodeinteractive.com/satellite/demo_images/"+$j(this).val()+".jpg'); \
									background-position: 0px 0px; \
									background-repeat: repeat; \
									background-attachment: fixed; \
								} \
							";
			jQuery('body').append('<style id="tootlbar_background_css" type="text/css">'+newSkin+'</style>'); 
			
		}else{
			newSkin ="body { \
									background-image: none; \
								} \
							";
			jQuery('body').append('<style id="tootlbar_background_css" type="text/css">'+newSkin+'</style>'); 
		}
	});
	
	$j('#tootlbar_boxed').change(function(){
	
		$j('body').removeClass('boxed');
		$j('body').addClass($j(this).val());
		
		if($j(this).val() != "boxed"){
			$j('#tootlbar_pattern').getSetSSValue('no');
			$j('#tootlbar_background').getSetSSValue('no');
		}
		
		
	});
	
	
	$j('#tootlbar_colors .color').each(function(){
		$j(this).on('click',function(){
				$j('#tootlbar_colors .color').removeClass('active');
				$j(this).addClass('active');
				var color = $j(this).data('color');
				
				if ($j("#toolbar_colors_css").length > 0){
					$j("#toolbar_colors_css").remove();
				}
				
				newSkin ="table th,\
									table tr:nth-child(odd) td,\
									.blog_holder article.blockquote .post_content_holder:hover .blockquote_holder,\
									.blog_holder article.link .post_content_holder:hover .link_holder,\
									.filter_holder ul li.active,\
									.button,\
									#submit_comment,\
									.load_more a,\
									.icon_inner,\
									.progress_bars_with_image_content .bar .bar_noactive, \
									.progress_bars_with_image_content .bar .bar_active,\
									.highlight,\
									.tabs .tabs-nav li.active a,\
									.accordion_holder.accordion h3.ui-state-active > span,\
									.accordion_holder.accordion h3:hover > span,\
									.progress_bars .progress_content,\
									.price_table_inner.active,\
									.price_table_inner:hover,\
									.list.number_with_background ul>li:before,\
									.progress_bars_with_image_content .bar,\
									.footer_top .social_menu.transparent li a,\
									.pie_graf_legend ul li .color_holder,\
									.line_graf_legend ul li .color_holder,\
									.widget .tagcloud a:hover,\
									.mejs-overlay-loading,\
									.mejs-container .mejs-controls,\
									.mejs-captions-text,\
									.mejs-controls .mejs-sourcechooser-button .mejs-sourcechooser-selector,\
									.mejs-postroll-layer,\
									.mejs-postroll-close\
									{ \
									background-color: "+color+"; \
									} \
									h2 a, \
									h3 a,\
									h5 a,\
									a:hover,\
									p a:hover,\
									.header_top #lang_sel ul > li:hover > a,\
									.header_top #lang_sel_click ul > li:hover > a,\
									.header_top #lang_sel ul li ul li a:hover,\
									.header_top #lang_sel_click ul li ul li a:hover,\
									.header_top #lang_sel_list ul li a,\
									.header_top #lang_sel_list ul li a:visited,\
									.header_top #lang_sel_list ul li a:hover,\
									.header_top #lang_sel_list ul li a.lang_sel_other:hover,\
									nav.main_menu > ul > li:hover > a,\
									nav.main_menu > ul > li.active > a,\
									.drop_down .second .inner ul li:hover a,\
									.drop_down .second .inner ul li.sub ul li:hover a,\
									.drop_down .wide .second ul li a,\
									.drop_down .wide .second .inner ul li h5,\
									.drop_down .wide .second ul li a:hover,\
									.drop_down .wide .second .inner ul li.sub ul li a:hover,\
									.drop_down .wide .second .inner ul li.sub .flexslider ul li a:hover,\
									.drop_down .wide .second ul li .flexslider ul li  a:hover,\
									.drop_down .wide .second .inner ul li .flexslider h5,\
									.drop_down .wide .second .inner ul li.sub .flexslider ul li  h5 a,\
									.drop_down .wide .second .inner ul li .flexslider ul li  h5 a,\
									.drop_down .wide.icons  .second i,\
									.blog_holder article .post_info .date,\
									.blog_holder article .post_info .blog_like:hover span,\
									.portfolio_like:hover span,\
									.blog_holder article .post_info .blog_like a.liked span,\
									.portfolio_like a.liked span,\
									.comment_holder .comment .text .replay, \
									.comment_holder .comment .text .comment-reply-link,\
									.pagination ul li span,\
									.pagination ul li.next a:hover,\
									.pagination ul li.prev a:hover,\
									.pagination ul li.last a:hover,\
									.pagination ul li.first a:hover,\
									.pagination ul li a:hover,\
									.portfolio_navigation .portfolio_prev a:hover,\
									.portfolio_navigation .portfolio_next a:hover,\
									.counter_holder span.counter,\
									.percentage,\
									.progress_bars .progress_number,\
									.price_table_inner ul li.table_title,\
									.price_in_table .value,\
									.price_in_table .price,\
									.price_in_table .mark,\
									.dropcap,\
									.ordered ol li,\
									.list.circle ul>li,\
									.list.number ul>li,\
									.list.number_with_background ul>li,\
									.list.number ul>li:before,\
									.list.number_with_background ul>li:before,\
									.social_share_holder:hover .social_share_icon,\
									.social_share_dropdown ul li span.share_title,\
									.social_share_dropdown ul li:hover .share_text,\
									.latest_post_bottom .latest_post_like a.liked span,\
									.latest_post_bottom .latest_post_like a:hover span,\
									aside .widget li a:hover,\
									.widget.widget_rss li a.rsswidget:hover,\
									aside #lang_sel_list ul li a:hover,\
									aside #lang_sel_list a.lang_sel_sel:hover,\
									aside #lang_sel_list ul li a.lang_sel_sel,\
									aside #lang_sel ul li ul li:hover a,\
									aside #lang_sel_click ul li ul li:hover a,\
									aside #lang_sel_click a.lang_sel_sel:hover,\
									aside #lang_sel a.lang_sel_sel:hover,\
									.widget .tagcloud a,\
									.footer_top ul li a:hover,\
									.footer_top .widget li a:hover,\
									.widget.widget_rss li a.rsswidget:hover,\
									.footer_top #lang_sel_list ul li a:hover,\
									.footer_top #lang_sel_list a.lang_sel_sel:hover,\
									.footer_top #lang_sel_list ul li a.lang_sel_sel,\
									.footer_top #lang_sel ul li ul li:hover a,\
									.footer_top #lang_sel_click ul li ul li:hover a,\
									.footer_top #lang_sel_click a.lang_sel_sel:hover,\
									.footer_top #lang_sel a.lang_sel_sel:hover,\
									#lang_sel_footer ul li a.lang_sel_sel,\
									#lang_sel_footer ul li a:hover,\
									#lang_sel_footer ul li a.lang_sel_sel:hover,\
									.stylish-select .newListHover \
									{ \
										color: "+color+"; \
									}\
									.blog_holder article .post_info .blog_like:hover,\
									.portfolio_like:hover,\
									.blog_holder article.blockquote .blockquote_holder,\
									.blog_holder article.link .link_holder,\
									#respond textarea:focus,\
									#respond input[type='text']:focus,\
									.contact_form input[type='text']:focus,\
									.contact_form  textarea:focus,\
									.pagination ul li span,\
									.pagination ul li.next a:hover,\
									.pagination ul li.prev a:hover,\
									.pagination ul li.last a:hover,\
									.pagination ul li.first a:hover,\
									.filter_holder ul li.active,\
									.button.with_border,\
									.icon_holder,\
									.tabs .tabs-nav li.active a,\
									.accordion_holder.accordion h3 > span,\
									.price_table_inner,\
									.dropcap,\
									.list.number ul>li:before,\
									.list.number_with_background ul>li:before,\
									.message,\
									.call_to_action,\
									.social_menu li a,\
									.widget .tagcloud a,\
									.side_menu .widget.widget_search form.form_focus,\
									.widget.widget_search form.form_focus,\
									.header_top .searchform.form_focus,\
									.footer_top_inner .searchform.form_focus,\
									.open \
									{\
										border-color: "+color+";\
									}\
									.projects_holder article span.text_holder, .portfolio_gallery .image_hover{\
										background-color: rgba("+hexToRgb(color).r+","+hexToRgb(color).g+","+hexToRgb(color).b+", 0.6); \
									}\
									";
				jQuery('body').append('<style id="toolbar_colors_css" type="text/css">'+newSkin+'</style>'); 
				
		});
	});
	
	
}); 

function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}