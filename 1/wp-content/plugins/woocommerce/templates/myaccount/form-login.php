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

<!--      <input  class="forgetkey" style="border:0 black solid;filter:Alpha(opacity=40); " name="reg_btn" value="--><?php //_e( 'Register', 'woocommerce' ); ?><!--" type="button" onclick="setReg();setShow();">-->
        <a href="<?php echo esc_url( wc_reg_url() ); ?>" class="forgetkey" ><?php _e( 'Register', 'woocommerce' ); ?></a>
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
