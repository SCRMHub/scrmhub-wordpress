<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

use SCRMHub\Framework\Utility\App;

class WordpressWebsiteFunctions {
	private $app;

	function __construct(App $app) {
		$this->app = $app;
		add_action('wp_ajax_scrmhub_connected', array(&$this, 'ajax_check_user_logged_in'));
		add_action('wp_ajax_nopriv_scrmhub_connected', array(&$this, 'ajax_check_user_logged_in'));
	}

	/** 
	 * Is a user logged in?
	 */
	function ajax_check_user_logged_in() {
		$response = array(
			'connected' => is_user_logged_in() ? true : false,
			'uuid'		=> $this->app->person->getPuuid(true)
		);

		wp_send_json($response);
	}
}