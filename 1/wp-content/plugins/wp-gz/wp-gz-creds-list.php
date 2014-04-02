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
</script> 
    <div>  
        <h2>积分查询</h2>  
        <form method="post" action="options.php">  
            <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>  
            <?php //wp_nonce_field('update-options'); ?>  
	    <h3>积分列表</h3>				
            <p>
		<?php
			global $wpdb;
			$sql = "SELECT a.user_id, SUM( a.creds ) as creds, b.meta_value AS 'mobile'
, d.user_registered as regtime
FROM `wp_10_mycred_log` AS a
LEFT JOIN wp_usermeta AS b ON a.user_id = b.user_id
LEFT JOIN wp_usermeta AS c ON c.user_id = a.user_id
AND c.meta_key = 'take_away'
AND c.meta_key !=1
LEFT JOIN wp_users AS d ON a.user_id = d.id
WHERE a.ref
IN (
'cascade_bonus', 'download'
)
AND b.meta_key = 'mobile'
GROUP BY a.user_id";
			$credsList = $wpdb->get_results( $sql );
			if( is_array($credsList) && $credsList != array()  ){
			echo '<table>';
?>
<table>
    <thead>
    <th style="text-align:center;" colspan="3"> 合作用户积分列表 </th>
    </thead>
    <thead>
    <th style="text-align:center;width:100px;">用户ID</th>
    <th style="text-align:center;width:100px;">积分</th>
    <th style="text-align:center;width:200px;">手机号</th>
    <th style="text-align:center;width:200px;">时间</th>
    <th style="text-align:center;width:100px;"></th>
    </thead>
<?php
				foreach ( $credsList as $cred1 ) {
			echo sprintf('<tr><td style="text-align:center;width:100px;">%1$s</td> <td style="text-align:center;width:100px;">%2$s</td>
		<td style="text-align:center;width:200px;">%3$s</td>
		<td style="text-align:center;width:200px;">%4$s</td>
		<td><a href="/#">设为兑换</a><td>
		</tr>', $cred1->user_id, $cred1->creds, $cred1->mobile, $cred1->regtime); 
				}
?>

</table>
<?php
			}
?>	

            </p>
            </p> 
        </form> 
    </div> 
<?php  
}   
?>    
