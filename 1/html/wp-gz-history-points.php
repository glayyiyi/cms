
<html lang="zh-CN" class="boxed ">
<head>

<meta charset="UTF-8" />

<title> 果子助手</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="http://www.appcn100.com/cms/iagent/wp-content/themes/mystile/style.css" media="screen" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<?php
require( dirname(__FILE__) . '/../wp-load.php' );
?>


<?php
// if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php wc_print_notices(); ?>

<form class="login" method="post">
    <div class="connerdiv">
        <?php $offset = -1;
        if (isset($_GET['offset']) && is_numeric($_GET['offset'])){
            $offset = (int)$_GET['offset'];
        }
        $offset += 1;
        echo do_shortcode('[mycred_history user_id="'.$_GET['uid'].'" number="10" offset="'.$offset.'"]'); ?>
    </div>
</form>
<script type="text/javascript">

</script>
