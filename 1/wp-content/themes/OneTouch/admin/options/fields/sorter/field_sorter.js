jQuery(document).ready(function(){
    blocks = new Object();
    blocks.enabled = new Array();
    blocks.disabled = new Array();
    //fieldSorterSetValues();

    jQuery('.enabled').sortable({
        connectWith:".disabled",
        change:fieldSorterSetValues,
        stop:fieldSorterSetValues
    });

    jQuery('.disabled').sortable({
        connectWith:".enabled",
        change:fieldSorterSetValues,
        stop:fieldSorterSetValues
    });

});

function fieldSorterSetValues(){
    var result = '';
    blocks['enabled'] = [];
    blocks['disabled'] = [];
    jQuery("ul.enabled li h4").each(function(){
        blocks['enabled'].push(jQuery(this).html());
    });
    jQuery("ul.disabled li h4").each(function(){
        blocks['disabled'].push(jQuery(this).html());
    });
    jQuery("#" + input_id).val( JSON.stringify(blocks).split('"').join('+') );
}