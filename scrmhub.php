<?php
/**
 * @package SCRM Hub Wordpress Plugin
 * @version 2.4.0
 */

/*
Plugin Name: SCRM Hub
Plugin URI: https://scrmhub.com
Description: SCRM Hub is a marketing platform that uses machine learning to help you market your business better through recommendations.
Version: 2.4.0
Author: SCRM Hub
Author URI: https://scrmhub.com/
License: tbc
Text Domain: scrmhub
*/

//Basic Wordpress Protection
if ( !function_exists( 'add_action' ) OR !defined( 'ABSPATH' ) OR !defined( 'WPINC' ) ) {
	echo 'Danger Will Robinson';
	exit;
}

global $wp_version;
if ( $wp_version < 4.3 ) {
	wp_die( __( 'The SCRM Hub Plugin Requires WordPress 4.3+ or higher', 'scrmhub'  ) );
}

//Define variables
define( 'SCRMHUB__VERSION', "2.4.0" );
define( 'SCRMHUB__VERSION_KEY', 'SCRMHUB__VERSION');
define( 'SCRMHUB__MINIMUM_WP_VERSION', '4.3' );
define( 'SCRMHUB__PLUGIN_URL', WP_PLUGIN_URL.'/scrmhub-wordpress/');
define( 'SCRMHUB__PLUGIN_ASSETS', SCRMHUB__PLUGIN_URL.'assets/');
define( 'SCRMHUB__PLUGIN_BASE_DIR', plugin_dir_path( __FILE__ ).'/');
define( 'SCRMHUB__PLUGIN_DIR', SCRMHUB__PLUGIN_BASE_DIR.'framework/');
define( 'SCRMHUB__PLUGIN_APP', SCRMHUB__PLUGIN_BASE_DIR.'framework/src/');
define( 'SCRMHUB__PERMISSION_LEVEL', 'manage_options');

//Composer autoloader
require_once SCRMHUB__PLUGIN_DIR."vendor/autoload.php";

//Wordpress actions
require_once SCRMHUB__PLUGIN_DIR."wordpress/actions.php";

//Load the core
$scrmhubCore = (new SCRMHub\WordpressPlugin\Bootstrap())->instance();

//Global functions because they're good for this bit
function scrmhub_error($message, $data = []) {
	global $scrmhubCore;
	$scrmhubCore->logger->addError($message, $data);
}
function scrmhub_warning($message, $data = []) {
	global $scrmhubCore;
	$scrmhubCore->logger->addWarning($message);
}
function scrmhub_info($message, $data = []) {
	global $scrmhubCore;
	$scrmhubCore->logger->addInfo($message);
}

/**
 * SCRM Hub Activation Hook
 * This lives here because Wordpress...
 */
function scrmhubplugin_activate() {
  global $scrmhubCore;
  $installer = new \SCRMHub\WordpressPlugin\Versioning\Installer($scrmhubCore);
  return $installer->doInstallUpdate(true);
}

/**
 * SCRM Hub Activation Hook
 * This lives here because Wordpress...
 */
function scrmhubplugin_deactivate() {
  global $scrmhubCore;
  $uninstaller = new \SCRMHub\WordpressPlugin\Versioning\Uninstaller($scrmhubCore);
  return $uninstaller->doUninstall();
}

//Our installation hooks
register_activation_hook( __FILE__, 	'scrmhubplugin_activate' );
register_deactivation_hook( __FILE__, 	'scrmhubplugin_deactivate' );