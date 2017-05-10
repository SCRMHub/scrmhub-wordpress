<?php
namespace SCRMHub\WordpressPlugin\Admin;

use SCRMHub\Framework\Utility\App;

abstract class _BaseAdminPage {
	protected $app;

	protected $template = null;

	protected
		$didUpdate = true;

	protected  $defaultOptions = array(
		'options' => array()
	);

	protected $multi = false;

	public function __construct(App $app, array $args) {
		$this->app 		= $app;
		$this->args 	= $args;
	}

	protected function actionName() {
		$actionName = $_GET['page'];

		return $actionName;
	}

	public function save() {
		echo 'verifying?';
	}

	protected function verifyForm() {
		$actionName = $this->actionName();

		if ( 
		    isset($_POST[$actionName.'-nonce'])
		    && wp_verify_nonce($_POST[$actionName.'-nonce'], $actionName ) 
		) {
			return true;
		}
		return false;
	}

	protected function render() {}

	protected function getSites() {
		global $wpdb;
		return $wpdb->get_results("SELECT blog_id,domain,path FROM ".$wpdb->base_prefix."blogs ORDER BY path");
	}

	protected function getPostTypes() {
		return get_post_types(array('public'   => true));
	}

	protected function getSiteRoles($includeAdmin = false) {
		if ((is_multisite() && is_super_admin()) || (!is_multisite() && current_user_can('manage_options')) ) {
			global $wp_roles;

			if(!$includeAdmin) {
				$wpRoles = apply_filters( 'editable_roles', $wp_roles->roles);
				$roles = array();
				foreach($wpRoles as $role => $roleOptions) {
					if(!isset($roleOptions['capabilities']['level_10']) || $roleOptions['capabilities']['level_10']) {
						$roles[$role] = $roleOptions;
					}
				}
				return $roles;
			} else {
				return (array)apply_filters( 'editable_roles', $wp_roles->roles);
			}
			
		}
		return array();		
	}

	protected function updateGlobalOption($key, $value) {
		if($this->app->multisite) {
			update_site_option($key, $value);
		} else {
			update_option($key, $value, true); //Auto load
		}
	}

	protected function getGlobalOption($key) {
		if($this->app->multisite) {
			return get_site_option($key);
		} else {
			return get_option($key);
		}
	}

	protected function save_success_message() {
		echo '<div class="notice notice-success is-dismissible"><p>'.__( 'Updated!!', 'scrmhub-intercom' ).'</p></div>';
	}
}