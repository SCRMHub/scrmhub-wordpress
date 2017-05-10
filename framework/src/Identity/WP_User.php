<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\WordpressPlugin\Service\SCRMHubError;

use SCRMHub\Framework\Utility\App;
use SCRMHub\WordpressPlugin\Identity\Data as UserData;

class WP_User {
	//Keep these safe
	private $app, $data;

	//Global UUID key
	private
		$user_puuid_key = 'scrmhub_uuid';

	/**
	 * Create the class
	 * @param App      $app  The SCRM Hub Plugin App
	 * @param UserData $data User Data object
	 */
	function __construct(App $app, UserData $data) {
		$this->app 	= $app;
		$this->data = $data;
	}

	/**
	 * Create a new user
	 * @param  array  $userObject 	What we got back from the APU
	 * @return int             		The user ID
	 */
	function create($userObject = array()) {
		if(empty($userObject)) {
			return false;
		}
		
		$user_id = 0;

		//Get their ID from Wordpress
		$user = get_user_by('email', $userObject->email);

		if(!empty($user)) {
			$user_id = (int)$user->ID;
		}

		//If null, try searching the SCRM Hub Meta table
		if($user_id <= 0) {
			//Have a puuid to look for
			if($puuid = $this->app->person->getPuuid()) {
				$check_user_id = $this->data->searchForUserKeyValue('puuid', $puuid);

				if($check_user_id) {
					$user_id = get_user_by('id', $check_user_id);
				}
			}

			//Search by email if still not found
			if(empty($user_id)) {
				$check_user_id = $this->data->searchForUserValue($userObject->email);

				//Double check that the user exists
				if($check_user_id) {
					$user_id = get_user_by('id', $check_user_id);
				}
			}
		}
		
		//If still no user go create one
		if((int)$user_id <= 0) {
			$user_id = $this->createWPUser($userObject);
		}

		//Got nothing for some reason
		if((int)$user_id <= 0) {
			return false;
		}

		//Add the user ID in
		$userObject->ID = $user_id;

		//And re-populate the session
		if($user_id && $user_id = $this->doLogin($user_id)) {
			if(!isset($userObject->display_name) || empty($userObject->display_name)) {
				$userObject->display_name = $userObject->name;
			}			

	        //Update the user details
	        $this->updateUser($user_id, $userObject);

	        //Update SCRM Hub table
	        $this->data->setBulk((array)$userObject->getAll());

	        //Set this puuid correctly
	        if($puuid = $userObject->uuid) {
	        	$this->app->person->setPuuid($puuid);
	        }
		}

		//Return the id
		return $user_id;
	}

	/*
	 * First off create the WP User
	 */
	function createWPUser($userObject) {
		$userObject->username = $this->generateUsername($userObject);

		//Safety first
		$userObject->email = sanitize_email($userObject->email);

		//Couldn't get a name
		if(!$userObject->username) {
			die('No username available');
			return false;
		}

		if (is_multisite()) {
			return $this->createMultiSite($userObject);
		} else {
			return $this->createSingleSite($userObject);
		}
	}

	/**
	 * Create a user in a single site environment
	 */
	private function createSingleSite($userObject) {
		add_filter ( 'wppb_register_admin_email_message_with_admin_approval', 		array(&$this, 'wpmu_disable_activation_email'), 10, 5 );
		add_filter ( 'wppb_register_admin_email_message_without_admin_approval', 	array(&$this, 'wpmu_disable_activation_email'), 10, 5 );

		return wp_create_user(
			$userObject->username,
			$this->generatePassword(),
			$userObject->email
		);
	}

	/**
	 * Create a user in a multi site environment
	 */
	private function createMultiSite($userObject) {
		global $wpdb;
		$user = false;

        //Get the user if they exist
		$user_details = wpmu_validate_user_signup($userObject->username, $userObject->email);

		//Error... Error...
		if (is_wp_error($user_details['errors']) && !empty($user_details['errors']->errors)) {
			$user_details['errors'];
			if(WP_Debug) {
				scrmhub_error(print_r($user_details['errors'], true));
			}

		//Create the user
        } else {
        	//Disable the email
        	add_filter('wpmu_signup_blog_notification', 	array(&$this, 'wpmu_disable_activation_email'), 1, 7);
			add_filter('wpmu_signup_user_notification', 	array(&$this, 'wpmu_disable_activation_email'), 1, 4);
			add_filter('wpmu_welcome_notification', 		array(&$this, 'wpmu_disable_activation_email'), 1, 5);
			add_filter('wpmu_welcome_user_notification', 	array(&$this, 'wpmu_disable_activation_email'), 1, 3);

			//Create the user
        	wpmu_signup_user($userObject->username, $userObject->email);       	

        	//Activate the user
        	$key = $wpdb->get_var($wpdb->prepare("SELECT activation_key FROM {$wpdb->signups} WHERE user_login = %s AND user_email = %s", $userObject->username, $userObject->email));

        	//Activate their account
            wpmu_activate_signup($key);

            //Get the user back
            $user = get_user_by('email', $userObject->email);
           
        }

        //Got a user
        if($user) {
        	//Give them access to all sites in the network
        	update_user_option($user->ID,'multi_user_level', 0, true);

            //Set their password
            wp_set_password(sanitize_text_field($this->generatePassword()), $user->ID);

            //Return the id
            return $user->ID;
        }

        //Return the user
        return false;
	}

	public function wpmu_disable_activation_email() {
		// Return false so no email sent
		return false;
	}

	/*
	 * Make a user name
	 */
	private function generateUsername($userObject) {
		if($userObject->name) {
			$username = $userObject->name;
		} else if($userObject->firstName && $userObject->lastName) {
			$username = ($userObject->firstName.' '.$userObject->lastName);
		} else {
			return false;
		}

		//The Way Wordpress does it
		$username = preg_replace( '/\s+/', '.', sanitize_user( strtolower($username), true ) );

		//What we're working with
		$usernameBase = $username;

		//Check if that exists
		$notfound 	= false;
		$count 		= 0;

		//Loop till we find a slot for that username
		while ($notfound == false) {
			if(username_exists($username)) {
				$count++;
				$username = $usernameBase.$count;

				//No point carrying on
				if($count >= 50)
					break;

			} else {
				//Found one!!!! YAY!
				break;
			}
		}

		//Return it
		return $username;
	}

	/*
	 * Generate a password for new users
	 */
	private function generatePassword() {
		//Generate a password using Wordpress' built in function
		$password = wp_generate_password(16);
		
		//Something went wrong
		if (empty($password)) {
			return new WP_Error('error', "<strong>ERROR: </strong>" . __('Error creating random password'));
		}

		//Return it
		return $password;
	}

	/**
	 *  Update a user
	 */
	private function updateUser($user_id, $user) {
		$user_id = wp_update_user((array)$user->getAllWP());

		//Add in uuid
		update_user_meta($user_id, $this->user_puuid_key, $user->uuid);

		//Photo url
		if($picture = $user->get('picture')) {
			update_user_meta($user_id, 'wsl_user_image', $picture );
			update_user_meta($user_id, 'scrmhub_profile_pic', $picture );
			update_user_meta($user_id, 'scrmhub_profile_pic_large', $picture );
		}

		//Got a large image
		if($picture_large = $user->get('picture_large')) {
			$this->app->logger->addError('picture large setting', $picture_large);
			update_user_meta ($user_id, 'scrmhub_profile_pic_large', $picture_large);
			update_user_meta ($user_id, 'wsl_user_image', $picture_large);
		}

		//Website
		if($websiteurl = $user->get('websiteurl')) {
			update_user_meta ($user_id, 'user_url', $websiteurl );
		}

		return $user_id;
	}

	/**
	 * log in automatically
	 */
	private function doLogin( $user_id ) {
		//Are they already logged in?
		$current_user_id = get_current_user_id();
		if($user_id == $current_user_id) {
			return $current_user_id;

		//This shouldn't happen, but jsut in case...
		} else if($current_user_id) {
			//Remove their auth cookie
			wp_clear_auth_cookie();
		}

		//Log the user in
		$user = get_user_by('id', $user_id );

		//Did we find a user?
		if(!$user ) {
			//Got this far, something went wrong
			throw new SCRMHubError('login_newuser', array('user_id' => $user_id));
			
			return false;
		}
		
		//Do the login magic
		$username = $user->user_login;
		wp_set_current_user( $user_id, $username);
		wp_set_auth_cookie($user_id);
		do_action( 'wp_login', $username );

		return $user_id;

		
	}
}
