<?php
namespace SCRMHub\WordpressPlugin\Admin\Pages;

use SCRMHub\WordpressPlugin\Admin\_BaseAdminPage;
use SCRMHub\WordpressPlugin\Templates\Admin\MultiSiteSettings as Template;

class MultiSiteSettings extends _BaseAdminPage {
	public function run() {
		$this->verify();

		$this->render();
	}

	protected function verify() {
		if ($this->verifyForm()) {
			$blogs = $this->getSites();
			foreach($blogs as $blog) {
				$blog_id    = $blog->blog_id;
				$field		= $blog_id.'_scrmhub_site_appkey';

				if(isset($_POST[$field])) {
					update_blog_option($blog_id, 'scrmhub_site_appkey', $_POST[$field]);
				}
			}

	        $this->save_success_message();
		}
	}

	protected function render() {
		//Give me an action
		$values = array(
			'multi'			=> true,
			'actionname' 	=> $this->actionName(),
			'blogs'			=> array()
		);

		$blogs = $this->getSites();

		foreach($blogs as $blog) {
			$thisBlog = array();
			$blog_id    		= $blog->blog_id;
            $thisBlog['label']	= $blog->domain.(!empty($blog->path) ? $blog->path : '');
            $thisBlog['field']	= $blog_id.'_scrmhub_site_appkey';
            $thisBlog['value']	= get_blog_option($blog_id, 'scrmhub_site_appkey');
            $values['blogs'][] 	= $thisBlog;
        }

		//Get the form values
		$values['scrmhub_global_login']	= get_site_option('scrmhub_global_login');

	    //Load the template
	    echo (new Template())->render($values);
	}
}