<?php


# Set/Read Cookie ref
$ref = 0;
$ref = (int)$_GET['ref'];
if (isset($_GET['ref']) && !empty($_GET['ref']) && $_GET['ref'] != 0) {
	$ref = (int)$_POST['ref'];
	setcookie('ref',(int)$_GET['ref'],time()+60*60*24*30);
}
else if (isset($_COOKIE['ref']) && !empty($_COOKIE['ref']))
$ref = (int)$_COOKIE['ref'];

/*
We want to parse DOMOB's request, insert the user's points into DB.
and count the referral bonus, insert the bonus points into referral person's account too.
///{-------DOMOB points , referral -----------
///-------DOMOB points 
*/

# Parse request URL, connect with DOMOB
function getSignatureWithDOMOB($params, $private_key){
        $signStr = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $signStr .= "{$k}={$v}";
        }
		$signStr .= $private_key;
        return md5($signStr);
    }
function getUrlSignature($orgurl, $private_key){
        $params = array();
	$url = '';
	$md5sign = '';
	$publicId = '';
	$signPos = strrpos( $orgurl, "&sign=" );
	if(  $signPos > 0 ){
		$md5sign = substr( $orgurl, $signPos + 6 );
		$url = substr( $orgurl, 0, strrpos( $orgurl, "&sign=") );
	}
	else
		return "no md5sum";
        $url_parse = parse_url($url);
        if (isset($url_parse['query'])){
            $query_arr = explode('&', $url_parse['query']);
            if (!empty($query_arr)){
                foreach($query_arr as $p){
                    if (strpos($p, '=') !== false){
                        list($k, $v) = explode('=', $p);
                        $params[$k] = urldecode($v);
			if( !empty($params['pubid'] ) )
				$publicId = $params['pubid'];
		
                    }
                }
            }
        }
        if ( getSignatureWithDOMOB($params, $private_key) == $md5sign 
		// && $publicId == get_option('referral_domob_public_id') 
		)
		return true;
	else
		return false;
    }


add_action( 'referral_with_domob', 'parse_domob_callback' );

function parse_domob_callback(){
	$url = trim('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	$ref = (int)$_GET['ref'];
	
	echo $url."\n";

        $params = array();
	$rate = get_option('referral_given_user_rate');
	if( empty($rate) )
		$rate = 0.5;

	$privateKey = get_option('referral_domob_private_key' );
	if( empty($privateKey) )
		$privateKey = 'd3c98aa2';

	if( getUrlSignature( $url, $privateKey ) == true ){
        $url_parse = parse_url($url);
        if (isset($url_parse['query'])){
            $query_arr = explode('&', $url_parse['query']);
            if (!empty($query_arr)){
                foreach($query_arr as $p){
                    if (strpos($p, '=') !== false){
                        list($k, $v) = explode('=', $p);
                        $params[$k] = urldecode($v);
                    }
                }
            }
        }
		
	}
	if( !empty($params) ){
		$userid = $params['user'];
		$price = $params['price'];
	
		if( !empty($userid) && $price > 0 ){
			mycred_load();
			do_action('admin_init');
			// current_level = 0, amount = price * 1/2  * 100 point, rate = 100%, max_level =1,
			count_referral_bonus( $userid, 0, $price * $rate * 100, 1, 1, $userid );
		}
	}
}


# Parse user's referral ,and added bonus
add_action( 'referral_bonus_count_recursion', 'count_referral_bonus' );

function count_referral_bonus( $user_id, $current_level, $amount, $rate, $max_level, $parent_id ) {
    global  $wpdb;
    $user = get_userdata( $user_id );
	if( empty($user) )
		return false;
    $referral_id = get_user_meta($user_id,'referral_id', true);
	$referral_level = $user->data->referral_level;
	$referral_rate = 0.2;//get_option( 'referral_level_'.($current_level + 1).'_rate' ); //$user->data->referral_rate;
	$referral_max_level = 2;//get_option( 'max_referral_levels' );//$user->data->max_referral_level;

	if( $current_level < 0 || $max_level < 0 || $current_level > $max_level ){
		// end the recursion 
		return true;
	}
	
	if( $current_level >= 0 && $max_level > 0 && $current_level <= $max_level ){
		//call edit_user_balance; 
	    $attr = array();
		$attr['user'] = $user_id;
		$attr['amount'] = $amount * $rate;
		$attr['entry'] = "added by" . $parent_id;
		
		echo $referral_id;
		echo $referral_rate;
		do_action('wp_mycred_outside_edit_users_balance',  $attr );
		if( !empty($referral_id ) && !empty($referral_rate) && $referral_rate > 0 )
		count_referral_bonus( $referral_id, $current_level + 1, $amount, $referral_rate, $referral_max_level, $user_id );
		return true;
	}

	return false;
}
///-------DOMOB points , referral -----------
///-------DOMOB points  END.}

# Edit User
add_action( 'personal_options_update', 'update_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'update_extra_profile_fields' );

function update_extra_profile_fields( $user_id ) { 
    global $current_user,$wpdb;
    get_currentuserinfo();
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;
    if (in_array('administrator', $current_user->roles) || $current_user->data->ID==$user_id){
	    $userdata = array();
	    //$userdata['ID'] = $user_id;                    
	    $userdata['referral_id'] = $_POST['referral_id'];
	    update_user_meta( $user_id, 'referral_id', $_POST['referral_id'] );
    }
}

# Adding User 
add_action('user_register', 'register_post_fields');
function register_post_fields($user_id, $password='', $meta=array())  {
    $userdata = array();
    $userdata['ID'] = $user_id;
    $userdata['referral_id'] = $_POST['referral_id'];
    wp_update_user($userdata);
    update_user_meta( $user_id, 'referral_id', $_POST['referral_id'] );    
}

# Listing User ID & Referral ID in profile page
function profile_fields($profile_fields) {
    global $current_user,$wpdb;
    get_currentuserinfo();
	// Add new fields ONLY for admin
	if (in_array('administrator', $current_user->roles)){
		$profile_fields['referral_id'] = '推荐人ID';
	}
	return $profile_fields;
}
add_filter('user_contactmethods', 'profile_fields');

# Adding hidden WP Refferal code to the registration form
add_action('register_form','register_extra_fields');
function register_extra_fields(){
	$ref = 0;
	$ref = (int)$_GET['ref'];
	if (isset($_COOKIE['ref']) && !empty($_COOKIE['ref']))
	$ref = (int)$_COOKIE['ref'];
	echo '<input type="hidden"  size="25" value="'.$ref.'" name="referral_id" readonly="readonly" />';
} 

# Adding hidden WP Refferal codein users listing
add_filter('manage_users_columns', 'add_user_data_column');
function add_user_data_column($columns) {
    $columns['user_id'] = '用户ID';
    $columns['referral_id'] = '推荐人ID';
    return $columns;
}
add_action('manage_users_custom_column',  'show_user_id_content', 10, 3);
function show_user_id_content($value, $column_name, $user_id) {
    $user = get_userdata( $user_id );
    switch ($column_name) {
        case 'user_id' :
        	return $user_id;
            break;
        case 'referral_id' :
            return get_the_author_meta('referral_id', $user->ID);
            break;
        default:
    }
    return $return;
}	

# Adding shortcode [referral_link]
function WP_Referral_link_shortcode( $atts ){
	if ( is_user_logged_in() ) {
		$html = "您的推广应用下载链接：".site_url('/go.php?ref=').get_current_user_id()."<br />";
		return $html;
	}
}
add_shortcode( 'referral_link', 'WP_Referral_link_shortcode' );
?>    
