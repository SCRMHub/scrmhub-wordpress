<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\_BaseAdminPage;
use SCRMHub\WordpressPlugin\Templates\Admin\LocationToken as Template;

class LocationToken extends _BaseAdminPage {
	public function run() {
		$this->verify();

		$this->render();
	}

	protected function verify() {
		//Form data sent
		if ($this->verifyForm()) {
			//Site App Key
			if(isset($_POST['scrmhub_site_appkey'])) {
	        	update_option('scrmhub_site_appkey', $_POST['scrmhub_site_appkey'], true);
	        	$this->save_success_message();
	    	}
		}
	}

	protected function render() {
		$values = array(
			'actionname' 			=> $this->actionName(),
			'scrmhub_site_appkey'	=> get_option('scrmhub_site_appkey')
		);

	    //Load the template
	    echo (new Template())->render($values);
	}
}