<?php
namespace SCRMHub\WordpressPlugin\Actions;

use SCRMHub\WordpressPlugin\Actions\_BaseAction;

class ShortUrl extends _BaseAction {
	function hash($hash) {
        //Disable Wordpress caching
        define( 'DONOTCACHEPAGE', 1 );
        nocache_headers();
        
        $shortlink = (new \SCRMHub\SDK\API\ShortUrl())->get(['hash' => $hash]);

        $result = $shortlink->getResult();

        //Was the URL found?
        if($result) {
        	wp_redirect($result['url']);

        //If not go to the hompeage
        } else {
        	wp_redirect(bloginfo('wpurl'));
        }

        //Just because...
        exit();
    }
}