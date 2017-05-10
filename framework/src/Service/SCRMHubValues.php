<?php
namespace SCRMHub\WordpressPlugin\Service;

use SCRMHub\Framework\Utility\App;
use SCRMHub\Framework\Utility\Bucket;

class SCRMHubValues {
	private $siteid = false;

	private $app;

	private $networkList;

	private $table_meta;

	private $commondomain = false;

	private $useUrls;

	private
		$settingsActivity,
		$settingsConnect,
		$settingsSharing;

	private
		$settingsConnectRedirect;

	private
		$appKey,
		$appSecret;

	function __construct(App $app) {
		$this->app = $app;
	}
	//global or local values?
	public function getGlobalOption($key) {
		if($this->app->multisite) {
			return get_site_option($key);
		} else {
			return get_option($key);
		}
	}
	function useUrls() {
		if(!$this->useUrls) {
			$this->useUrls = $this->getGlobalOption('scrmhub_urls');
		}
		return $this->useUrls;
	}
	function metaTableName() {
		if(!$this->table_meta) {
			global $wpdb;
	    	$this->table_meta = $wpdb->base_prefix . 'scrmhub_usermeta';
	    }
	    return $this->table_meta;
	}
	function commondomain() {
		return null;
		
		if($this->commondomain === false) {
			if(defined('DOMAIN_CURRENT_SITE')) {
				$values['commondomain'] = DOMAIN_CURRENT_SITE;
			} else {
				$this->commondomain = null;
			}
		}

		return $this->commondomain;
	}
	function getAppKey() {
		if(empty($this->appKey)) {
			$this->appKey = get_option('scrmhub_site_appkey');
		}
		return $this->appKey;
	}
	function getAppSecret() {
		if(empty($this->appSecret)) {
			$this->appSecret = $this->app->encrypto->decrypt(utf8_decode($this->getGlobalOption('scrmhub_secret')));
		}
		return $this->appSecret;
	}	
	function getNetworkList() {
		if(!$this->networkList) {
			$this->networkList = require(SCRMHUB__PLUGIN_APP.'NetworkCore/_list.php');
		}
		return $this->networkList;
	}

	function getSiteId() {
		if($this->siteid === false) {
			global $wpdb;
			$this->siteid 	= $wpdb->siteid;
		}
		return $this->siteid;
	}


	/**
	 * Get Activity Settings
	 * @return \SCRMHub\Framework\Utility\Bucket Nice Bucket object
	 */
	function getSettingsActivity() {
		if(!$this->settingsActivity) {
			$settings = @unserialize(get_option('scrmhub_site_activity_options'));

			$options = [
				'enabled' => isset($settings['enabled']) ? (bool)$settings['enabled'] : false
			];

			$this->settingsActivity  = new Bucket($options);
		}
		return $this->settingsActivity;		
	}

	/**
	 * Get Connect Settings
	 * @return \SCRMHub\Framework\Utility\Bucket Nice Bucket object
	 */
	function getSettingsConnect() {
		if(!$this->settingsConnect) {
			$settings = @unserialize(get_option('scrmhub_site_connect_options'));

			if($settings['enabled']) {
				if(isset($settings['options'])) {
					$options = $settings['options'];
				} else{
					$options = [];
				}				
				$options['enabled'] 	= (bool)$settings['enabled'];
				$options['networks'] 	= (array)$settings['networks'];
			} else {
				$options = ['enabled' => false];
			}

			$this->settingsConnect  = new Bucket($options);
		}
		return $this->settingsConnect;		
	}

	function getLoginRedirect() {
		if(!$this->settingsConnectRedirect) {
			$redirect = $this->getSettingsConnect()->redirect;

			//Decide what we're doing
			switch($redirect) {
				case 'custom':
					$redirect = $this->getSettingsConnect()->redirecturl;
					break;
				case 'home':
					$redirect = get_home_url();
					break;
				default:
					$redirect = get_admin_url();

			}

			//save ot
			$this->settingsConnectRedirect = $redirect;

		}

		return $this->settingsConnectRedirect;
	}

	/**
	 * Get Sharing Settings
	 * @return \SCRMHub\Framework\Utility\Bucket Nice Bucket object
	 */
	function getSettingsSharing() {
		if(!$this->settingsSharing) {
			$settings = @unserialize(get_option('scrmhub_site_sharing_options'));

			if($settings['enabled']) {
				if(isset($settings['options'])) {
					$options = $settings['options'];
				} else{
					$options = [];
				}				
				$options['enabled'] 	= (bool)$settings['enabled'];
				$options['networks'] 	= (array)$settings['networks'];
			} else {
				$options = ['enabled' => false];
			}

			$this->settingsSharing  = new Bucket($options);
		}
		return $this->settingsSharing;		
	}
}