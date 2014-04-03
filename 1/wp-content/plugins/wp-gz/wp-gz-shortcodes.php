<?php
//if ( !defined( 'myCRED_VERSION' ) ) exit;
/**
 * myCRED Shortcode: my_balance
 * Returns the current users balance.
 * @see
 * @contributor
 * @since
 * @version
 */
if (!function_exists('gz_cred_list')) {
    function gz_cred_list($atts)
    {
        $result_list = query_gz_cred();

        return gz_cred_display($result_list);
    }
}

if (!function_exists('gz_cred_display')) {
    function gz_cred_display($list)
    {
        $table_head = '<form method="get" action="">
            <label for="mobile">手机号</label>
            <input type="hidden" name="page" value="'.$_GET['page'].'"/>
            <input type="text" id="mobile" name="mobile" placeholder="请输入手机号"/>
            <input type="submit" value="查看" onclick="multiCheck()">
            <table><thead>
            <th style="text-align:center;" colspan="3"> 合作用户积分列表 </th>
            </thead>
            <thead>
            <th style="text-align:center;width:100px;">用户ID</th>
            <th style="text-align:center;width:100px;">积分</th>
            <th style="text-align:center;width:200px;">手机号</th>
            <th style="text-align:center;width:200px;">时间</th>
            <th style="text-align:center;width:100px;"><input type="checkbox" click="clickAll()" id="all"/> 全选</th>
            </thead>';

        $output = '';
        $total = 0;
        foreach ( $list as $cred1 ) {
            $output.='<tr><td style="text-align:center;width:100px;">'
                . $cred1->user_id
                .'</td> <td style="text-align:center;width:100px;">'
                .$cred1->creds.
                '</td><td style="text-align:center;width:200px;">'
                .$cred1->mobile.'</td><td style="text-align:center;width:200px;">'
                .$cred1->regtime.'</td><td>
            <input type="checkbox" class="checkbox" name="account[]" value="'.$cred1->user_id.'"/> 设为兑换<td></tr>';
            $total += (int)$cred1->creds;
        }
        if($output == ''){
            $output .= '<tr><td text-align="center" colspan="5">当前还有没合作用户</td></tr>';
        } else {
            $output .= '<tr><td text-align="center" colspan="5">当前列表总积分：'.$total.'</td></tr>';
        }
        $output .= '</form>';
        return $table_head . $output;
    }
}
?>
