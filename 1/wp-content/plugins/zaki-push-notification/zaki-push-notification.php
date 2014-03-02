<?php
/*
Plugin Name: Zaki Push Notification
Description: Add Apple Push Notification system for post and custom post type
Author: Zaki Design
Author URI: http://www.zaki.it 
Version: 1.1
*/

define('ZAKI_PN_PATH',__FILE__);

// CSS main
function ZakiPushNotification_BackendCss() {
    wp_register_style('zaki-push-notification-css',plugins_url('css/main.css', ZAKI_PN_PATH));
    wp_enqueue_style('zaki-push-notification-css');
}
add_action('admin_init','ZakiPushNotification_BackendCss');

// JS main
function ZakiPushNotification_JqueryCheck() {
	wp_enqueue_media();
    wp_enqueue_script('js-zaki-push-notification', plugins_url('js/js-zaki-push-notification.js', ZAKI_PN_PATH), array('jquery'));
}
add_action('admin_enqueue_scripts','ZakiPushNotification_JqueryCheck');

// Classe main
require_once plugin_dir_path(ZAKI_PN_PATH).'classes/class-zaki-push-notification.php';

// Hooks & Init
add_action('admin_init', 'ZakiPushNotification_SettingsInit');
add_action('admin_menu', 'ZakiPushNotification_AddMenuPages');
register_activation_hook(ZAKI_PN_PATH, 'ZakiPushNotification_Activation');
register_deactivation_hook(ZAKI_PN_PATH, 'ZakiPushNotification_Deactivation');
register_uninstall_hook(ZAKI_PN_PATH, 'ZakiPushNotification_Uninstall');

// Ajax request
add_action('wp_ajax_zaki-push-notification-ajax','ZakiPushNotification_AjaxSave');

// Activation plugin
function ZakiPushNotification_Activation() {   

    global $wpdb;    
    
    // Install db table
    $table_name = ZakiPushNotification::getTableName();
    $wpdb->query("CREATE TABLE IF NOT EXISTS $table_name (
        `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `udid` CHAR( 64 ) NOT NULL ,
        `token` CHAR( 64 ) NOT NULL ,
        `registration_date` DATE NOT NULL ,
        `last_update` DATE NOT NULL
        ) ENGINE = MYISAM;");
                                
    // Create the PEM file folder with .htaccess protection
    $upload_dir = wp_upload_dir();
    $protected_dir = $upload_dir['basedir'].'/zaki-pem-folder/';
    if(!file_exists($protected_dir)) {
        if(wp_mkdir_p($protected_dir)) {
            $ht_protected = $protected_dir.'.htaccess';
            if(!file_exists($ht_protected)) {
                if(!file_put_contents($ht_protected,"Order deny,allow\nDeny from all")) wp_die("<p>Non posso creare il file htaccess nella cartella protetta</p>");
            }
        } else { wp_die("<p>Non posso creare la cartella protetta</p>"); }
    }
    
    // Init options
    if(!get_option('zaki_push_notification_options')) :
        $settings = array(
            'pem_file' => '',
            'pem_password' => '',
            'ssl_server' => 'gateway.sandbox.push.apple.com',
            'ssl_server_port' => '2195'
        );
        update_option('zaki_push_notification_options', $settings);
    endif;
    
    // First activation
    update_option('zaki_push_notification_fistactivationcheck', true);
}

// Deactivation plugin
function ZakiPushNotification_Deactivation() {
        
    // Delete rewrite option
    delete_option('zaki_push_notification_fistactivationcheck');
            
}

// Uninstall plugin
function ZakiPushNotification_Uninstall() {
    // Uninstall db table
    global $wpdb;
    $table_name = ZakiPushNotification::getTableName();
    $wpdb->query("DROP TABLE IF EXISTS $table_name;");
        
    // Delete rewrite option
    delete_option('zaki_push_notification_fistactivationcheck');
            
    // Unregister options
    unregister_setting('zaki_push_notification_options','zaki_push_notification_options');
}

// Settings Init
function ZakiPushNotification_SettingsInit() {
    
    // Register options
    register_setting('zaki_push_notification_options','zaki_push_notification_options');
    
    // Add setting fields
    add_settings_section(
        'zaki_push_notification_options_section_main',
        __('General Settings','zaki'),
        'ZakiPushNotification_PageSetting_Section_Main_Callback',
        'zaki-push-notification');
        
        add_settings_field(
            'zaki_push_notification_op_pem_file',
            __('Upload PEM file','zaki'),
            'ZakiPushNotification_PageSetting_Section_Main_pemFile_Callback',
            'zaki-push-notification',
            'zaki_push_notification_options_section_main');
            
        add_settings_field(
            'zaki_push_notification_op_pem_pass',
            __('Password of PEM file for SSL connection','zaki'),
            'ZakiPushNotification_PageSetting_Section_Main_pemPass_Callback',
            'zaki-push-notification',
            'zaki_push_notification_options_section_main');
            
        add_settings_field(
            'zaki_push_notification_op_ssl_server',
            __('SSL server url','zaki'),
            'ZakiPushNotification_PageSetting_Section_Main_sslServer_Callback',
            'zaki-push-notification',
            'zaki_push_notification_options_section_main');
            
        add_settings_field(
            'zaki_push_notification_op_ssl_server_port',
            __('SSL server port','zaki'),
            'ZakiPushNotification_PageSetting_Section_Main_sslServerPort_Callback',
            'zaki-push-notification',
            'zaki_push_notification_options_section_main');
}

// Sezione generale
function ZakiPushNotification_PageSetting_Section_Main_Callback() {
    echo '';
}

    // Settaggio PEM file
    function ZakiPushNotification_PageSetting_Section_Main_pemFile_Callback() {
        $settings = get_option('zaki_push_notification_options');
        $pem_file = (isset($settings['pem_file'])) ? $settings['pem_file'] : '';
        $upload_dir = wp_upload_dir();
        if($pem_file!='' and !file_exists($upload_dir['basedir'].'/zaki-pem-folder/'.$pem_file)) $pem_file = '';
        ?>
        <fieldset>
            <label for="zaki_push_notification_options[pem_file]">
                <input id="upload_pem_file" type="text" size="36" name="zaki_push_notification_options[pem_file]" value="<?=$pem_file?>" /> 
                <input id="upload_pem_file_button" class="button" type="button" data-uploader_title="PEM Uploader" value="Open Library" />
                <br />
                <p class="description">
                    <?=__('The file will be stored in a protected folder under /uploads','zaki')?>
                </p>
            </label>
        </fieldset>
        <?php
    }
    
    // Settaggio password per PEM file
    function ZakiPushNotification_PageSetting_Section_Main_pemPass_Callback() {
        $settings = get_option('zaki_push_notification_options');
        $pem_pass = (isset($settings['pem_pass'])) ? $settings['pem_pass'] : '';
        ?>
        <fieldset>
            <label for="zaki_push_notification_options[pem_pass]">
                <input name="zaki_push_notification_options[pem_pass]" type="text" value="<?=$pem_pass?>" />
            </label>
        </fieldset>
        <?php
    }
    
    // Settaggio SSL Server Path
    function ZakiPushNotification_PageSetting_Section_Main_sslServer_Callback() {
        $settings = get_option('zaki_push_notification_options');
        $ssl_server = (isset($settings['ssl_server'])) ? $settings['ssl_server'] : '';
        ?>
        <fieldset>
            <label for="zaki_push_notification_options[ssl_server]">
                <input name="zaki_push_notification_options[ssl_server]" type="text" value="<?=$ssl_server?>" />
                <br />
                <p class="description">
                    <?=__('Use:','zaki')?><br />
                    <?=__('gateway.sandbox.push.apple.com (developer sandbox server)','zaki')?><br />
                    <?=__('gateway.push.apple.com (real push notification server)','zaki')?>
                </p>
            </label>
        </fieldset>
        <?php
    }
    
    // Settaggio SSL Server Port
    function ZakiPushNotification_PageSetting_Section_Main_sslServerPort_Callback() {
        $settings = get_option('zaki_push_notification_options');
        $ssl_server_port = (isset($settings['ssl_server_port'])) ? $settings['ssl_server_port'] : '';
        ?>
        <fieldset>
            <label for="zaki_push_notification_options[ssl_server_port]">
                <input name="zaki_push_notification_options[ssl_server_port]" type="text" value="<?=$ssl_server_port?>" class="zakismall" />
                <br />
                <p class="description">
                    <?=__('Standard port: 2195','zaki')?>
                </p>
            </label>
        </fieldset>
        <?php
    }

// Inizializzazione pagine menu
function ZakiPushNotification_AddMenuPages() {
    add_menu_page(
        __('Zaki Push Notification','zaki'),
        __('Zaki Push Notification','zaki'),
        'manage_options',
        'zaki-push-notification',
        'ZakiPushNotification_PageSettingHtml'
        );
        add_submenu_page(
            'zaki-push-notification',
            __('Documentation','zaki'),
            __('Documentation','zaki'),
            'manage_options',
            'zaki-push-notification-documentation',
            'ZakiPushNotification_SubPageDocumentationHtml'
            );
        add_submenu_page(
            'zaki-push-notification',
            __('Credits','zaki'),
            __('Credits','zaki'),
            'manage_options',
            'zaki-push-notification-credits',
            'ZakiPushNotification_SubPageCreditsHtml'
            );
}

// HTML Pagina principale di settaggio (main)
function ZakiPushNotification_PageSettingHtml() {
    $settings = get_option('zaki_push_notification_options');
    if(isset($_GET['settings-updated']) && $_GET['settings-updated']) :
        echo '<div class="updated"><p>'.__('Settings saved successfully','zaki').'</p></div>';
    endif;
    ?>  
    <div class="wrap zaki_push_notification_page zaki_push_notification_page_main">
        <?php screen_icon('options-general'); ?><h2><?=__('Zaki Push Notification','zaki')?></h2>      
        
        <form method="post" action="options.php">
            <?php settings_fields('zaki_push_notification_options'); ?>
            <?php do_settings_sections('zaki-push-notification'); ?>
            <p class="submit">
               <input name="submit" type="submit" id="submit" class="button-primary" value="<?=__('Save','zaki')?>" />
            </p>
            <input type="hidden" name="zaki-plugin-page" value="<?=get_current_screen()->parent_base?>" />
        </form>
    </div>
    <?php
}

    // HTML Pagina documentation Zaki
    function ZakiPushNotification_SubPageDocumentationHtml() {
        ?>  
        <div class="wrap zaki_push_notification_page zaki_push_notification_page_documentation">
            <?php screen_icon('options-general'); ?><h2><?=__('Zaki Post Order','zaki')?> - <?=__('Documentation','zaki')?></h2>
            <h3><?=__('Devices management','zaki')?></h3>
            <p><?=__('For a correct use of this plugin you must send a registration/deletion request from your app to this site and storing UDID code and token.','zaki')?></p>
            <table class="form-table">
                <tr>
                    <td style="vertical-align:top;">
                        <p><strong><?=__('For registration','zaki')?></strong></p>
                    </td>
                    <td>
                        <p class="description">
                            <?=__('You need to send the request via this address by replacing the entries for codes','zaki')?><br />
                            <strong><?=get_bloginfo('url')?>/zaki-push-notification/put/token/UDID_TOKEN</strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">
                        <p><strong><?=__('For deletion','zaki')?></strong></p>
                    </td>
                    <td>
                        <p class="description">
                            <?=__('You need to send the request via this address by replacing the entries for codes','zaki')?><br />
                            <strong><?=get_bloginfo('url')?>/zaki-push-notification/delete/token/UDID_TOKEN</strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p><?=__('Both calls return a JSON with the information on the outcome of the request','zaki')?></p>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
    
    // HTML Pagina dei credits Zaki
    function ZakiPushNotification_SubPageCreditsHtml() {
        ?>  
        <div class="wrap zaki_push_notification_page zaki_push_notification_page_credits">
            <?php screen_icon('options-general'); ?><h2><?=__('Zaki Post Order','zaki')?> - <?=__('Credits','zaki')?></h2>
            <p>Developed by <a target="_blank" href="http://www.zaki.it">Zaki Design</a></p>
        </div>
        <?php
    }
    
/* Change Upload folder */
 
add_filter('wp_handle_upload_prefilter', 'ZakiPushNotification_upload_prefilter');
add_filter('wp_handle_upload', 'ZakiPushNotification_upload');

function ZakiPushNotification_upload_prefilter( $file ) {
    add_filter('upload_dir', 'ZakiPushNotification_upload_dir');
    add_filter('upload_mimes', 'ZakiPushNotification_mime_enable');
    return $file;
}

function ZakiPushNotification_upload( $fileinfo ) {
    remove_filter('upload_mimes', 'ZakiPushNotification_mime_disable');
    remove_filter('upload_dir', 'ZakiPushNotification_upload_dir');
    return $fileinfo;
}

function ZakiPushNotification_mime_enable($mimes) {
    $mimes['pem'] = 'application/x-pem-file';
    return $mimes;
}

function ZakiPushNotification_mime_disable($mimes) {
    unset($mimes['pem']);
    return $mimes;
}

function ZakiPushNotification_upload_dir( $path ) {
           
    // Check if uploading from outside the plugin setting page
    $use_default_dir = (strpos($_SERVER['HTTP_REFERER'],'zaki-push-notification')) ? false : true;
    if($use_default_dir ) return $path;

    $path['path']    = str_replace( $path['subdir'], '/zaki-pem-folder', $path['path']); 
    $path['url']     = str_replace( $path['subdir'], '/zaki-pem-folder', $path['url']); 
    $path['subdir']  = '';
        
    return $path;
}

/* Add meta box in posts */
add_action( 'post_submitbox_misc_actions', 'ZakiPushNotification_metabox' );
function ZakiPushNotification_metabox() {
    global $post;
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('#zaki_push_notification_send').live('click', function( event ){
                event.preventDefault();
                if(confirm('<?=__("Send push notification of this article?","zaki")?>')) {
                    jQuery("#zaki_push_notification_loader").fadeIn("fast");
                    jQuery.post( ajaxurl, { action: 'zaki-push-notification-ajax', zaki_pn_id: <?=$post->ID?> }, function(results) {
                        console.log(results);
                        if(results=='OK') {
                            jQuery("#zaki_push_notification_ajax_response").html('<div class="message updated fade"><p><?=__('Notification sent','zaki')?></p></div>');
                        } else {
                            jQuery("#zaki_push_notification_ajax_response").html('<div class="message error fade"><p><?=__('Error sending notification','zaki')?></p></div>');
                        }
                        jQuery("#zaki_push_notification_loader").fadeOut("fast");
                        jQuery("#zaki_push_notification_ajax_response div").delay(3000).hide("slow");
                    });
                }
            });
        });
    </script>
    <div class="misc-pub-section zaki_push_notification_box">
        <div id="zaki_push_notification_ajax_response"></div>
        <?=__('Zaki Push Notification','zaki')?>: 
        <button class="button" id="zaki_push_notification_send" name="zaki_push_notification_send"><?=__('Send','zaki')?></button>
        <div id="zaki_push_notification_loader"></div>
    </div>
    <?php
}
    
function ZakiPushNotification_AjaxSave() {       
    $pn_id = $_POST['zaki_pn_id'];
    $postInfo = get_post($pn_id);
    echo ZakiPushNotification::sendPN($postInfo);
    exit();
}

/* Add REST API */
add_filter( 'query_vars', 'ZakiPushNotification_QueryVars' );
function ZakiPushNotification_QueryVars( $vars ){
    $vars[] = "zpn_action";
    $vars[] = "zpn_field";
    $vars[] = "zpn_value";
    return $vars;
}

add_action('admin_init','ZakiPushNotification_RestApiRewrite');
function ZakiPushNotification_RestApiRewrite() {
    $fistActCheck = get_option('zaki_push_notification_fistactivationcheck');
    add_rewrite_rule('^zaki-push-notification/([^/]*)/([^/]*)/([^/]*)/?','index.php?zpn_action=$matches[1]&zpn_field=$matches[2]&zpn_value=$matches[3]','top');
    if($fistActCheck) :
        flush_rewrite_rules();
        update_option('zaki_push_notification_fistactivationcheck',false);
    endif;
}

add_action('wp','ZakiPushNotification_RestApiActions');
function ZakiPushNotification_RestApiActions() {
    $action = strtoupper(get_query_var('zpn_action'));
    $jsonResp = array();
    if($action) :
        switch($action) : 
            case 'PUT':
                $jsonResp = ZakiPushNotification::RestPut();
                break;
            case 'DELETE':
                $jsonResp = ZakiPushNotification::RestDelete();
                break;
            default:
        endswitch;
        echo json_encode($jsonResp); 
        exit();
    endif;
}


