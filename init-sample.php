<?php

define( 'WPFP_VERSION', '0.0.0' );
define( 'PARENT_THEME_DIR', TEMPLATEPATH );
define( 'PARENT_THEME_URI', get_template_directory_uri() );
define( 'WPFP_DIR', PARENT_THEME_DIR . '/WPFPX' );
define( 'WPFP_URI', PARENT_THEME_URI . '/WPFPX' );

define( 'WPFP_COMPOSER_DIR', ABSPATH.'../composer' );
define( 'WPFP_FIREBASE_ADMIN_JSON', WPFP_COMPOSER_DIR . '/adminsdk.json' );
define( 'WPFP_STRIPE_SECRET', 'sk_test_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX' );
define( 'WPFP_SESSION_LIFETIME', 2592000*3 ); // SECOND / 2592000 = a month
define( 'WPFP_LOGIN_SUCCESS_REDIRECT_URI', '/');
// set Random Session Key 
// 1. It should contain only alphanumeric characters 
// 2. Long key won't valid
define( 'WPFP_SESSION_SECRET_KEY', 'xxxxxxxxxxxxxxxxxxxxx');

require_once( WPFP_DIR . '/classes/WPFP.php' );
require_once( WPFP_DIR . '/classes/WPFPToken.php' );
require_once( WPFP_DIR . '/classes/WPFPServices.php' );
require_once( WPFP_DIR . '/classes/WPFPRouter.php' );

require_once( WPFP_DIR . '/classes/WPFPLogin.php' );
require_once( WPFP_DIR . '/classes/WPFPLogout.php' );
require_once( WPFP_DIR . '/classes/WPFPPayment.php' );

$wpfp = new WPFPRouter();
$wpfp->init();
$wpfp->execute();
