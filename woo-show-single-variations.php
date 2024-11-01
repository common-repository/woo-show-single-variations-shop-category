<?php
/**
 * Plugin Name: Show Single Variations Shop & Category for WooCommerce
 * Description: Show all different variation as single product in shop page and category page
 * Version:     2.0
 * Author:      Gravity Master
 * License:     GPLv2 or later
 * Text Domain: gmwsvs
 */

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/* All constants should be defined in this file. */
if ( ! defined( 'WSSVSC_PREFIX' ) ) {
	define( 'WSSVSC_PREFIX', 'wssvsc' );
}
if ( ! defined( 'WSSVSC_PLUGINDIR' ) ) {
	define( 'WSSVSC_PLUGINDIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WSSVSC_PLUGINBASENAME' ) ) {
	define( 'WSSVSC_PLUGINBASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'WSSVSC_PLUGINURL' ) ) {
	define( 'WSSVSC_PLUGINURL', plugin_dir_url( __FILE__ ) );
}

/* Auto-load all the necessary classes. */
if( ! function_exists( 'wssvsc_class_auto_loader' ) ) {
	
	function wssvsc_class_auto_loader( $class ) {
		
	 	$includes = WSSVSC_PLUGINDIR . 'includes/' . $class . '.php';
		
		if( is_file( $includes ) && ! class_exists( $class ) ) {
			include_once( $includes );
			return;
		}
		
	}
}
spl_autoload_register('wssvsc_class_auto_loader');
new WSSVSC_Cron();
new WSSVSC_Admin();
new WSSVSC_Frontend();
?>