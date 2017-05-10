<?php
namespace SCRMHub\WordpressPlugin\Service;

use SCRMHub\WordpressPlugin\NetworkCore\BaseNetworks;

use SCRMHub\Framework\Utility\App;

class SCRMHubNetworks extends BaseNetworks {
	private $sharingDefaults = array(
			'position'  	=> 'manual',
			'login'			=> false,
			'post_types'	=> 'all',
			'icononly'		=> false
		);

	private $connectDefaults = array(
			'icononly'			=> false,
			'commentconnect'	=> false,
			'loginform'			=> false

		);


	function __construct(App $app) {
		parent::__construct($app);
	}

	/**
	 * Initialise the network functions
	 */
	public function init() {
		//Get the networks
		$this->loadAllNetworks();

		//Only do this for the website
		if (!is_admin() ) {
			//Configure the sharing
			$this->setupSharing();

			//Configure the connect
			$this->setupConnect();
		}
	}

	/** 
     * Creates an instance of a network
     * @param string $network The Social Network to load
     * @return mixed Instance of a network
     */
    protected function load($network) {
        if(!in_array($network, $this->networkList))
            die('Invalid network');

        //build the path
        $libraryClass = 'SCRMHub\\WordpressPlugin\\Networks\\'.ucfirst(strtolower($network));

        //Create the class and return it
        return new $libraryClass($this->app);
    }

    /**
     * Setup the sharing settings
     */
	private function setupSharing() {
		$sharing_options = @unserialize(get_option('scrmhub_site_sharing_options'));

		//Get the option
		$this->app->sharing_enabled = $sharing_enabled = isset($sharing_options['enabled']) ? (bool)$sharing_options['enabled'] : false;

		//Sharing enabled?
		if($sharing_enabled) {
			//Store the options
	    	$this->app->sharing_options		= isset($sharing_options['options']) ? (array)$sharing_options['options'] : $this->sharingDefaults;

	    	//Get the networks
	    	$this->app->sharing_networks_settings = isset($sharing_options['networks']) ? (array)$sharing_options['networks'] : array();

	    	//List of enabled things
	    	$share_enabled = [];

	    	//Loop through the networks
    		foreach($this->app->sharing_networks_settings as $network => $config) {
	    		if($config['enabled'] == 1) {
    				$this->get($network)->setShareConfig($config);

    				$share_enabled[$network] = $this->get($network);
    			}
	    	}

	    	$this->app->sharing_networks = $share_enabled;
		}
	}

	/**
     * Setup the connect settings
     */
	private function setupConnect() {
		$connect_options  = @unserialize(get_option('scrmhub_site_connect_options'));

		//Get the option
		$this->app->connect_enabled = $connect_enabled = isset($connect_options['enabled']) ? (bool)$connect_options['enabled'] : false;

		//Sharing enabled?
		if($connect_enabled) {
	    	//Get the options
	    	$this->app->connect_options = isset($connect_options['options']) ? (array)$connect_options['options'] : $this->connectDefaults;

	    	//Network settings
	    	$this->app->connect_networks_settings = isset($connect_options['networks']) ? (array)$connect_options['networks'] : array();

	    	$connect_enabled = [];

	    	//Loop through the networks
    		foreach($this->app->connect_networks_settings as $network => $config) {
    			if($config['enabled'] == 1) {
    				$this->get($network)->setConnectConfig($config);

    				$connect_enabled[$network] = $this->get($network);
    			}
	    	}

	    	$this->app->connect_networks = $connect_enabled;
		}
	}
}