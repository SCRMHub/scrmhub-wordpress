<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\_BaseAdminPage;
use SCRMHub\WordpressPlugin\Templates\Admin\ConnectSettings as Template;

class ConnectSettings extends _BaseAdminPage {
	protected $defaultOptions = [
		'enabled' => false,
		'options' => array(
			'redirect' 			=> 'admin',
			'redirecturl'		=> null,
			'loginform'			=> true,
			'user_photo'		=> true,
			'commentconnect'	=> false,
			'icononly'			=> false
		),
		'networks' 	=> array()
	];

	public function run() {
		$this->verify();

		$this->render();
	}

	protected function verify() {
		if ($this->verifyForm()) {
			//Networks
			if(isset($_POST['connect_options'])) {
				update_option('scrmhub_site_connect_options', serialize($_POST['connect_options']), true);
	        	$this->save_success_message();
			}
		}
	}

	protected function render() {
		$values = array(
			'multi'					=> $this->multi,
	    	'shareNetworks'			=> $this->app->values->getNetworkList(),
	    	'actionname'			=> $this->actionName(),
	    	'connect_options'		=> array_merge($this->defaultOptions, (array)@unserialize(get_option('scrmhub_site_connect_options')))
	   	);

	    //Load the template
	    echo (new Template())->render($values);
	}
}