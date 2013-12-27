/**
 * Created with JetBrains PhpStorm.
 * User: Office
 * Date: 30.11.12
 * Time: 11:45
 * To change this template use File | Settings | File Templates.
 */
jQuery(document).ready(function(){

    var current_menu = jQuery("#menu-list").val();
    jQuery("#menu-list").on("change",function(){
        jQuery("ul.metro-menu-items").hide();
        jQuery("ul[data-menu=" + jQuery(this).val() + "]").show();
    });

    jQuery("li.metro-menu-item>a").on("click",function(e){
        e.preventDefault();
    });

    jQuery("ul[data-menu=" + current_menu + "]").show();

    jQuery("li.metro-menu-item>a").each(function(){
        var color = rgb2hex(jQuery(this).css("background-color"));
        jQuery(this).next().find(".metro-item-color").css("background-color", color ).val( color );
    });

    //Creating and initializing colorpicker
    color_picker = jQuery.farbtastic('#metro-menu-colorpicker');
    color_picker.linkTo(function(){
        jQuery('.metro-item-color:eq(0)').css("background-color",this.color ).val(this.color);
    })

    jQuery(".remove-image").on("click",function(){
        jQuery('.bgimage-item-' + jQuery(this).data("item")).val("")
            .closest(".metro-menu-item").children("a").css("background-image","none");
    });

    jQuery(".remove-icon").on("click",function(){
        jQuery('.icon-item-' + jQuery(this).data("item")).val("")
            .closest(".metro-menu-item").find('.tile-icon').remove();
    });

    //Setting bg color of menu item
    jQuery('.metro-item-color').on("click", function(){
        jQuery('#metro-menu-colorpicker').show();
        var item = jQuery(this).data("item");
        var field = jQuery(this);
        color_picker.linkTo(function(){
            field.css("background-color",this.color ).val(this.color).parent().prev().css("background-color",this.color );
        });
        if(field.val() != '')
            color_picker.setColor(field.val());
        else
            color_picker.setColor(rgb2hex( field.parent().prev().css("background-color") ));
    });

    //Setting bg image
    jQuery(".metro-item-bgimage").on("click",function(){
        jQuery('#metro-menu-colorpicker').hide();
        bgimage_item = jQuery(this).data("item");
        upload = 'image';
        tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true');
        jQuery("#TB_window").css({
            position:"absolute",
            top:"30%",
            left:"50%"
        });
        jQuery("#TB_window").append("<a  class='close'>X</a>");
        jQuery("#TB_window  a.close").css({position:"absolute", top:"10px",right:"10px", cursor:"pointer"});
        return false;
    });

    jQuery(".metro-item-icon").on("click",function(){
        jQuery('#metro-menu-colorpicker').hide();
        icon_item = jQuery(this).data("item");
        upload = 'icon';
        tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true');
        jQuery("#TB_window, #TB_overlay").css({
            position:"absolute",
            top:"30%",
            left:"50%"
        });

        jQuery("#TB_window").append("<a  class='close'>X</a>");
        jQuery("#TB_window  a.close").css({position:"absolute", top:"10px",right:"0px"});
        return false;
    });

    jQuery("body").on("click","#TB_window a.close",function(){
        jQuery(this).remove();
        tb_remove();
    });

    window.send_to_editor = function(html) {
        html = '<div>'+html+'</div>';
        imgurl = jQuery('img',html).attr('src');
        if( upload == 'image'){
            jQuery('.bgimage-item-' + bgimage_item).val(imgurl)
                .closest(".metro-menu-item").children('a').css("background-image","url(" + imgurl + ")");
        } else if(upload == 'icon'){
            jQuery('.icon-item-' +icon_item).val(imgurl);
            //alert(icon_item);
            var img = new Image();
            img.onload = function(){
                var image_width = img.width;
                var image_height = img.height;
                if((img.width > 96) || (img.height > 96)){
                    style = "background-size:contain;";
                } else{
                    style = '';
                }
                var icon_container = jQuery('.icon-item-' + icon_item).val(imgurl)
                    .closest(".metro-menu-item").children('a');

                if(icon_container.find(".tile-icon").size() > 0)
                    icon_container.find(".tile-icon").remove();

                jQuery('.icon-item-' + icon_item).val(imgurl)
                    .closest(".metro-menu-item").children('a').append("<div class='tile-icon' style='" + style +"background-image:url("+imgurl+")'></div>");
            };
            img.src = imgurl;
        }
        tb_remove();
    }


    jQuery("#save_metro_menu").on("click", function(){
        var result = {};
        var current_menu = jQuery("#menu-list").val();
        result.menu_slug = current_menu;
        result.menu_items = {};
        var items = jQuery("ul[data-menu=" + current_menu + "]>li");
        items.each(function(){
            var settings_block = jQuery(this).find(".metro-menu-settings");
            var id = settings_block.find(".metro-item-color").data("item");
            result.menu_items[id] = {};
            var color = settings_block.find(".metro-item-color").val();
            var bgimage = settings_block.find(".metro-item-bgimage").val();
            var icon = settings_block.find(".metro-item-icon").val();
            result.menu_items[id].color = color;
            result.menu_items[id].bgimage = bgimage;
            result.menu_items[id].icon = icon;

        });
        var url = document.location.href;
        dir = url.substring( 0, url.lastIndexOf("/") + 1 );
        jQuery.ajax({
            url: dir + "admin-ajax.php",
            type: 'POST',
            data:{
                action:'save_metro_menu',
                menu:JSON.stringify(result)
            },
            success:function(results){
                alert("Saved");
            }
        });
    });

});

/*RGB to HEX */
var hexDigits = new Array
    ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");


function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function hex(x) {
    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
}
