<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use SCRMHub\WordpressPlugin\NetworkCore\_BaseSocialNetwork as BaseSocialNetwork;

class Twitter extends BaseSocialNetwork {
	private $shareLink = 'https://twitter.com/intent/tweet';

	protected $network = 'twitter';

    protected $networkLabel = 'Twitter';


    protected $supports_callback = true;

    private $shortUrlLength = 24; //t.co link

	/**
     * Build an external share link
     * @param array         $data       The data to build the share url
     * @return url
     */
    public function shareLink($data) {

        if(isset($this->config['share']['via'])) {
            $this->baseShare['via'] = $this->config['share']['via'];
        }

        //Make sure we have all the data set
        $data = array_merge($this->baseShare, $data);

        //Make sure the data is nice
        $data = $this->trimTweet($data);

        //build the query string
    	$query = [];
        $query['tw_p']         = 'tweetbutton';
        $query['url'] 			= $data['link'];
        $query['text'] 			= $data['title'];
        $query['hashtags'] 		= $data['tags'];
        $query['via']           = empty($data['via']) ? null : str_replace('@', '', $data['via']);
        $query['picture'] 		= $data['picture'];

        if($this->config['share']['quotes']) {
            $query['text'] = '“' . $query['text'] .'”';
        }

        //Return it
        return $this->shareLink.'?'.http_build_query($query);
    }


    /**
     * Special Function to trim the title to fit with Twitter's logic
     */
    private function trimTweet($data) {
        //Our starter length for 24 points
        $suffix_length = $this->shortUrlLength;

        if(!empty($data['via'])) {
            //Make sure no @ symbol
            $data['via'] = str_replace('@','',$data['via']);

            //Theoretical text
            $viaText = ' via @'.$data['via'];

            //Length
            $suffix_length += strlen($viaText);
        }


        if(!empty($data['tags'])) {
            $tags = explode(',', $data['tags']);
            $tagsTidy = [];

            //Make sure that there's no spaces or hashes in the text
            foreach($tags as $tag) {
                $tagsTidy[] = str_replace(' ', '', str_replace('#', '', $tag));
            }            

            if(empty($tagsTidy)) {
                $data['tags'] = null;
            } else {
                //Update the tags field
                $data['tags'] = implode(',',$tagsTidy);

                //Simulate what it would be
                $tagsText = ' #'.implode(',#',$tagsTidy);

                //Length
                $suffix_length += strlen($tagsText);
            }

            
        }



        // $sig is handled by twitter in their 'via' argument.
        // $post_link is handled by twitter in their 'url' argument.
        if ( 140 < strlen( $data['title'] ) + $suffix_length ) {
            // The -1 is for "\xE2\x80\xA6", a UTF-8 ellipsis.
            $data['title'] = substr($data['title'], 0, 140 - $suffix_length - 1 ) . "\xE2\x80\xA6";
        } else {
            $data['title'] = $data['title'];
        }

        return $data;
    }
}