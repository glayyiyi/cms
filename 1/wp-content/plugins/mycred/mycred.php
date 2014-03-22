<?php
/**
 * Plugin Name: myCRED
 * Plugin URI: http://mycred.me
 * Description: <strong>my</strong>CRED is an adaptive points management system for WordPress powered websites, giving you full control on how points are gained, used, traded, managed, logged or presented.
 * Version: 1.3.3.2
 * Tags: points, tokens, credit, management, reward, charge, buddypress, bbpress, jetpack, woocommerce
 * Author: Gabriel S Merovingi
 * Author URI: http://www.merovingi.com
 * Author Email: support@mycred.me
 * Requires at least: WP 3.1
 * Tested up to: WP 3.8.1
 * Text Domain: mycred
 * Domain Path: /lang
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * SSL Compatible: yes
 * bbPress Compatible: yes
 * WordPress Compatible: yes
 * BuddyPress Compatible: yes
 * Forum URI: http://mycred.me/support/forums/
 */
define( 'myCRED_VERSION',      '1.3.3.2' );
define( 'myCRED_SLUG',         'mycred' );
define( 'myCRED_NAME',         '<strong>my</strong>CRED' );

define( 'myCRED_THIS',          __FILE__ );
define( 'myCRED_ROOT_DIR',      plugin_dir_path( myCRED_THIS ) );
define( 'myCRED_ABSTRACTS_DIR', myCRED_ROOT_DIR . 'abstracts/' );
define( 'myCRED_ADDONS_DIR',    myCRED_ROOT_DIR . 'addons/' );
define( 'myCRED_ASSETS_DIR',    myCRED_ROOT_DIR . 'assets/' );
define( 'myCRED_INCLUDES_DIR',  myCRED_ROOT_DIR . 'includes/' );
define( 'myCRED_LANG_DIR',      myCRED_ROOT_DIR . 'lang/' );
define( 'myCRED_MODULES_DIR',   myCRED_ROOT_DIR . 'modules/' );
define( 'myCRED_PLUGINS_DIR',   myCRED_ROOT_DIR . 'plugins/' );

require_once( myCRED_INCLUDES_DIR . 'mycred-functions.php' );
require_once( myCRED_INCLUDES_DIR . 'mycred-about.php' );

require_once( myCRED_ABSTRACTS_DIR . 'mycred-abstract-hook.php' );
require_once( myCRED_ABSTRACTS_DIR . 'mycred-abstract-module.php' );

/**
 * myCRED_Core Class
 * Removed in 1.3 but defined since some customizations
 * use this to check that myCRED exists or is installed.
 * @since 1.3
 * @version 1.0
 */
if ( !class_exists( 'myCRED_Core' ) ) {
	final class myCRED_Core {

		public $plug;

		/**
		 * Construct
		 */
		function __construct() {
			$this->plug = plugin_basename( myCRED_THIS );
			// no longer used
		}
	}
}

/**
 * Required
 * @since 1.3
 * @version 1.1
 */
function mycred_load() {
	require_once( myCRED_INCLUDES_DIR . 'mycred-remote.php' );
	require_once( myCRED_INCLUDES_DIR . 'mycred-log.php' );
	require_once( myCRED_INCLUDES_DIR . 'mycred-network.php' );
	require_once( myCRED_INCLUDES_DIR . 'mycred-protect.php' );

	// Bail now if the setup needs to run
	if ( is_mycred_ready() === false ) return;

	require_once( myCRED_INCLUDES_DIR . 'mycred-rankings.php' );
	require_once( myCRED_INCLUDES_DIR . 'mycred-widgets.php' );

	// Add-ons
	require_once( myCRED_MODULES_DIR . 'mycred-module-addons.php' );
	$addons = new myCRED_Addons();
	$addons->load();

	do_action( 'mycred_ready' );

	add_action( 'init',         'mycred_init', 5 );
	add_action( 'widgets_init', 'mycred_widgets_init' );
	add_action( 'admin_init',   'mycred_admin_init' );
}
mycred_load();

/**
 * Plugin Activation
 * @since 1.3
 * @version 1.0
 */
register_activation_hook( myCRED_THIS, 'mycred_plugin_activation' );
function mycred_plugin_activation()
{
	// Load Installer
	require_once( myCRED_INCLUDES_DIR . 'mycred-install.php' );
	$install = new myCRED_Install();

	// Compatibility check
	$install->compat();

	// First time activation
	if ( $install->ver === false )
		$install->activate();
	// Re-activation
	else
		$install->reactivate();

	// Add Cron Schedule
	if ( !wp_next_scheduled( 'mycred_reset_key' ) ) {
		$frequency = apply_filters( 'mycred_cron_reset_key', 'daily' );
		wp_schedule_event( date_i18n( 'U' ), $frequency, 'mycred_reset_key' );
	}

	// Delete stray debug options
	delete_option( 'mycred_catch_fires' );
}

/**
 * Runs when the plugin is deactivated
 * @since 1.3
 * @version 1.0
 */
register_deactivation_hook( myCRED_THIS, 'mycred_plugin_deactivation' );
function mycred_plugin_deactivation() {
	// Clear Cron
	wp_clear_scheduled_hook( 'mycred_reset_key' );
	wp_clear_scheduled_hook( 'mycred_banking_recurring_payout' );
	wp_clear_scheduled_hook( 'mycred_banking_interest_compound' );
	wp_clear_scheduled_hook( 'mycred_banking_interest_payout' );

	do_action( 'mycred_deactivation' );
}

/**
 * Runs when the plugin is deleted
 * @since 1.3
 * @version 1.0
 */
register_uninstall_hook( myCRED_THIS, 'mycred_plugin_uninstall' );
function mycred_plugin_uninstall()
{
	// Load Installer
	require_once( myCRED_INCLUDES_DIR . 'mycred-install.php' );
	$install = new myCRED_Install();

	do_action( 'mycred_before_deletion', $install );

	// Run uninstaller
	$install->uninstall();

	do_action( 'mycred_after_deletion', $install );
	unset( $install );
}

/**
 * myCRED Plugin Startup
 * @since 1.3
 * @version 1.0
 */
add_action( 'plugins_loaded', 'mycred_plugin_start_up', 999 );
function mycred_plugin_start_up()
{
	global $mycred;
	$mycred = new myCRED_Settings();

	require_once( myCRED_INCLUDES_DIR . 'mycred-shortcodes.php' );

	// Load Translation
	load_plugin_textdomain( 'mycred', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	// Adjust the plugin links
	add_filter( 'plugin_action_links_mycred/mycred.php', 'mycred_plugin_links', 10, 4 );
	add_filter( 'plugin_row_meta', 'mycred_plugin_description_links', 10, 2 );

	// Lets start with Multisite
	if ( is_multisite() ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		if ( is_plugin_active_for_network( 'mycred/mycred.php' ) ) {
			$network = new myCRED_Network();
			$network->load();
		}
	}

	// Load only hooks that we have use of
	if ( defined( 'JETPACK__PLUGIN_DIR' ) ) 
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-jetpack.php' );

	if ( class_exists( 'bbPress' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-bbPress.php' );

	if ( function_exists( 'invite_anyone_init' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-invite-anyone.php' );

	if ( function_exists( 'wpcf7' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-contact-form7.php' );

	if ( class_exists( 'BadgeOS' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-badgeOS.php' );

	if ( function_exists( 'vote_poll' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-wp-polls.php' );

	if ( function_exists( 'wp_favorite_posts' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-wp-favorite-posts.php' );

	if ( function_exists( 'bp_em_init' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-events-manager-light.php' );

	if ( defined( 'STARRATING_DEBUG' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-gd-star-rating.php' );

	if ( defined( 'SFTOPICS' ) )
		require_once( myCRED_PLUGINS_DIR . 'mycred-hook-simplepress.php' );

	// Load Settings
	require_once( myCRED_MODULES_DIR . 'mycred-module-general.php' );
	$settings = new myCRED_General();
	$settings->load();

	// Load hooks
	require_once( myCRED_MODULES_DIR . 'mycred-module-hooks.php' );
	$hooks = new myCRED_Hooks();
	$hooks->load();

	// Load log
	require_once( myCRED_MODULES_DIR . 'mycred-module-log.php' );
	$log = new myCRED_Log();
	$log->load();
	
	do_action( 'mycred_pre_init' );
}

/**
 * Init
 * @since 1.3
 * @version 1.1
 */
function mycred_init()
{
	// Enqueue scripts & styles
	add_action( 'wp_enqueue_scripts',    'mycred_enqueue_front' );
	add_action( 'admin_enqueue_scripts', 'mycred_enqueue_admin' );

	add_action( 'admin_head',     'mycred_admin_head', 999 );
	// Admin Menu
	add_action( 'admin_menu',     'mycred_admin_menu', 9 );

	// Admin Bar / Tool Bar
	add_action( 'admin_bar_menu', 'mycred_hook_into_toolbar' );

	// Shortcodes
	if ( !is_admin() ) {
		add_shortcode( 'mycred_history',     'mycred_render_shortcode_history'    );
		add_shortcode( 'mycred_leaderboard', 'mycred_render_leaderboard'          );
		add_shortcode( 'mycred_my_ranking',  'mycred_render_my_ranking'           );
		add_shortcode( 'mycred_my_balance',  'mycred_render_shortcode_my_balance' );
		add_shortcode( 'mycred_give',        'mycred_render_shortcode_give'       );
		add_shortcode( 'mycred_send',        'mycred_render_shortcode_send'       );
	}

	// Let others play
	do_action( 'mycred_init' );
}

/**
 * Widgets Init
 * @since 1.3
 * @version 1.0
 */
function mycred_widgets_init()
{
	// Register Widgets
	register_widget( 'myCRED_Widget_Balance' );
	register_widget( 'myCRED_Widget_List' );

	// Let others play
	do_action( 'mycred_widgets_init' );
}

/**
 * Admin Init
 * @since 1.3
 * @version 1.1
 */
function mycred_admin_init()
{
	// Load admin
	require_once( myCRED_INCLUDES_DIR . 'mycred-admin.php' );
	$admin = new myCRED_Admin();
	$admin->load();

	require_once( myCRED_INCLUDES_DIR . 'mycred-overview.php' );

	// Let others play
	do_action( 'mycred_admin_init' );

	if ( get_transient( '_mycred_activation_redirect' ) === apply_filters( 'mycred_active_redirect', false ) )
		return;

	delete_transient( '_mycred_activation_redirect' );

	$url = add_query_arg( array( 'page' => 'mycred' ), admin_url( 'index.php' ) );
	wp_safe_redirect( $url );
	die;
}

/**
 * Remove About Page
 * @since 1.3.2
 * @version 1.0
 */
function mycred_admin_head()
{
	remove_submenu_page( 'index.php', 'mycred' );
	remove_submenu_page( 'index.php', 'mycred-credit' );
}

/**
 * Adjust the Tool Bar
 * @since 1.3
 * @version 1.1
 */
function mycred_hook_into_toolbar( $wp_admin_bar )
{
	global $bp;
	if ( isset( $bp ) ) return;

	$mycred = mycred_get_settings();
	$user_id = get_current_user_id();
	if ( $mycred->exclude_user( $user_id ) ) return;

	$cred = $mycred->get_users_cred( $user_id );

	$wp_admin_bar->add_group( array(
		'parent' => 'my-account',
		'id'     => 'mycred-actions',
	) );

	if ( $mycred->can_edit_plugin() )
		$url = 'users.php?page=mycred_my_history';
	else
		$url = 'profile.php?page=mycred_my_history';

	$my_balance = apply_filters( 'mycred_label_my_balance', __( 'My Balance: %cred_f%', 'mycred' ), $user_id, $mycred );
	$wp_admin_bar->add_menu( array(
		'parent' => 'mycred-actions',
		'id'     => 'user-creds',
		'title'  => $mycred->template_tags_amount( $my_balance, $cred ),
		'href'   => add_query_arg( array( 'page' => 'mycred_my_history' ), get_edit_profile_url( $user_id ) )
	) );

	// Let others play
	do_action( 'mycred_tool_bar', $mycred );
}

/**
 * Add myCRED Admin Menu
 * @uses add_menu_page()
 * @since 1.3
 * @version 1.1
 */
function mycred_admin_menu()
{
	$mycred = mycred_get_settings();
	$name = mycred_label( true );

	$icon = 'dashicons-star-filled';
	if ( isset( $GLOBALS['wp_version'] ) && version_compare( $GLOBALS['wp_version'], '3.8', '<' ) )
		$icon = '';

	$pages = array();
	$pages[] = add_menu_page(
		$name,
		$name,
		$mycred->edit_creds_cap(),
		'myCRED',
		'',
		apply_filters( 'mycred_icon_menu', $icon )
	);

	$about_label = sprintf( __( 'About %s', 'mycred' ), $name );
	$pages[] = add_dashboard_page(
		$about_label,
		$about_label,
		$mycred->edit_creds_cap(),
		'mycred',
		'mycred_about_page'
	);

	$cred_label = __( 'Awesome People', 'mycred' );
	$pages[] = add_dashboard_page(
		$cred_label,
		$cred_label,
		$mycred->edit_creds_cap(),
		'mycred-credit',
		'mycred_about_credit_page'
	);

	foreach ( $pages as $page )
		add_action( 'admin_print_styles-' . $page, 'mycred_admin_page_styles' );

	// Let others play
	do_action( 'mycred_add_menu', $mycred );
}

/**
 * Enqueue Front
 * @filter 'mycred_remove_widget_css'
 * @since 1.3
 * @version 1.0
 */
function mycred_enqueue_front()
{
	global $mycred_sending_points;

	// Send Points Shortcode
	wp_register_script(
		'mycred-send-points',
		plugins_url( 'assets/js/send.js', myCRED_THIS ),
		array( 'jquery' ),
		myCRED_VERSION . '.1',
		true
	);

	// Widget Style (can be disabled)
	if ( apply_filters( 'mycred_remove_widget_css', false ) === false ) {
		wp_register_style(
			'mycred-widget',
			plugins_url( 'assets/css/widget.css', myCRED_THIS ),
			false,
			myCRED_VERSION . '.1',
			'all'
		);
		wp_enqueue_style( 'mycred-widget' );
	}

	// Let others play
	do_action( 'mycred_front_enqueue' );
}

/**
 * Enqueue Admin
 * @since 1.3
 * @version 1.2.1
 */
function mycred_enqueue_admin()
{
	$mycred = mycred_get_settings();
	// General Admin Script
	wp_register_script(
		'mycred-admin',
		plugins_url( 'assets/js/accordion.js', myCRED_THIS ),
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ),
		myCRED_VERSION . '.1'
	);
	wp_localize_script( 'mycred-admin', 'myCRED', apply_filters( 'mycred_localize_admin', array( 'active' => '-1' ) ) );

	// Management Admin Script
	wp_register_script(
		'mycred-manage',
		plugins_url( 'assets/js/management.js', myCRED_THIS ),
		array( 'jquery', 'mycred-admin', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-slide' ),
		myCRED_VERSION . '.1'
	);
	wp_localize_script(
		'mycred-manage',
		'myCREDmanage',
		array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'token'         => wp_create_nonce( 'mycred-management-actions' ),
			'working'       => __( 'Processing...', 'mycred' ),
			'confirm_log'   => __( 'Warning! All entries in your log will be permamenly removed! This can not be undone!', 'mycred' ),
			'confirm_reset' => __( 'Warning! All user balances will be set to zero! This can not be undone!', 'mycred' ),
			'done'          => __( 'Done!', 'mycred' ),
			'export_close'  => __( 'Close', 'mycred' ),
			'export_title'  => $mycred->template_tags_general( __( 'Export users %plural%', 'mycred' ) )
		)
	);

	// Inline Editing Script
	wp_register_script(
		'mycred-inline-edit',
		plugins_url( 'assets/js/inline-edit.js', myCRED_THIS ),
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-slide' ),
		myCRED_VERSION . '.1'
	);
	wp_localize_script(
		'mycred-inline-edit',
		'myCREDedit',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'title'   => sprintf( __( 'Edit Users %s balance', 'mycred' ),$mycred->plural() ),
			'close'   => __( 'Close', 'mycred' ),
			'working' => __( 'Processing...', 'mycred' )
		)
	);

	// Admin Style
	wp_register_style(
		'mycred-admin',
		plugins_url( 'assets/css/admin.css', myCRED_THIS ),
		false,
		myCRED_VERSION . '.1',
		'all'
	);
	wp_register_style(
		'mycred-inline-edit',
		plugins_url( 'assets/css/inline-edit.css', myCRED_THIS ),
		false,
		myCRED_VERSION . '.1',
		'all'
	);
	wp_register_style(
		'mycred-dashboard-overview',
		plugins_url( 'assets/css/overview.css', myCRED_THIS ),
		false,
		myCRED_VERSION . '.1',
		'all'
	);

	// Let others play
	do_action( 'mycred_admin_enqueue' );
}

/**
 * Enqueue Admin Styling
 * @since 1.3
 * @version 1.0
 */
function mycred_admin_page_styles()
{
	wp_enqueue_style( 'mycred-admin' );
}

/**
 * Reset Key
 * @since 1.3
 * @version 1.0
 */
add_action( 'mycred_reset_key', 'mycred_reset_key' );
function mycred_reset_key()
{
	$protect = mycred_protect();
	$protect->reset_key();
}

/**
 * myCRED Plugin Links
 * @since 1.3
 * @version 1.1
 */
function mycred_plugin_links( $actions, $plugin_file, $plugin_data, $context )
{
	// Link to Setup
	if ( ! is_mycred_ready() )
		$actions['_setup'] = '<a href="' . admin_url( 'plugins.php?page=myCRED-setup' ) . '">' . __( 'Setup', 'mycred' ) . '</a>';
	else
		$actions['_settings'] = '<a href="' . admin_url( 'admin.php?page=myCRED' ) . '" >' . __( 'Settings', 'mycred' ) . '</a>';

	ksort( $actions );
	return $actions;
}

/**
 * myCRED Plugin Description Links
 * @since 1.3.3.1
 * @version 1.0
 */
function mycred_plugin_description_links( $links, $file )
{
	$plugin = plugin_basename( myCRED_THIS );
	if ( $file != $plugin ) return $links;
	
	// Link to Setup
	if ( ! is_mycred_ready() ) {
		$links[] = '<a href="' . admin_url( 'plugins.php?page=myCRED-setup' ) . '">' . __( 'Setup', 'mycred' ) . '</a>';
		return $links;
	}

	$links[] = '<a href="' . admin_url( 'index.php?page=mycred' ) . '">' . __( 'About', 'mycred' ) . '</a>';
	$links[] = '<a href="http://mycred.me/support/tutorials/" target="_blank">' . __( 'Tutorials', 'mycred' ) . '</a>';
	$links[] = '<a href="http://codex.mycred.me/" target="_blank">' . __( 'Codex', 'mycred' ) . '</a>';
	$links[] = '<a href="http://mycred.me/store/" target="_blank">' . __( 'Store', 'mycred' ) . '</a>';

	return $links;

}


///{-------DOMOB points , referral -----------
/*
We want to parse DOMOB's request, insert the user's points into DB.
and count the referral bonus, insert the bonus points into referral person's account too.
*/
///-------DOMOB points 

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
	else{
		echo "no md5sum";
		return false;
	}
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
		){
		echo "true\n";
		return true;
	}
	else{
		echo "false\n";
		return false;
	}
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
		//	echo "<-test-->\n";
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
	//	echo $params['user']."\n";

	$haveAddedOrder =  get_option( "domob_orderid_".$params['orderid'], NULL );
	if( !empty($params) ){
		$userid = $params['user'];
		$price = $params['price'];
		$entry = " 安装使用 ".$params['ad'];
		$memo = " order=".$params['orderid']." ad=".$params['ad']." adid=".$params['adid']." device=".$params['device']." real= ".$params['price']." ";
		if( !empty($haveAddedOrder) && strlen($haveAddedOrder) > 6 ){
			echo "[domob]\n".$haveAddedOrder;
			return;
		}

		//echo " \n".$userid." ";

		if( !empty($userid) && $price > 0 ){
			//	echo " ".$price."\n ";
			//mycred_load();
			//do_action('mycred_admin_init');
			require_once( myCRED_INCLUDES_DIR . 'mycred-admin.php' );
			$admin = new myCRED_Admin();
			$admin->load();
			//do_action('admin_init');
			// current_level = 0, amount = price * 1/2  * 100 point, rate = 100%, max_level =1,
			add_option( "domob_orderid_".$params['orderid'], $userid." added" );
			//echo " domob_orderid=".$userid."\n ";

			$this_count_price = $price * 100 * $rate;
			$memo .= " price=".$this_count_price;

			$settings = new myCRED_Settings();
			$user =get_user_by('login', $userid);
			if(empty($user) ){
				$user = get_user_by('id', $userid);
				if( empty($user) )
					return;
			}
			$settings->add_creds('download', $user->id, $this_count_price,
					" 由 " . $userid . " " . $entry, $params['adid'], '', 'mycred_default', $memo);

			count_referral_bonus( $user->id, 0, $this_count_price , 1 ); //, $entry);
		}
	}
}


# Parse user's referral ,and added bonus

function count_referral_bonus( $userlogin, $current_level, $amount, $max_level) {
    if( $current_level < 0 || $max_level < 0 || $current_level > $max_level ){
        // end the recursion
        return true;
    }

    $user =get_user_by('id', $userlogin);
	if( empty($user) ){
        return false;
    }

    //call edit_user_balance;
//    $attr = array();
//    $attr['user'] = $user->ID;
//    $attr['amount'] = $amount * $rate;
//    $attr['entry'] = " 由 " . $parent_id . " " . $entry;
//    $attr['memo'] = $memo;
//    do_action('wp_mycred_outside_edit_users_balance',  $attr );

    $referral_id = get_user_meta($user->ID, 'referral_id', true);
	$referral_level = $user->data->referral_level;
	$referral_rate = 0.2;//get_option( 'referral_level_'.($current_level + 1).'_rate' ); //$user->data->referral_rate;
	$referral_max_level = 2;//get_option( 'max_referral_levels' );//$user->data->max_referral_level;
    if( !empty($referral_id ) && !empty($referral_rate) && $referral_rate > 0 ){
        $price = $amount * $referral_rate;
        $settings = new myCRED_Settings();
        $settings->add_creds('cascade_bonus', $referral_id, $price,
            "下家安装应用时获取提成", $user->ID, '', 'mycred_default', 'price='.$price);

        count_referral_bonus( $referral_id, $current_level + 1, $amount * 0.5,
            $referral_max_level);
    }

    return true;

}
///-------DOMOB points , referral -----------
///-------DOMOB points  END.}
?>
