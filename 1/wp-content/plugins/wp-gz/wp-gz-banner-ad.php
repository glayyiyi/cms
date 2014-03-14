<?php
add_action( 'request_adfeed', 'domob_request_adfeed' );

function domob_request_adfeed( ){
$ipb = $_REQUEST['ipb'];
if( $ipb !=  "96ZJ1pPwzeOxHwTAII" )//get_option( 'referral_domob_public_id' ) )
die( json_encode( array( 'status' => 'ERROR', 'msg' => 'no such publicId' ) ) );
//$url = "http://".get_option( 'domob_adfeed_url' )."?ipb=".$ipb;
$url = "http://r.ow.domob.cn/ow/interface/common/adfeed".get_option( 'domob_adfeed_url' )."?ipb=".$ipb;

$json_ret = curl_file_get_contents($url);
$result = json_decode(trim($json_ret));
$num = count($result);
    $resultArray = array();
for($i=0;$i<$num;++$i){
    $key = $result[$i]->app_store_url;
    $existResult = $resultArray[$key];
    $isEmpty = empty($existResult);

    if ( $isEmpty || $existResult->point < $result[$i]->point){
        if (!$isEmpty){
            $result[$i]->banner_image_url = $existResult->banner_image_url;
        }
        $existResult = $result[$i];
    }

    if (empty($existResult->banner_image_url)){
        $existResult->banner_image_url = "";
    }
    $resultArray[$key] = $existResult;
}
die( json_encode($resultArray) );
}
?>