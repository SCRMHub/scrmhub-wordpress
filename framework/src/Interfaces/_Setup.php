<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

use SCRMHub\Framework\Utility\App;

class _Setup {
	private $app;
	function __construct(App $app) {
		$this->app = $app;
	}

	/**
	 * Load the interface items
	 */
	public function init() {
		//Hooky things
		$this->setupActions();

		//Login to comment
		if((bool)$this->app->get('connect_options.commentconnect') == true) {
    		$this->setupConnectCommenting(); 
    	}

    	//Add connect to login form
		if((bool)$this->app->get('connect_options.loginform') == true) {
    		$this->setupConnectLogin(); 
    	}
	}

	//Some nice custom actions
	private function setupActions() {
		//Render connect actions
		add_action('scrmhub_connect', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'auto')); //Render connect or logout 
		add_action('scrmhub_connect_login', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'login')); //Render just the login
		add_action('scrmhub_connect_logout', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'logout')); //render just the logout
		add_action('scrmhub_connect_login_button', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'button')); //render a single connect button

		//Ajax callbacks
		add_action('scrmhub_connect_ajax', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'ajaxpanel')); //Render connect panel


		add_action('scrmhub_shortlink', array('\SCRMHub\WordpressPlugin\Interfaces\Shortener', 'doShortUrlFromLink'));

		//Render all shares
		add_action('scrmhub_share', array('\SCRMHub\WordpressPlugin\Interfaces\Share', 'render_share'));
		add_action('scrmhub_share_button', array('\SCRMHub\WordpressPlugin\Interfaces\Share', 'button'));
		add_action('scrmhub_share_url', array('\SCRMHub\WordpressPlugin\Interfaces\Share', 'doShareUrl'));
	}

	private function setupConnectCommenting() {	
		global $user_ID;
		if(intval($user_ID) == 0){
			add_action('comment_form_must_log_in_after', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'login_comments')); 
		} else {
			add_action('comment_form_before', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'login_comments'));
		}
	}

	private function setupConnectLogin() {
		add_action('login_form', array('\SCRMHub\WordpressPlugin\Interfaces\Connect', 'login_admin'));
	}
}