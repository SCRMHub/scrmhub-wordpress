<?php
namespace SCRMHub\WordpressPlugin\Cron;

use SCRMHub\Framework\Utility\App;

class Core {
	private
		$app;

	private 
		$cronNameHourly = 'scrmhub_cronjob';

	private 
		$cronNameTwiceDaily = 'scrmhub_cronjob_twice_daily';
	
	private 
		$cronNameDaily = 'scrmhub_cronjob_daily';

	function __construct(App $app) {
		$this->app = $app;
	}

	/**
	 * Install the Cron job
	 */
	function install() {
		//Check for the hourly cron
		if(!$timestamp = wp_next_scheduled( $this->cronNameHourly )){
			wp_schedule_event( time(), 'hourly', $this->cronNameHourly);
		}

		//Check for the daily cron
		if(!$timestamp = wp_next_scheduled( $this->cronNameTwiceDaily )){
			wp_schedule_event( time(), 'twicedaily', $this->cronNameTwiceDaily);
		}

		//Check for the daily cron
		if(!$timestamp = wp_next_scheduled( $this->cronNameDaily )){
			wp_schedule_event( time(), 'daily', $this->cronNameDaily);
		}
	}

	/**
	 * Removes the SCRM Hub cron job
	 */
	function remove() {
		wp_clear_scheduled_hook($this->cronNameDaily);
		wp_clear_scheduled_hook($this->cronNameTwiceDaily);
		wp_clear_scheduled_hook($this->cronNameHourly);
	}

	/**
	 * Hourly tasks
	 */
	function hourly() {
		//anything to do
	}

	/**
	 * Twice daily tasks
	 * @todo  add auto update code
	 */
	function twice_daily() {
		//Check for updates
		//AUTO UPDATE CODE
	}

	/**
	 * Daily tasls
	 * @return [type] [description]
	 */
	function daily() {
		//check for updates
	}
}