<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use SCRMHub\WordpressPlugin;

use SCRMHub\Framework\Utility\App;

abstract class _BaseSocialNetwork {
	//Allow the config to be accessed by the classes
	protected $config = [];

	//Is sharing enabled?
	protected
		$canShare 	= false,
		$canConnect = false;

	protected
		$canCallback= false;

	//force sharing to always be off
	protected
		$disableShare = false;

	//Force connect to always be off
	protected
		$disableConnect = false;

	protected $network;

	protected $networkLabel;

	protected $title;

	protected $app;

	protected $stateRequestField = 'state';

	protected $baseShare = [
		'title' 		=> null,
		'subtitle'		=> null,
		'callback'		=> null,
		'description' 	=> null,
		'picture'		=> null,
		'tags'			=> null,
		'link'			=> null,
		'via'			=> null
	];

	//Construct
	function __construct(App $app) {
		$this->app = $app;
	}

	public function setConfig($config) {
		$this->config = $config;

		if(isset($config['enabled']) && $config['enabled']) {
			$this->enableShare();
		}
	}

	public function setShareConfig($config) {
		$this->config['share'] = $config;

		if(isset($config['enabled']) && $config['enabled']) {
			$this->enableShare();
		}
	}

	public function setConnectConfig($config) {
		$this->config['connect'] = $config;

		if(isset($config['enabled']) && $config['enabled']) {
			$this->enableConnect();
		}
	}


	public function enableShare() {
		if(!$this->disableShare)
			$this->canShare = true;
	}

	public function enableConnect() {
		if(!$this->disableConnect)
			$this->canConnect = true;
	}

	public function getName() {
		return $this->network;
	}

	public function getLabel() {
		if(!empty($this->networkLabel)) {
			return $this->networkLabel;
		}
		return $this->network;
	}


	/**
	 * Can this network share?
	 * @return bool Can you share
	 */ 
	public function canShare() {
		return $this->canShare;
	}

	/**
	 * Can this network connect?
	 * @return bool Can you connect to this network
	 */ 
	public function canConnect() {
		return $this->canConnect;
	}

	/**
	 * Can this network do callbacks?
	 * @return bool Can you callback from this network
	 */ 
	public function canCallback() {
		return $this->canCallback;
	}

	/**
	 * Create the redirect to 3rd party link
	 * If no share setup, will return false
	 *
	 * @return string url
	 */
	function shareLink($config) {
		if($this->canShare) {
			return $this->buildShareLink($config);
		}

		return false;
	}

	protected function buildShareLink($config) {
		return false;
	}
}