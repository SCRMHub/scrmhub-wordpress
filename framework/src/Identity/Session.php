<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\Framework\Utility\Bucket;

use SCRMHub\Framework\Utility\App;

class Session extends Bucket {
    /**
     * Global SCRM Hub App Object
     */
    private
        $app;

	/**
	 * 
	 */
	function __construct(App $app) {
        //The global scrmhub app
        $this->app = $app;

        //register the session start function
        $this->setBeforeSet(array(&$this, 'start')); 
	}

    /**
     * Tell the bucket to use the SESSION global store
     */
    private function setGlobalStore() {
        $this->setGlobalObjectStore('SESSION');
    }

	/**
	 * Start the session
	 */
	public function start($key) {
		if(session_id()) {
            //Make sure the store is a global one
            $this->setGlobalStore();

            //Done
			return true;
        }

		//Start the session
	    session_start();

	    //Setup the store correctly
        $this->setGlobalStore();

        // /* Security check */
        // Something in Wordpress should handle this
        // if($this->get('HTTP_USER_AGENT') != '') {
        //     if (!WP_DEBUG && $this->get('HTTP_USER_AGENT') != $_SERVER['HTTP_USER_AGENT']) {
        //         // Finally, destroy the session.
        //         $this->destroy();

        //         //Bad session
        //         throw new \Exception('Bad session data');
        //         return false;
        //     }
        // } else {
        //     $this->set('HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        // }

        //Assign them a token that lasts for 2 hours, but renew it after 1 just to be safe
        if($this->get('user_session_created') && time() - $this->get('user_session_created') > 3600) {
            //Change the session id if it's renewing
            $this->regenerateId();

            //Flag the start time
            $this->set('user_session_created', time());
        }

        //Return the value
        return true;
	}

	/**
     * Completely destroy a user session
     */
    public function destroy() {
        //Clear all the values
        $this->clearAll();

        //Destory the session
        @session_destroy();
    }

    /**
     * write the session
     */
    public function commit() {
    	//Silent on errors just in case
        @session_write_close();
    }

    /**
     * Regenerate the user's session id
     */
    public function regenerateId() {
        session_regenerate_id(true);
    }
}