jQuery(document).ready(function($){
    colorPicker = $.farbtastic("#colorpicker");
    $("#colorpicker").hide();
    jQuery("#colorpicker .farbtastic").append("<a class='close' style='position:absolute;top:0;right:0;'>X</a>");

    jQuery("input.title, input.subtitle").on("keyup",function(){
        serializeBlocks();
    })

    jQuery("#add_block").on("click",function(e){
        var fieldValue = jQuery("#new_block_name").val();
        jQuery("#new-block-validation-error").html("");
        if( fieldValue== ''){
            jQuery("#new-block-validation-error").html("Block name is empty!");
        } else {
            jQuery(".custom-block h4").each(function(){
                if(jQuery(this).html() == fieldValue)
                    jQuery("#new-block-validation-error").html("Block name is not unique!");
            });
        }
        if( jQuery("#new-block-validation-error").html()=='') {
            jQuery("#block-list").append(displayBlock(fieldValue));
            serializeBlocks();
        }

        e.preventDefault();
    });

    $(".block-pages").append(pages);
    loadBlocks();

});

jQuery("body").on("keyup","input.title, input.subtitle",function(){
    serializeBlocks();
})

jQuery("body").on("change",".block-page", function(){
    var checked = jQuery(this).find("option:selected");
    if( (checked.val() == 'special-1') || (checked.val() == 'special-2')){
        checked.attr("selected",false);
        checked.next().attr("selected", "selected");
    }

});

function displayBlock(name){
    var id = name.split(' ').join('-');
    id = id.toLowerCase();
    return '<div class="custom-block" id="'+id +'"><h4>' + name + '<a class="delete-block" href ="">Delete block</a></h4>' +
           '<input type="text"  class="title" /> Type here block title<div></div><br>' +
           '<input type="text"  class="subtitle" /> Type here block subtitle<div></div><br>' +
           '<input type="text"  class="color" /> Click on field to set block background color<div></div><br>' +
           '<a class="button-secondary upload-bg">Upload background image</a>' +
           '<p class = "image_url"></p><a class="button-secondary remove-bg">Remove Image</a>' +
           '<p><strong> Select content page to display in block </strong></p> <select class="block-page">' + pages +
           '</select>' +
           '</div>';
}

function loadBlocks(){
    var settings = jQuery.parseJSON(jQuery("#block-list").next('input').val().split('+').join('"'));
    for(name in settings){
        jQuery("#block-list").append(displayBlock(name));
        var block = jQuery("#" +  settings[name].id);
        block.find('.color').val(settings[name].color)
            .css("background-color",settings[name].color);
        block.find('.image_url').html(settings[name].bgimage);
        block.find('.subtitle').val(settings[name].subtitle);
        block.find('.title').val(settings[name].title);

        block.find('.block-page option').each(function(){
            if(jQuery(this).val() == settings[name].page)
                jQuery(this).attr("selected","selected");
        })
    }

    jQuery(".custom-block").each(function(){
        if(jQuery(this).find(".image_url").html() == ''){
            jQuery(this).find(".remove-bg").hide();
        }
    });
}

function serializeBlocks(){

    var result = new Object();
    jQuery(".custom-block").each(function(){
        var name = jQuery(this).find("h4").html();
        name = name.substr(0, name.indexOf("<a"));
        var id = jQuery(this).attr('id');
        var color = jQuery(this).find('.color').val();
        var bgimage = jQuery(this).find('.image_url').html();
        var page = jQuery(this).find('.block-page').val();
        var title = jQuery(this).find('.title').val();
        var subtitle = jQuery(this).find('.subtitle').val();
        result[name] = {
            id:id,
            color:color,
            bgimage:bgimage,
            page:page,
            title:title,
            subtitle:subtitle
        };
    });
    jQuery("#block-list").next('input').val(JSON.stringify(result).split('"').join('+'));

}

jQuery("body").on("change",".block-page",function(){
    serializeBlocks();
});

jQuery("body").on("click",".delete-block",function(e){
    jQuery(this).closest(".custom-block").remove();
    serializeBlocks();
    e.preventDefault();
});

jQuery("body").on("click",".remove-bg",function(e){
    jQuery(this).closest(".custom-block").find(".image_url").html("");
    jQuery(this).hide();
    serializeBlocks();
});


jQuery("body").on("click",".color",function(){
    colorInput = jQuery(this);
    //blockId = colorInput.closest(".custom-block").attr("id");
    var value = colorInput.val();
    jQuery("#colorpicker").show().css("top", (colorInput.offset().top - 30) + "px");
    colorPicker.setColor(value);
    colorPicker.linkTo(function(){
        colorInput.val(colorPicker.color).css("background-color",colorPicker.color);
        serializeBlocks();
    });
});

jQuery("body").on("click",".upload-bg",function(){
    blockId = jQuery(this).closest(".custom-block").attr("id");
    tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true');
});

window.send_to_editor = function(html) {
    imgurl = jQuery('img',html).attr('src');
    jQuery("#" + blockId).find(".image_url").html("<img src = '" + imgurl + "' />");
    jQuery("#" + blockId).find(".remove-bg").show();
    tb_remove();
    serializeBlocks();
};

jQuery("body").on("click","#colorpicker .close",function(){
    serializeBlocks();
    jQuery("#colorpicker").hide();


});
