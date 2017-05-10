<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\_BaseAdminPage;
use SCRMHub\WordpressPlugin\Templates\Admin\ActivitySettings as Template;

class ActivitySettings extends _BaseAdminPage {
	protected $defaultOptions = array(
		'enabled' => true
	);

	public function run() {
		$this->verify();
		$this->render();
	}

	public function verify() {
		if ($this->verifyForm()) {
			//Form data sent
	        if(isset($_POST['activity_options'])) {
	        	update_option('scrmhub_site_activity_options', serialize($_POST['activity_options']), true);
	        	$this->save_success_message();
	        }
	    }
	}

	protected function render() {
		$values = array(
			'multi'					=> $this->multi,
	    	'actionname'			=> $this->actionName(),
			'activity_options'		=> array_merge($this->defaultOptions, (array)@unserialize(get_option('scrmhub_site_activity_options')))
	   	);

	    //Load the template
	    echo (new Template())->render($values);
	}
}