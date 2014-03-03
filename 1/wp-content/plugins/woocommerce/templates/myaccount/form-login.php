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
<style>
.buttons button{
    width:auto;
    overflow:visible;
    padding:1px 1px 1px 1px; /* IE6 */
}
.buttons button[type]{
    padding:1px 1px 1px 1px; /* Firefox */
    line-height:10px; /* Safari */
}
*:first-child+html button[type]{
    padding:4px 10px 3px 7px; /* IE7 */
}
.buttons button img, .buttons a img{
    margin:0 3px -3px 0 !important;
    padding:0;
    border:none;
    width:16px;
    height:16px;
}
</style>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="col2-set" id="customer_login" style="display:block">

	<div class="col-1">

<?php endif; ?>

<!--		<h2><?php _e( 'Login', 'woocommerce' ); ?></h2> -->

		<form method="post" class="login">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

<div class="connerdiv">
      <dl class="connerbox">
        <dd >
        <span class="required">*</span><label for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> </label>:
          <input tabindex="3" class="text_input" placeholder="<?php _e( 'Username or email address', 'woocommerce' ); ?>" name="username" id="username" type="text">
        </dd>
        <dd >
          <span class="required">*</span><label for="password"><?php _e( 'Password', 'woocommerce' ); ?> </label>:
          <input tabindex="4" class="text_input" placeholder="<?php _e( 'Password', 'woocommerce' ); ?>" name="password" id="password" type="password">
        </dd>
      </dl>
                        <?php do_action( 'woocommerce_login_form' ); ?>
      <dl for="rememberme" class="inline">
          <input tabindex="6" name="rememberme" id="rememberme" value="forever" type="checkbox"> <?php _e( 'Remember me', 'woocommerce' ); ?>
      </dl>

        <?php wp_nonce_field( 'woocommerce-login' ); ?>
      <dl class="connerbox">
      <input  tabindex="5" class="btn_red" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" type="submit">
      </dl>

      <input  class="forgetkey" style="border:0 black solid;filter:Alpha(opacity=40); " name="reg_btn" value="<?php _e( 'Register', 'woocommerce' ); ?>" type="button" onclick="setReg();setShow();">
 <!--       <a href="<?php echo esc_url( wc_reg_url() ); ?>" class="forgetkey" ><?php _e( 'Register', 'woocommerce' ); ?></a> -->
        <a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" class="fr forgetkey" ><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>

                        <?php do_action( 'woocommerce_login_form_end' ); ?>

                </form>

    <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

        </div>

</div>

</div>
<?php endif; ?>
<script type="text/javascript">
function setReg() 
{ 
document.getElementById( "register_div" ).style.display = "inline"; 
document.getElementById( "customer_login" ).style.display = "none"; 
} 
function setShow() 
{ 
//document.getElementById( "register_div" ).style.display = "inline"; 
document.getElementById( "customer_login" ).style.display = "none"; 
} 
</script>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
<div id="register_div" style="display:none">

    <form  class="login" method="post">
        <?php do_action( 'woocommerce_register_form_start' ); ?>
        <div class="connerdiv">
            <dl class="connerbox">
                <dd><span><?php _e('mobile number', 'woocommerce')?></span><input id="user_name" name="username" placeholder="<?php _e('type mobile number', 'woocommerce')?>" maxlength="50" class="text_input" type="text"></dd>
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
                var phone = $('#user_name').val();
                if (phone==''){
                    alert("<?php _e( 'please type mobile number', 'woocommerce'); ?>" );
                    return;
                }

                var data={
                    action:'validate_captcha',
                    mobile: phone
                };
                $.get("<?php echo admin_url('admin-ajax.php');?>", data, function(response) {
                    if (response.indexOf('1') == -1){
                        alert("<?php _e( 'failed to validate captcha', 'woocommerce'); ?>")
                    } else {
                        document.getElementById( "submitdiv" ).style.display = "inline";
                        document.getElementById( "captchadiv" ).style.display = "none";
                        $('#user_name').attr("readonly","readonly");
                    }
                });
            });
        }
        var sixtySecond = 60;
        var inter = null;
        function countdown(){
            jQuery(document).ready(function($){
                var button = $("#messageBtn")
                if (sixtySecond == 0){
                    button.text("<label>&nbsp;<?php _e('send captcha', 'woocommerce')?></label>")
                    button.removeAttr("disabled");
                    if (inter != null){
                        clearInterval(inter);
                    }
                }
                button.text(sixtySecond--)
            })
        }

        function postMessage(){
            jQuery(document).ready(function($){
                var phone = $('#user_name').val();
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
                        $("#messageBtn").attr("disabled", disabled);
                    }
                });
            });
        }

    </script>

</div>