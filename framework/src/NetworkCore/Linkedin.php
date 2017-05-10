<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use SCRMHub;
use SCRMHub\WordpressPlugin\NetworkCore\_BaseSocialNetwork as BaseSocialNetwork;

class Linkedin extends BaseSocialNetwork {
	private $shareLink = 'https://linkedin.com/shareArticle';

	protected $network = 'linkedin';

    protected $networkLabel = 'LinkedIn';
    
	/**
     * Build an external share link
     * @param array         $data       The data to build the share url
     * @return url
     */
    public function shareLink($data) {
    	$query = [];
        $query['mini']          = true;
        $query['url'] 			= $data['link'];
        $query['title'] 		= $data['title'];
        $query['summary']       = $data['description'];
        $query['picture'] 		= $data['picture'];

        //Return it
        return $this->shareLink.'?'.http_build_query($query);
    }
}