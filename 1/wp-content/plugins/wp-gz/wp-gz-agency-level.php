<?php
if( is_admin() ) {
  /*  利用 admin_menu 钩子，添加菜单 */   
    add_action('admin_menu', 'display_agency_level_menu');
}

function display_agency_level_menu() {  
    /* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */ 
    /* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */ 
    add_options_page('代理等级设置', '代理等级', 'administrator','agency_level', 'agency_level_html_page');
}

function agency_level_html_page() {  
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
        <h2>代理等级设置</h2>  
        <form method="post" action="options.php">  
            <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>  
            <?php wp_nonce_field('update-options'); ?>  
	    <h3>Max Referral Levels</h3>				
            <p>
			<select name="max_referral_levels_select" id="max_referral_levels_select" onchange="selectedMaxLevels();" >
		<?php
			$cur_max_referral_levels = get_option('max_referral_levels');
			$selected_levels = 1;
			while($selected_levels < 6){
				echo '<option value="'.$selected_levels.'" '; 
				if( $selected_levels == $cur_max_referral_levels)
					echo 'selected="true" autofocus="true"';
				echo '>' . $selected_levels . '  Level(s)</option>';
				$selected_levels++;
			}
		?>	
			</select>
                <input type="hidden" name="max_referral_levels" id ="cur_levels" value="<?php $cur_max_referral_levels ?>" />  

            </p>
	<div id="agency_level_list">
	<?php
		$cur_level_count = 1;
		while( $cur_level_count < $selected_levels ){
	    echo '<h3>referral_level_'.$cur_level_count .'</h3>';
            echo '<p>';
	    echo' <label for="agency_level_'.$cur_level_count.'_commission_percent">Bonus Rate：</label>';
	echo'<input type="text"  name="referral_level_'.$cur_level_count.'_rate" id="referral_level_'.$cur_level_count.'_rate" value="';
	echo get_option('referral_level_'.$cur_level_count.'_rate') ;
	echo '"/>';	
	echo '<label> 0.xx (0 ~ 1)</label>';
            echo'</p>';
		$cur_level_count ++;
		
		}
	?>
	</div>
            <p>  
                <input type="hidden" name="action" value="update" />  
                <input type="hidden" name="page_options" value="
<?php
	$cur_level_count = 1;
	while( $cur_level_count < $selected_levels ){
		echo 'referral_level_'.$cur_level_count.'_rate,';
		$cur_level_count ++;
	}
	echo 'max_referral_levels';
 ?>
		" />  
 
                <input type="submit" value="Save Setting" class="button-primary" />  
            </p> 
        </form> 
    </div> 
<?php  
}   
?>    
