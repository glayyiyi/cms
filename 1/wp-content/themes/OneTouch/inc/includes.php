<?php

require_once locate_template('/inc/utils.php');                  // Utility functions
require_once locate_template('/inc/activation.php');             // Theme activation
require_once locate_template('/inc/cleanup.php');                // Cleanup
require_once locate_template('/inc/actions.php');                // Actions
require_once locate_template('/inc/scripts.php');                // Scripts and stylesheets
require_once locate_template('/inc/post-types.php');             // Custom post types
require_once locate_template('/inc/custom.php');                 // Custom functions
require_once locate_template('/admin/nhp-options.php');      // Theme options
require_once locate_template('/inc/widgets.php');                // Widgets
require_once locate_template('/inc/aq_resizer.php');  // Image resizer

require_once locate_template('/inc/custom/custom_fields/custom_fields.php');  // Custom fields

require_once locate_template('/inc/homepage_builder/aq-page-builder.php');  // Custom fields

require_once locate_template('/inc/shortcodes/shortcodes.php');  // Shortcodes


require_once locate_template('/lib/plugins.php');
require_once locate_template('/inc/vc-tile/vc-tile.php');  // VC tiles including



if(is_admin())
    require_once locate_template('/inc/menu_customizer/menu_customizer.php'); //Customizing of menu


//Initialize the update checker.
require locate_template('/inc/theme-update-checker.php');
$example_update_checker = new ThemeUpdateChecker(
    'OneTouch',
    'http://up.crumina.net/onetouch/info.json'
);


if (0) comment_form();
if (0) add_theme_support( 'automatic-feed-links'  );

