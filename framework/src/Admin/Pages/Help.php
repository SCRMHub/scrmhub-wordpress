<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\_BaseAdminPage;
use SCRMHub\WordpressPlugin\Templates\Admin\Help as Template;

class Help extends _BaseAdminPage {
	public function run() {
		$this->render();
	}

	protected function render() {
	    //Load the template
	    echo (new Template())->render();
	}
}