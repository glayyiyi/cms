
<html lang="zh-CN" class="boxed ">
<head>

    <meta charset="UTF-8" />

    <title> 果子助手</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php
    require( dirname(__FILE__) . '/../wp-load.php' );

    $user_login = $_GET['username'];
    $password = $_GET['password'];

    $user = wp_signon(array('user_login'=>$user_login, 'user_password'=>$password), false);

    if ( !is_wp_error($user)) {
        $redirect_to = admin_url();
        if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
            $redirect_to = user_admin_url();
        elseif ( is_multisite() && !$user->has_cap('read') )
            $redirect_to = get_dashboard_url( $user->ID );
        elseif ( !$user->has_cap('edit_posts') )
            $redirect_to = admin_url('profile.php');

        wp_safe_redirect($redirect_to);
        exit();
    }

    ?>
    <html>
    </html>

