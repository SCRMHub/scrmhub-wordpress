<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\Framework\Utility\App;

class Token {
	private $app, $data;

	private $token,
			$session;

	private $tokenName = 'scrmhub_token';

	function __construct(App $app, $data) {
		$this->app = $app;
		$this->data = $data;
		$this->session = $app->session;

		if($token = $this->session->get($this->tokenName)) {
			$this->token = $token;
		}
	}

	function get() {
		if(empty($this->token)) {
			$this->token = $this->data->get($this->tokenName);
		}
		return $this->token;
	}

	function set($token, $save = false) {
		$this->token = $token;
		$this->session->set($this->tokenName, $token);

		if($save) {
			$this->save();
		}
	}

	/**
	 * Save the loaded token to the DB
	 */
	private function save() {
		if($this->token) {
			$this->data->set($this->tokenName, $this->token, true);
		}
	}

	/**
	 * Delete a token from the database and session
	 */
	public function delete() {		
		//Data in our table
		$this->data->delete($this->tokenName);
		$this->session->delete($this->tokenName);
		$this->token = null;

	}

	/**
	 *
	 * @return bool 		Valid or not?
	 */
	private function verifyTokenFormat($token) {
		return (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $token) ? true : false);
	} 
}