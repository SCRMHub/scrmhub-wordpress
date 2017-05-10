<?php
namespace SCRMHub\WordpressPlugin\Identity;

use SCRMHub\Framework\Utility\App;

class UserPhoto {
	private
		$app;

	function __construct(App $app) {
		$this->app = $app;
	}

	/**
	 * Add the filter
	 */
	function setup() {
		add_filter('get_avatar', array(&$this, 'user_photo'), 10, 5 );
	}

	/**
	 * Get a user's photo
	 */
	public function user_photo($avatar, $id_or_email, $size, $default, $alt) {
		$user = false;

	    if ( is_numeric( $id_or_email ) ) {
	        $id = (int) $id_or_email;
	        $user = get_user_by( 'id' , $id );

	    } elseif ( is_object( $id_or_email ) ) {
	        if ( ! empty( $id_or_email->user_id ) ) {
	            $id = (int) $id_or_email->user_id;
	            $user = get_user_by( 'id' , $id );
	        }

	    } else {
	        $user = get_user_by( 'email', $id_or_email );	
	    }

	    if ( $user && is_object( $user ) ) {
            $avatar_img = get_user_meta($id, 'scrmhub_profile_pic_large', true);

            if(empty($avatar_img)) {
            	$avatar_img = 'https://d9zhq0a7rlngk.cloudfront.net/wp-content/uploads/2016/09/activation-logo-1000x1000-125x125.png';
            }

           	$avatar = '<img alt="'.$alt.'" src="'.$avatar_img.'" class="avatar avatar-'.$size.' photo" height="'.$size.'" width="'.$size.'" />';
	    }

    	return $avatar;
	}
}