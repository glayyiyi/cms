<?php wc_print_notices(); ?>
<script type="text/javascript">
    WeixinJSBridge.invoke('getBrandWCPayRequest', <?php echo $_GET['requestJson']?>, function (res) {
        // 返回 res.err_msg,取值
        // get_brand_wcpay_request:cancel 用户取消 // get_brand_wcpay_request:fail 发送失败
        // get_brand_wcpay_request:ok 发送成功
        WeixinJSBridge.log(res.err_msg);
        alert(res.err_code + res.err_desc);
    });
</script>
