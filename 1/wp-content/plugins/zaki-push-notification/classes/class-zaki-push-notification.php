<?php

/* Class Zaki Push Notification Plugin */

final class ZakiPushNotification {

    public static function getTableName() {
        global $wpdb;
        return trim($wpdb->prefix . 'zaki_push_notification');
    }

    public static function sendPN($post_pn) {
        if(!$post_pn) return false;
    
        global $wpdb;       
        $settings = get_option('zaki_push_notification_options');
        $uploads = wp_upload_dir();
        $pem_file = $uploads['basedir'].'/zaki-pem-folder/'.$settings['pem_file'];
                
        $arrayDevices = $wpdb->get_results('SELECT * FROM '.self::getTableName().' WHERE 1','OBJECT');
               
        if($arrayDevices) : 
            // Send requests
            $context = stream_context_create();
            stream_context_set_option($context, 'ssl', 'local_cert', $pem_file);
            stream_context_set_option($context, 'ssl', 'passphrase', $settings['pem_pass']);
            $connection = stream_socket_client(
                            'ssl://'.$settings['ssl_server'].':'.$settings['ssl_server_port'],
                            $err,
                            $errstr,
                            60,
                            STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,
                            $context);           
            if(!$connection) wp_die("Impossibile connettersi: $err $errstr" . PHP_EOL); 
        
            foreach($arrayDevices as $device) :
                $pn_mess['aps'] = array(
                    'alert' => array(
                        'body' => stripslashes($post_pn->post_title),
                        'action-loc-key' => __('Leggi la notizia','zaki')
                    ),
                    'sound' => 'default',
                    'badge' => 1
                );	    
                $pn_mess_json = json_encode($pn_mess);
                _log($pn_mess_json);
                $message = chr(0) . pack('n', 32) . pack('H*', $device->token) . pack('n', strlen($pn_mess_json)) . $pn_mess_json;
                $result = fwrite($connection, $message, strlen($message));
                if(!$result) wp_die("Impossibile inviare la richiesta: $err $errstr" . PHP_EOL);
            endforeach;   
        
            fclose($connection);
            return 'OK';
        endif;   
        return false;    
    }
    
    public static function RestPut() {
        global $wpdb;
        $action = get_query_var('zpn_action');
        $field = get_query_var('zpn_field');
        $value = get_query_var('zpn_value');
        $udid_token = explode('_',$value);
        
        // Check if udid is already registered
        $checkudid = $wpdb->get_row("SELECT * FROM ".self::getTableName()." WHERE udid = '".$udid_token[0]."'");
        
        if(!$checkudid) :
            $queryInsert = $wpdb->query( $wpdb->prepare( 
                "
                    INSERT INTO ".self::getTableName()."
                    ( udid, token, registration_date, last_update )
                    VALUES ( %s, %s, %s, %s )
                ",  
                $udid_token[0],
                $udid_token[1], 
                date('Y-m-d'),
                date('Y-m-d') 
            ));
            $result = (!$queryInsert) ? __("ERROR! Token can't be registered","zaki") : __("OK","zaki");
        else :
            // Check if token must be update
            if($checkudid->token == $udid_token[1]) :
                $result = __("ERROR! UDID already registered","zaki");
            else : 
                // Update token
                $queryUpdate = $wpdb->query( $wpdb->prepare( 
                    "
                        UPDATE ".self::getTableName()." 
                        SET token = %s, last_update = %s )
                        WHERE udid = '".$checkudid->token."'
                    ",  
                    $udid_token[1],
                    date('Y-m-d') 
                ));
                $result = (!$queryUpdate) ? __("ERROR! Token can't be updated","zaki") : __("OK! Token updated","zaki");
            endif;
        endif;
        
        $arrayRes = array(
            'zpn_action' => $action,
            'zpn_field' => $field,
            'zpn_value' => $value,
            'zpn_result' => $result
        );
        return $arrayRes;
    }
    
    public static function RestDelete() {
        global $wpdb;
        $action = get_query_var('zpn_action');
        $field = get_query_var('zpn_field');
        $value = get_query_var('zpn_value');
        $udid_token = explode('_',$value);
        
        $checkudid = $wpdb->get_row("SELECT * FROM ".self::getTableName()." WHERE udid = '".$udid_token[0]."'");
        
        if($checkudid) :
            $queryDelete = $wpdb->query($wpdb->prepare("DELETE FROM ".self::getTableName()." WHERE id=%d",  $checkudid->id));
            $result = (!$queryDelete) ? __("ERROR! Token can't be deleted","zaki") : __("Token deleted","zaki");
        else :
            $result = __("ERROR! Token unknown","zaki");
        endif;
        
        $arrayRes = array(
            'zpn_action' => $action,
            'zpn_field' => $field,
            'zpn_value' => $value,
            'zpn_result' => $result
        );
        return $arrayRes;
    }
    
}