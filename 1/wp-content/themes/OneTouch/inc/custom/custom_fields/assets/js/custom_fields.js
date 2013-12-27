//Start of colorpicker block
const CSColorpicker = "#custom-fields-colorpicker";
var colorpicker;
var colorpickerInput;
var uploading_type;

jQuery(document).ready(function(){
    try {
        colorpicker = jQuery.farbtastic(CSColorpicker);

    } catch (e){
        console.log("Colorpicker problems");
    }

    CF_initColorFields();

    jQuery("input.colorpicker").on("click",CF_showColorpicker);
});

function CF_showColorpicker(){
    var colorpickerDiv = jQuery(CSColorpicker);

    var $this = jQuery(this);
    colorpickerDiv.show();
    if( colorpickerInput == $this.attr("id") ) {
        colorpickerDiv.hide();
        colorpickerInput = '';
    } else {
        colorpickerInput = $this.attr("id");
        colorpickerDiv.show();
    }


    colorpicker.setColor($this.val());
    colorpicker.linkTo(function(color){
        jQuery("#" + colorpickerInput).val(color).css("background-color",color);

    });

    var top = $this.offset().top - $this.closest(".postbox").offset().top;

    colorpickerDiv.css({
        left:450 + "px",
        top:(top - 50) +"px"
    });
}

function CF_initColorFields(){
    jQuery("input.colorpicker").each(function(){
        jQuery(this).css("background-color", jQuery(this).val());
    });
}
//End of colorpicker block


//Start of image uploader block

var targetOfImage;
jQuery(document).ready(function(){
    jQuery(".cf-add-image").on("click", CF_loadCustomImage);
    jQuery(".cf-remove-image").on("click", CF_removeCustomImage);

});

function CF_removeCustomImage(){
    var target = jQuery(this).data("target");

    jQuery("#" + target ).val("");
    jQuery("#image-" + target).empty();
    jQuery(this).hide();
}

function CF_loadCustomImage(){
    targetOfImage = jQuery(this).data("target");
    tb_show('', 'media-upload.php?type=image&post_id=1&TB_iframe=true&flash=0&simple_slideshow=true');
    CF_processUploadedImage();
}

function CF_processUploadedImage(){
    window.default_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html){

        html = '<div>'+html+'</div>';
        imgurl = jQuery('img',html).attr('src');

        jQuery("#" + targetOfImage).val(imgurl);
        jQuery("#image-" + targetOfImage).html("<img src = '" + imgurl + "' class = 'CF-uploaded-image' />");
        jQuery(".cf-remove-image[data-target=" + targetOfImage + "]").show();

        tb_remove();
        window.send_to_editor = window.default_send_to_editor;
    }
}
//End of image uploader block

//Taxonomy selector element
jQuery(document).ready(function($){

    $(".taxonomy-selector").each(function(){
        var id = $(this).data('id');
        var values = $('#' + id).val().split(',');
        $(this).val(values);
    });

    $(".taxonomy-selector").on("change",function(){
        var id = $(this).data('id');
        $('#' + id).val($(this).val());
    });
});
//end taxonomy selector element

//Start of video uploader block

var targetOfVideo;
jQuery(document).ready(function(){
    jQuery(".cf-add-video").on("click", CF_loadCustomVideo);
    jQuery(".cf-remove-video").on("click", CF_removeCustomVideo);
});

function CF_removeCustomVideo(){
    var target = jQuery(this).data("target");
    jQuery("#" + target ).val("");
    jQuery("#image-" + target).empty();
    jQuery(this).hide();
}

function CF_loadCustomVideo() {
    targetOfVideo = jQuery(this).data("target");
    tb_show('', 'media-upload.php?type=image&post_id=1&TB_iframe=true&flash=0&simple_slideshow=true');
    CF_processUploadedVideo();
}

function CF_processUploadedVideo() {
    window.default_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html){

        html = '<div>'+html+'</div>';
        var video_url = jQuery('a',html).attr( 'href' );

        jQuery("#" + targetOfVideo).val( video_url );
        jQuery("#image-" + targetOfVideo).html( video_url );
        jQuery(".cf-remove-video[data-target=" + targetOfVideo + "]").show();

        tb_remove();
        window.send_to_editor = window.default_send_to_editor;
    }
}

//End of video uploader block


var blogPagesTemplate = [
    'blog-masonry.php',
    'posts-2left-sidebar.php',
    'posts-2right-sidebar.php',
    'posts-both-sidebar.php',
    'posts-left-sidebar.php',
    'posts-right-sidebar.php',
    'posts-sidebar-sel.php'
];

//Show/hide metabox, depending on element value
jQuery(document).ready(function(){
    toggleMetaboxOnFormat("post_video_custom_fields", 'video');
    toggleMetaboxOnFormat("post_quote_custom_fields", 'quote');



    jQuery("input[name=post_format]").on("change",function() {
        toggleMetaboxOnFormat("post_video_custom_fields", 'video');
        toggleMetaboxOnFormat("post_quote_custom_fields", 'quote');
        toogleMetaboxOnTemplate('page_portfolio_custom_fields', 'portfolio');
    });


    toogleMetaboxOnTemplateEntry('page_portfolio_custom_fields', 'portfolio');
    toogleMetaboxOnTemplateEnumeration('page_blog_custom_fields', blogPagesTemplate);
    jQuery("#page_template").on("change",function(){
        toogleMetaboxOnTemplateEntry('page_portfolio_custom_fields', 'portfolio');
        toogleMetaboxOnTemplateEnumeration('page_blog_custom_fields', blogPagesTemplate);
    });

});

function toggleMetaboxOnFormat(metaboxId, value) {
    var format = jQuery("input[name=post_format]:checked").val();
    if(format != value )
        jQuery("#" + metaboxId).slideUp("fast");
    else
        jQuery("#" + metaboxId).slideDown("fast");
}

function toogleMetaboxOnTemplateEntry(metaboxId, value) {
    var template = jQuery("#page_template").val();
    template = ( template !== undefined ) ? template : '';
    if( template !== undefined && !(template.indexOf(value) + 1) )
        jQuery("#" + metaboxId).slideUp("fast");
    else
        jQuery("#" + metaboxId).slideDown("fast");
}



function toogleMetaboxOnTemplateEnumeration(metaboxId, arrayOfTemplates){
    var template = jQuery("#page_template").val();
    template = ( template !== undefined ) ? template : '';
    var show = false;

    for(key in arrayOfTemplates){
        if(arrayOfTemplates[key] == template)
            show = true;
    }

    if(show)
        jQuery("#"+metaboxId).slideDown('fast');
    else
        jQuery("#"+metaboxId).slideUp('fast');
}




