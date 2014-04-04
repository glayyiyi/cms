<?php
//if ( ! defined( 'myCRED_VERSION' ) ) exit;
if (!class_exists('Cred_page')) {
    final class Cred_page
    {

//        public $plug;

        public $result_list;
        public $total;

        public $page_number = 0;
        public $page_size = 50;

        /**
         * Construct
         */
        function __construct()
        {
        }

        function page_navigation()
        {
            $current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;

            if (isset($_GET['paged'])) {
                unset($_GET['paged']);
            }

            $base_url = add_query_arg($_GET, admin_url('admin.php'));

            $total_pages = ceil($this ->total / $this->page_size);

            $first_page_url = $base_url . '&amp;paged=1';
            $last_page_url = $base_url . '&amp;paged=' . $total_pages;

            if ($current_page > 1 && $current_page < $total_pages) {
                $prev_page = $current_page - 1;
                $prev_page_url = $base_url . '&amp;paged=' . $prev_page;

                $next_page = $current_page + 1;
                $next_page_url = $base_url . '&amp;paged=' . $next_page;
            } elseif ($current_page == 1) {
                $prev_page_url = '#';
                $first_page_url = '#';
                if ($total_pages > 1) {
                    $next_page = $current_page + 1;
                    $next_page_url = $base_url . '&amp;paged=' . $next_page;
                } else {
                    $next_page_url = '#';
                }
            } elseif ($current_page == $total_pages) {
                $prev_page = $current_page - 1;
                $prev_page_url = $base_url . '&amp;paged=' . $prev_page;
                $next_page_url = '#';
                $last_page_url = '#';
            }

            $pagination = '<div class="tablenav bottom" ><div class="tablenav-pages" >
        <span class="displaying-num" > 每页 '
                . $this->page_size . '共 ' . $total_pages . '</span><span class="pagination-links">
            <a class="first-page '
                . ($current_page == 1 ? 'disabled' : '') . '" title="前往第一页"
       href="' . $first_page_url . '">«</a><a class="prev-page '
                . ($current_page == 1 ? 'disabled' : '') . '" title="前往上一页"
           href="' . $prev_page_url . '">‹</a><span class="paging-input">第 '
                . $current_page . ' 页，共 <span class="total-pages">' . $total_pages . '</span> 页</span>
            <a class="next-page '
                . ($current_page == $total_pages ? 'disabled' : '') . '" title="前往下一页"
               href="' . $next_page_url . '">›</a>
            <a class="last-page ' . ($current_page == $total_pages ? 'disabled' : '') . '" title="前往最后一页"
               href="' . $last_page_url . '">»</a></span></div><br class="clear"></div>';
            return $pagination;
        }

        function gz_cred_display()
        {
            $output = '';
            $total = 0;
            foreach ($this->result_list as $cred1) {
                $output .= '
<tr>
    <td style="text-align:center;width:100px;">'
                    . $cred1->user_id
                    . '
    </td>
    <td style="text-align:center;width:100px;">'
                    . $cred1->creds .
                    '
                </td>
                <td style="text-align:center;width:200px;">'
                    . $cred1->mobile . '
    </td>
    <td style="text-align:center;width:200px;">'
                    . $cred1->regtime . '
    </td>
    <td>
        <input type="checkbox" class="checkbox" name="account[]" value="' . $cred1->user_id . '"/> 设为兑换
    <td>
</tr>';
                $total += (int)$cred1->creds;
            }
            if ($output == '') {
                $output .= '
<tr>
    <td text-align="center" colspan="5">当前还有没合作用户</td>
</tr>';
            } else {
                $output .= '
<tr>
    <td text-align="center" colspan="5">当前列表总积分：' . $total . '</td>
</tr>';
            }
            $output .= '</form>';
            return $output;
        }

        function query_gz_cred()
        {
            global $wpdb;

            $log_table = 'wp_10_mycred_log'; //$wpdb->prefix . 'mycred_log';

            $condition = " FROM " . $log_table . " AS a
LEFT JOIN wp_usermeta AS b ON a.user_id = b.user_id
LEFT JOIN wp_usermeta AS c ON c.user_id = a.user_id
AND c.meta_key = 'take_away'
AND c.meta_key !=1
LEFT JOIN wp_users AS d ON a.user_id = d.id
WHERE a.ref
IN ('cascade_bonus', 'download')
AND b.meta_key = 'mobile'";

            if (isset($_GET['mobile'])) {
                $condition .= " and b.meta_value like '%" . sanitize_text_field($_GET['mobile']) . "%' ";
            }

            if(isset($_GET['from_date'])){
                $condition .= " and d.user_registered >= '".sanitize_text_field($_GET['from_date']) . "' ";
            }

            if(isset($_GET['end_date'])){
                $condition .= " and d.user_registered < '".sanitize_text_field($_GET['end_date']) . "' ";
            }

            $condition .= " GROUP BY a.user_id order by d.user_registered asc";

            $total = "select count(1) ";
            $total .= $condition;
            $this->total = $wpdb->get_var($total);

            if (isset($_GET['page_number'])) {
                $this->page_number = absint($_GET['page_number']);
            }

            if (isset($_GET['page_size'])) {
                $this->page_size = absint($_GET['page_size']);
            }

            $first_index = $this->page_size * $this->page_number;
            $condition .= " limit " . ($first_index) . ',' . ($first_index + $this->$page_size);

            $sql = "SELECT a.user_id, SUM( a.creds ) as creds, b.meta_value AS 'mobile', d.user_registered as regtime ";
            $sql .= $condition;
            $this->result_list = $wpdb->get_results($sql);
        }
    }
}
?>
