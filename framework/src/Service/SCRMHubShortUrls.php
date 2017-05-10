<?php
namespace SCRMHub\WordpressPlugin\Service;

use SCRMHub\Framework\Utility\App;
use SCRMHub\SDK\API\Url as ShortUrl;

class SCRMHubShortUrls {
	private $app;

	private $metaKey = 'scrmhub_urlhash';

	function __construct(App $app) {
		$this->app = $app;
	}


	/**
	 * Get the Short URL for a post
	 * @param  [type] $postId [description]
	 * @return [type]         [description]
	 */
	function getPostHash($postId) {
		$url = get_permalink($postId);

		//Does it exist?
		$shortUrl = get_post_meta($postId, $this->metaKey, true);

		//No url found 
		if(!$shortUrl) {
			$post = get_post($postId);
			$shortUrl = $this->fetchPostUrl($post->ID, $url);
		}

		//Return the full url
		return $shortUrl;
	}

	/**
	 * Get the Short URL for a post
	 * @param  [type] $postId [description]
	 * @return [type]         [description]
	 */
	function getPostUrl($postId) {
		$shortUrl = $this->getPostHash($postId);

		//Return the full url
		return $this->app->settings['shorturl'].$shortUrl;
	}	

	

	/**
	 * Fetch the URLS via the SCRM Hub API
	 * @param  object $posts The posts that need some love
	 */
	private function fetchUrls($posts) {
		foreach($posts as $post) {
			$this->fetchPostUrl($post->id, $post->guid);	        
		} 
	}

	/**
	 * Fetch the short url for this post
	 * Public to allow actions to update on save directly
	 * @param  [type] $postId [description]
	 * @param  [type] $url    [description]
	 * @return [type]         [description]
	 */
	public function fetchPostUrl($postId, $url = false) {
		//Get the post urls
		if(!$url) {
			$url = get_permalink($postId);
		}

		//the package
		$data = ['url' => $url, 'outbound' => true];		

        //New identity class
        $api = new ShortUrl();
        $response = $api->create($data);

        //Got a good response?
        if($shortUrl = $response->getResult()) {
        	update_post_meta($postId, $this->metaKey, $shortUrl);

        	return $shortUrl;
        }

        //Something went wrong
        return false;
	}

	/**
	 * Get the Short URL of a link
	 * @param  [type] $url [description]
	 * @return [type]      [description]
	 */
	public function getLinkShortUrl($link) {	
		//Search first
		$response = $this->queryUrl($link);

		if(!empty($response)) {
			$url = $response[0]->link_short;
		} else {
			$url = $this->createUrl($link);
		}

		return $url;
	}

	/**
	 * Query for a link for this blog
	 * @param  [var] 	$link [link to find]
	 * @return [array]        [result]
	 */
	private function queryUrl($link) {
		global $wpdb;
		$blog_id = $this->app->values->getGlobalOption( 'id') || 1;

		$table = $wpdb->prefix.'scrmhub_links';

		return $wpdb->get_results( $wpdb->prepare(
		    "SELECT link_id, link_short, link_hash FROM ".$table." WHERE blog_id = %d AND link_url = %s",
		    array(
		        $blog_id,
		        $link
		    )
		));
	}

	/**
	 * Query for a link for this blog
	 * @param  [var] 	$link [link to find]
	 * @return [array]        [result]
	 */
	private function createUrl($link) {
		if($hash = $this->fetchUrl($link)) {
			$shortUrl = $this->app->settings['shorturl'].$hash;

			global $wpdb;
			$blog_id = $this->app->values->getGlobalOption( 'id') || 1;

			$table = $wpdb->prefix.'scrmhub_links';
			$wpdb->query( $wpdb->prepare(
			    "INSERT INTO ".$table."(blog_id, link_url, link_short, link_hash) VALUES(%d, %s, %s, %s)",
			    array(
			        $blog_id,
			        $link,
			        $shortUrl,
			        $hash
			    )
			));

			return $shortUrl;
		}

		return false;
	}

	/**
	 * Call the API and get the url
	 * @param  [string] $link [A link to shorten]
	 * @return [string]       [A URL Hash]
	 */
	private function fetchUrl($link) {
		//the package
		$data = ['url' => $link, 'outbound' => true];		

        //New identity class
        $api = new ShortUrl();
        $response = $api->create($data);

        //Got a good response?
        if($shortUrl = $response->getResult()) {
        	return $shortUrl;
        }

        //Something went wrong
        return false;
	}
}