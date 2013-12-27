jQuery(window).load(function(){
    jQuery(".element_font_typo").each(function(){
        id = jQuery(this).attr("id");
        var value = jQuery(this).val();
        var slashPosOne = value.indexOf('/');
        var slashPosTwo = value.lastIndexOf('/');
        var size = value.substr(0, slashPosOne );
        var style = value.substr( slashPosTwo + 1 );
        var family = value.substr( slashPosOne + 1 , slashPosTwo - 5 );
        jQuery('#' + id + '_size').val( size );
        jQuery('#' + id + '_family').val( family );
        jQuery('#' + id + '_style').val( style );
        if(value == '')
            jQuery(this).val(jQuery('#' + id + '_size').val() + '/' +
                    jQuery('#' + id + '_family').val() + '/' +
                    jQuery('#' + id + '_style').val()
            );
    });

    jQuery('.typo_size').live('change',function(){
        var id = jQuery(this).attr('id');
        id = id.substr(0, id.lastIndexOf('_'));
        var size = jQuery('#' + id + '_size').val();
        var family = jQuery('#' + id + '_family').val();
        var style = jQuery('#' + id + '_style').val();
        var result = size + '/' + family + '/' + style;
        jQuery('#' + id).val(result);
    });

    jQuery('.typo_family').live('change',function(){
        var id = jQuery(this).attr('id');
        id = id.substr(0, id.lastIndexOf('_'));
        var size = jQuery('#' + id + '_size').val();
        var family = jQuery('#' + id + '_family').val();
        var style = jQuery('#' + id + '_style').val();
        var result = size + '/' + family + '/' + style;
        jQuery('#' + id).val(result);
    });

    jQuery('.typo_style').live('change',function(){
        var id = jQuery(this).attr('id');
        id = id.substr(0, id.lastIndexOf('_'));
        var size = jQuery('#' + id + '_size').val();
        var family = jQuery('#' + id + '_family').val();
        var style = jQuery('#' + id + '_style').val();
        var result = size + '/' + family + '/' + style;
        jQuery('#' + id).val(result);
    });
});