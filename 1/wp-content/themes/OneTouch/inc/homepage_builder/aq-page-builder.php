<?php
/** بسم الله الرحمن الرحيم **
 *
 * Plugin Name: Aqua Page Builder
 * Plugin URI: http://aquagraphite.com/page-builder
 * Description: Easily create custom page templates with intuitive drag-and-drop interface. Requires PHP5 and WP3.5
 * Version: 1.0.6
 * Author: Syamil MJ
 * Author URI: http://aquagraphite.com
 * License: GPLV3
 *
 * @package   Aqua Page Builder
 * @author    Syamil MJ <http://aquagraphite.com>
 * @copyright Copyright (c) 2012, Syamil MJ
 * @license   http://www.gnu.org/copyleft/gpl.html
 *
 * @todo      - Preview template
 			  - Inactive blocks (for staging)
 			  - Template tabs sorting
 			  - Duplicate block
 */

//definitions
if(!defined('AQPB_VERSION')) define( 'AQPB_VERSION', '1.0.6' );
if(!defined('AQPB_PATH')) define( 'AQPB_PATH', get_template_directory().'/inc/homepage_builder/' );
if(!defined('AQPB_DIR')) define( 'AQPB_DIR', get_template_directory_uri().'/inc/homepage_builder/' );

//required functions & classes
require_once(AQPB_PATH . 'functions/aqpb_config.php');
require_once(AQPB_PATH . 'functions/aqpb_blocks.php');
require_once(AQPB_PATH . 'classes/class-aq-page-builder.php');
require_once(AQPB_PATH . 'classes/class-aq-block.php');
require_once(AQPB_PATH . 'classes/class-aq-plugin-updater.php');
require_once(AQPB_PATH . 'functions/aqpb_functions.php');

//some default blocks

require_once(AQPB_PATH . 'blocks/aq-column-block.php');
require_once(AQPB_PATH . 'blocks/aq-clear-block.php');
require_once(AQPB_PATH . 'blocks/aq-widgets-block.php');
//require_once(AQPB_PATH . 'blocks/aq-alert-block.php');
//require_once(AQPB_PATH . 'blocks/aq-tabs-block.php');
require_once(AQPB_PATH . 'blocks/aq-pages-block.php');
require_once(AQPB_PATH . 'blocks/aq-pages-15-block.php');
require_once(AQPB_PATH . 'blocks/aq-pages-15-block.php');
require_once(AQPB_PATH . 'blocks/aq-pages-11-block.php');
require_once(AQPB_PATH . 'blocks/aq-pages-7-block.php');
require_once(AQPB_PATH . 'blocks/aq-pages-4-block.php');
require_once(AQPB_PATH . 'blocks/aq-recent-block.php');
require_once(AQPB_PATH . 'blocks/aq-info-row-block.php');
require_once(AQPB_PATH . 'blocks/aq-info-block.php');
require_once(AQPB_PATH . 'blocks/aq-info-button-block.php');
require_once(AQPB_PATH . 'blocks/aq-call2act-block.php');
require_once(AQPB_PATH . 'blocks/aq-single-post-block.php');
require_once(AQPB_PATH . 'blocks/aq-mini-gallery-block.php');

require_once(AQPB_PATH . 'blocks/aq-hslider-block.php');// Parent class for horizontal sliders
require_once(AQPB_PATH . 'blocks/aq-hslider-posts-block.php');
require_once(AQPB_PATH . 'blocks/aq-hslider-portfolio-block.php');
require_once(AQPB_PATH . 'blocks/aq-hslider-product-block.php');
require_once(AQPB_PATH . 'blocks/aq-quote-block.php');


//register default blocks
aq_register_block('AQ_Column_Block');
aq_register_block('AQ_Clear_Block');
aq_register_block('AQ_Widgets_Block');
//aq_register_block('AQ_Alert_Block');
//aq_register_block('AQ_Tabs_Block');
aq_register_block('AQ_Page_4_Block');
aq_register_block('AQ_Page_7_Block');
aq_register_block('AQ_Page_11_Block');
aq_register_block('AQ_Page_15_Block');
aq_register_block('AQ_Recent_Block');

aq_register_block('AQ_Info_Row_Block');
aq_register_block('AQ_Info_Button_Block');
aq_register_block('AQ_Info_Block');
aq_register_block('AQ_Quote_Block');
aq_register_block('AQ_Call2act_Block');
aq_register_block('AQ_Single_Post_Block');
aq_register_block('AQ_Mini_Gallery_Block');

aq_register_block('AQ_HSlider_Portfolio_Block');
aq_register_block('AQ_HSlider_Posts_Block');
aq_register_block('AQ_HSlider_Product_Block');

//fire up page builder
$aqpb_config = aq_page_builder_config();
$aq_page_builder =& new AQ_Page_Builder($aqpb_config);
if(!is_network_admin()) $aq_page_builder->init();

//set up & fire up plugin updater
$aqpb_updater_config = array(
	'api_url'	=> 'http://aquagraphite.com/api/',
	'slug'		=> 'aqua-page-builder',
	'filename'	=> 'aq-page-builder.php'
);
$aqpb_updater = new AQ_Plugin_Updater($aqpb_updater_config);
