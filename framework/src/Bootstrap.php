<?php
namespace SCRMHub\WordpressPlugin;

use SCRMHub\WordpressPlugin\Versioning\Installer;

use SCRMHub\WordpressPlugin\Cron\Core as Cron;
use SCRMHub\WordpressPlugin\Bootstrap\Admin;
use SCRMHub\WordpressPlugin\Bootstrap\Website;

use SCRMHub\WordpressPlugin\Service\SCRMHubNetworks;
use SCRMHub\WordpressPlugin\Service\SCRMHubValues;
use SCRMHub\WordpressPlugin\Service\SCRMHubShortUrls;

use SCRMHub\WordpressPlugin\Identity\Session;

use SCRMHub\WordpressPlugin\Interfaces\WordpressWebsiteFunctions;

use SCRMHub\Framework\Utility\App;
use SCRMHub\Framework\Utility\EncryptDecrypt;

use SCRMHub\SDK\API;

use DeviceDetector\DeviceDetector;
use Doctrine\Common\Cache\PhpFileCache;

class Bootstrap {
	//Container object
	private $app;

	//Setup the class
	function __construct() {	
		//Create the App Object
		$app = new App();

		//Get settings
		$app->settings = $settings = require_once('Config/Settings.php');

		//Install the thing
		$app->installer = function() {
			return new \SCRMHub\WordpressPlugin\Versioning\Installer();
		};

		if(isset($_GET['scrmhub_updatedb'])) {
			$app->installer->doInstallUpdate(true);
		}

		//Setup the encryption class
		$encrypt_key 	= SECURE_AUTH_KEY;
		$app->encrypto = function() use($app, $encrypt_key) {
			return new \SCRMHub\Framework\Utility\EncryptDecrypt($app, $encrypt_key);
		};

		//Services
		$app->networks 	= function() use ($app) {
			return new \SCRMHub\WordpressPlugin\Service\SCRMHubNetworks($app);
		};
		$app->values 		= function() use ($app) {
			return new \SCRMHub\WordpressPlugin\Service\SCRMHubValues($app);
		};

		$this->app = $app;
		
		//Global multi site flag
		$app->multisite = is_multisite();
		
		//Setup the session
		$app->session 	= function() use ($app) {
			return new \SCRMHub\WordpressPlugin\Identity\Session($app);
		};

		//Set the global object
		$GLOBALS['scrmhub'] 		= $app;
		$GLOBALS['scrmhub_session'] = $app->session;

		//Add cookie and person classes as lazy loaders
		$app->cookie 		= function() use($app) {
			return new \SCRMHub\WordpressPlugin\Identity\Cookie($app);
		};
		$app->person 		= function() use($app) {
			return new \SCRMHub\WordpressPlugin\Identity\Person($app);
		};

		//Cron manager
		$app->cron 		= function() use($app) {
			return new \SCRMHub\WordpressPlugin\Cron\Core($app);
		};

		//Setup the session
		$app->shorturls 	= function() use ($app) {
			return new \SCRMHub\WordpressPlugin\Service\SCRMHubShortUrls($app);
		};

		$app->user_photo 		= function() use($app) {
			return new \SCRMHub\WordpressPlugin\Identity\UserPhoto($app);
		};

		/**
		 * Device details library
		 */
		$app->device = function() use($settings) {
		    $dd = new DeviceDetector(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		    $dd->setCache(new PhpFileCache(SCRMHUB__PLUGIN_DIR.'/cache/devices/'));
		    $dd->parse();

		    return $dd;
		};

		//Sentry Logger
		$app->logger_raven = function () {
		    //Setup Sentry
		    $dsn = 'https://f986ee9d802e49519c95b0d0ef9260d9:17f7842190f1460da57ed3832d26804a@sentry.io/112441';
		    $client = new \Raven_Client($dsn,
		        [
		            'release'       => SCRMHUB__VERSION,
		            'processorOptions' => array(
		                'Raven_SanitizeDataProcessor' => array(
		                    'fields_re' => '/(_scrmhub|scj|access_token|authorization|password|passwd|secret|password_confirmation|card_number|auth_pw|scrmhub|tokentwo|uuid|puuid)/i',
		                )
		            )
		        ]);
		    return new \Monolog\Handler\RavenHandler($client, \Monolog\Logger::ERROR);
		};

		//Monolog for our errors because Wordpress..
		$app->logger = function() use($app, $settings) {
		    $logger = new \Monolog\Logger($settings['logger']['name']);
		    $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		    $logger->pushProcessor(new \Monolog\Processor\PsrLogMessageProcessor());

		    //Add in raven
		    if((!defined('environment') || environment != 'dev') && !$app->device->isBot()) {
		        $logger->pushHandler($app->logger_raven);
		    }

		    //3 days of logs only
		    $stream = new \Monolog\Handler\RotatingFileHandler($settings['logger']['path'].'error.log', \Monolog\Logger::WARNING, 3);
		    $formatter = new \Monolog\Formatter\LineFormatter(null, null, true);
		    $stream->setFormatter($formatter);
		    $logger->pushHandler($stream);

		    //Return the logger
		    return $logger;
		};

		//Other bits to prepare
		if ( is_admin() ) {
			$app->scrmhub = new Admin($app);
		} else {
			$app->scrmhub = new Website($app);
		}

		//Load the assets
		$this->addActions();
		$this->addFilters();

		//Send the App Back
		return $app;
	}

	public function instance() {
		return $this->app;
	}

	private function initialiseApi() {
		// initialise SCRMHub API
        $apiCore = API::init(array(
            'appkey' 	  => $this->app->values->getAppKey(),
            'url'	 	  => $this->app->settings['api']
        ));
        $apiCore->set('appsecret', $this->app->values->getAppSecret());
        $apiCore->setOverrideUrl($this->app->settings['api']);
        $this->app->apiCore = $apiCore;
	}

	private function addActions() {
		if(function_exists('list_plugins')) {
			//Network admin action
			add_action( 
	            'network_admin_plugin_action_links', 
	            array( $this, 'list_plugins' ), 
	            10, 4 
	        );
		}

		add_action('init', array($this, 'init'));

        //CSS
		add_action(
            'wp_enqueue_scripts',
            array( $this, 'loadAssets')
        );	
		add_action(
			'admin_enqueue_scripts',
			array( $this, 'loadAssets')
		);
		add_action(
			'login_enqueue_scripts',
			array( $this, 'loadAssets')
		);
	}

	private function addFilters() {
		//Our link shortener
		//add_filter( 'pre_get_shortlink', array('\SCRMHub\WordpressPlugin\Interfaces\Shortener', 'get'), 1, 4 );
	}

	public function init() {
		//Wordpress Website Functions
		new WordpressWebsiteFunctions($this->app);
		//Load translations
		//load_plugin_textdomain('scrmhub', FALSE, SCRMHUB__PLUGIN_DIR.'languages/');

		//Setup API
		$this->initialiseApi();
	}

	public function loadAssets() {
		if ( is_admin() ) {
        	wp_enqueue_style( 
	            'scrmhub-admin-css', 
	            SCRMHUB__PLUGIN_ASSETS . 'css/scrmhub-admin.css'
	        );

	        wp_enqueue_script( 
	            'scrmhub-admin-js', 
	            SCRMHUB__PLUGIN_ASSETS . 'script/scrmhub.admin-min.js', 
	            array(), 
	            false, 
	            true 
	        );
        } else {
        	wp_enqueue_script( 
	            'scrmhub-js', 
	            SCRMHUB__PLUGIN_ASSETS . 'script/scrmhub-min.js', 
	            array('jquery'), 
	            false, 
	            true 
	        );
	        	
        	wp_enqueue_style( 
	            'foundation-icons', 
	            'https://cdn.jsdelivr.net/foundation-icons/3.0/foundation-icons.min.css'
	        );
	        wp_enqueue_style( 
	            'scrmhub-css', 
	            SCRMHUB__PLUGIN_ASSETS . 'css/scrmhub.css'
	        );
        }
	}
}