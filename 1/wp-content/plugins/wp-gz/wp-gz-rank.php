<?php
// if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php wc_print_notices(); ?>

<?php _e('my rank', 'woocommerce'); ?>
<strong><?php echo do_shortcode('[mycred_my_ranking user_id="' . $_GET['uid'] . '"]'); ?></strong>
<table>
    <thead>
    <th style="text-align:center;" colspan="3"><?php _e('ranking list', 'woocommerce'); ?></th>
    </thead>
    <thead>
    <th style="text-align:center;"><?php _e('ranking', 'woocommerce'); ?></th>
    <th style="text-align:center;"><?php _e('Account username', 'woocommerce'); ?></th>
    <th style="text-align:center;"><?php _e('points', 'woocommerce'); ?></th>
    </thead>
    <?php echo do_shortcode('[mycred_leaderboard number="10"]<tr><td style="text-align:center;">%ranking%</td> <td style="text-align:center;">%display_name%</td><td style="text-align:center;">%cred_f%</td></tr>[/mycred_leaderboard]'); ?>
</table>
