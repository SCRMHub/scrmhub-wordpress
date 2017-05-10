<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\SDK\API\Person as PersonAPI;
use SCRMHub\WordpressPlugin\Service\SCRMHubError;

use SCRMHub\Framework\Utility\App;

class Uuid {
    private $app;

    private $session;
    private $data;

    private $uuid;

    private $uuid_name      = 'scrmhub_uuid';
    private $uuid_dbname    = 'uuid';

    function __construct(App $app, $data) {
        $this->app      = $app;
        $this->data     = $data;
        $this->session  = $app->session;

        if($uuid = $this->session->get($this->uuid_name)) {
            $this->uuid = $uuid;
        }
    }


    /**
     * Get the user uuid
     * @param book $generate    Retrieve one if we don't have it
     * @return string           The Uuid
     */
    function get($generate = false) {
        //check it we already have one
        if(empty($this->uuid)) {
            $this->checkUuidAvailable();
        }       

        //No PUUID and the request asks for one so make one
        if($generate && empty($this->uuid)) {
            $this->generateUuid();
        }

        return $this->uuid;
    }

    /**
     * Check if the PUUID is available
     * @return string           The UUID
     */
    private function checkUuidAvailable() {
        //Got it in a session
        if($uuid = $this->app->session->get($this->uuid_name)) {
            $this->uuid = $uuid;

        //Got it in the DB
        } elseif($uuid = $this->data->get($this->uuid_dbname)) {
            $this->set($uuid, false);

        //Got it in a cookie (last resort)
        //NOTE: We do not 100% trust this value so will not save it to the DB
        } else if ($uuid = $this->app->cookie->get($this->uuid_name)) {
            // $this->uuid = $uuid;
            // $this->app->session->set($this->uuid_name, $uuid);
            $this->set($uuid, false);
        }

        //Return any value stored here
        return $this->uuid;
    }   



    function set($uuid, $save = true, $verify = true) {
        if($this->verifyTokenFormat($uuid)) {
            $this->uuid = $uuid;
            $this->session->set($this->uuid_name, $uuid);

            //Prefer to do this client side
            $this->app->cookie->set(
                $this->uuid_name,
                $uuid
            );

            if($save) {
                $this->save();
            }
        }       
    }

    /**
     * Save the loaded uuid to the DB
     */
    private function save() {
        if($this->uuid) {
            $this->data->set($this->uuid_dbname, $this->uuid);
        }
    }

    /**
     * Generate a PUUID from the API
     * @return string               The UUID
     */
    private function generateUuid() {
        $api = new PersonAPI();

        $response = $api->get();

        if ($response->isOk()) {
            $uuid = $response->getResult();
            $this->set($uuid);
        } else {
            // handle error            
            throw new SCRMHubError('person_uuid_get', array('message' => $response->getError()));
        }

        return $this->uuid;
    }

    /**
     * Delete a token from the database and session
     */
    private function delete() {     
        $this->uuid = null;
        $this->data->delete($this->uuid_dbname);
        $this->app->session->delete($this->uuid_name);
        $this->app->cookie->delete($this->uuid_name);
    }

    /**
     *
     * @return bool         Valid or not?
     */
    private function verifyTokenFormat($token) {
        return (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $token) ? true : false);
    } 
}