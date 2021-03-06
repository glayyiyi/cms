var $j = jQuery.noConflict();
var logo_height;
var $scroll = 0;
var menu_dropdown_height_set = false;


$j(document).ready(function() {
	"use strict";

	$j('.content').css('min-height',$j(window).height()-$j('header').height()-$j('footer').height() + 90); // min height for content to cover side menu bar, 90 is negative margin on content
	initSideMenu();
	setDropDownMenuPosition();
	initDropDownMenu();
	initFlexSlider();
	fitVideo();
	initToCounter();
	initCounter();
	initElementsAnimation();
	initMobileMenu();
	initAccordion();
	initAccordionContentLink();
	initPieChart();
	prettyPhoto();
	initProgressBars();
	initListAnimation();
	loadMore();
	initMessages();
	initProgressBarsIcon();
	backButtonShowHide();
	backToTop();
	initNiceScroll();
	showElementFadeIn();
	placeholderReplace();
	addPlaceholderSearchWidget();
	initParallax(parallax_speed);
	initParallaxTitle();
	socialShare();
	$j([theme_root+'css/img/qode_like_blue.png',
		theme_root+'css/img/qode_like_blue@1_5x.png',
		theme_root+'css/img/qode_like_blue@2x.png',
		theme_root+'css/img/social_share_blue.png',
		theme_root+'css/img/social_share_blue@1_5x.png',
		theme_root+'css/img/social_share_blue@2x.png',
		theme_root+'css/img/mobile_menu_arrow_down.png',
		theme_root+'css/img/blockquote_mark_white.png',
		theme_root+'css/img/blockquote_mark_white@1_5x.png',
		theme_root+'css/img/blockquote_mark_white@2x.png',
		theme_root+'css/img/link_mark_white.png',
		theme_root+'css/img/link_mark_white@1_5x.png',
		theme_root+'css/img/link_mark_white@2x.png']).preload();

	$j('.widget #searchform').mousedown(function(){$j(this).addClass('form_focus')}).focusout(function(){$j(this).removeClass('form_focus')});
});

$j(window).load(function(){
	"use strict";

	$j('.main_menu li:has(div.second)').doubleTapToGo(); // load script to close menu on touch devices
	$j('.side_menu').css('visibility', 'visible');
	logo_height = $j('.logo img').height();
	
	if($j(window).width() > 1000){
		headerSize($scroll);
	}else{
		logoSizeOnSmallScreens();
	}
	$j('.logo a').css({'visibility':'visible'});
	
	initPortfolioSingleInfo();
	initPortfolio();
	fitAudio();
	initBlog();
	initTabs();
	initTestimonials();
});

$j(window).scroll(function() {
	"use strict";
	
	$scroll = $j(window).scrollTop();
	if($j(window).width() > 1000){
		headerSize($scroll);
	}
	
	$j('.touch .drop_down > ul > li').mouseleave();
	$j('.touch .drop_down > ul > li').blur();
	
});

$j(window).resize(function() {
	"use strict";
	if($j(window).width() < 1000){
		logoSizeOnSmallScreens();
	}
	setDropDownMenuPosition();
	initDropDownMenu();
	initBlog();
	fitAudio();
	initTestimonials();
});

function initParallaxTitle(){
	"use strict";

	if(($j('.title').length > 0) && ($j('.touch').length == 0)){		
		if($j('.title.has_fixed_background').length){
			var title_holder_height = $j('.title.has_fixed_background').height();
			var title_rate = (title_holder_height / 10000) * 3;
			
			var title_distance = $scroll - $j('.title.has_fixed_background').offset().top;
			var title_bpos = -(title_distance * title_rate);
			$j('.title.has_fixed_background').css({'background-position': 'center ' + title_bpos + 'px' });
		}
		var title = $j('.title_holder');
		title.css({ 'opacity' : (1 - $scroll/($j('.title').height()*0.6)) });
		
		$j(window).on('scroll', function() {
			title.css({ 'opacity' : (1 - $scroll/($j('.title').height()*0.6)) });
			
			if($j('.title.has_fixed_background').length){ 
				var title_distance = $scroll - $j('.title.has_fixed_background').offset().top;
				var title_bpos = -(title_distance * title_rate);
				$j('.title.has_fixed_background').css({'background-position': 'center ' + title_bpos + 'px' });
			}
		});
	}
}

function initSideMenu(){
	"use strict";

	$j('.side_menu_button_wrapper.right a, a.close_side_menu').on("click",function(e){
		e.preventDefault();
		if(!$j('.side_menu_button_wrapper.right a').hasClass('opened') || !$j('a.close_side_menu').hasClass('opened')){
			$j('.side_menu_button_wrapper.right a').addClass('opened');
			$j('a.close_side_menu').addClass('opened');
			$j('body').addClass('right_side_menu_opened');
		}else{
			$j('.side_menu_button_wrapper.right a').removeClass('opened');
			$j('a.close_side_menu').removeClass('opened');
			$j('body').removeClass('right_side_menu_opened');
		}
	});
}

function initDropDownMenu(){
	"use strict";
	
	var menu_items = $j('.drop_down > ul > li');
	
	menu_items.each( function(i) {
		if ($j(menu_items[i]).find('.second').length > 0) {
			if($j(menu_items[i]).hasClass('wide')){
				$j(this).find('.second').css('left',0);
				
				var row_number;
				if($j(this).find('.second > .inner > ul > li').length > 4){
					row_number = 4;
				}else{
					row_number = $j(this).find('.second > .inner > ul > li').length;
				} 
				
				var width = row_number*($j(this).find('.second > .inner > ul > li').width() + 30); //30 is left and right padding
				$j(this).find('.second > .inner > ul').width(width);
				
				var left_position = ($j(window).width() - 2 * ($j(window).width()-$j(this).find('.second').offset().left))/2 + (width+30)/2;

				$j(this).find('.second').css('left',-left_position);
			}
			
			if(!menu_dropdown_height_set){
				$j(menu_items[i]).data('original_height', $j(menu_items[i]).find('.second').height() + 'px');
				$j(menu_items[i]).find('.second').height(0);
			}
			
			if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
				$j(menu_items[i]).on("touchstart mouseenter",function(){
					$j(menu_items[i]).find('.second').css({'height': $j(menu_items[i]).data('original_height'), 'overflow': 'visible', 'visibility': 'visible', 'opacity': '1'});
				}).on("mouseleave", function(){
					$j(menu_items[i]).find('.second').css({'height': '0px','overflow': 'hidden', 'visivility': 'hidden', 'opacity': '0'});
				});
			
			}else{
					var config = {    
					interval: 50,
					over: function(){
						$j(menu_items[i]).find('.second').css({'height': $j(menu_items[i]).data('original_height'), 'overflow': 'visible', 'visibility': 'visible', 'opacity': '1'});
					},  
					timeout: 150,    
					out: function(){
						$j(menu_items[i]).find('.second').css({'height': '0px','overflow': 'hidden', 'visivility': 'hidden', 'opacity': '0'});
					}
				};
				$j(menu_items[i]).hoverIntent(config);
			}
			
			
		}
	});
	$j('.drop_down ul li.wide ul li a').on('click',function(){
		var $this = $j(this);
		setTimeout(function() {
			$this.mouseleave();
		}, 500);
		
	});
	
	menu_dropdown_height_set = true;
}


function setDropDownMenuPosition(){
	"use strict";

	var menu_items = $j(".drop_down > ul > li.narrow");
	menu_items.each( function(i) {

		var browser_width = $j(window).width()-16; // 16 is width of scroll bar
		var menu_item_position = $j(menu_items[i]).offset().left;
		var sub_menu_width = $j(menu_items[i]).find('.second .inner ul').width();
		var menu_item_from_left = browser_width - menu_item_position + 25; // 25 is right padding between menu elements
		var sub_menu_from_left;
			
		if($j(menu_items[i]).find('li.sub').length > 0){
			sub_menu_from_left = browser_width - menu_item_position - sub_menu_width + 25; // 30 is right padding between menu elements
		}
		
		if(menu_item_from_left < sub_menu_width || sub_menu_from_left < sub_menu_width){
			$j(menu_items[i]).find('.second').addClass('right');
			$j(menu_items[i]).find('.second .inner ul').addClass('right');
		}
	});
}

function initFlexSlider(){
	"use strict";
	
	$j('.flexslider').flexslider({
		animationLoop: true,
		controlNav: false,
		useCSS: false,
		pauseOnAction: true,
		pauseOnHover: true,
		slideshow: true,
		animation: 'slides',
		animationSpeed: 600,
		slideshowSpeed: 8000,
		start: function(){
			setTimeout(function(){$j(".flexslider").fitVids(); initNiceScroll();},100);
		}
	});
	
	$j('.flex-direction-nav a').click(function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		e.stopPropagation();
	});
}

function fitVideo(){
	"use strict";
	
	$j(".portfolio_images").fitVids();
	$j(".video_holder").fitVids();
	$j(".post_image_video").fitVids();
}

var $scrollHeight;
function initPortfolioSingleInfo(){
	"use strict";

	var $sidebar   = $j(".portfolio_single_follow");
	if($j(".portfolio_single_follow").length > 0){
	
		var offset = $sidebar.offset();
		$scrollHeight = $j(".portfolio_container").height();
		var $scrollOffset = $j(".portfolio_container").offset();
		var $window = $j(window);
		
		var $menuLineHeight = parseInt($j('.main_menu > ul > li > a').css('line-height'), 10);
		
		$window.scroll(function() {
			if($window.width() > 960){
				if ($window.scrollTop() + $menuLineHeight + 3 > offset.top) {
					if ($window.scrollTop() + $menuLineHeight + $sidebar.height() + 24 < $scrollOffset.top + $scrollHeight) {
						$sidebar.stop().animate({
							marginTop: $window.scrollTop() - offset.top + $menuLineHeight
						});
					} else {
						$sidebar.stop().animate({
							marginTop: $scrollHeight - $sidebar.height() - 24
						});
					}
				} else {
					$sidebar.stop().animate({
						marginTop: 0
					});
				}		
			}else{
				$sidebar.css('margin-top',0);
			}
		});
	}
}

function prettyPhoto(){
	"use strict";		

	$j('a[data-rel]').each(function() {
		$j(this).attr('rel', $j(this).data('rel'));
	});

	$j("a[rel^='prettyPhoto']").prettyPhoto({
		animation_speed: 'normal', /* fast/slow/normal */
		slideshow: false, /* false OR interval time in ms */
		autoplay_slideshow: false, /* true/false */
		opacity: 0.80, /* Value between 0 and 1 */
		show_title: true, /* true/false */
		allow_resize: true, /* Resize the photos bigger than viewport. true/false */
		default_width: 650,
		default_height: 400,
		counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
		theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
		hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
		wmode: 'opaque', /* Set the flash wmode attribute */
		autoplay: true, /* Automatically start videos: True/False */
		modal: false, /* If set to true, only the close button will close the window */
		overlay_gallery: false, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
		keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
		deeplinking: false,
		social_tools: false
	});
}

(function($) {
	"use strict";

	$.fn.countTo = function(options) {
		// merge the default plugin settings with the custom options
		options = $.extend({}, $.fn.countTo.defaults, options || {});

		// how many times to update the value, and how much to increment the value on each update
		var loops = Math.ceil(options.speed / options.refreshInterval),
		increment = (options.to - options.from) / loops;

		return $(this).each(function() {
			var _this = this,
			loopCount = 0,
			value = options.from,
			interval = setInterval(updateTimer, options.refreshInterval);

			function updateTimer() {
				value += increment;
				loopCount++;
				$(_this).html(value.toFixed(options.decimals));

				if (typeof(options.onUpdate) === 'function') {
					options.onUpdate.call(_this, value);
				}

				if (loopCount >= loops) {
					clearInterval(interval);
					value = options.to;

					if (typeof(options.onComplete) === 'function') {
						options.onComplete.call(_this, value);
					}
				}
			}
		});
	};

	$.fn.countTo.defaults = {
		from: 0,  // the number the element should start at
		to: 100,  // the number the element should end at
		speed: 1000,  // how long it should take to count between the target numbers
		refreshInterval: 100,  // how often the element should be updated
		decimals: 0,  // the number of decimal places to show
		onUpdate: null,  // callback method for every time the element is updated,
		onComplete: null,  // callback method for when the element finishes updating
	};
})(jQuery);

function initToCounter(){
	"use strict";
	
	if($j('.counter.zero').length){
		$j('.counter.zero').each(function() {
			if(!$j(this).hasClass('executed')){
				$j(this).addClass('executed');
				$j(this).appear(function() {
					$j(this).parent().css('opacity', '1');
					var $max = parseFloat($j(this).text());
					$j(this).countTo({
						from: 0,
						to: $max,
						speed: 1500,
						refreshInterval: 100
					});
				},{accX: 0, accY: -200});
			}	
		});
	}
}

function initCounter(){
	"use strict";
	
	if($j('.counter.random').length){
		$j('.counter.random').each(function() {
			
			if(!$j(this).hasClass('executed')){
				$j(this).addClass('executed');
				$j(this).appear(function() {
					$j(this).parent().css('opacity', '1');
					$j(this).absoluteCounter({
						speed: 2000,
						fadeInDelay: 1000
					});
				},{accX: 0, accY: -200});
			}
		});
	}
}

function initPortfolio(){
	"use strict";
	
	if($j('.projects_holder_outer').length){
	
		$j('.projects_holder_outer').each(function(){
		
			$j(this).find('.projects_holder').mixitup({
				showOnLoad: 'all',
				transitionSpeed: 600,
				minHeight: 150
			});
		});
	}
}

function headerSize(scroll){
	"use strict";

	if((header_height - scroll) > min_header_height){	
		$j('header').removeClass('scrolled');
		$j('header nav.main_menu > ul > li > a').css('line-height', header_height - scroll+'px');
		$j('header .drop_down .second').css('top', header_height - scroll+'px');
		$j('header .side_menu_button').css('height', header_height - scroll+'px');
		$j('header .logo_wrapper').css('height', header_height - scroll+'px');
		
	} else if((header_height - scroll) < min_header_height){
		$j('header').addClass('scrolled');
		$j('header nav.main_menu > ul > li > a').css('line-height', min_header_height+'px');
		$j('header .drop_down .second').css('top', min_header_height+'px');
		$j('header .side_menu_button').css('height', min_header_height+'px');
		$j('header .logo_wrapper').css('height', min_header_height+'px');		
	}


	if((header_height - scroll < logo_height) && (header_height - scroll) > min_header_height && logo_height > min_header_height - 10){
		$j('.logo a').height(header_height - scroll - 10);
	}else if((header_height - scroll < logo_height) && (header_height - scroll) < min_header_height && logo_height > min_header_height - 10){
		$j('.logo a').height(min_header_height - 10);
	}else if((header_height - scroll < logo_height) && (header_height - scroll) < min_header_height && logo_height < min_header_height - 10){
		$j('.logo a').height(logo_height);
	}else if(scroll === 0 && logo_height > header_height - 10){
		$j('.logo a').height(logo_height);
	}else{
		$j('.logo a').height(logo_height);
	}
	
	$j('.logo a img').css('height','100%');
}

function logoSizeOnSmallScreens(){
	"use strict";
	// 100 is height of header on small screens
	
	if((100 - 20 < logo_height)){
		$j('.logo a').height(100 - 20);
	}else{
		$j('.logo a').height(logo_height);
	}
	$j('.logo a img').css('height','100%');
}

function initElementsAnimation(){
	"use strict";

	if($j(".element_from_fade").length){
		$j('.element_from_fade').each(function(){
			var $this = $j(this);
						
			$this.appear(function() {
				$this.addClass('element_from_fade_on');	
			},{accX: 0, accY: -200});
		});
	}
	
	if($j(".element_from_left").length){
		$j('.element_from_left').each(function(){
			var $this = $j(this);
						
			$this.appear(function() {
				$this.addClass('element_from_left_on');	
			},{accX: 0, accY: -200});		
		});
	}
	
	if($j(".element_from_right").length){
		$j('.element_from_right').each(function(){
			var $this = $j(this);
						
			$this.appear(function() {
				$this.addClass('element_from_right_on');	
			},{accX: 0, accY: -200});
		});
	}
	
	if($j(".element_from_top").length){
		$j('.element_from_top').each(function(){
			var $this = $j(this);
						
			$this.appear(function() {
				$this.addClass('element_from_top_on');	
			},{accX: 0, accY: -200});
		});
	}
	
	if($j(".element_from_bottom").length){
		$j('.element_from_bottom').each(function(){
			var $this = $j(this);
						
			$this.appear(function() {
				$this.addClass('element_from_bottom_on');	
			},{accX: 0, accY: -200});			
		});
	}
	
	if($j(".element_transform").length){
		$j('.element_transform').each(function(){
			var $this = $j(this);
						
			$this.appear(function() {
				$this.addClass('element_transform_on');	
			},{accX: 0, accY: -200});	
		});
	}	
}

function fitAudio(){
	"use strict";
	
	$j('audio').mediaelementplayer({
		audioWidth: '100%'
	});
}

function initBlog(){
	"use strict";
	
	if($j('.massonary').length){
		var width_blog = $j('.container_inner').width();
		if($j('.massonary').closest(".column_inner").length) {
			width_blog = $j('.massonary').closest(".column_inner").width();
		}
	
		$j('.massonary').width(width_blog);
		var $container = $j('.massonary');
		var $cols = 3;
			
		if($container.width() < 420) {
			$cols = 1;
		} else if($container.width() <= 768) {
			$cols = 2;
		}
		
		$container.isotope({
			itemSelector: 'article',
			resizable: false,
			masonry: { columnWidth: $j('.massonary').width() / $cols }
		});

		$j(window).resize(function(){
			if($container.width() < 420) {
				$cols = 1;
				
			} else if($container.width() <= 768) {
				$cols = 2;
				
			}  else {
				$cols = 3;
				
			}
		});
		
		$j(window).smartresize(function(){
			$container.isotope({
				masonry: { columnWidth: $j('.massonary').width() / $cols}
			});
		});
	$j('.massonary').animate({opacity: "1"}, 500);
	}	
}

function initMobileMenu(){
	"use strict";
	
	$j(".mobile_menu_button span").click(function () {
		if ($j(".mobile_menu > ul").is(":visible")){
			$j(".mobile_menu > ul").slideUp(200);
		} else {
			$j(".mobile_menu > ul").slideDown(200);
		}
	});
	
	$j(".mobile_menu > ul > li.has_sub > a > span.mobile_arrow, .mobile_menu > ul > li.has_sub > h5 > span.mobile_arrow").click(function () {
		if ($j(this).closest('li.has_sub').find("> ul.sub_menu").is(":visible")){
			$j(this).closest('li.has_sub').find("> ul.sub_menu").slideUp(200);
			$j(this).closest('li.has_sub').removeClass('open_sub');
		} else {
			$j(this).closest('li.has_sub').addClass('open_sub');
			$j(this).closest('li.has_sub').find("> ul.sub_menu").slideDown(200);
		}
	});

	$j(".mobile_menu > ul > li.has_sub > ul.sub_menu > li.has_sub > a > span.mobile_arrow, .mobile_menu > ul > li.has_sub > ul.sub_menu > li.has_sub > h5 > span.mobile_arrow").click(function () {
		if ($j(this).parent().parent().find("ul.sub_menu").is(":visible")){
			$j(this).parent().parent().find("ul.sub_menu").slideUp(200);
			$j(this).parent().parent().removeClass('open_sub');
		} else {
			$j(this).parent().parent().addClass('open_sub');
			$j(this).parent().parent().find("ul.sub_menu").slideDown(200);
		}
	});
	
	$j(".mobile_menu ul li a").click(function () {
		if(($j(this).attr('href') !== "http://#") && ($j(this).attr('href') !== "#")){
			$j(".mobile_menu > ul").slideUp();
		}else{
			return false;
		}
	});

	$j(".mobile_menu ul li a span.mobile_arrow").click(function () {
		return false;
	});
}

function initTabs(){
	"use strict";

	var $tabsNav = $j('.tabs-nav');
	var $tabsNavLis = $tabsNav.children('li');
	$tabsNav.each(function() {
		var $this = $j(this);
		$this.next().children('.tab-content').stop(true,true).hide().first().show();
		$this.children('li').first().addClass('active').stop(true,true).show();
	});
	$tabsNavLis.on('click', function(e) {
		var $this = $j(this);
		$this.siblings().removeClass('active').end().addClass('active');
		$this.parent().next().children('.tab-content').stop(true,true).hide().siblings( $this.find('a').attr('href') ).fadeIn();
		e.preventDefault();
	}); 
}

function initTestimonials(){
	"use strict";

	if($j('.testimonials').length){
		$j('.testimonials').each(function(){
			$j(this).css('visibility','visible');
			var $tabsNav = $j(this).find('.testimonial_nav');
			var $tabsNavLis = $tabsNav.children('li');
			$tabsNav.each(function() {
				var $this = $j(this);
				$this.prev().children('.testimonial_content').stop(true,true).hide().first().show();
				$this.children('li').first().addClass('active').stop(true,true).show();
			});
			$tabsNavLis.on('click', function(e) {
				var $this = $j(this);
				$this.siblings().removeClass('active').end().addClass('active');
				$this.parent().prev().children('.testimonial_content').stop(true,true).hide().siblings( $this.find('a').attr('href') ).fadeIn();
				e.preventDefault();
			});
		});
	}
}

function initAccordion(){
	"use strict";
	
	$j( ".accordion" ).accordion({
		animate: "swing",
		collapsible: true,
		icons: "",
		heightStyle: "content"
	});
	
	$j(".toggle").addClass("accordion ui-accordion ui-accordion-icons ui-widget ui-helper-reset")
	.find("h3")
	.addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
	.hover(function() { $j(this).toggleClass("ui-state-hover"); })
	.click(function() {
	$j(this)
		.toggleClass("ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom")
		.next().toggleClass("ui-accordion-content-active").slideToggle(200);
		return false;
	})
	.next()
	.addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom")
	.hide(); 
}

function initAccordionContentLink(){
	"use strict";

	$j('.accordion_holder .accordion_inner .accordion_content a').click(function(){
		if($j(this).attr('target') === '_blank'){
			window.open($j(this).attr('href'),'_blank');
		}else{
			window.open($j(this).attr('href'),'_self');
		}
		return false;
	});
}

function initPieChart(){
	"use strict";
 
	if($j('.percentage').length){
		$j('.percentage').each(function() {

			var $barColor = '#146484';

			if($j(this).data('active') !== ""){
				$barColor = $j(this).data('active');
			}

			var $trackColor = '#cbcbcb';

			if($j(this).data('noactive') !== ""){
				$trackColor = $j(this).data('noactive');
			}

			var $line_width = 2;

			if($j(this).data('linewidth') !== ""){
				$line_width = $j(this).data('linewidth');
			}
			
			var $size = 165;

			$j(this).appear(function() {
				initToCounterPieChart($j(this));
				$j(this).parent().css('opacity', '1');
				
				$j(this).easyPieChart({
					barColor: $barColor,
					trackColor: $trackColor,
					scaleColor: false,
					lineCap: 'butt',
					lineWidth: $line_width,
					animate: 1500,
					size: $size
				}); 
			},{accX: 0, accY: -200});
		});
	}
}

function initToCounterPieChart($this){
	"use strict";

	$j($this).css('opacity', '1');
	var $max = parseFloat($j($this).find('.tocounter').text());
	$j($this).find('.tocounter').countTo({
		from: 0,
		to: $max,
		speed: 1500,
		refreshInterval: 50
	});
}

function initProgressBars(){
	"use strict";

	if($j('.progress_bars').length){
		$j('.progress_bars').each(function() {
			$j(this).appear(function() {
				initToCounterHorizontalProgressBar($j(this));
				$j(this).find('.progress_bar').each(function() {
					var percentage = $j(this).find('.progress_content').data('percentage');
					var percent_width = $j(this).find('.progress_number').width();
					$j(this).find('.progress_content').css('width', '0%');
					$j(this).find('.progress_content').animate({'width': percentage+'%'}, 1500);
					$j(this).find('.progress_number').css('width', percent_width+'px');
				});
			},{accX: 0, accY: -200});
		});
	}
}

function initToCounterHorizontalProgressBar($this){
	"use strict";

	if($this.find('.progress_number span').length){
		$this.find('.progress_number span').each(function() {
			$j(this).parent().css('opacity', '1');
			var $max = parseFloat($j(this).text());
			$j(this).countTo({
				from: 0,
				to: $max,
				speed: 1500,
				refreshInterval: 50
			});
		});
	}
}

function initListAnimation(){
	"use strict";
	
	$j('.animate_list').each(function(){
		$j(this).appear(function() {
			$j(this).find("li").each(function (l) {
				var k = $j(this);
				setTimeout(function () {
					k.animate({
						opacity: 1,
						top: 0
					}, 1500);
				}, 100*l);
			});
		},{accX: 0, accY: -200});
	});
}

function initMessages(){
	"use strict";

	$j('.message').each(function(){
		$j(this).find('.close').click(function(e){
			e.preventDefault();
			$j(this).parent().fadeOut(500);
		});
	});
}

var timeOuts = []; 
function initProgressBarsIcon(){
	"use strict";

	if($j('.progress_bars_with_image_holder').length){
		$j('.progress_bars_with_image_holder').each(function() {
			var $this = $j(this);
			$this.appear(function() {
				$this.find('.progress_bars_with_image').each(function() {
					var number = $j(this).find('.progress_bars_with_image_content').data('number');
					var bars = $j(this).find('.bar');
				
					bars.each(function(i){
						if(i < number){
							var time = (i + 1)*150;
							timeOuts[i] = setTimeout(function(){
								$j(bars[i]).addClass('active');
							},time);
						}
					});
				});
			},{accX: 0, accY: -200});
		});
	}
}

function totop_button(a) {
	"use strict";

	var b = $j("#back_to_top");
	b.removeClass("off on");
	if (a === "on") { b.addClass("on"); } else { b.addClass("off"); }
}

function backButtonShowHide(){
	"use strict";

	$j(window).scroll(function () {
		var b = $j(this).scrollTop();
		var c = $j(this).height();
		var d;
		if (b > 0) { d = b + c / 2; } else { d = 1; }
		if (d < 1e3) { totop_button("off"); } else { totop_button("on"); }
	});
}

function backToTop(){
	"use strict";
	
	$j(document).on('click','#back_to_top',function(e){
		e.preventDefault();
		
		$j('body,html').animate({scrollTop: 0}, $j(window).scrollTop()/3, 'swing');
	});
}

function initNiceScroll(){
	"use strict";

		if($j('.smooth_scroll').length){	
			$j("html").niceScroll({ 
				scrollspeed: 80, 
				mousescrollstep: 25, 
				cursorwidth: 10, 
				cursorborder: 0,
				cursorborderradius: 0,
				cursorcolor: "#ffffff",
				autohidemode: false, 
				horizrailenabled: false 
			});
		}

}

function loadMore(){
	"use strict";
	
	var i = 1;
	
	$j('.load_more a').on('click', function(e)  {
		e.preventDefault();
		
		var link = $j(this).attr('href');
		var $content = '.projects_holder';
		var $anchor = '.portfolio_paging .load_more a';
		var $next_href = $j($anchor).attr('href'); // Get URL for the next set of posts
		var filler_num = $j('.projects_holder .filler').length;
		$j.get(link+'', function(data){
			$j('.projects_holder .filler').slice(-filler_num).remove();
			var $new_content = $j($content, data).wrapInner('').html(); // Grab just the content
			$next_href = $j($anchor, data).attr('href'); // Get the new href
			$j('article.mix:last').after($new_content); // Append the new content
			
			var min_height = $j('article.mix:first').height();
			$j('article.mix').css('min-height',min_height);
			
			$j('.projects_holder').mixitup('remix','all');
			prettyPhoto();
			if($j('.load_more').attr('rel') > i) {
				$j('.load_more a').attr('href', $next_href); // Change the next URL
			} else {
				$j('.load_more').remove(); 
			}
			$j('.projects_holder .portfolio_paging:last').remove(); // Remove the original navigation
			$j('article.mix').css('min-height',0);
			
		});
		i++;
	});
}

function showElementFadeIn(){
	"use strict";
	
	$j('.element_fade_in').each(function(){
		$j(this).appear(function() {
			$j(this).addClass('show_item');
		},{accX: 0, accY: -200});
	});
}

function placeholderReplace(){
	"use strict";

	$j('[placeholder]').focus(function() {
		var input = $j(this);
		if (input.val() === input.attr('placeholder')) {
			if (this.originalType) {
				this.type = this.originalType;
				delete this.originalType;
			}
			input.val('');
			input.removeClass('placeholder');
		}
	}).blur(function() {
		var input = $j(this);
		if (input.val() === '') {
			if (this.type === 'password') {
				this.originalType = this.type;
				this.type = 'text';
			}
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		}
	}).blur();

	$j('[placeholder]').parents('form').submit(function () {
		$j(this).find('[placeholder]').each(function () {
			var input = $j(this);
			if (input.val() === input.attr('placeholder')) {
				input.val('');
			}
		});
	});
}

function addPlaceholderSearchWidget(){
	"use strict";
	
	$j('.header_top .searchform input:text, .widget.widget_search form input:text, footer .footer_top_inner .searchform input:text').each(
		function(i,el) {
		if (!el.value || el.value === '') {
			el.placeholder = 'Search here';
		}
	});
}

function initParallax(speed){
	"use strict";
	
	if($j('.parallax section').length){
		if($j('html').hasClass('touch')){
			$j('.parallax section').each(function() {
				var $self = $j(this);
				var section_height = $self.data('height');
				$self.height(section_height);
				var rate = 0.5;
				
				var bpos = (- $j(this).offset().top) * rate;
				$self.css({'background-position': 'center ' + bpos  + 'px' });
				
				$j(window).bind('scroll', function() {
					var bpos = (- $self.offset().top + $j(window).scrollTop()) * rate;
					$self.css({'background-position': 'center ' + bpos  + 'px' });
				});
			});
		}else{
			$j('.parallax section').each(function() {
				var $self = $j(this);
				var section_height = $self.data('height');
				$self.height(section_height);
				var rate = (section_height / $j(document).height()) * speed;
				
				var distance = $scroll - $self.offset().top + 104;
				var bpos = - (distance * rate);
				$self.css({'background-position': 'center ' + bpos  + 'px' });
				
				$j(window).bind('scroll', function() {
					var distance = $scroll - $self.offset().top + 104;
					var bpos = - (distance * rate);
					$self.css({'background-position': 'center ' + bpos  + 'px' });
				});
			});
		}
		return this;
	}
}

function socialShare(){
	"use strict";
	
	var menu_item = $j('.social_share_dropdown');

	if ($j(menu_item).length > 0) {
		menu_item.each( function(i) {
			$j(menu_item[i]).parent().mouseenter(function(){
				$j(menu_item[i]).css({'visibility':'visible','overflow': 'visible','display': 'block'});
			}).mouseleave( function(){
				$j(menu_item[i]).css({'overflow':'hidden','visibility': 'hidden','display':'none'});
			});
		});
	}
}

$j.fn.preload = function() {
	"use strict";

	this.each(function(){
		$j('<img/>')[0].src = this;
	});
};