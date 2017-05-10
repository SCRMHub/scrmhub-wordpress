<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use SCRMHub;
use SCRMHub\WordpressPlugin\NetworkCore\_BaseSocialNetwork as BaseSocialNetwork;
use DateTime;

class Facebook extends BaseSocialNetwork {
	// private $shareLink = 'https://www.facebook.com/dialog/feed';

	protected $network = 'facebook';

    protected $networkLabel = 'Facebook';

    protected $supports_callback = true;
	/**
     * Build an external share link
     * @param array         $data       The data to build the share url
     * @return url
     */
    protected function buildShareLink($data) {
        $data = array_merge($this->baseShare, $data);


        // $appId = $this->config['share']['app_id'];
        // if(empty($appId)) {
        //     _e('The Facebook ID is not configured in the plugin settings.', 'scrmhub');
        //     die();
        // }

    	$query = [];
        $query['app_token']		= $this->app->values->getAppKey();
        $query['link'] 			= $data['link'];
        $query['name'] 			= $data['title'];
        $query['caption'] 		= $data['subtitle'];
        $query['description'] 	= $data['description'];
        $query['picture'] 		= $data['picture'];
        $query['display']       = 'popup';
        $query['redirect_uri']  = $data['callback'];
        $query['_t']            = $data['_t'];

        //Get the URL
        $sharerelay = $this->app->settings['sharerelay'].$this->network;

        //Return it
        return $sharerelay.'?'.http_build_query($query, null, '&');
    }
}