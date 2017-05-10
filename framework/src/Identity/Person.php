<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\WordpressPlugin\Identity\API;
use SCRMHub\WordpressPlugin\Identity\Data;
use SCRMHub\WordpressPlugin\Identity\Token;
use SCRMHub\WordpressPlugin\Identity\Uuid;
use SCRMHub\WordpressPlugin\Identity\Profile;

use SCRMHub\SDK\API\Identity as IdentityApi;

use SCRMHub\Framework\Utility\Bucket;

use SCRMHub\WordpressPlugin\Service\SCRMHubError;

use SCRMHub\Framework\Utility\App;

class Person {
	private $app;
	private $data;
	private $person;
	private $puuid;
	private $token;
	private $uuid;
	private $profile;

	private $sessionName 	= 'scrmhub';

	function __construct(App $app) {
		$this->app = $app;

		//The handler
		$this->data 	= new Data($app);
		$this->uuid 	= new Uuid($app, $this->data);
		$this->token 	= new Token($app, $this->data);
		$this->person 	= new WP_User($app, $this->data);

		
	}

	function getProfile() {
		//Already loaded
		if($this->profile) {
			return $this->profile;
		}

		if($profile = $this->app->session->get('scrmhub_profile')) {
			$this->profile = new Profile($profile);
		}

		//Ok, how about from the DB?

		return $this->profile;
	}

	/**
	 * Create user
	 */
	function create($profile, $network = false) {
		if($success = $this->person->create($profile)) {
			//Set the uuid
			$this->uuid->set($profile->uuid);

			//Set the access token
			$this->token->set($profile->scrmhub_token, true);

			//Flag they
			if($network) {
				//Flag that they connected to this network
            	$this->setConnectedTo($network);
			}

			//return the profile
			return $profile;				
		}

		throw new SCRMHubError('scrmhub_person_create', array('profile' => $profile, 'uuid' => $uuid));

		return false;
	}

	/**
	 * Load a profile using an access token
	 * @param string $accessToken 	A Valid access token
	 * @param string $uuid 			The corresponding user id
	 */
	public function loadProfileFromToken($accessToken, $uuid) {
		if(!$accessToken || !$uuid) {
			 throw new SCRMHubError(
            	'error_scrmhub_identity_api',
            	array('accesstoken' => $accessToken, 'uuid' => $uuid)
            );
			return false;
		}

		$api = new IdentityApi();
		$data = array(
			'puuid'			=> $uuid,
			'token' 		=> $accessToken
		);

		//Get it
		$response = $api->me($data);

		//Was it ok?
		if ($response->isOk()) {
			//Load the profile
			$userProfile = new Profile($response->getResult());
			$userProfile->scrmhub_token = $accessToken;

			//Stick it in the session
			$this->app->session->set('scrmhub_profile', $userProfile->getAll());
			$this->profile = $userProfile;

			//Return the object
			return $userProfile;

        } else {
        	// handle error            
            throw new SCRMHubError(
            	'scrmhub_identity_api', array(
            		'api' => '/identity/me',
            		'description' => 'Couldn\'t get a person using tokens',
            		'uuid' => $uuid,
            		'accesstoken' => $accessToken
            	)
            );
        }		

		return false;
	}
	
	/**
	 * Get a puuid or not...
	 * @param bool $generate 	Generate a UUID if there isn't one?
	 * @return string 			The UUID
	 */
	public function getPuuid($generate = false) {
		return $this->uuid->get($generate);
	}

	/**
	 * Set the puuid value correctly
	 * @param string $uuid 			The UUID to set
	 */
	function setPuuid($uuid) {
		return $this->uuid->set($uuid);
	}	

	function getToken() {
		return $this->token->get();
	}
	//Set it and store it in session and DB (optional)
	function setToken($token, $save = false) {
		return $this->token->set($token, $save);
	}
	//Write it to the DB
	function saveToken() {
		return $this->token->save();
	}
	function deleteToken() {
		$this->token->delete();
	}

	function setConnectedTo($network) {
		if($networks = $this->data->get('scrmhub_connected')) {
			$networks = unserialize($networks);
		} else {
			$networks = array();
		}

		$networks[$network] = true;
		$this->data->set('scrmhub_connected', $networks);
	}

	function checkConnectedTo($network) {
		if($networks = $this->data->get('scrmhub_connected')) {
			$networks = unserialize($networks);
			if(isset($networks[$network])) {
				return true;
			}
		}
		return false;
	}


	/**
	LOG A USER OUT
	**/

	/**
	 * Logout
	 */
	function logout($logout = null) {
		if($user_id = get_current_user_id()) {
			//Remove the token
			$this->deleteToken();

			//Session
	        $this->app->session->destroy();
	    }

        return $logout;
	}
}