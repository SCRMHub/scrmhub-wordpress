<?php
return [
	'scrmhub_appid' 				=> null,
	'scrmhub_secret' 				=> null,
	'scrmhub_site_appkey' 			=> null,
	'scrmhub_autoupdate' 			=> true,
	'scrmhub_fulluninstall'			=> false,
	'scrmhub_site_activity_options' => [
		'enabled'	=> true
	],
	'scrmhub_site_connect_options' 	=> [
		'enabled' => false,
		'options' => array(
			'redirect' 			=> 'admin',
			'redirecturl' 		=> null,
			'loginform'			=> true,
			'user_photo'		=> true,
			'commentconnect'	=> false,
			'icononly'			=> false
		),
		'networks' 	=> array()
	],
	'scrmhub_site_sharing_options' 	=> [		
		'enabled' 	=> false,
		'options' 	=> array(
			'position' => 'bottom',
			'types' 	=> array(),
			'login'		=> false,
			'icononly'	=> false,
			'types'		=> array(
				'page', 'post'
			)
		),		
		'networks' 	=> array(
			'twitter' => array(
				'enabled' 	=> false,
				'via'		=> null,
				'quotes'	=> true
			)
		)
	]
];