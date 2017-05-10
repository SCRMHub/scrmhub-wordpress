<?php
namespace SCRMHub\WordpressPlugin\Bootstrap;

use SCRMHub\WordpressPlugin\Admin\Menu;

use SCRMHub\WordpressPlugin\Versioning\Installer;
use SCRMHub\WordpressPlugin\Versioning\AutoUpdate;

use SCRMHub\Framework\Utility\App;

class Admin {
	function __construct(App $app) {
		$this->app = $app;

		$this->app->admin_templates = function() use($app) {
			return new \SCRMHub\WordpressPlugin\Admin\Pages($app);
		};

		$this->app->interfaces = function() use($app) {
			return new \SCRMHub\WordpressPlugin\Interfaces\_Setup($app);
		};

		//Load the menu
		$menu = new Menu($this->app);

		//Load the menu
		add_action('admin_menu', array(&$menu, 'SiteAdmin'));
		add_action('network_admin_menu', array(&$menu, 'NetworkAdmin'));
		add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2 );

		//Enable auto update
		//$this->autoUpdate();

		add_action('admin_init', array(&$this, 'wp_ready'));

		//Add header JS
		add_action('admin_head', 	array('\SCRMHub\WordpressPlugin\Interfaces\Header', 'render'));

		//Activation hooks
		register_deactivation_hook(__FILE__, 'deactivate_scrmhub');
		register_uninstall_hook(__FILE__, 'uninstall_scrmhub');

		//Set user profile image
		if($this->app->values->getSettingsConnect()->user_photo == 1) {
			$this->app->user_photo->setup();
		}
	}

	function plugin_action_links( $links, $file ) {
		if ( $file == 'scrmhub/scrmhub.php' ) {
			$links[] =  sprintf( '<a href="%s" class="thickbox">%s</a>',
					esc_url( network_admin_url('options-general.php?page=scrmhub') ),
					esc_html__( 'Settings', 'scrmhub' )
				);
		}

		return $links;
	}

	/**
	 * Add in all the hooks
	 */
	public function wp_ready() {
		//Initialise the networks
		$this->app->networks->init();


	}

	/**
	 * Set up the auto update functions
	 */
	public function autoUpdate() {
		//Uncomment this to test
		//set_site_transient( 'update_plugins', null );

		//Auto update class
		new AutoUpdate($this->app);
	}
}


function deactivate_scrmhub() {
	$uninstaller = new \SCRMHub\WordpressPlugin\Versioning\Uninstaller();
	$uninstaller->deactivate();
	//die();
}

function uninstall_scrmhub() {
	$uninstaller = new \SCRMHub\WordpressPlugin\Versioning\Uninstaller();
	$uninstaller->fullUninstall();
}