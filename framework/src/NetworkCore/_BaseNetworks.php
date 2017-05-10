<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use Exception;

abstract class BaseNetworks {
    protected $networkList;

	protected $networks = [];

	protected $app;

    /**
     * Construct the class
     * @param object $app The base application
     */
	function __construct($app) {
        //Store the app reference
		$this->app = $app;

        //load this list of classes
        $this->networkList = require '_list.php';
	}

    /**
     *
     */
    protected function loadAllNetworks() {
        foreach($this->networkList as $network) {
            $this->get($network);
        }
    }

	/** 
     * Get an instance of a network class
     * @param string $network The Social Network to load
     * @return object The Network class
     */
    public function get($network) {
    	//Consistency
    	$network = strtolower($network);

        //Check if it's loaded already
        if(!array_key_exists($network, $this->networks)) {
            //If not, load an instance
            $this->networks[$network] = $this->load($network);
        }

        //Return the the network instance
        return $this->networks[$network];
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
        $libraryClass = 'SCRMHub\\Framework\\Networks\\'.ucfirst(strtolower($network));

        //Create the class and return it
        return new $libraryClass($this->app);
    }
}