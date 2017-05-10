<?php
namespace SCRMHub\WordpressPlugin\Actions;

abstract class _BaseAction {
	protected $app;

	protected $values;

	/**
     * @var string 	$network 		The Network name being worked with currently
     * @var mixed 	$networkClass	The Network Class loaded
     */
	protected 
		$network,
		$networkClass;  

	protected function app() {
		if(empty($this->app)) {
			global $scrmhub;
			$this->app = $scrmhub;
		}
		return $this->app;
	}

	protected function getNetwork() {
		global $wp_query;
	 
	    // if this is not a request for json or a singular object then bail
	    if (!isset($_GET['network']))
	        return;

	    //Load the network
	    if($networkClass = $this->app()->networks->get($_GET['network'])) {
	    	$this->networkClass = $networkClass;
	    	$this->network = $networkClass->getName();
	    }

	    //Return the network name
	    return $this->networkClass;
	}
}