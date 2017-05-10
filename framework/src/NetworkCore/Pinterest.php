<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use SCRMHub\WordpressPlugin\NetworkCore\_BaseSocialNetwork as BaseSocialNetwork;

class Pinterest extends BaseSocialNetwork {
    protected $followUrl    = 'http://pinterest.com/';
    protected $shareLink    = 'https://pinterest.com/pin/create/button/';
    protected $network      = 'pinterest';
    protected $networkLabel = 'Pinterest';
    
    /**
     * Build an external share link
     * @param array $data The data to build the share url
     * @return url
     */
    public function shareLink($data) {
        $query = [];
        $query['url']           = $data['link'];
        $query['media']         = $data['picture'];
        $query['description']   = $data['title'].".\n".$data['description'];

        //Return it
        return $this->shareLink.'?'.http_build_query($query, '&');
    }
}