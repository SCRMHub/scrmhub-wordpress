<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\Framework\Utility\App;

class API {
	private $app;

	function __construct(App $app) {
		$this->app = $app;
	}
}
