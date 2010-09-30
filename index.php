<?php
/**
 * Developed by Simon Fletcher
 * http://wakecodesleep.com - http://twitter.com/simonify
 *
 * Some code inspired by phpMoAdmin
 *
 */
date_default_timezone_set('America/New_York');

// Set $path
define( 'APP_PATH', dirname( __FILE__ ) . '/' );

// Set include path
set_include_path( get_include_path() . PATH_SEPARATOR . APP_PATH );

// Require bootstrap
require '_includes/GAS/Bootstrap.php';

GAS_Bootstrap::boot( APP_PATH . '_application/configs/site.xml', APP_PATH, 'production' );