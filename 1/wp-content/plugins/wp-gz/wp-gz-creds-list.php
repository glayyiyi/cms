<?php
if( is_admin() ) {
    /*  利用 admin_menu 钩子，添加菜单 */
    add_action('admin_menu', 'display_cred_list_menu');
}

function display_cred_list_menu() {
    /* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */
    /* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */
    add_options_page('金币查询', '金币兑换管理', 'administrator','cred_list_adm', 'cred_list_adm_html_page');
}

function cred_list_adm_html_page() {
    ?>
    <script type='text/javascript'>
        function selectedMaxLevels()
        {
            var obj = document.getElementById('max_referral_levels_select'); //selectid
            var index = obj.selectedIndex;
            var value = obj.options[index].value; // 选中值
            document.getElementById('cur_levels').value = value;

        }

        function clickAll(){
            var checked = document.getElementById("all").checked;
            var boxes = document.getElementsByName("account[]");
            for (var i=0; i< boxes.length; i++){
                boxes[i].name = 'account['+i+']'
                boxes[i].checked = checked;
            }
        }
    </script>
    <div>
        <h2>积分查询</h2>
        <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>
        <?php //wp_nonce_field('update-options'); ?>
        <h3>积分列表</h3>
        <p>
            <?php
            if(isset($_GET['account'])){
                foreach($_GET['account'] as $item){
                    update_user_meta($item, 'take_away', 1);
                }
                }
            $credsList = query_gz_cred();
            echo  gz_cred_display($credsList);
            ?>

        </p>
    </div>
<?php
}
?>    
