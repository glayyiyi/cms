<?php
if (is_admin()) {
    /*  利用 admin_menu 钩子，添加菜单 */
    add_action('admin_menu', 'display_cred_list_menu');
}

function display_cred_list_menu()
{
    /* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */
    /* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */
    add_options_page('金币查询', '金币兑换管理', 'administrator', 'cred_list_adm', 'cred_list_adm_html_page');
}

function cred_list_adm_html_page()
{
    ?>
    <script type='text/javascript'>
        function clickAll() {
            var checkes = document.getElementsByName("account[]");
            var checked = document.getElementById("all").checked;
            for (var i = 0; i < checkes.length; i++) {
                checkes[i].checked = checked;
            }
        }

        function formSubmit() {
            var checkes = document.getElementsByName("account[]");
            var index = 0;
            for (var i = 0; i < checkes.length; i++) {
                var box = checkes[i]
                if (box.checked) {
                    box.name = 'account[' + index + ']';
                    index++;
                }
            }
        }
    </script>
    <div>
        <script type="text/javascript" src="<?php echo plugins_url('assets/js/My97DatePicker/WdatePicker.js', GZ_THIS);?>"></script>
        <h2>积分查询</h2>
        <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>
        <?php //wp_nonce_field('update-options'); ?>
        <h3>积分列表</h3>

        <form method="get" action="">
            <label for="mobile">手机号</label>
            <input type="hidden" name="page" value="<?php echo $_GET['page'] ;?>"/>
            <input type="text" id="mobile" name="mobile" value="<?php echo $_GET['mobile'] ;?>" placeholder="请输入手机号"/>
            起始时间<input class="Wdate" name="from_date" id="from_date" value="<?php echo $_GET['from_date'] ;?>" type="text" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm:ss', maxDate: '#F{$dp.$D(\'end_date\')}'})">
            截止时间<input class="Wdate" name="end_date" id="end_date" value="<?php echo $_GET['end_date'] ;?>" type="text" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm:ss', minDate: '#F{$dp.$D(\'from_date\')}'})">
            <input type="submit" value="查看">
            </form>
            <?php $cred_page = new Cred_page();
            $cred_page -> query_gz_cred();
            echo $cred_page->page_navigation();?>
        <form method="get" action="" onsubmit="formSubmit()">
            <input type="submit" value="兑换">
            <table>
                <thead>
                <th style="text-align:center;width:100px;">用户ID</th>
                <th style="text-align:center;width:100px;">积分</th>
                <th style="text-align:center;width:200px;">手机号</th>
                <th style="text-align:center;width:200px;">时间</th>
                <th style="text-align:center;width:100px;">
                    <input type="checkbox" onclick="clickAll()" id="all"/> 全选
                </th>
                </thead>
                <?php
                if (isset($_GET['account'])) {
                    foreach ($_GET['account'] as $item) {
                        update_user_meta($item, 'take_away', 1);
                    }
                }

                echo $cred_page -> gz_cred_display();
                ?>

        </form>
    </div>
<?php
}

?>
