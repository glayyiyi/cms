<?php
global $wpdb;
 
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = 20;
$offset = ( $pagenum - 1 ) * $limit;
$query = "select u.* from $wpdb->usermeta um left join $wpdb->users u on u.id = um.user_id where um.meta_key = 'referral_id' and um.meta_value = '".get_current_user_id()."'";
$entries = $wpdb->get_results("$query LIMIT $offset, $limit" );
 
echo '<div class="wrap">';
 
?>
<table class="widefat">
    <thead>
        <tr>
	    <th scope="col" class="manage-column column-name" style="">序号</th>
            <th scope="col" class="manage-column column-name" style="">被推荐人ID</th>
            <th scope="col" class="manage-column column-name" style="">被推荐人昵称</th>
        </tr>
    </thead>
 
    <tbody>
        <?php if( $entries ) { ?>
 
            <?php
            $count = 1;
            $class = '';
            foreach( $entries as $entry ) {
                $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
            ?>
 
            <tr<?php echo $class; ?>>
            	<td><?php echo $count; ?></td>
                <td><?php echo $entry->id; ?></td>
                <td><?php echo $entry->user_nickname; ?></td>
            </tr>
 
            <?php
                $count++;
            }
            ?>
 
        <?php } else { ?>
        <tr>
            <td colspan="2">尚未推荐其它用户</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
 
<?
 
$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM ($query)" );
$num_of_pages = ceil( $total / $limit );
$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum', '%#%' ),
    'format' => '',
    'prev_text' => __( '«', 'aag' ),
    'next_text' => __( '»', 'aag' ),
    'total' => $num_of_pages,
    'current' => $pagenum
) );
 
if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
 
echo '</div>';
?>    