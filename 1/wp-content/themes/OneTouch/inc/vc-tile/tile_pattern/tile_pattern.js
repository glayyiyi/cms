
jQuery(".tile-pattern-container input").each(function(){
    jQuery(this).parent().find(".tile-pattern").removeClass("active");
    jQuery(this).parent().find(".tile-pattern[data-pattern="+jQuery(this).val()+"]").addClass("active");
});

if( jQuery(".tile-pattern-container .tile-pattern:eq(0)").hasClass("active") )
    jQuery(".number").closest(".row-fluid").hide();

jQuery("body").on("click", ".tile-pattern-container .tile-pattern", function(){
    jQuery(".tile-pattern-container .tile-pattern").removeClass("active");
    jQuery(this).addClass("active");
    var name = jQuery(this).parent().data("name");
    var pattern = jQuery(this).data("pattern");
    jQuery("input[name=" + name + "]").val( pattern ).attr("value", pattern);
    jQuery(".row-fluid:gt(1)").hide();
    var controls = patternControls[pattern];
    for(key in controls){
        jQuery("." + controls[key]).closest(".row-fluid").show();
    }
});


var patternControls = {   //Object, storages displayed fields for each pattern
    BTC:new Array('bgcolor', 'text', 'link','size'),
    BTR:new Array('bgcolor', 'text', 'link','size'),
    BTL:new Array('bgcolor', 'text', 'link','size'),
    TLD:new Array('bgcolor', 'text', 'link','size'),
    TRD:new Array('bgcolor', 'text', 'link','size'),
    TTR:new Array('bgcolor', 'text', 'link','size'),
    TTL:new Array('bgcolor', 'text', 'link','size'),
    IBG:new Array('bgcolor', 'text', 'link', 'icon','size'),
    SIC:new Array('bgcolor', 'link', 'icon','size'),
    BIC:new Array('bgcolor', 'link', 'icon','size'),
    TI:new Array('bgcolor', 'text', 'icon', 'link','size'),
    TTLI:new Array('bgcolor', 'text', 'icon', 'link','size'),
    TTRI:new Array('bgcolor', 'text', 'icon', 'link','size'),
    TIN:new Array('bgcolor', 'text', 'icon', 'number', 'link','size'),
    SIN:new Array('bgcolor', 'icon', 'number', 'link','size')
}