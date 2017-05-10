<?php
namespace SCRMHub\WordpressPlugin\Actions;

use SCRMHub\WordpressPlugin\Actions\_BaseAction;
use SCRMHub\WordpressPlugin\Templates\Site\ShareComplete as Template;

use SCRMHub\WordpressPlugin\Interfaces\Shortener;

use Exception;
use SCRMHub\WordpressPlugin\Service\SCRMHubError;

use SCRMHub\SDK\API\Activity;
 
class Share extends _BaseAction {
	/** 
	 * Run the process
	 */
	function run($action) {
        //Disable Wordpress caching
        define( 'DONOTCACHEPAGE', 1 );
        nocache_headers();
        
		//Get the network - will fail if it's bad
        $network = $this->getNetwork();

        if(!$network) {
            throw new Exception('Invalid network');
            die();
        }

        //Share actions
		switch($action) {
            case 'start':
                $share = $this->start();
                break;

            case 'finish':
                $this->finish();
                break;

            default:
                throw new \Exception('Incorrect action supplied');
                exit;
        }
	}

	/**
	 * Start the share process
	 */
	private function start() {
        //Double check this is enabled
        if(!$this->networkClass->canShare()) {
            exit((new Template())->render(array('message' => 'Sharing is not enabled for the network '.$this->network)));
        }

        $postid=(int)$_GET['postid'];
        if($postid <= 0 || !$post = get_post($postid)) {
            exit('Post not found');
        }

        //Get the post object
        $uuid = $this->app()->person->getPuuid(true);

        //Get the thumbnail if there is one
        $image = (
            has_post_thumbnail($post->ID) ?
            wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'single-post-thumbnail') :
            array());


        //$link = get_permalink($post->ID);
        $link = $cleanLink = $this->app->shorturls->getPostUrl($post->ID);

        //Arguments to append
        $linkArgs = [
            'shref' => $uuid
        ];

        //Build the final link
        $link.= (strpos($link, '?') > 0 ? '&' : '?').http_build_query($linkArgs, '&');

        //Get the information from the post
        $shareData = [
            'title'         => $post->post_title,
            'subtitle'      => get_bloginfo('name'),
            'description'   => !empty($post->post_excerpt) ? $post->post_excerpt : $post->post_title,
            'link'          => $link,
            'picture'       => isset($image[0]) ? urldecode($image[0]) : null,
            'tags'          => '', //coming later
            '_t'            => (new \DateTime())->format('YmdHis')
        ];

        //Tracking
        $trackData = [
            'puuid'         => $uuid,
            'type'          => 'social',
            'target'        => $this->network,
            'useraction'    => 'share',
            'id'            => $this->app->shorturls->getPostHash($post->ID),
            'referrer'      => $post->guid,
        ];

        //Does the network support callbacks?
        if($this->networkClass->canCallback()) {
            $shareData['callback']     = $this->buildCallbackUrl($post->ID);
            $trackData['useraction']   = 'share-start';
        }

        //Track it
        $response = (new Activity())->create($trackData);
        
        //Load the class
        try {
            $shareUrl = $this->networkClass->shareLink($shareData);

            //Couldn't make the link
            if(empty($shareUrl) || !$shareUrl) {
                throw new SCRMHubError('scrmhub_api_shorturl', array('description' => 'Error creating share URL for '. $this->network, 'data' => $shareData));
            }
        } catch(SCRMHubError $e) {
            //Do we need to stop?
            die('Something went wrong with the share. We\'re looking into it');
        } catch(Exception $e) {
            //Or here?
            die('Something went wrong with the share. We\'re looking into it');
        }

        //redirect
        wp_redirect($shareUrl);
        exit();
	}

	/**
     * Track a completed link share
     * @param array         $shareData       The data to build the share url from a $_GET request
     * @return url
     */
	private function finish() {
        $shareData = $_GET;

        $postid=(int)$_GET['postid'];
        if($postid <= 0 || !$post = get_post($postid)) {
            exit('Post not found');
        }

        //The data to send
        $trackData = [
            'puuid'         => $this->app()->person->getPuuid(true),
            'type'          => 'social',
            'target'        => $shareData['network'],
            'useraction'    => 'share',
            'referrer'      => $post->guid,
        ];

        //Was there a 3rd party callback id
        switch($shareData['network']) {
            case 'facebook':
                if(isset($shareData['post_id']) && !empty($shareData['post_id'])) {
                    $trackData['id'] = $shareData['post_id'];
                } else {
                    $trackData['useraction'] = 'share-cancelled';
                }
                
                break;
        }

        //Track complete
        (new Activity())->create($trackData);

        //Response
        $shareData = array(
            'network'       => $this->networkClass->getName(),
            'networkLabel'  => $this->networkClass->getLabel(),
            'message'       => 'Share Complete',
            'uuid'          => $this->app()->person->getPuuid(),
        );

        exit((new Template($shareData))->render($shareData));
	}

    /** 
     * Build the return URL
     * @param int $postid The post we want to come back to
     * @return url The URL to come back to
     */
    private function buildCallbackUrl($postid) {
        $args = [
            'scrmhub_action'    => 'share_callback',
            'network'           => $this->network,
            'postid'            => $postid,
            '_t'                => (new \DateTime())->format('YmdHis')
        ];

        return get_home_url().'?'.http_build_query($args);
    }


    public function track($uuid) {
        //Was the URL found?
        if($posturl = get_permalink()) {
            try {
                //Tracking
                $trackData = [
                    'type'          => 'inbound',
                    'target'        => 'refuser:'.$uuid,
                    'puuid'         => $this->app()->person->getPuuid(), // The current user
                    'useraction'    => 'shorturl',
                    'id'            => $posturl,
                    'referrer'      => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null
                ];

                //Track it
                (new Activity())->create($trackData);
            } catch (Exception $e) {
                scrmhub_warning($e);
                //No need to fail. Silent error
            }
        }
    }
}