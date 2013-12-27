try{
    jQuery(document).ready(function(){
        jQuery('.new_widget_add').on('click', function(){
            var id = jQuery(this).data("name");
            var name = jQuery("#" + id).val();
            jQuery.ajax({
                url: base_url + '/wp-admin/admin-ajax.php',
                type: 'POST',
                data:{
                    action:'add_sidebar',
                    name:name
                },
                success:function(result){
                    //window.location.reload();
                    //alert( result );
                    var section = '<div class="sidebar_'+ name +'">';
                    section += '<span style="width:170px; margin:10px 5px 5px 0;display: inline-block">' + name + '</span>';
                    section += '&nbsp;&nbsp;<a class="button-secondary delete_widget_sidebar" data-sidebar =' + name + ' data-name="new_sidebar_' + name +'">Delete sidebar</a> <br>';
                    section += '</div>';
                    jQuery('.sidebar-list').append(section);

                }
            });
        });

        jQuery("body").on('click', '.delete_widget_sidebar',function(){
            var name = jQuery(this).data("sidebar");

            jQuery.ajax({
                url: base_url + '/wp-admin/admin-ajax.php',
                type: 'POST',
                data:{
                    action:'delete_sidebar',
                    name:name
                },
                success:function(result){
                    //window.location.reload();
                    var selector = ('.sidebar_' + name).split(" ").join(".");
                    jQuery(selector).remove();
                }
            });

        });
    });
} catch (e){

}

