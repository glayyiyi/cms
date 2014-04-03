<?php
//if ( ! defined( 'myCRED_VERSION' ) ) exit;


if (!function_exists('query_gz_cred')) {
    function query_gz_cred()
    {
        global $wpdb;

        $log_table = 'wp_10_mycred_log';//$wpdb->prefix . 'mycred_log';

        $sql = "SELECT a.user_id, SUM( a.creds ) as creds, b.meta_value AS 'mobile', d.user_registered as regtime FROM " . $log_table . " AS a
        LEFT JOIN wp_usermeta AS b ON a.user_id = b.user_id
        LEFT JOIN wp_usermeta AS c ON c.user_id = a.user_id
        AND c.meta_key = 'take_away'
        AND c.meta_key !=1
        LEFT JOIN wp_users AS d ON a.user_id = d.id
        WHERE a.ref
        IN ('cascade_bonus', 'download')
        AND b.meta_key = 'mobile'";
        if(isset($_GET['mobile'])){
            $sql .= " and b.meta_value like '%".$_GET['mobile']."%' ";
        }
        $sql .= " GROUP BY a.user_id order by d.user_registered asc";
        if(isset($_GET['page_number'])){
            $page_number = 0;
        }

        if(isset($_GET['page_size'])){
            $page_size = 50;
        }
//        $page_number = absint($page_number);
//        $page_size = absint($page_size);

        //$sql .= "limit ".($page_number*$page_size).','.(($page_number +1) * $page_size);

        $credsList = $wpdb->get_results($sql);
        return $credsList;
    }
}

?>
