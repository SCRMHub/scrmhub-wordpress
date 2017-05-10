<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\Framework\Utility\Bucket;

use SCRMHub\Framework\Utility\App;

class Cookie extends Bucket {
	private $app;
	private $defaultTTL = 31536000;
	
	function __construct(App $app) {
		$this->app = $app;
		

		//Set the object
		$this->setGlobalObjectStore('COOKIE');
	}

	/**
	 * Set a cookie
	 */
 	public function set($key, $value, $ttl = null) {
 		if(!$ttl)
 			$ttl = $this->defaultTTL;

 		if(!empty($value) && !empty($key)) {
 			setcookie($key, $value, time() + $ttl, '/', null, is_ssl());
 		}
 	}

 	/** 
 	 * Delete a cookie
 	 */
 	public function delete($name) {
 		//Write the cookie as empty an expiring in the past
		setcookie($name, '', time() - 3600);
	}
}