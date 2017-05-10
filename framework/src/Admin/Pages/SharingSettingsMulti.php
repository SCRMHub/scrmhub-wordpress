<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\Pages\SharingSettings;
use SCRMHub\WordpressPlugin\Templates\Admin\SharingSettings as Template;

class SharingSettingsMulti extends SharingSettings {
	protected $multi = true;

	public function run() {
		$this->verify();
		$this->render();
	}

	protected function verify() {
		if ($this->verifyForm()) {

			//Networks
			if(isset($_POST['sharing_multi_enable'])) {
				$settings = $_POST['sharing_settings'];
				$settings['enabled'] = $_POST['sharing_multi_enable'];

				//Add the log in flag
				if(!isset($settings['options']['login'])) {
					$settings['options']['login'] = false;
				}

				$settingsSave = serialize($settings);

				//Store the global setting
				update_site_option('scrmhub_network_sharing_options', $settingsSave);

				//Then loop through all the sites
				$blogs = $this->getSites();
				foreach($blogs as $blog) {
					$blog_id    = $blog->blog_id;

					//Make a backup, just in case...
					if($backupValue = get_blog_option($blog_id, 'scrmhub_site_sharing_options')) {
						update_blog_option($blog_id, 'scrmhub_site_sharing_options_backup', $backupValue);
					}

					//And the new values
					update_blog_option($blog_id, 'scrmhub_site_sharing_options', $settingsSave);
				}

				
	        	$this->save_success_message();

				//update_option('scrmhub_site_sharing_options', serialize($_POST['sharing_settings']));
			}
		}
	}

	protected function render() {
		$values = array(
			'multi'					=> $this->multi,
	    	'shareNetworks'			=> $this->app->values->getNetworkList(),
	    	'actionname'			=> $this->actionName(),
	    	'postTypes'				=> get_post_types(array('public'   => true)),
	    	'sharing_settings'		=> array_merge($this->defaultOptions, (array)@unserialize(get_site_option('scrmhub_network_sharing_options')))
	   	);

	    //Load the template
	    echo (new Template())->render($values);
	}
}