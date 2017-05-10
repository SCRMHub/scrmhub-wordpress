<?php
namespace SCRMHub\WordpressPlugin\Versioning;

use SCRMHub\Framework\Utility\EncryptDecrypt;

use Crypto;
use Ex\CryptoTestFailedException;
use Ex\CannotPerformOperationException;
use InvalidCiphertextException;
use Exception;

class Installer {
	private $pluginData = array();

	private $app;

	function __construct($app) {
		$this->app = $app;
	}

	public function doInstallUpdate($everything = false) {
		$pluginData = $this->getPluginData();

		if(!get_option(SCRMHUB__VERSION_KEY) || $everything) {
			$message = $this->install();
		} else if(get_option(SCRMHUB__VERSION_KEY) != $this->pluginData['Version']) {
			$message = $this->install(true);
		} else {
			$message = false;
		}

		//Display a message
		if($message) {
			$this->bail_on_activation($message, true);
			exit();
		}

		return true;
	}

	/**
	 * Auto Update function	 */
	public function autoUpdate() {
		$pluginData = $this->getPluginData();

		$message = $this->install(true);
		
		//Display a message
		if($message) {
			$this->bail_on_activation($message, true);
			exit();
		}

		return true;
	}

	private function getPluginData() {
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		$this->pluginData   = get_plugin_data(SCRMHUB__PLUGIN_BASE_DIR.'scrmhub.php');
		return $this->pluginData;
	}

	/**
	 * Install the plugin 
	 */
	private function install($update = false) {
		if($message = $this->checkClasses()) {
			$this->bail_on_activation($message, true);
		}

		//Install or update the DB		
		$this->InstallDB();

		//Make sure the DB plays nice first time
		$this->DefaultOptions();

		//Install the cron jobs
		$this->InstallCron();

		//Update it
		if(is_multisite()) {
			update_site_option(SCRMHUB__VERSION_KEY, $this->pluginData['Version']);
		} else {
			update_option(SCRMHUB__VERSION_KEY, $this->pluginData['Version'], true);
		}
		

		return false;
	}

	/**
	 * Check classes
	 */
	private function checkClasses() {
		if ( version_compare( $GLOBALS['wp_version'], SCRMHUB__MINIMUM_WP_VERSION, '<' ) ) {
			$this->bail_on_activation('This plugin requires a wordpress version of '.SCRMHUB__MINIMUM_WP_VERSION.' or above');
		}

		//Check for security keys
		if(!defined('SECURE_AUTH_SALT') || !defined('SECURE_AUTH_KEY') || empty(SECURE_AUTH_SALT) || empty(SECURE_AUTH_KEY)) {
			$this->bail_on_activation('Please make sure that the SECURE_AUTH_SALT and SECURE_AUTH_KEY are defined in your configuration and not empty. You can find more information here: https://codex.wordpress.org/Editing_wp-config.php#Security_Keys');
		}

		//Then check crypo can run
		try {
		    Crypto::RuntimeTest();
		    return false;
		} catch (CryptoTestFailedException $ex) {
			$this->bail_on_activation('There was an issue with the Crypto Library.<br>'.print_r($ex, true));
		} catch (CannotPerformOperationException $ex) {
			$this->bail_on_activation('There was an issue with the Crypto Library.<br>'.print_r($ex, true));
		}


	}

	/**
	 * Install the database
	 * Work out if we're creating or updating the DB
	 */
	private function InstallDB() {
	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		global $wpdb;
	    $table = $wpdb->base_prefix . 'scrmhub_usermeta';

	    $sql = "CREATE TABLE `".$table."` (
			`user_id` bigint(20) unsigned NOT NULL,
			`meta_key` varchar(100) NOT NULL DEFAULT '',
			`meta_value` longtext,
			`encrypted` binary(1) NOT NULL DEFAULT '0',
			`created_at` datetime DEFAULT NULL,
			`updated_at` datetime DEFAULT NULL,
			`expires_at` datetime DEFAULT NULL,
			PRIMARY KEY (`user_id`,`meta_key`)
		) CHARSET=utf8;";	    
	    @dbDelta($sql);


	    $table = $wpdb->base_prefix . 'scrmhub_links';
	    $sql = "CREATE TABLE `".$table."` (
				  `link_id` bigint(20) unsigned NOT NULL,
				  `blog_id` bigint(20) unsigned NOT NULL,
				  `link_url` varchar(2048) NOT NULL DEFAULT '',
				  `link_short` varchar(50) NOT NULL DEFAULT '',
				  `link_hash` varchar(10) NOT NULL DEFAULT '',
				  PRIMARY KEY (`link_id`),
				  KEY `link_short` (`link_short`),
				  KEY `blog_id` (`blog_id`,`link_url`(255))
				) CHARSET=utf8;";
		
	    @dbDelta($sql);
	}

	/**
	 * 
	 */
	private function DefaultOptions() {
		$baseSettings = require_once(realpath(__DIR__ .'/../').'/Config/BaseConfig.php');

		//Loop and add the option
		foreach($baseSettings as $option_name => $option_value) {
			//Doing this allows us to keep some settings should they deactivate and re-activate
			if(!get_option($option_name)) {
				if(is_array($option_value)) {
					$option_value = serialize($option_value);
				}
				update_option($option_name, $option_value, true);
			}
			
		}

	}

	/**
	 * Register the Cron Job
	 */
	private function InstallCron() {
		$scrmhub = $GLOBALS['scrmhub'];
  		$scrmhub->cron->install();
	}

	/**
	 * Shut it down. It didn't work
	 * @param  string  $message    What to tell the user
	 * @param  boolean $deactivate Turn the plugin off too?
	 */
	private function bail_on_activation( $message, $deactivate = true ) {
		$plugins = get_option( 'active_plugins' );
		$scrmhub = plugin_basename( SCRMHUB__PLUGIN_DIR . 'scrmhub.php' );
		$update  = false;
		foreach ( $plugins as $i => $plugin ) {
			if ( $plugin === $scrmhub ) {
				$plugins[$i] = false;
				$update = true;
			}
		}

		if ( $update ) {
			update_option( 'active_plugins', array_filter( $plugins ) );
		}
?><!doctype html>
	<html>
	<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<style>
	* {
		text-align: center;
		margin: 0;
		padding: 0;
		font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
	}
	p {
		margin-top: 1em;
		font-size: 18px;
	}
	</style>
	<body>
	<p><?php echo esc_html( $message ); ?></p>
	</body>
</html><?php
		exit;
	}
}