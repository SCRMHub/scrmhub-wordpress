<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\Framework\Utility\Bucket;

class Profile extends Bucket {
	private $wp_fields = array(
		'firstName' => 'first_name',
		'lastName'	=> 'last_name',
	);

	/**
	 * Function to convert our array to a wordpress friendly one
	 */
	public function getAllWP() {
		$items = array();
		foreach($this->items as $key => $value) {
			if(isset($this->wp_fields[$key])) {
				$key = $this->wp_fields[$key];
			}
			$items[$key] = $value;
		}
		return $items;
	}
}