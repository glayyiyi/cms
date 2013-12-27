jQuery(document).ready(function(){

});

jQuery(".tile_size input").each(function(){
    jQuery(this).parent().find(".size").removeClass("active");
    jQuery(this).parent().find(".size[data-size="+jQuery(this).val()+"]").addClass("active");
});

jQuery("body").on("click", ".tile_size .size", function(){
    jQuery(".tile_size .size").removeClass("active");
    jQuery(this).addClass("active");
    var name = jQuery(this).parent().data("name");
    jQuery("input[name=" + name + "]").val( jQuery(this).data("size")).attr("value", jQuery(this).data("size"));
    jQuery()
});

