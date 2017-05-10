<?php
if(!defined('ENVIRONMENT')) {
	define('ENVIRONMENT', 'live');
}

//The base application settings
$settings = [
	'version'	=> SCRMHUB__VERSION,
	'api' 		=> 'https://api.scrmhub.com/',
	'shorturl'	=> 'https://u.scrmhub.com/',
	'sharerelay'=> 'https://app.scrmhub.com/wprelay/',
	'logger' => [
        'name' => 'scrmhub-wpplugin-'.SCRMHUB__VERSION,
        'path' => SCRMHUB__PLUGIN_DIR.'logs/'
    ],
    'paths' => [
    	'logs'	=> SCRMHUB__PLUGIN_DIR.'logs'
    ]
];



//Custom environment overrides if you need to test
switch(ENVIRONMENT) {
	case 'dev':
		$devsettings = [
			'api' 		=> 'https://api-sit.scrmhub.com/',
			'shorturl'	=> 'https://u-sit.scrmhub.com/',
			'sharerelay'=> 'https://app.socialcrm.dev/wprelay/',
			'logger' => [
		        'name' => 'scrmhub-wpplugin-dev'.SCRMHUB__VERSION,
        		'path' => SCRMHUB__PLUGIN_DIR.'logs/scrmhub-wpplugin-dev-'.SCRMHUB__VERSION.'-'
		    ]
		];

		$settings = (new SCRMHub\Framework\Utility\ArrayHelper())->mergeRecursively($settings, $devsettings);
		break;

	case 'sit':
		$devsettings = [
			'api' 		=> 'https://api-sit.scrmhub.com/',
			'shorturl'	=> 'https://u-sit.scrmhub.com/',
			'sharerelay'=> 'https://app-sit.scrmhub.com/wprelay/',
			'logger' => [
		        'name' => 'scrmhub-wpplugin-dev'.SCRMHUB__VERSION,
        		'path' => SCRMHUB__PLUGIN_DIR.'logs/scrmhub-wpplugin-dev-'.SCRMHUB__VERSION.'-'
		    ]
		];

		$settings = (new SCRMHub\Framework\Utility\ArrayHelper())->mergeRecursively($settings, $devsettings);
		break;

	default:
}

return $settings;