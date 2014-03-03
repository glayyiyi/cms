<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//把下面代码放到主题的function.php里


if ( ! function_exists( 'post_message' ) ) :
    function post_message(){
        $time = date('ymdHi');
        $captcha = rand(1000,9999);
        $mobile = $_POST['mobile'];
        $xml = '<?xml version="1.0" encoding="UTF-8"?><MtPacket><cpid>010000001969</cpid><mid>0</mid><cpmid>'.time().'</cpmid><mobile>'.$mobile.'</mobile><port>010121</port><msg>'.$captcha.'（果子助手验证码，十分钟内有效）【爱普科技】</msg><signature>'.md5('d7d32d62942801a3811e297db1a81164'.$time).'</signature><timestamp>'.$time.'</timestamp><validtime>0</validtime></MtPacket>';

        $url = 'http://api.10690909.com/providermt';
//接收XML地址

        $header = 'Content-type: text/xml';
//定义content-type为xml
        $ch = Curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);
//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//设置是否返回信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, 1);
//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
//POST数据
        $response = curl_exec($ch);
//接收返回信息
        if(curl_errno($ch)){
//出错则显示错误信息
            print curl_error($ch);
        } else {
            update_option($mobile, $captcha);
        }
        curl_close($ch);
//关闭curl链接
        //echo $xml;
        echo $response;
//显示返回信息
    }
endif;

add_action('wp_ajax_post_message', 'post_message');
add_action('wp_ajax_nopriv_post_message', 'post_message');

if ( ! function_exists( 'validate_captcha' ) ) :
    function validate_captcha(){
        $captcha = $_POST['captcha'];
        $mobile = $_POST['mobile'];
        $existCaptcha = get_option($mobile);

        $isRight = (!empty($captcha) && !empty($existCaptcha) && ($captcha == $existCaptcha));
        if ($isRight){
            update_option($mobile.'isRight', $isRight);
        }
        echo $captcha; //$isRight?'1':'0';
    }
endif;

add_action('wp_ajax_validate_captcha', 'validate_captcha');
add_action('wp_ajax_nopriv_validate_captcha', 'validate_captcha');
