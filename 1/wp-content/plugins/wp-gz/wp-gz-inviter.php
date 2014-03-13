<?php
wp_enqueue_script('jquery');
$can_refer = !empty($_GET['uid']) && get_user_meta($_GET['uid'], 'referral_id', true) == '';
wc_print_notices();
?>
<style type="text/css">
    .invite{border-style: solid; border-width: 1px 0 1px 0; padding: 5px 4px 5px 4px; color:#FF7F00;}
</style>

<?php if ($can_refer) { ?>
    <div id="tips">
        <form method="post">
            <label><?php _e('your inviter: ', 'woocommerce'); ?></label>
            <input type="text" name="inviter" id="inviter" placeholder="<?php _e('please type account', 'woocommerce'); ?>"/>
            <button onclick="submit();return false;"><label>
                    &nbsp;<?php _e('OK', 'woocommerce'); ?></label></button>
        </form>
    </div>
<?php } ?>
<div class="invite">
    <?php _e('invite good friend ', 'woocommerce'); ?>
</div>
<h4>
    <?php _e('invitation explanation', 'woocommerce'); ?>
</h4>
<dl>
    <dt>1、邀请好友使用 果子助手，每个受邀者安装激活后，你就将长期获得30%佣金提成，即时到账。如：
        邀请成功10个好友后：下家下载得金币，你将获得20％提成；下下家下载得金币，你将获得10％提成；
        邀请成功10个好友前，下家下载得金币，你将获得10％提成；下下家下载得金币，你将获得 5％提成；</dt>
    <dt>2、如受邀请者为作弊用户，将双倍扣除金币，严重者封号处理！</dt>
    <dt>3、受邀者需要通过手机打开链接，并下载安装使用，或者让你的好友在“邀请有奖”处，输入你的果子号即可；</dt>
    <dt>4、你可以通过手机QQ、微信、微博等方式发给朋友。</dt>
</dl>

<script type="text/javascript">
    function submit() {
        jQuery(document).ready(function ($) {
            var inviter = $("#inviter").val();
            if (inviter == ""){
                alert("<?php _e('please type account', 'woocommerce'); ?>");
                return ;
            }

            var data = {"uid": "<?php echo $_GET['uid']?>", "referral_id": inviter};
            $.post("<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/api/register/addInviter';?>", data, function (response) {
                if (response.status == 'ok') {
                    $('#tips').remove();
                } else {
                    alert(response.message)
                }
            }, "json");
        });
    }

</script>
