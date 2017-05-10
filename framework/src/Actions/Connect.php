<?php
namespace SCRMHub\WordpressPlugin\Actions;

use SCRMHub\WordpressPlugin\Actions\_BaseAction;
use SCRMHub\SDK\API\Identity;

use Exception;
use SCRMHub\WordpressPlugin\Service\SCRMHubError;

use SCRMHub\WordpressPlugin\Templates\Site\ConnectComplete as Template;

class connect extends _BaseAction {
    /** 
     * Run the process
     */
    public function run($action) {
        //Disable Wordpress caching
        define( 'DONOTCACHEPAGE', 1 );
        nocache_headers();
        
        //Get the network - will fail if it's bad
        $network = $this->getNetwork();


        if(!$network) {
            throw new Exception('Invalid network');
            die();
        }

        //Share actions
        switch($action) {
            case 'start':
                $share = $this->start();
                break;

            case 'finish':
                $this->finish();
                break;          

            default:
                throw new \Exception('Incorrect action supplied');
                exit;
        }
    }

    /**
     * Log a user out
     */
    public function logout($redirect = true) {
        if(isset($_SERVER['HTTP_REFERER'])) {
            $returnUrl = $_SERVER['HTTP_REFERER'];
        } else {
            $returnUrl = home_url();
        }

        //Run any actions
        do_action('wp_logout');

        //Wordpress' logout call
        wp_logout();

        //Kill the session
        $this->app()->session->destroy();

        //Back home we go
        if($redirect) {
           wp_redirect($returnUrl); 
        }        
    }

    /**
     * Start the share process
     */
    private function start() {
        global $post;

        
        //Start by making sure they aren't logged in (amazing how many problems this causes)
        wp_clear_auth_cookie();

        //Arguments to be sent
        $requestArgs = [
            'action'        => 'connect',
            'appkey'        => $this->app()->values->getAppKey(),
            'callback'      => $this->buildCallbackUrl(),
            'network'       => $this->network,
            'permissions'   => null,
            'puuid'         => $this->app()->person->getPuuid(true),
            '_t'            => (new \DateTime())->format('YmdHis')
        ];
        
        //Build the login url
        $loginUrl = $this->app->settings['api'].'identity/?'.http_build_query($requestArgs,null,'&');

        //redirect
        wp_redirect($loginUrl);
    }


    /**
     * Handle a completed connect
     */
    private function finish() {
        $connectData    = $_GET;
        $csrfToken      = $_GET['scrmhub_nonce'];

        try {
            //Random API Error
            if(isset($_GET['error_message'])) {
                throw new SCRMHubError('scrmhub_api_error', array('api' => 'api/identity/', 'message' => $_GET['error_message']));
            }

            //Does the CSRF Token pass?
            $csrfToken = $this->CSRFTokenCheck();

            //Get the access token
            $accessToken = $this->tokenExchange($csrfToken, isset($_GET['referrer']) ? $_GET['referrer'] : null );

            //Get their profile
            $profile = $this->app()->person->loadProfileFromToken($accessToken, $this->app()->person->getPuuid());

            //Create the Wordpress user
            $this->app()->person->create($profile, $this->network);

            //Set the nice response by default
            $code       = 200;
            $success    = true;
            $message    = 'Connect complete';
            
        } catch(SCRMHubError $error) {
            $message    = $error->getMessage();
            $code       = $error->getCode();
            $success    = false;
        }      
        
        //Response
        $connectData = array(
            'network'       => $this->networkClass->getName(),
            'networkLabel'  => $this->networkClass->getLabel(),
            'message'       => $message,
            'uuid'          => $this->app()->person->getPuuid(),
            'response'      => array(
                'success'               => $success,
                'redirect'              => false,
                'message'               => $message,
                'parent_function'       => null,
                'disableparentrefresh'  => filter_var($this->app()->get('connect_options.disableparentrefresh'), FILTER_VALIDATE_BOOLEAN)
            )
        );

        //Call a parent function
        if(isset($_GET['parent_function']) && !empty($_GET['parent_function'])) {
            $connectData['response']['parent_function'] = esc_js($_GET['parent_function']);
        }

        // Any redirects specified?
        if($success && isset($_GET['redirect']) && !empty($_GET['redirect'])) {
            $connectData['response']['redirect'] = esc_js($_GET['redirect']);

        //Use the default
        } else {
            $connectData['response']['redirect'] = esc_js($this->app->values->getLoginRedirect());
        }

        if(isset($_GET['referrer']) && !empty($_GET['referrer'])) {
            //redirect to the referrer
            $connectData['response']['referrer'] = $_GET['referrer'];
        }

        //Render the finished page
        $template = new Template($connectData);
        echo $template->setResponseCode($code)->render($connectData);
        exit;
    }

    /**
     * Exchange the token
     * @param string $csrfToken     The callback token used to verify the request
     */
    private function tokenExchange($csrfToken, $referrer) {
        if(!isset($_GET['code']))
            return false;

        //Request Arguments
        $data = [
            'code'      => $_GET['code'], //The code returned
            'callback'  => $this->buildCallbackUrl($csrfToken, $referrer) //This is attached for security on the token end
        ];

        //New identity class
        $api            = new Identity();
        $response       = $api->connectexchange($data);
        $accessToken    = $response->getResult();

        //Got a good response
        if(!empty($accessToken)) {
            return $accessToken;
        } else {
            scrmhub_error('scrmhub_connect_tokenexchange', array('response' => $response->getAll()));
            throw new SCRMHubError('scrmhub_connect_tokenexchange', array('response' => $response->getAll()));
        }
        return false;
    }

    /**
     * Create the token name to validate with
     *
     * @return string The Token name
     */
    private function CSRFTokenName() {
        //Return the token name
        return 'Connect_'.$this->network;
    }

    /** 
     * Generate a token
     */
    private function CSRFTokenCheck() {
        global $scrmhub_session;
        $tokenName = $this->CSRFTokenName();

        //Get the current value
        $tokenValue= $scrmhub_session->$tokenName;

        //Make sure it doesn't get re-used
        if(!WP_DEBUG) {
            $scrmhub_session->delete($tokenName);   
        }

        //Check Nonce
        if ( 
            $tokenValue
            && isset($_GET['scrmhub_nonce'])
            && ($_GET['scrmhub_nonce'] === $tokenValue) 
        ) {
            return $tokenValue;
        }
        scrmhub_error('error_csrf_001', array('GET' => $_GET));
        throw new SCRMHubError('error_csrf_001', array('GET' => $_GET));
        return false;
    }

    /**
     * Create a token
     */
    private function CSRFToken() {
        global $scrmhub_session;

        if (function_exists('mcrypt_create_iv')) {
            $tokenValue = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        } else {
            $tokenValue = bin2hex(openssl_random_pseudo_bytes(32));
        }

        //Session
        $tokenName = $this->CSRFTokenName();
        $scrmhub_session->set($tokenName, $tokenValue);

        return $tokenValue;
    }

    /**
     * Build a callback URL
     */
    private function buildCallbackUrl($csrfToken = null, $referrer = null) {
        global $post;

        //Fresh request so make the csrf
        if(!$csrfToken)
            $csrfToken = $this->CSRFToken();

        if (!$referrer && isset($_GET['referrer']))
            $referrer = $_GET['referrer'];

        //Arguments for the request
        $callbackArgs = [
            'scrmhub_action'    => 'connect_callback',
            'network'           => $this->network,
            'scrmhub_nonce'     => $csrfToken,
            'referrer'          => $referrer
        ];

        if(isset($_GET['parent_function'])) {
            $callbackArgs['parent_function'] = $_GET['parent_function'];
        } else if(isset($_GET['parent_redirect'])) {
            $callbackArgs['parent_redirect'] = $_GET['parent_redirect'];
        }

        //Build it and send it back
        return get_bloginfo('wpurl').'?'.http_build_query($callbackArgs); 
    }
}