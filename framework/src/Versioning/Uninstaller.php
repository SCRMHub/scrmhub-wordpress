<?php
namespace SCRMHub\WordpressPlugin\Versioning;

class Uninstaller {
	private $app;

	function __construct($app) {
		$this->app = $app;
	}

	function doUninstall() {
		if($this->app->values->getGlobalOption('scrmhub_fulluninstall')) {
			$this->doFullUninstall();
		}
		return true;
	}

	/*
	 * This will remove everything
	 */
	private function doFullUninstall() {
		//Always run these on uninstall
		$this->dropTables();
		$this->deleteScheduledTasks();

		//Core settings
		$this->deleteGlobalSettings();

		//And for each sub site
		if(is_multisite()) {
			$this->deleteMultiSiteSettings();			
		} else {
			$this->deleteSiteSettings();
		}
	}

	/**
	 * Delete a single site
	 */
	private function deleteGlobalSettings() {
		//Global settings
		delete_option('scrmhub_appid');
		delete_option('scrmhub_secret');
		delete_option('scrmhub_site_appkey');
		delete_option(SCRMHUB__VERSION_KEY);
	}



	/**
	 * Delete settings for all blogs
	 */
	private function deleteMultiSiteSettings() {
		//Get all sites and delete settings
		global $wpdb;
		$allsites = $wpdb->get_results("SELECT blog_id,path FROM ".$wpdb->base_prefix."blogs ORDER BY path");

		//Loop through all the blogs
		foreach($blogs as $blog) {
			//Switch to site
			switch_to_blog($blog->blog_id);

			//Remove settings for the current site
			$this->deleteSiteSettings();
		}

		//And back to the original place
		restore_current_blog();
	}

	//Site specific settings
	private function deleteSiteSettings() {
		delete_option('scrmhub_site_appkey');
		delete_option('scrmhub_site_connect_options');
		delete_option('scrmhub_site_connect_options_backup');
		delete_option('scrmhub_site_sharing_options');
		delete_option('scrmhub_site_sharing_options_backup');
	}

	//Drop our tabls
	private function dropTables() {
		global $wpdb;

		//User Meta
		$sql = "DROP TABLE IF EXISTS ".$wpdb->base_prefix."scrmhub_usermeta;";
		$e = $wpdb->query($sql);

		//Link Table
		$sql = "DROP TABLE IF EXISTS ".$wpdb->base_prefix."scrmhub_links;";
		$e = $wpdb->query($sql);
	}

	/**
	 * Delete any scheduled tasks
	 */
	private function deleteScheduledTasks() {
		$scrmhub = $GLOBALS['scrmhub'];
  		$scrmhub->cron->remove();
	}
}
