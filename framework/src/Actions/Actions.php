<?php
namespace SCRMHub\WordpressPlugin\Actions;

use SCRMHub\WordpressPlugin\Actions\Share;
use SCRMHub\WordpressPlugin\Actions\ShortUrl;
use SCRMHub\WordpressPlugin\Actions\Connect;

class Actions {
	public static function run() {
		global $wp_query;

		if(isset($wp_query->query_vars['scrmhub_hash'])) {
			return (new ShortUrl)->hash($wp_query->query_vars['scrmhub_hash']);
		}

		//Post id
	    if (isset($wp_query->query_vars['scrmhub_refuuid'])) {
	    	(new Share)->track($wp_query->query_vars['scrmhub_refuuid']);
	        return;
	    }
	 
	    // if this is not a request for json or a singular object then bail
	    if (!isset($wp_query->query_vars['scrmhub_action']))
	        return;

	    

	    

	    switch($wp_query->query_vars['scrmhub_action']) {
	    	case 'share':
	    		(new Share)->run('start');
	    		break;

	    	case 'share_callback':
		    	(new Share)->run('finish');
	    		break;

	    	case 'share_click':
		    	(new Share)->click();
	    		break;

	    	case 'connect':
	    		(new Connect)->run('start');
	    		break;

	    	case 'connect_callback':
		    	(new Connect)->run('finish');
	    		break;

	    	case 'logout':
	    		(new Connect)->logout();
	    		break;
	    }
	}
}