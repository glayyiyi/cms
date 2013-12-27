jQuery(document).ready(function() {
    var countElements = jQuery(".scroll-box .grid .gr-box").size();
    jQuery(".scroll-box .grid").width(countElements*728);

    var scrollbox = jQuery(".scroll-box");
    var indent = ( jQuery(window).width() - jQuery(".fifteen.columns>.wrap").width() ) / 2;

    setBoxedSlider();

    var animateTime = 1,
        offsetStep = 5;

    scrollWrapper = jQuery('.scroll-box');
    scrollContent = jQuery('.scroll-box .grid');

    //event handling for buttons "left", "right"
    jQuery('.bttL')
        .mousedown(function() {
            scrollContent.data('loop', true).loopingAnimation(jQuery(this), jQuery(this).is('.bttR') );
        })
        .bind("mouseup mouseout", function(){
            //scrollContent.data('loop', false).stop();
        });

    jQuery.fn.loopingAnimation = function(el, dir){
        if(this.data('loop')){
            var sign = (dir) ? '-=' : '+=';
            this.animate({ marginLeft: sign + offsetStep + 'px' }, animateTime, function(){ jQuery(this).loopingAnimation(el,dir) });
        }
        return false;
    };
    //jQuery('.scroll-box').tinyscrollbar({ axis: 'x'});
   alert();
});

jQuery(window).resize(function(){
    setBoxedSlider();
    setBoxedSlider();
});

function setBoxedSlider(){
    scrollbox = jQuery(".scroll-box");

    if(scrollbox.data("boxed") == "3"){
        var marginLeft = jQuery('.fifteen.columns').width();
        marginLeft = (jQuery(window).width() - marginLeft)/2 - 9;

        scrollbox.width(jQuery(window).width() );

        if(marginLeft > 0)
            scrollbox.closest(".wrap").css("margin-left",(-marginLeft)+"px");
        scrollbox.closest(".wrap").width(jQuery(window).width());
    }
    else if(scrollbox.data("boxed") == "1"){
        scrollbox.closest(".wrap").css("width","100%");
        scrollbox.css("width","100%");
    }
    else if(scrollbox.data("boxed") == "2") {

        scrollbox.closest(".wrap").css("width","100%");
        scrollbox.css("width","100%");
        var indent = jQuery(window).width() - jQuery(".fifteen").width();

        scrollbox.width(jQuery(".fifteen").width() + indent/2 + 9);
    }
    scrollbox.getNiceScroll().resize();
}