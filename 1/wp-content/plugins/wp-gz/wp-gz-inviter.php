<?php
wp_enqueue_script('jquery');
$can_refer = !empty($_GET['referral_id']) && !empty($_GET['uid']) && get_user_meta($_GET['uid'], 'referral_id', true) == '';
wc_print_notices();
?>
<style type="text/css">
    .invite{border-style: solid; border-width: 1px 0 1px 0; padding: 5px 4px 5px 4px; color:#FF7F00;}
</style>

<?php if ($can_refer) { ?>
    <div id="tips">
        <form method="post">
            <label><?php _e('your inviter: ', 'woocommerce') ?></label>
            <input type="text" name="inviter" id="inviter" placeholder="<?php _e('please type account', 'woocommerce'); ?>"/>
            <button onclick="submit();return false;"><label>
                    &nbsp;<?php _e('OK', 'woocommerce'); ?></label></button>
        </form>
    </div>
<?php } ?>

<div class="invite">
    <?php _e('invite good friend ', 'woocommerce'); ?>
</div>
<h4 id="tips">
    <?php _e('invitation explanation', 'woocommerce'); ?>
</h4>
<dl>
    <dt>1、直接点签到按钮，即可完成签到赚取金币，每日限一次哦。</dt>
    <dt>2、首日签到奖励100金币</dt>
    <dt>3、连续签到1-10天，每天奖励100金币</dt>
    <dt>4、连续签到11-25天，每天奖励200金币</dt>
    <dt>5、连续签到26-50天，每天奖励300金币</dt>
    <dt>6、连续签到50天以上，每天奖励500金币</dt>
</dl>

<script type="text/javascript">
    function submit() {
        jQuery(document).ready(function ($) {
            var data = {
                uid: "<?php echo $_GET['uid']?>"
            };
            $.get("<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/api/points/signInPerDay';?>", data, function (response) {
                if (response.status == 'OK') {
                    $('#tips').remove();
                } else {
                    alert(response.message)
                }
            });
        });
    }

</script>
