<?php
namespace SCRMHub\WordpressPlugin\Admin;

use SCRMHub\Framework\Utility\App;

class Pages {
	private $app;

	function __construct(App $app) {
		$this->app = $app;
	}

	/**
	 * Load the admin page
	 */
	function __call ($class, $args) {
		$className = "\\SCRMHub\\WordpressPlugin\\Admin\\Pages\\$class";
		
		try {
			$class = new $className($this->app, $args);
			$class->run();
		} catch(Exception $e) {
			exit('bad call');
		}
	}
}