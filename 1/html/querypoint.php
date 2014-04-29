

<html lang="zh-CN" class="boxed ">
<head>

<meta charset="UTF-8" />

<title> 果子助手</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="http://www.appcn100.com/cms/iagent/wp-content/themes/mystile/style.css" media="screen" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<?php
require( dirname(__FILE__) . '/../wp-load.php' );
?>

<?php

    ?>
    <script type='text/javascript'>
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
            <input type="hidden" name="page" value="<?php echo $_GET['page'] ;?>"/>
            <table>
                <thead>
                <th style="text-align:center;width:100px;">用户ID</th>
                <th style="text-align:center;width:100px;">积分</th>
                <th style="text-align:center;width:200px;">手机号</th>
                <th style="text-align:center;width:200px;">时间</th>
                </thead>
                <?php

                echo $cred_page -> gz_cred_display();
                ?>

        </form>
    </div>
<?php

?>
