<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\Framework\Utility\App;

class Data {
	private $app;

	private $mustEncrypt = array(
			'scrmhub_token'
		);

	function __construct(App $app) {
		$this->app = $app;
	}

	function setBulk($data) {
		foreach($data as $key => $value) {
			$this->set($key, $value);
		}
	}

	/**
	 * Set a user value
	 * @param string $key 		The name of the user option
	 * @param string $value 	The value of the user option
	 * @param boolean $encrypt 	Whether to encrypt the value or not
	 * @param int $expires 		Optional, how lon the token lasts in seconds
	 * @return Wordpress WP_Query result
	 */
	function set($key, $value, $encrypt = false, $expires = null) {
		global $wpdb;
		//Get Current User ID
		$user_id = get_current_user_id();

		if(empty($key) || empty($value)) {
			return false;
		}

		//no user
		if(empty($user_id) || $user_id === 0) {
			return false;
		}

		//store arrays nicely
		if(is_array($value)) {
			$value = serialize($value);
		}

		//Encrypt the value
		if($encrypt || isset($this->mustEncrypt[$key])) {
			$value = utf8_encode($this->app->encrypto->encrypt($value));
		}

		//consistency
		$key = strtolower($key);

		if($expires) {
			//Make it into a data

			$sql = "INSERT INTO ".$wpdb->base_prefix."scrmhub_usermeta
						(user_id, meta_key, meta_value, encrypted, created_at, updated_at, expires_at)
					VALUES
						(%d, %s, %s, %s, %s, %s, %s)
					ON DUPLICATE KEY
						UPDATE
							meta_value 	= %s,
							encrypted	= %s,
							updated_at 	= %s,
							expires_at 	= %s;
					";

			//Prepare it and run it
			$sql = $wpdb->prepare($sql,
				$user_id,
				$key,
				$value,
				$encrypt,
				current_time('mysql', 1),
				current_time('mysql', 1),
				$expires,
				$value,
				$encrypt,
				current_time('mysql', 1),
				$expires
			);
		} else {
			$sql = "INSERT INTO ".$wpdb->base_prefix.'scrmhub_usermeta'."
					(user_id, meta_key, meta_value, encrypted, created_at, updated_at)
				VALUES
					(%d, %s, %s, %s, %s, %s)
				ON DUPLICATE KEY
					UPDATE
						meta_value 	= %s,
						encrypted	= %s,
						updated_at 	= %s;
				";

			//Prepare it and run it
			$sql = $wpdb->prepare($sql,
				$user_id,
				$key,
				$value,
				$encrypt,
				current_time('mysql', 1),
				current_time('mysql', 1),
				$value,
				$encrypt,
				current_time('mysql', 1)
			);
		}
		
		return $wpdb->query($sql);
	}

	function get($key = false) {
		if(!$key)
			return false;

		//consistency
		$key = strtolower($key);

		global $wpdb;

		//Get Current User ID
		$user_id = get_current_user_id();

		$sql = "SELECT meta_value, encrypted
				FROM ".$wpdb->base_prefix.'scrmhub_usermeta'."
				WHERE
					user_id 	= %d AND
					meta_key 	= %s AND
					(expires_at > NOW() OR expires_at is null)
				";
		
		$sql = $wpdb->prepare($sql,$user_id,$key);
		$result = $wpdb->get_results($sql, ARRAY_A);

		if(!empty($result)) {
			$result = $result[0];
			if($result['encrypted'] == 1) {
				return $this->app->encrypto->decrypt(utf8_decode($result['meta_value']));
			}
			return $result['meta_value'];
		}

		return false;
	}


	function getAll() {
		global $wpdb;
		//Get Current User ID
		$user_id = get_current_user_id();

		$sql = "SELECT meta_key, meta_value, encrypted
				FROM ".$wpdb->base_prefix.'scrmhub_usermeta'."
				WHERE
					user_id 	= %d
				";
		
		$sql = $wpdb->prepare($sql,$user_id);
		$result = $wpdb->get_results($sql, ARRAY_A);

		if(!empty($result)) {
			$response = array();

			//Loop through fields
			foreach($result as $row) {
				if($row['encrypted'] != 1) {
					//Only include un-encryrpted items
					$response[$row['meta_key']] = $row['meta_value'];
				}
			}
			return $response;
		}

		return false;
	}

	/**
	 * Search for a user id using a key and value
	 */
	function searchForUserKeyValue($key, $value) {
		global $wpdb;
		$sql = "SELECT user_id
				FROM ".$wpdb->base_prefix.'scrmhub_usermeta'."
				WHERE
					meta_key 	= %s AND
					meta_value 	= %s;
				";

		$sql = $wpdb->prepare($sql,$key,$value);
		$result = $wpdb->get_results($sql, ARRAY_A);


		if(!empty($result)) {
			return $result[0]['user_id'];
		}

		return false;
	}

	/**
	 * Search for a user id by a value only
	 */
	function searchForUserValue($value) {
		global $wpdb;
		$sql = "SELECT user_id
				FROM ".$wpdb->base_prefix.'scrmhub_usermeta'."
				WHERE
					meta_value 	= %s
				LIMIT 1;
				";

		$sql = $wpdb->prepare($sql,$value);
		$result = $wpdb->get_results($sql, ARRAY_A);

		if(!empty($result)) {
			return $result[0]['user_id'];
		}

		return false;
	}

	/**
	 * Delete an item
	 */
	function delete($key = false, $user_id = false) {
		if(!$key)
			return false;

		global $wpdb;
		//Get Current User ID
		if(!$user_id)
			$user_id = get_current_user_id();

		if(!$user_id)
			return;

		$sql = "DELETE
				FROM ".$wpdb->base_prefix.'scrmhub_usermeta'."
				WHERE
					user_id 	= %d AND
					meta_key 	= %s
				";
		
		$sql = $wpdb->prepare($sql,$user_id,$key);
		$result = $wpdb->get_results($sql, ARRAY_A);
	}
}
