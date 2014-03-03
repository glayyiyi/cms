<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>


<style type="text/css">

.connerdiv{ padding:10px 8px;}
.connerbox{ margin-bottom:10px; background:#fff; border-radius:4px; border:#dadada 1px solid; box-shadow:0px 1px 2px rgba(0,0,0,.1) inset; -webkit-box-shadow:0px 1px 2px rgba(0,0,0,.1) inset;-moz-box-shadow:0px 1px 2px rgba(0,0,0,.1) inset;}
.connerbox dd{ padding:6px 10px; font-style:normal; font-size:14px; border-bottom:#e0e0e0 1px solid;}
.connerbox dd span{ float:left; display:inline-block; line-height:30px;}
.connerbox dd:last-child{ border-bottom:none;}
.connerbox .text_input{ background:none; border:none; width:200px; height:30px;}

.btn_red{color:#fff;width:100%;box-shadow:0 1px 1px rgba(0,0,0,.2);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff3c9b',endColorstr='#e61f80');background:-ms-linear-gradient(top, #ff3c9b, #e61f80);/*IE10*/background: -webkit-gradient(linear, 0 0, 0 100%, from(#ff3c9b), to(#e61f80));background: -moz-linear-gradient(top, #ff3c9b, #e61f80); border:#db207b 1px solid;}
</style>
<form  class="login" method="post">
    <?php do_action( 'woocommerce_register_form_start' ); ?>
    <div class="connerdiv">
        <dl class="connerbox">
            <dd><span><?php _e('mobile number', 'woocommerce')?></span><input id="username" name="username" placeholder="<?php _e('type mobile number', 'woocommerce')?>" maxlength="50" class="text_input" type="text"></dd>
        </dl>
        <dl>
            <dl id="captchadiv">
                <span><?php _e('captcha', 'woocommerce')?></span>
                <input name="captcha" maxlength="4" class="testcode" type="text">

                <button id="messageBtn" onclick="postMessage();return false;"><label>&nbsp;<?php _e('send captcha', 'woocommerce')?></label></button>
                <dd><button onclick="validateCaptcha();return false;"><label>&nbsp;<?php _e('OK', 'woocommerce')?></label></button></dd>
            </dl>
            <div id="submitdiv" style="display: none" >
                <dl>
                    <dd><span><?php _e('Password', 'woocommerce')?></span><input name="password" value="" maxlength="50" placeholder="<?php _e('password strict', 'woocommerce')?>" class="text_input" type="password"></dd>
                    <dd><span><?php _e('confirm password', 'woocommerce')?></span><input name="confirm_password" value="" maxlength="50" placeholder="<?php _e('password strict', 'woocommerce')?>" class="text_input" type="password"></dd>
                </dl>
                <div class="space15"></div>
                <?php do_action( 'woocommerce_register_form' ); ?>
                <?php do_action( 'register_form' ); ?>
                <input name="register" class="btn_red largerbtn longbtn" value="<?php _e('Register', 'woocommerce')?>" type="submit">
                <?php do_action( 'woocommerce_register_form_end' ); ?>
            </div>
            <div class="space15"></div>
    </div>
</form>
<script type="text/javascript">
     function validateCaptcha(){
         jQuery(document).ready(function($){
             var phone = $('#username').val();
             if (phone==''){
                 alert("<?php _e( 'please type mobile number', 'woocommerce'); ?>" );
                 return;
             }

             var data={
                 action:'validate_captcha',
                 mobile: phone
             };
             $.post("<?php echo admin_url('admin-ajax.php');?>", data, function(response) {
                 if (response.indexOf('1') == -1){
                     alert("<?php _e( 'failed to validate captcha', 'woocommerce'); ?>")
                 } else {
                     document.getElementById( "submitdiv" ).style.display = "inline";
                     document.getElementById( "captchadiv" ).style.display = "none";
                     $('#username').attr("readonly","readonly");
                 }
             });
         });
     }
     var sixtySecond = 60;
     var inter = null;
     function countdown(){
         jQuery(document).ready(function($){
             var button = $("#messageBtn")

             if (!button.prop("disabled")){
                 button.attr("disabled", "disabled");
             }
             button.text(--sixtySecond)

             if (sixtySecond == 0){
                 button.html("<label>&nbsp;<?php _e('send captcha', 'woocommerce')?></label>")
                 button.removeAttr("disabled");
                 if (inter != null){
                     clearInterval(inter);
                 }
                 sixtySecond = 60;
             }
         })
     }

     function postMessage(){
	jQuery(document).ready(function($){
		var phone = $('#username').val();
		if (phone==''){
			alert("<?php _e( 'please type mobile number', 'woocommerce'); ?>" );
			return;
		}
				
		var data={
			action:'post_message',
			mobile: phone
		};
		$.post("<?php echo admin_url('admin-ajax.php');?>", data, function(response) {			
		 if ('0'!=$(response).find('result').text()){
			alert("<?php _e( 'failed to send message', 'woocommerce'); ?>")
		} else {
             inter = window.setInterval(countdown, 1000)
         }
		});
	});
}

</script>
