<?php
namespace SCRMHub\WordpressPlugin\Versioning;

use SCRMHub\WordpressPlugin\Versioning\Install;

class AutoUpdate extends Installer {
    private $app;
    private $slug; // plugin slug
    private $pluginData; // plugin data
    private $username; // GitHub username
    private $pluginFile; // __FILE__ of our plugin
    private $githubAPIResult; // holds data from GitHub

    private $bbOwner = 'scrmhub';
    private $bbRepo  = 'wordpress-plugin';
    private $bbBranch= 'Develop'; 

    private $BBAuth;
    private $BBSrc;

    function __construct($app) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        //Need this a few times
        $this->app = $app;

        add_filter( 'http_request_args', array(&$this, 'http_request_args' ), 10, 2 );   
        add_filter( "pre_set_site_transient_update_plugins", array(&$this, "check_update" ) );
        add_filter( "upgrader_post_install", array(&$this, "postInstall" ), 10, 3 );
        add_filter( "wp_update_plugins", array(&$this, "initPluginData" ), 10, 3 );
    }

    /**
     * Get information regarding our plugin from WordPress
     **/
    private function initPluginData() {
        //Make sure this only happens once
        if(!$this->pluginFile) {
            //Path to SCRM Hub
            $this->pluginFile       = SCRMHUB__PLUGIN_DIR.'scrmhub.php';

            // code here
            $this->slug         = plugin_basename( $this->pluginFile );
            $this->pluginData   = get_plugin_data( $this->pluginFile );

            //Useful
            $slugParts = explode('/',$this->slug);
            $this->pluginFolder       = $slugParts[0];
            $this->pluginFilename     = $slugParts[1];

            //
            $this->pluginActivated = is_plugin_active( $this->slug );

            //Get the user
            $this->initBBUser();
        }
    }

    private function initBBUser() {
        if(!$this->username) {
            //Get the username and password
            $this->username = $this->app->encrypto->decrypt(utf8_decode($this->app->values->getGlobalOption('scrmhub_bitbucket_user')));
            $this->password = $this->app->encrypto->decrypt(utf8_decode($this->app->values->getGlobalOption('scrmhub_bitbucket_pass')));
        }

        //Let it know that it returned
        return $this->username;
    }

    /**
     * Add in the bitbucket auth info
     */
    public function http_request_args($args, $url) {
        if (false === stristr( $url, 'bitbucket.org/scrmhub' ) ) {
            $this->initBBUser();
            $args['headers']['Authorization']   = 'Basic ' . base64_encode( $this->username . ':' . $this->password );
            $args['headers']['recursive']       = '1';

        }

        return $args;
    }

    /**
     * Get an instance of the repo and the user
     */
    private function getRepo() {
        if(!$this->BBSrc) {
            //Get the file info
            $this->BBSrc = new \Bitbucket\API\Repositories\Src();
            $this->BBSrc->setCredentials($this->BBAuth());
        }
        
        //Return the repo
        return $this->BBSrc;
    }

    private function BBAuth() {
        if(!$this->BBAuth) {
            //Get the user details
            $this->initBBUser();

            //Create a user Auth
            $this->BBAuth = new \Bitbucket\API\Authentication\Basic($this->username, $this->password);
        }        

        return $this->BBAuth;
    }
 
    /**
     * Get information regarding our plugin from GitHub
     *
     * @return null
     * @todo change to master 
     */
    private function getRepoReleaseInfo() {        
        //Return the file info
        $info = $this->getRepo()->get($this->bbOwner, $this->bbRepo, $this->bbBranch, $this->pluginFilename);

        //Decode the response
        $this->updateInfoRaw    = @json_decode($info->getContent());

        //Which commit is it?
        $this->updateNode       = $this->updateInfoRaw->node;

        //Pull out the headers
        $this->updateInfo = $this->getAllPluginHeaders($this->updateInfoRaw->data);

        //Return our info
        return $this->updateInfo;
    }

    /**
     * Generate the bitBucket link
     */
    private function getRepoDownloadLink() {
        $downloadLink = implode( '/', array( 'https://bitbucket.org', $this->bbOwner, $this->bbRepo, 'get', $this->bbBranch.'.zip' ) );

       return $downloadLink;
    }

    /**
     * Push in plugin version information to get the update notification
     *
     * @param  object $transient
     * @return object
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        echo '<p>Downloading files...</p>';
        

        $doUpdate = $this->doUpdate();

        //check if we're update
        if ($doUpdate > 0) {
            // Create the plugin info
            $obj = $this->createTransientObj();

            //Add it to the tranient object
            $transient->response[$this->slug] = $obj;
        }
 
        //And go back...
        return $transient;
    }

    /** 
     * Create the transient update object
     */
    private function createTransientObj() {
        $obj = new \stdClass();
        $obj->slug          = dirname($this->slug);
        $obj->plugin        = $this->slug;
        $obj->new_version   = $this->updateInfo->Version;
        $obj->url           = 'https://scrmhub.com';
        $obj->package       = $this->getRepoDownloadLink();
        $obj->node          = $this->updateNode;

        return $obj;
    }

    private function doUpdate() {
        // Get plugin & GitHub release information
        $this->initPluginData();

        //Get the Repo information
        $this->pluginInfo = $this->getRepoReleaseInfo();

        //check if we're update
        return version_compare( $this->updateInfo->Version, $this->pluginData['Version'] );
    }
 
    /**
     * Perform additional actions to successfully install our plugin
     *
     * @param  boolean $true
     * @param  string $hook_extra
     * @param  object $result
     * @return object
     */
    public function postInstall($true, $hook_extra, $result) {
        global $wp_filesystem;

        //Needs some bits
        $this->initPluginData();       

        // BitBucket files have the name reponame-tagname change it to our original one:
        $pluginFolder   = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'scrmhub/';

        echo '<p>Moving new version...</p>';
        

        //Move it
        $wp_filesystem->move( $result['destination'], $pluginFolder );

        echo '<p>Downloading extensions...</p>';        

        //Download anything extra
        $this->downloadAdditonalParts();

        echo '<p>Updating database...</p>';
        

        //Run any updates
        $this->app->installer->autoUpdate();

        // Re-activate plugin if needed
        if ($this->pluginActivated) {
            echo '<p>Reactivating plugin...</p>';
            
            $activate = activate_plugin( $this->slug );
        }
        
 
        //done...
        return $result;
    }


    /**
     * Parse the file and get the headers back
     * Crude, horrible but it works
     *
     * @param string $contents  The download file
     * @return array
     */
    protected function getAllPluginHeaders( $response) {
        //The list we want to get - Should match Wordpress
        $plugin_headers = array(
            'Name'        => 'Plugin Name',
            'PluginURI'   => 'Plugin URI',
            'Version'     => 'Version',
            'Description' => 'Description',
            'Author'      => 'Author',
            'AuthorURI'   => 'Author URI',
            'TextDomain'  => 'Text Domain',
            'DomainPath'  => 'Domain Path',
            'Network'     => 'Network',
        );

        /*
         * Make sure we catch CR-only line endings.
         */
        $file_data = str_replace( "\r", "\n", $response );

        //Loop through each header
        foreach ( $plugin_headers as $field => $regex ) {
            if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
                //Clean it up using Wordpress cleaning function
                $plugin_headers[ $field ] = _cleanup_header_comment( $match[1] );
            } else {
                //Else blank it
                $plugin_headers[ $field ] = '';
            }
        }

        //Reutn what we got
        return (object)$plugin_headers;
    }

    private function downloadAdditonalParts() {
        $this->initPluginData();

        $parts = array(
            array(
                'source'    => 'https://bitbucket.org/scrmhub/api-php-sdk/get/master.zip',
                'target'    => SCRMHUB__PLUGIN_DIR.'vendor/scrmhub/api-php-sdk/'
            )
        );

        $this->updatePath = SCRMHUB__PLUGIN_DIR.'updates/';

        //Make sure the folder exists - PHP only access
        @mkdir($this->updatePath, 0644);

        //Loop and download them
        foreach($parts as $additonal) {
            $this->downloadPart($additonal['source'], $additonal['target']);
        }

        //remove updates folder        
        $this->deleteDir($this->updatePath);
    }


    private function downloadPart($source, $target) {
        global $wp_filesystem;

        $zipFile    = $this->updatePath.'additionalPart.zip';

        //Open a file for writing
        $fp = fopen($zipFile, "w");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        //$ch_info        = curl_getinfo($ch);
        $status_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        $result         = curl_exec($ch);
        curl_close ($ch);

        //Extract it
        $zip = new \ZipArchive;

        //Check it can be opened
        if($zip->open($zipFile ) != "true"){
            echo "Error :- Unable to open the Zip File";
            die();
        } else {
            /* Extract Zip File */
            $zip->extractTo($this->updatePath);

            $folder = explode('/', $zip->getNameIndex(1));
        }

        //Tidyp
        $zip->close();
        @unlink($zipFile);

        //remove any old files
        $this->deleteDir($target);

        //Move the extracted file
        rename($this->updatePath.$folder[0].'/', $target);
    }

    private function deleteDir($dir) { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($dir."/".$object)) {
                       $this->deleteDir($dir."/".$object);
                    } else {
                       @unlink($dir."/".$object); 
                    }
                } 
            }
            @rmdir($dir); 
        } 
    }
}
