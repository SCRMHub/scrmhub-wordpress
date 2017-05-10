<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use SCRMHub\WordpressPlugin\NetworkCore\_BaseSocialNetwork as BaseSocialNetwork;

class Weibo extends BaseSocialNetwork {
	protected $network = 'weibo';

    protected $networkLabel = 'Weibo';

    private $shareLink = 'http://service.weibo.com/share/share.php';

	/**
     * Build an external share link
     * @param array         $data       The data to build the share url
     * @return url
     * @todo track inbound clicks
     */
    protected function buildShareLink($data) {
    	$query = [];
        $query['appkey'] 		= $this->config['appid'];
        $query['url'] 			= $data['link'];
        $query['title'] 		= $data['caption'];
        $query['caption'] 		= $data['sitename'];
        $query['description'] 	= $data['description'];
        $query['pic'] 		    = $data['image'];
        $query['redirect_uri']	= $data['redirect_uri'];
        $query['source']        = 'bookmark';

        //Return it
        return $this->shareLink.'?'.http_build_query($query);
    }

}