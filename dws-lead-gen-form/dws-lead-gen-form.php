<?php

/**
 * Plugin Name: DWS Lead Generation Form
 * Description: Plugin POC for Review
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

/**
 * Let's get this shortcode party started!
 */
function dwslgf_plugin_setup() {
	require_once( 'vendor/autoload.php' );

	( new DWSLGF\DWSLGF() )->init();
	( new DWSLGF\Customer() )->setup();
}

add_action( 'after_setup_theme', 'dwslgf_plugin_setup' );

