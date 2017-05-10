<?php
namespace SCRMHub\WordpressPlugin\Networks;

use SCRMHub\WordpressPlugin\NetworkCore\Linkedin as LinkedinCore;

class Linkedin extends LinkedinCore {
	public function buildInternalShareUrl($post) {
		$args = [
			'scrmhub_action' 	=> 'share',
			'network'			=> $this->getName(),
			'postid'					=> $post->ID
		];

		return get_home_url().'?'.http_build_query($args);
	}
}