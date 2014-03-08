<?php
// if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php wc_print_notices(); ?>

<form class="login" method="post">
    <div class="connerdiv">
        <?php echo do_shortcode('[mycred_my_ranking user_id="'.$_GET['uid'].'"]'); ?>
        <?php echo do_shortcode('[mycred_leaderboard number="10"]'); ?>
    </div>
</form>
<script type="text/javascript">

</script>
