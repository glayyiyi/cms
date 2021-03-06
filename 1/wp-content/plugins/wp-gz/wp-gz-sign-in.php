<?php
wp_enqueue_script('jquery');
// if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$canReward = class_exists( 'myCRED_Hook_Logging_In' ) && (new myCRED_Hook_Logging_In( array('myCRED_Hook_Logging_In') ))->reward_login( $_GET['uid'], false);

?>

<?php wc_print_notices(); ?>
<style type="text/css">
    .btn_red{color:#fff; width: 100%; height: 45px; box-shadow:0 1px 1px rgba(0,0,0,.2);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#3cb371',endColorstr='#32B25D');background:-ms-linear-gradient(top, #3cb371, #3cb371);/*IE10*/background: -webkit-gradient(linear, 0 0, 0 100%, from(#3cb371), to(#32B25D));background: -moz-linear-gradient(top, #3cb371, #32B25D); border:#32B25D 1px solid;}
</style>
<form class="login" method="post">
    <div class="connerdiv">
        <h4 id="tips">
            <?php if ($canReward) { ?>
                今天还没有签到，快来签到吧
            <?php } else { ?>
                今天已经签到过，明天再来吧
            <?php } ?>
        </h4>
        <dl>
            <dt>1、直接点签到按钮，即可完成签到赚取金币，每日限一次哦。</dt>
            <dt>2、首日签到奖励100金币</dt>
            <dd>连续签到1-10天，每天奖励100金币</dd>
            <dd>连续签到11-25天，每天奖励200金币</dd>
            <dd>连续签到26-50天，每天奖励300金币</dd>
            <dd>连续签到50天以上，每天奖励500金币</dd>
        </dl>
        <?php if ($canReward) { ?>
            <div id="checkindiv">
                <button onclick="checkin();return false;" class='btn_red'><label>
                        &nbsp;<?php _e('check in', 'woocommerce') ?></label></button>
            </div>
        <?php } ?>
    </div>
</form>
<script type="text/javascript">
    function checkin() {
        jQuery(document).ready(function ($) {
            var data = {
                uid: "<?php echo $_GET['uid']?>"
            };
            $.get("<?php echo get_site_url().'/api/points/signInPerDay';?>", data, function (response) {
                if (response.status == 'ok') {
                    $('#tips').text("<?php _e('hascheckedin', 'woocommerce')?>");
                    $('#checkindiv').remove();
                } else {
                    alert(response.message)
                }
            });
        });
    }

</script>
