<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

use SCRMHub\WordpressPlugin\Interfaces\_BaseInterface;
use SCRMHub\WordpressPlugin\Templates\Site\Header as Template;

class Header extends _BaseInterface {
	/**
	 * The shortcode version
	 * This is what we render into the Wordpress header
	 * It is namespaced into window.scrmhub
	 */
	public static function render($atts) {
		global $post;

		$values = [
			'isbot'				=> null,
			'appkey' 			=> self::app()->values->getAppKey(),
			'analytics'			=> self::app()->activity_options,
			'pagePathUrl'		=> $_SERVER['REQUEST_URI'],
			'apiUrl'			=> self::app()->settings['api'],
			'ajaxurl'			=> admin_url('admin-ajax.php'),
			'identifier'		=> isset($post->ID) ? $post->ID : null,
			'tokenTwo'			=> null
		];
		
		echo (new Template())->render($values);
	}
}