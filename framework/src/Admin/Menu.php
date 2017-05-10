<?php
namespace SCRMHub\WordpressPlugin\Admin;

use SCRMHub\Framework\Utility\App;

class Menu {
	function __construct(App $app) {
		$this->app = $app;
	}

	/**
	 * [SiteAdmin description]
	 */
	public function SiteAdmin() {
		if(!$this->app->multisite) {
			add_menu_page(
		    	'scrmhub',
		    	'SCRM Hub',
		    	SCRMHUB__PERMISSION_LEVEL,
		    	'scrmhub',
		    	null,
		    	SCRMHUB__PLUGIN_ASSETS . 'images/icon_16x16.png'
		    );

		    add_submenu_page(
				'scrmhub',
				'Site Settings',
				'Site Settings',
				SCRMHUB__PERMISSION_LEVEL,
				'scrmhub',
				array(&$this->app->admin_templates, 'SiteSettings')
			);

		    add_submenu_page(
				'scrmhub',
				'Location Token',
				'Location Token',
				SCRMHUB__PERMISSION_LEVEL,
				'scrmhub-location-token',
				array(&$this->app->admin_templates, 'LocationToken')
			);

		} else {
		    add_menu_page(
		    	'scrmhub',
		    	'SCRM Hub',
		    	SCRMHUB__PERMISSION_LEVEL,
		    	'scrmhub',
		    	null,
		    	SCRMHUB__PLUGIN_ASSETS . 'images/icon_16x16.png'
		    );

		    add_submenu_page(
				'scrmhub',
				'Location Token',
				'Location Token',
				SCRMHUB__PERMISSION_LEVEL,
				'scrmhub',
				array(&$this->app->admin_templates, 'LocationToken')
			);
		}

		add_submenu_page(
			'scrmhub',
			'Sharing Settings',
			'Sharing Settings',
			SCRMHUB__PERMISSION_LEVEL,
			'scrmhub-sharingsettings',
			array(&$this->app->admin_templates, 'SharingSettings')
		);

		add_submenu_page(
			'scrmhub',
			'Connect Settings',
			'Connect Settings',
			SCRMHUB__PERMISSION_LEVEL,
			'scrmhub-connectsettings',
			array(&$this->app->admin_templates, 'ConnectSettings')
		);

		add_submenu_page(
			'scrmhub',
			'Activity Settings',
			'Activity Settings',
			SCRMHUB__PERMISSION_LEVEL,
			'scrmhub-activitysettings',
			array(&$this->app->admin_templates, 'ActivitySettings')
		);

		add_submenu_page(
			'scrmhub',
			'Help',
			'Help',
			SCRMHUB__PERMISSION_LEVEL,
			'scrmhub-help',
			array(&$this->app->admin_templates, 'Help')
		);
	}

	/**
	 * The Network Admin menu type
	 */
	public function NetworkAdmin() {
		add_menu_page(
	    	'scrmhub',
	    	'SCRM Hub',
	    	SCRMHUB__PERMISSION_LEVEL,
	    	'scrmhub-apisettings',
	    	array(&$this->app->admin_templates, 'AppSettings'),
	    	SCRMHUB__PLUGIN_ASSETS . 'images/icon_16x16.png'
	    );

	    add_submenu_page(
			'scrmhub-apisettings',
			'Network App Keys',
			'Network App Keys',
			SCRMHUB__PERMISSION_LEVEL,
			'scrmhub-networkappkeys',
			array(&$this->app->admin_templates, 'MultiSiteSettings')
		);

		add_submenu_page(
			'scrmhub-apisettings',
			'Network Connect',
			'Network Connect',
			SCRMHUB__PERMISSION_LEVEL,
			'scrmhub-networkconnect',
			array(&$this->app->admin_templates, 'ConnectSettingsMulti')
		);

		add_submenu_page(
			'scrmhub-apisettings',
			'Network Sharing',
			'Network Sharing',
			SCRMHUB__PERMISSION_LEVEL,
			'scrmhub-networksharing',
			array(&$this->app->admin_templates, 'SharingSettingsMulti')
		);

		
	}

	public function options() {
		$this->_page = Util_Request::get_string( 'page' );
		if ( !Util_Admin::is_w3tc_admin_page() )
			$this->_page = 'w3tc_dashboard';

	}
}