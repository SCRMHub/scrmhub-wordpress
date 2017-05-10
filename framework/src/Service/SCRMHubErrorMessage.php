<?php
namespace SCRMHub\WordpressPlugin\Service;

use SCRMHub\Framework\Utility\Bucket;
use Exception;

class SCRMHubErrorMessage extends Bucket {
	protected $items = array(
			'critical'	=> false,
			'errorcode' => null,
			'code'		=> 500,
			'message'	=> null,
			'data'		=> null
		);

	function __construct($id = 'unknown', $data = array()) {
		$this->errorcode = $errorName = 'error_'.$id;

		//Any data
		$this->data = $data;

		if(method_exists($this, $errorName)) {
			$this->bulkset($this->$errorName());
		} else {
			$this->bulkset($this->error_unknown());
		}

		//Allow overriding from with the data
		if($message = $this->get('data.message')) {
			$this->message = __($message, 'scrmhub');
		}		

		//Return this object
		return $this;
	}

	private function error_unknown() {
		return array(
			'message' => 'Something went wrong. Please try again.'
		);
	}

	private function error_csrf_001() {
		return array(
			'message' 	=> 'Invalid security token returned'
		);
	}

	private function error_scrmhub_api_error() {
		return array(
			'message' => 'SCRM Hub API Error'
		);
	}

	private function error_scrmhub_connect_tokenexchange() {
		return array(
			'message' => 'Unable to exchange code for token'
		);
	}

	private function error_person_uuid_get() {
		return array(
			'message'	=> 'Unable to get SCRM Hub UUID'
		);
	}

	private function error_login_newuser() {
		return array(
			'message' => 'Unable to log in to Wordpress'
		);
	}

	private function error_scrmhub_person_create() {
		return array(
			'message' => 'Unable to create the site user account.'
		);
	}

	private function error_scrmhub_person_create_no_email() {
		return array(
			'message' => 'Unable to create the site user account because there is no email address.'
		);
	}

	private function error_scrmhub_identity_api() {
		return array(
			'message' => 'An error occured loading the profile.'
		);
	}

	

	
}