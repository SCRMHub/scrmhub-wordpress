<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\_BaseAdminPage;
use SCRMHub\WordpressPlugin\Templates\Admin\SharingSettings as Template;

class SharingSettings extends _BaseAdminPage {
	protected $defaultOptions = array(
		'enabled' 	=> false,
		'options' 	=> array(
			'position' => 'bottom',
			'types' 	=> array(),
			'login'		=> false,
			'icononly'	=> false
		),
		'networks' 	=> array()
	);

	public function run() {
		$this->verify();
		$this->render();
	}

	protected function verify() {
		if ($this->verifyForm()) {

			//Networks
			if(isset($_POST['sharing_settings'])) {
				$settings = $_POST['sharing_settings'];

				//Add the log in flag
				if(!isset($settings['options']['login'])) {
					$settings['options']['login'] = false;
				}

				//Save it
				update_option('scrmhub_site_sharing_options', serialize($settings), true);
				
	        	$this->save_success_message();
			}
		}
	}

	protected function render() {
		$values = array(
			'multi'					=> $this->multi,
	    	'shareNetworks'			=> $this->app->values->getNetworkList(),
	    	'actionname'			=> $this->actionName(),
	    	'postTypes'				=> $this->getPostTypes(true),
	    	'sharing_settings'		=> array_merge($this->defaultOptions, (array)@unserialize(get_option('scrmhub_site_sharing_options')))
	   	);

	    //Load the template
	    echo (new Template())->render($values);
	}
}