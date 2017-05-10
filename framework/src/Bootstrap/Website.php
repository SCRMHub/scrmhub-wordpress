<?php
namespace SCRMHub\WordpressPlugin\Bootstrap;

use SCRMHub\WordpressPlugin\Identity\Cookie;
use SCRMHub\WordpressPlugin\Identity\Person;

use SCRMHub\Framework\Utility\App;

class Website {
	private $app;

	function __construct(App $app) {
		$this->app = $app;

		$this->app->interfaces = function() use($app) {
			return new \SCRMHub\WordpressPlugin\Interfaces\_Setup($app);
		};

		//Add the init and loaded
		add_action('init', array(&$this, 'init'));
		add_action('wp_loaded', array(&$this, 'wp_loaded'));

		//Set user profile image
		if($this->app->values->getSettingsConnect()->user_photo == 1) {
			$this->app->user_photo->setup();
		}
	}

	/**
	 * Initialise the Website
	 */
	public function init() {
		add_rewrite_endpoint('scrmhub_action', 	EP_ROOT);
		add_rewrite_endpoint('scrmhub_hash', 	EP_ROOT);
		add_rewrite_endpoint('scrmhub_refuuid', EP_ROOT);
		add_rewrite_endpoint('scrmhub_shareid',	EP_ROOT);

		//Running our template hooks
		add_action('template_redirect', array('\SCRMHub\WordpressPlugin\Actions\Actions', 'run'));

		//Some nice house cleaning actions
		add_action('wp_logout',array(&$this, 'logout'));

		//Initialise the networks
		$this->app->networks->init();
	}

	/**
	 * Add in all the hooks
	 */
	public function wp_loaded() {
		//Load any interface elements
		$this->app->interfaces->init();

		//Get the activity settings
		$this->activityOptions();

		//Add header JS
		add_action('wp_head', 	array('\SCRMHub\WordpressPlugin\Interfaces\Header', 'render'));
		add_action('login_head',array('\SCRMHub\WordpressPlugin\Interfaces\Header', 'render'));

		//Add content filters
		add_filter('the_content', array('\SCRMHub\WordpressPlugin\Interfaces\FilterContent', 'render'), 1000, 1);
	}

	//Logout properly
	public function logout() {
		$this->app->person->logout();
	}

	/**
	 * Load activity settings
	 */
	private function activityOptions() {
		$activity_options  = $this->app->values->getSettingsActivity();

		//On or off?
		$this->app->activity_options = array('enabled' => $activity_options->enabled);
	}	
}

/**
 * Global functions because Wordpress...
 */
//get the url of a specific network
global $scrmhub_connect_url;
$scrmhub_connect_url = function($network) {
	return call_user_func(array('SCRMHub\WordpressPlugin\Interfaces\Connect', 'loginUrl'), $network);
};

global $scrmhub_share_url;
$scrmhub_share_url = function($network) {
	return call_user_func(array('SCRMHub\WordpressPlugin\Interfaces\Share', 'getShareUrl'), $network);
};

global $scrmhub_short_link;
$scrmhub_short_link = function($link) {
	return call_user_func(array('SCRMHub\WordpressPlugin\Interfaces\Shortener', 'getShortUrlFromLink'), $link);
};