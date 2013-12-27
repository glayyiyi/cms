/**
 * Created with JetBrains PhpStorm.
 * User: Office
 * Date: 12.12.12
 * Time: 12:13
 * To change this template use File | Settings | File Templates.
 */
jQuery(document).ready(function(){
    setInterval(function(){
        jQuery(".wpb_tile").each(function(){
            var pattern = jQuery(this).find(".pattern").html();
            var bgcolor = jQuery(this).find(".bgcolor").html();
            var text = jQuery(this).find(".text").html();
            jQuery(this).find(".wpb_vc_param_value").hide();
            jQuery(this).find(".wpb_element_wrapper").css("background-color",bgcolor);
        });
    },3000);
});
