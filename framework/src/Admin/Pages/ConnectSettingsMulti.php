<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\Pages\ConnectSettings;
use SCRMHub\WordpressPlugin\Templates\Admin\ConnectSettings as Template;

class ConnectSettingsMulti  extends ConnectSettings {
	protected $multi = true;

	public function run() {
		$this->verify();
		$this->render();
	}

	protected function verify() {
		if ($this->verifyForm()) {

			//Networks
			if(isset($_POST['connect_multi_enable'])) {
				$settings = $_POST['connect_options'];
				$settings['enabled'] = $_POST['connect_multi_enable'];
				$settingsSave = serialize($settings);

				//Store the global setting
				update_site_option('scrmhub_network_connect_options', $settingsSave, true);

				//Then loop through all the sites
				$blogs = $this->getSites();
				foreach($blogs as $blog) {
					$blog_id    = $blog->blog_id;

					//Make a backup, just in case...
					if($backupValue = get_blog_option($blog_id, 'scrmhub_site_connect_options')) {
						update_blog_option($blog_id, 'scrmhub_site_connect_options_backup', $backupValue);
					}

					//And the new values
					update_blog_option($blog_id, 'scrmhub_site_connect_options', $settingsSave);
				}
				
	        	$this->save_success_message();
			}
		}
	}

	protected function render() {
		$values = array(
			'multi'					=> $this->multi,
	    	'shareNetworks'			=> $this->app->values->getNetworkList(),
	    	'actionname'			=> $this->actionName(),
	    	'connect_options'		=> array_merge(
	    		$this->defaultOptions,
	    		(array)@unserialize(get_site_option('scrmhub_network_connect_options'))
	    	)
	   	);

	    //Load the template
	    echo (new Template())->render($values);
	}
}