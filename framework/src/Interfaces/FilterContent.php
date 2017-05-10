<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

use SCRMHub\WordpressPlugin\Interfaces\_BaseInterface;

class FilterContent extends _BaseInterface {
	/** 
	 * Render the content
	 * @todo Add in the page / post type check
	 */
	public static function render($content) {
		global $atts, $post, $wp_current_filter, $wp;

		//Only show in a post / page
		if (!is_singular() == 1)
			return $content;

        //Front page, no
        if (is_front_page() == 1)
        	return $content;

        //RSS Feed
        if (is_feed())
            return $content;

		if($sharingStyle = (string)self::app()->sharing_options['position']) {
			switch($sharingStyle) {
				case 'manual':
					//nothing but just in case
					break;


				case 'both';
					$shareInterface = new \SCRMHub\WordpressPlugin\Interfaces\Share;
					$content = $shareInterface->render($atts, 'top') . $content . $shareInterface->render($atts, "bottom");
					break;


				case 'top';
					$content = (new \SCRMHub\WordpressPlugin\Interfaces\Share)->render($atts, 'top') . $content;
					break;

				case 'bottom';
					$content.= (new \SCRMHub\WordpressPlugin\Interfaces\Share)->render($atts, "bottom");
					break;
			}
		}

		return $content;
	}


	
}