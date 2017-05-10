<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\_BaseAdminPage;
use SCRMHub\WordpressPlugin\Templates\Admin\SiteSettings as Template;

class SiteSettings extends _BaseAdminPage {
	public function run() {
		$this->verify();
		$this->render();
	}

	protected function verify() {
		if ($this->verifyForm()) {
			//Form data sent
	        if(isset($_POST['scrmhub_appid']) && !empty($_POST['scrmhub_appid'])) {
	        	$this->updateGlobalOption('scrmhub_appid', $_POST['scrmhub_appid']);
	        }
	        
	        if(isset($_POST['scrmhub_secret']) && !empty($_POST['scrmhub_secret'])) {
	        	$scrmhub_secret = utf8_encode($this->app->encrypto->encrypt($_POST['scrmhub_secret']));
	        	$this->updateGlobalOption('scrmhub_secret', $scrmhub_secret);
	        }

	        if(isset($_POST['scrmhub_urls']) && !empty($_POST['scrmhub_urls'])) {
	        	//Did it get enabled?
	        	$oldUrlsSetting = $this->getGlobalOption('scrmhub_urls');
	        	$newUrlsSetting = $_POST['scrmhub_urls'];

	        	// //
	        	// if($newUrlsSetting == '1' && $oldUrlsSetting == '0') {
	        	// 	$this->generateUrlsCron();
	        	// }

	        	$this->updateGlobalOption('scrmhub_urls', $newUrlsSetting);
	        }

	        /**
	         * Full Uninstall
	         */
	        if(isset($_POST['scrmhub_fulluninstall'])) {
	    		$this->updateGlobalOption('scrmhub_fulluninstall', $_POST['scrmhub_fulluninstall']);
	    	}


	        /**
	         * Auto update settings
	         */
	        if(isset($_POST['scrmhub_autoupdate'])) {
	    		$this->updateGlobalOption('scrmhub_autoupdate', $_POST['scrmhub_autoupdate']);
	    	}

	    	//Bit Bucket User
	     //    if(isset($_POST['scrmhub_bitbucket_user'])) {
	     //    	$user = utf8_encode($this->app->encrypto->encrypt($_POST['scrmhub_bitbucket_user']));
	     //    	$this->updateGlobalOption('scrmhub_bitbucket_user', $user);
	    	// }

	    	//Bit Bucket User
	     //    if(isset($_POST['scrmhub_bitbucket_pass']) && !empty($_POST['scrmhub_bitbucket_pass']) && $_POST['scrmhub_bitbucket_pass'] != 'fillerpassword') {
	     //    	$pass = utf8_encode($this->app->encrypto->encrypt($_POST['scrmhub_bitbucket_pass']));
	     //    	$this->updateGlobalOption('scrmhub_bitbucket_pass', $pass);
	    	// }
	    	
	    	$this->save_success_message();
	    	
		}
	}

	protected function render() {
		$values = array(
			'actionname' 			=> $this->actionName(),
			'scrmhub_appid'			=> $this->getGlobalOption('scrmhub_appid'),
	   		'scrmhub_secret'		=> $this->app->values->getAppSecret(),
	   		'scrmhub_urls'			=> filter_var($this->getGlobalOption('scrmhub_urls'), FILTER_VALIDATE_BOOLEAN),
			'scrmhub_autoupdate'	=> $this->getGlobalOption('scrmhub_autoupdate'),
			'scrmhub_fulluninstall' => $this->getGlobalOption('scrmhub_fulluninstall')
	   	);

	    //Load the template
	    echo (new Template())->render($values);
	}
}

	    