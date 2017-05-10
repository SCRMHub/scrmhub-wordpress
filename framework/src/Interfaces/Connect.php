<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

use SCRMHub\WordpressPlugin\Interfaces\_BaseInterface;
use SCRMHub\WordpressPlugin\Templates\Site\ActionWrapper;
use SCRMHub\Framework\Utility\UrlHelper;

class Connect extends _BaseInterface {
	private static $currentUrl;

	/**
	 * Render a single button
	 * @param array $args The values to use : network, label, class (optional)
	 */
	public static function button($args = array()) {
		if(!is_array($args)) {
			$args = array('network' => $args);
		}

		if($networkClass = self::app()->networks->get($args['network'])) {
			if($networkClass->canConnect()) {
				echo self::render_button($args);
                
                //Stop the next bit running
				return;
			}
		}

		die('Connect is disabled for the network '.$args['network'].'. Please check your admin settings and ensure you have enabled it.');
	}

	/**
	 * Render a single log in button
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	private static function render_button($args = array()) {
		if(!isset($args['label'])) {
			$args['label'] = __( $args['network'], 'scrmhub_label_'.$args['network'] );
		}

		$class = 'scrmhub-button scrmhub-button-connect scrmhub-'.$args['network'];
		if(isset($args['button_class'])) {
			$class .= ' '.$args['button_class'];
		}
		if((bool)self::app()->get('connect_options.icononly') == true) {
        	$class .= ' scrmhub-icononly';
        }

		return '<a class="'.$class.'" href="'.self::loginUrl($args).'" target="_blank" rel="nofollow">
				<i class="fi-social-'.$args['network'].'"></i>
				<span class="text">'
                . $args['button_label']
                . '</span></a>';
	}

	/**
	 * Automatically show log in or log out and all networks
	 */
	public static function auto($args = array()) {
		if (is_user_logged_in() ) {
			self::logout($args);
		} else {
			self::login($args);
		}
	}

	/**
	 * Render our login function
	 */
	public static function login($args = array()) {
		echo self::render($args);
	}

	/**
	 * Render our login function
	 */
	public static function login_admin($args = array()) {
		if(!is_array($args)) {
			if(!empty($args)) {
				$args = (array)$args;
			} else {
				$args = [];
			}
			
		}

		//$args['parent_function'] = 'scrmhub_login_callback';
		//$args['parent_redirect'] = '/wp-admin/';

		echo self::render($args);
	}

	/**
	 * Render our login function
	 */
	public static function login_comments($args) {
		return self::render($args);
	}

	/**
	 * Render an AJAX Connect panel
	 */
	public static function ajaxpanel($args) {
		$login = self::render($args);
		$logout= self::renderLogout($args);

		echo '<div class="scrmhub-connect-ajax-panel">
		<div class="scrmhub-connected hidden">'.$logout.'</div>
		<div class="scrmhub-loggedout hidden">'.$login.'</div>
		</div>';
	}

	/**
	 * Logout of Wordpress
	 */
	public static function logout($args = array()) {
		echo self::renderLogout($args );
	}

	/**
	 * Render the logout button
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	private static function renderLogout($args) {
		$logout_url = '/?scrmhub_action=logout';

		$class = 'scrmhub-button scrmhub-logout';

		if(isset($args['button_class'])) {
			$class .= ' '.$args['class'];
		}

		if((bool)self::app()->get('sharing_options.icononly') == true) {
        	$class .= ' scrmhub-icononly';
        }

		$buttons = array(
			'<a class="'.$class.'" href="'.$logout_url.'" rel="nofollow"><i class="fi-minus-circle"></i><span class="text">'
            . __('Logout', 'scrmhub_label_logout')
            . '</span></a>'
        );

        $class = 'scrmhub_connect scrmhub_connect_logout '.(isset($args['class']) ? $args['class'] : null);

        return (new ActionWrapper())->render(array('buttons' => $buttons, 'title' => null, 'class' => $class));
	}





	/** 
	 * The render version
	 */
	private static function render($args = array()) {
        $buttons = array();

        $networks = self::app()->connect_networks;

        if(!empty($args) && isset($args['networks'])) {
        	$renderNetworks = array();
        	foreach($args['networks'] as $network) {
        		if(isset($networks[$network]))
        			$renderNetworks[$network] = $networks[$network];
        	}

        	$networks = $renderNetworks;
        }

        //Loop through the buttons
        foreach($networks as $network) {
        	if($network->canConnect()) {
        		$networkArgs 					= $args;
        		$networkArgs['network'] 		= $network->getName();
        		$networkArgs['button_label']	= __($network->getName(), 'scrmhub' );

        		//Render the button
        		$buttons[] = self::render_button($networkArgs);
        	}                
        }

        if(empty($buttons))
            return null;

        //Set the title
        $title = isset($args['title']) ? $args['title'] : __('Connect', 'scrmhub_site_action_connect');
        $class = 'scrmhub_connect '.(isset($args['class']) ? $args['class'] : null);

        if((bool)self::app()->get('connect_options.icononly') == true) {
        	$class .= ' scrmhub-icononly';
        }

		return (new ActionWrapper())->render(array('buttons' => $buttons, 'title' => $title, 'class' => $class));
	}

	/**
	 * Get the connect url for a network
	 */
	public static function loginUrl($args) {
		//Single value
		if(is_string($args)) {
			$args = ['network' => $args];
		}

		$params = array('scrmhub_action' => 'connect');

		if(is_array($args) && isset($args['network']) && !empty($args['network'])) {
			$params['network'] 			= $args['network'];
			$params['parent_function']	= (isset($args['parent_function']) ? $args['parent_function'] : null);
			$params['parent_redirect']	= (isset($args['parent_redirect']) ? $args['parent_redirect'] : null);
			$params['redirect']			= (isset($args['redirect']) ? $args['redirect'] : null);
		} else {
			return false;
		}
		
		return get_home_url().'?'.http_build_query($params);
	}
}