
<html lang="zh-CN" class="boxed ">
<head>

<meta charset="UTF-8" />

<title> 果子助手</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="http://www.appcn100.com/cms/iagent/wp-content/themes/mystile/style.css" media="screen" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<script type='text/javascript' src='http://www.appcn100.com/cms/iagent/wp-admin/load-scripts.php?c=1&amp;load%5B%5D=jquery-core,jquery-migrate,utils,jquery-ui-core,jquery-ui-widget,jquery-ui-accordion&amp;ver=3.8.1'></script>
<?php
require( dirname(__FILE__) . '/../wp-load.php' );

$user_login = $_GET['username'];
$password = $_GET['password'];
$user = wp_authenticate($user_login, $password);

$user_id = $user->ID;
wp_set_current_user($user_id, $user_login);
do_action('wp_login', $user_login);
?>
</html>
