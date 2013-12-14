<?php

/** 
 * WordPress 基础配置文件。
 *
 * 本文件包含以下配置选项：MySQL 设置、数据库表名前缀、密钥、
 * WordPress 语言设定以及 ABSPATH。如需更多信息，请访问
 * {@link http://codex.wordpress.org/zh-cn:%E7%BC%96%E8%BE%91_wp-config.php
 * 编辑 wp-config.php} Codex 页面。MySQL 设置具体信息请咨询您的空间提供商。
 *
 * 这个文件用在于安装程序自动生成 wp-config.php 配置文件，
 * 您可以手动复制这个文件，并重命名为“wp-config.php”，然后输入相关信息。
 *
 * @package WordPress
 */

// ** MySQL 设置 - 具体信息来自您正在使用的主机 ** //
/** WordPress 数据库的名称 */
//define('WP_CACHE', true); //Added by WP-Cache Manager
//define( 'WPCACHEHOME', '/opt/appstack/apps/cms/1/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'cms');

/** MySQL 数据库用户名 */
define('DB_USER', 'cms');

/** MySQL 数据库密码 */
define('DB_PASSWORD', 'cmscms');

/** MySQL 主机 */
define('DB_HOST', 'rdsqa6zqmbjvzze.mysql.rds.aliyuncs.com:3306');

/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8');

/** 数据库整理类型。如不确定请勿更改 */
define('DB_COLLATE', '');
/** BCMS queue Name */
define('BCMS_QUEUE', '86c3084fd35a9d00e327bf8a0fa773cf');

/**#@+
 * 身份认证密匙设定。
 *
 * 您可以随意写一些字符
 * 或者直接访问 {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org 私钥生成服务}，
 * 任何修改都会导致 cookie 失效，所有用户必须重新登录。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'X0gfj5Hq/)}|vpN!eh+{@-8&!&^(Aw-x#z~)ompl+s*D?xx{-tWmakok&sC.PdC>');
define('SECURE_AUTH_KEY',  'q4h&%o+LTFfiu]?&[s_:mdoxFi@t[gd;*mZpy:Zf+rQZQzm=DE//.r?PvQ>c6Yz ');
define('LOGGED_IN_KEY',    'Z/|F}gZ?ERK)$6CWTczQ{.SHk@k/oZ!B]4].iFh)OiT!DtW$#R)v=?lLsfE>*^4*');
define('NONCE_KEY',        'rI&p|9c_(;1[Hgroe<5e;6uwZ<us_4xibg`ZU*Gp{Qx*nIcEP0cXD-bY%Eodil5j');
define('AUTH_SALT',        'yIgg9OixkvU,ysT.LK>C$P!@|?iftr<#*&|Wn WK>S+D{hNk<[s$m>+xm%km`lo|');
define('SECURE_AUTH_SALT', '-Y#^.OEZBN#q8ykp58ab+h-a/K(b/z-^;+$!).[_.d[4|3ev]kz(#xXa%`$i[M(X');
define('LOGGED_IN_SALT',   'r.i~i|R kj2d0N>&C<yTDRVU;3S93>}/#QS_BuNxk]T9:@cm|F1Dpr3|-19+zPUW');
define('NONCE_SALT',       'C1i@N&KNmwIv/Q}:?mNlCSA5_%R Rcbn0a(9voJd+!_>8)@;e(:^cy/N:(xq.rO/');

/**#@-*/

/**
 * WordPress 数据表前缀。
 *
 * 如果您有在同一数据库内安装多个 WordPress 的需求，请为每个 WordPress 设置不同的数据表前缀。
 * 前缀名只能为数字、字母加下划线。
 */
$table_prefix  = 'wp_';

/**
 * WordPress 语言设置，中文版本默认为中文。
 *
 * 本项设定能够让 WordPress 显示您需要的语言。
 * wp-content/languages 内应放置同名的 .mo 语言文件。
 * 要使用 WordPress 简体中文界面，只需填入 zh_CN。
 */
define('WPLANG', 'zh_CN');

/**
 * 开发者专用：WordPress 调试模式。
 *
 * 将这个值改为“true”，WordPress 将显示所有用于开发的提示。
 * 强烈建议插件开发者在开发环境中启用本功能。
 */
define('WP_DEBUG', false);
//define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'www.appcn100.com');
define('PATH_CURRENT_SITE', '/cms/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
/* 好了！请不要再继续编辑。请保存本文件。使用愉快！ */

/** WordPress 目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** 设置 WordPress 变量和包含文件。 */
require_once(ABSPATH . 'wp-settings.php');
