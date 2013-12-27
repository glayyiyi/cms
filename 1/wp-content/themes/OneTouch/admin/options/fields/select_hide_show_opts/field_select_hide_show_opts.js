jQuery(document).ready(function(){
    jQuery('.nhp-opts-select-hide-show-opts').each(function() {
        showHideOptionsAccordingToSelected(this);
    });

    jQuery('.nhp-opts-select-hide-show-opts').on("change",function(){
        showHideOptionsAccordingToSelected(this);
    });
});

//Processing options according to results
function showHideOptionsAccordingToSelected( element ){
    var option = jQuery('option:selected', element);

    var result = explode( ",", option.data("opts") );
    for( var z in result){
        jQuery("#" + result[z]).closest("tr").fadeOut(500);
    }

    var option = jQuery('option:not(:selected)', element);
    option.each(function(){
        var result = explode( ",", jQuery(this).data("opts") );
        for( var z in result){
            jQuery("#" + result[z]).closest("tr").fadeIn(500);
        }
    });
}



//The same to PHP 'explode' function
function explode( delimiter, string ) {	// Split a string by string
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: kenneth
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

    var emptyArray = { 0: '' };

    if ( arguments.length != 2
        || typeof arguments[0] == 'undefined'
        || typeof arguments[1] == 'undefined' )
    {
        return null;
    }

    if ( delimiter === ''
        || delimiter === false
        || delimiter === null )
    {
        return false;
    }

    if ( typeof delimiter == 'function'
        || typeof delimiter == 'object'
        || typeof string == 'function'
        || typeof string == 'object' )
    {
        return emptyArray;
    }

    if ( delimiter === true ) {
        delimiter = '1';
    }

    return string.toString().split ( delimiter.toString() );
}
