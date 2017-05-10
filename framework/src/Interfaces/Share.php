<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

use SCRMHub\WordpressPlugin\Interfaces\_BaseInterface;
use SCRMHub\WordpressPlugin\Interfaces\Connect;
use SCRMHub\WordpressPlugin\Templates\Site\ActionWrapper;

use SCRMHub\WordpressPlugin\Service\SCRMHubError;

class Share extends _BaseInterface {
	private static $isValid = null;

	/**
	 * Render a single button
	 * @param array $args The values to use : network, label, class (optional)
	 */
	public static function button($args = array()) {
		if(!is_array($args)) {
			$args = array('network' => $args);
		}

		if($networkClass = self::app()->networks->get($args['network'])) {
			if($networkClass->canShare()) {
				echo self::render_button($args);
				return;
			}
		}

		die('Sharing is disabled for the network '.$args['network'].'. Please check you settings and ensure you have enabled it.');
	}


	/**
	 * Build a single network share button
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	private static function render_button($args = array()) {
		if(!isset($args['network'])) {
			return false;
		} 

		if($shareUrl = self::shareUrl($args['network'])) {
			if(!isset($args['label'])) {
				$args['label'] = __( $args['network'], 'scrmhub_label_'.$args['network'] );
			}

			$class = 'scrmhub-button scrmhub-button-share scrmhub-' . $args['network'];

			if(isset($args['class'])) {
				$class .= ' '.$args['class'];
			}

			if((bool)self::app()->get('sharing_options.icononly') == true) {
	        	$class .= ' scrmhub-icononly';
	        }

			return '<a class="'.$class.'" href="'.$shareUrl.'" target="_blank" rel="nofollow"><i class="fi-social-'.$args['network'].'"></i><span class="text">'
                . $args['label']
                . '</span></a>';
		}
		return false;
	}

	/**
	 * Output a render share block
	 * This is for use with the do_action call
	 * @param  array  $args Networks
	 */
	public static function render_share($args = array()) {
		echo self::render($args);
	}

	/** 
	 * The render version
	 * This will return the share item
	 */
	public static function render($args = array(), $position = null) {

		if(!self::isValidShareType())
			return;

		if((bool)self::app()->get('sharing_options.login') && !is_user_logged_in()) {
			if(isset($args['title_login'])) {
				$args['title'] = $args['title_login'];
			} else {
				$args['title'] = __('Connect to share', 'scrmhub');
			}

    		return Connect::login($args);
    	
    	} else {
	        return self::renderWrapper($args, $position);
	    }
	}

	/** 
	 * Prepare the wrapper
	 * Put the wrapper round the button
	 */
	private static function renderWrapper($args = array(), $position = null) {
        $buttons = array();

        $options = self::app()->get('sharing_options');

		//Check in case it hasn't been setup
        if(is_array(self::app()->sharing_networks)) {

        	foreach(self::app()->sharing_networks as $network) {
	        	if($network->canShare()) {
		            $args = array(
		            		'network' 		=> $network->getName(),
		            		'label'			=> __( $network->getName(), 'scrmhub')
		            	);

		            $buttons[] = self::render_button($args);
		        }
	        }
        }       

        if(empty($buttons))
            return null;

        //Set the title
        $title = isset($args['title']) ? $args['title'] : __('Share', 'scrmhub_site_action_share');
        $class = 'scrmhub-share'.(isset($args['class']) ? ' '.$args['class'] : null);

        if($position) {
        	$class .= ' scrmhub-share-'.$position;
        }

        return (new ActionWrapper())->render(array('buttons' => $buttons, 'title' => $title, 'class' => $class));
	}

	/**
	 * Returns a share url
	 * @param  [type] $network [description]
	 * @return [type]          [description]
	 */
	public static function getShareUrl($network) {
		return self::shareUrl($network);
	}

	/**
	 * Output the share url into a template
	 * @param  [type] $network [description]
	 * @return [type]          [description]
	 */
	public static function doShareUrl($network) {
		echo self::shareUrl($network);
	}

	/**
	 * Build the share url
	 */
	private static function shareUrl($network) {
		if(!self::isValidShareType())
			return;

        if($network = self::app()->networks->get($network)) {
        	global $post;

        	$url = $network->buildInternalShareUrl($post);
        	return $url;
        }

        return false;
	}

	/**
	 * Check that that a share type is valid
	 * @return boolean Was it ok
	 */
	private static function isValidShareType() {
		if(self::$isValid === null) {
			$validTypes = (array)self::app()->sharing_options['types'];

			if(empty($validTypes)) {
				self::$isValid = true;
			} else if(in_array(get_post_type(), $validTypes)) {
				self::$isValid = true;
			} else {
				self::$isValid = false;
			}
		}
		
		return self::$isValid;
	}
}