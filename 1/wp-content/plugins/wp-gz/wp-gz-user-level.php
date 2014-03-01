<?php
if( is_admin() ) {
  /*  利用 admin_menu 钩子，添加菜单 */
    add_action('admin_menu', 'display_user_level_menu');   
}

function display_user_level_menu() {  
    /* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */ 
    /* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */ 
    add_options_page('用户等级设置', '用户等级', 'administrator','user_level', 'user_level_html_page');  
}

function user_level_html_page() {  
?>  
    <div>  
        <h2>用户等级设置</h2>  
        <form method="post" action="options.php">  
            <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>  
            <?php wp_nonce_field('update-options'); ?>  
	    <h3>用户等级1</h3>				
            <p>
		<label for="user_level_1_desc">名称：</label>  
                <input type="text"  
                    name="user_level_1_desc" 
                    id="user_level_1_desc" value="<?php echo get_option('user_level_1_desc'); ?>"/>
            </p>
            <p>
		<label for="user_level_1_month_setup">月装机量：</label> 
		<input type="text"  
                    name="user_level_1_month_setup" 
                    id="user_level_1_month_setup" value="<?php echo get_option('user_level_1_month_setup'); ?>"/>
            </p>
            <p>
		<label for="user_level_1_award_commission_percent">奖励佣金比例：</label> 
		<input type="text"  
                    name="user_level_1_award_commission_percent" 
                    id="user_level_1_award_commission_percent" value="<?php echo get_option('user_level_1_award_commission_percent'); ?>"/>
            </p>
            
		<h3>用户等级2</h3>				
            <p>
		<label for="user_level_2_desc">名称：</label>  
                <input type="text"  
                    name="user_level_2_desc" 
                    id="user_level_2_desc" value="<?php echo get_option('user_level_2_desc'); ?>"/>
            </p>
            <p>
		<label for="user_level_2_month_setup">月装机量：</label> 
		<input type="text"  
                    name="user_level_2_month_setup" 
                    id="user_level_2_month_setup" value="<?php echo get_option('user_level_2_month_setup'); ?>"/>
            </p>
            <p>
		<label for="user_level_2_award_commission_percent">奖励佣金比例：</label> 
		<input type="text"  
                    name="user_level_2_award_commission_percent" 
                    id="user_level_2_award_commission_percent" value="<?php echo get_option('user_level_2_award_commission_percent'); ?>"/>
            </p>

	    <h3>用户等级3</h3>				
            <p>
		<label for="user_level_3_desc">名称：</label>  
                <input type="text"  
                    name="user_level_3_desc" 
                    id="user_level_3_desc" value="<?php echo get_option('user_level_3_desc'); ?>"/>
            </p>
            <p>
		<label for="user_level_3_month_setup">月装机量：</label> 
		<input type="text"  
                    name="user_level_3_month_setup" 
                    id="user_level_3_month_setup" value="<?php echo get_option('user_level_3_month_setup'); ?>"/>
            </p>
            <p>
		<label for="user_level_3_award_commission_percent">奖励佣金比例：</label> 
		<input type="text"  
                    name="user_level_3_award_commission_percent" 
                    id="user_level_3_award_commission_percent" value="<?php echo get_option('user_level_3_award_commission_percent'); ?>"/>
            </p>

	    <h3>用户等级4</h3>				
            <p>
		<label for="user_level_4_desc">名称：</label>  
                <input type="text"  
                    name="user_level_4_desc" 
                    id="user_level_4_desc" value="<?php echo get_option('user_level_4_desc'); ?>"/>
            </p>
            <p>
		<label for="user_level_4_month_setup">月装机量：</label> 
		<input type="text"  
                    name="user_level_4_month_setup" 
                    id="user_level_4_month_setup" value="<?php echo get_option('user_level_4_month_setup'); ?>"/>
            </p>
            <p>
		<label for="user_level_4_award_commission_percent">奖励佣金比例：</label> 
		<input type="text"  
                    name="user_level_4_award_commission_percent" 
                    id="user_level_4_award_commission_percent" value="<?php echo get_option('user_level_4_award_commission_percent'); ?>"/>
            </p>
            
            <p>  
                <input type="hidden" name="action" value="update" />  
                <input type="hidden" name="page_options" value="user_level_1_desc,user_level_1_month_setup,user_level_1_award_commission_percent,user_level_2_desc,user_level_2_month_setup,user_level_2_award_commission_percent,user_level_3_desc,user_level_3_month_setup,user_level_3_award_commission_percent,user_level_4_desc,user_level_4_month_setup,user_level_4_award_commission_percent" />  
 
                <input type="submit" value="保存设置" class="button-primary" />  
            </p>  
        </form>  
    </div>  
<?php  
} 


  
?>    
