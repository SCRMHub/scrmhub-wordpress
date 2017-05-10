<?php
namespace SCRMHub\WordpressPlugin\Networks;

use SCRMHub\WordpressPlugin\NetworkCore\Pinterest as PinterestCore;

class Pinterest extends PinterestCore {
	public function buildInternalShareUrl($post) {
		$args = [
			'scrmhub_action' 	=> 'share',
			'network'			=> $this->getName(),
			'postid'			=> $post->ID
		];

		return get_home_url().'?'.http_build_query($args);
	}
}