<?php
namespace SCRMHub\WordpressPlugin\Service;

use SCRMHub\WordpressPlugin\Service\SCRMHubErrorMessage;
use Exception;

use SCRMHub\Framework\Utility\App;

class SCRMHubError extends Exception {
	/**
	 * Throw a PHP error nicely
	 */
	function __construct($code = 'unknown', $data = []) {
		$errorObject = new SCRMHubErrorMessage($code, $data);

		//Monolog it
		scrmhub_error($code, $data);
		
		//save it for later
		$this->errorObject 	= $errorObject;
		$this->message 		= $errorObject->message; 
		$this->code 		= $errorObject->code; 
		
		//Return the object if needed
		return $errorObject;
	}
}