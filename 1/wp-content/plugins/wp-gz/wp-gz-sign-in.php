<?php
// if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$isChecked = true; //checkedin($_GET['uid'])
?>

<?php wc_print_notices(); ?>

<form class="login" method="post">
    <div class="connerdiv">
        <h4 id="tips">
            <?php if ($isChecked) { ?>
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
        <?php if (!$isChecked) { ?>
            <div id="checkindiv">
                <button onclick="checkin();return false;" style='margin-left:50%;'><label>
                        &nbsp;<?php _e('checkin', 'woocommerce') ?></label></button>
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
            $.get("<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/api/points/signInPerDay';?>", data, function (response) {
                if (response.status == 'OK') {
                    $('#tips').text("<?php _e('hascheckedin', 'woocommerce')?>");
                    $('#checkindiv').remove();
                } else {
                    alert(response.message)
                }
            });
        });
    }

</script>
