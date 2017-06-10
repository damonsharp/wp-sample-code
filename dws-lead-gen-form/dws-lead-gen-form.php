<?php

/**
 * Plugin Name: DWS Lead Generation Form
 * Description: Plugin Shortcode POC
 * Version: 1.0.0
 * Author: Damon Sharp
 * Author URI: http://damonsharp.me
 *
 * @package DWSLGF
 */

/**
 * Setup constants
 */
if ( ! defined( 'DWSLGF_PLUGIN_DIR' ) ) {
	define( 'DWSLGF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'DWSLGF_PLUGIN_DIR_URL' ) ) {
	define( 'DWSLGF_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'DWSLGF_PLUGIN_INC_DIR' ) ) {
	define( 'DWSLGF_PLUGIN_INC_DIR', DWSLGF_PLUGIN_DIR . 'inc/' );
}

if ( ! defined( 'DWSLGF_PREFIX' ) ) {
	define( 'DWSLGF_PREFIX', 'dwslgf' );
}

add_action( 'after_setup_theme', function () {
	require_once( DWSLGF_PLUGIN_INC_DIR . 'libraries/Carbon/src/Carbon/Carbon.php' );
	require_once( DWSLGF_PLUGIN_INC_DIR . 'libraries/wordpress-fieldmanager/fieldmanager.php' );
	require_once( DWSLGF_PLUGIN_INC_DIR . 'class-dwslgf-data-structures.php' );
	require_once( DWSLGF_PLUGIN_INC_DIR . 'class-dwslgf-customer.php' );
	require_once( DWSLGF_PLUGIN_INC_DIR . 'class-dwslgf.php' );
} );
