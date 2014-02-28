<?php

# list my referral users [referral_users]
function WP_Referral_Users_shortcode( $atts ){
	global $wpdb;
	$referralUserHtml = "";
	$query = "select u.* from $wpdb->usermeta um left join $wpdb->users u on u.id = um.user_id where um.meta_key = 'referral_id' and um.meta_value = '".get_current_user_id()."'";

	if ( is_user_logged_in() ) {
		$referralUsers = $wpdb->get_results($query);
		foreach ( $referralUsers as $referralUser ) 
		{
			$referralUserHtml .= '<p>'.$referralUser->user_login.'</p>';
		}
	}
	return $referralUserHtml;
}

add_shortcode( 'referral_users', 'WP_Referral_Users_shortcode' );

?>    