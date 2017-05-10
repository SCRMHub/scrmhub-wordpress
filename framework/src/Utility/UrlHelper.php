<?php
namespace SCRMHub\Framework\Utility;

/*
Example
$url = new SCRMHub\WordpressPlugin\Utility\UrlHelper('https://scrmhub.com/admin/index.php?foo=bar');
var_dump($url->getEverything());
die();
*/

/**
 * URL Helper for working with a url
 *
 * It will deconstruct and build URLs for you
 *
 * @author Gregory Brine <greg.brine@scrmhub.com>
 */
class UrlHelper {
    private
        $url,
        $urlParts,
        $urlFileInfo;


    private
        $extension,
        $isFullUrl,
        $fullUrl,
        $topLevelDomain = null,
        $sameDomain = null,
        $sameTopLevelDomain = null;

    /**
     * Construct the class
     * @param string optional       $url        The starter URL
     */
    function __construct($url = false) {
        //If no url, assume self
        if($url) {
            //Set the URL
            $this->setUrl($url);
        }
    }

    /**
     * Set the BASE url to the current page
     *
     * @return url      The base url of the page
     */
    function setBaseURL() {
        $pageURL = 'http';

        if ($this->isSecure()) {
            $pageURL .= "s";
        }
        $pageURL .= "://".$_SERVER["SERVER_NAME"];

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_PORT"];
        }

        //The page requested
        $pageURL .= $_SERVER["REQUEST_URI"];

        $this->setUrl($pageURL);
        
        return $pageURL;
    }

    /**
     * Set the URL to work with
     *
     * @param string        $url        The URL to work with
     */
    function setUrl($url) {
        //Fix PHP 5.3 '//'
        $this->url = $url = $this->fixSlashUrl($url);
        $this->urlParts = parse_url($url);        

        //Add the schema in
        if(!isset($this->urlParts['scheme']) || empty($this->urlParts['scheme']) || ($this->isSecure() && $this->urlParts['scheme'] == 'http')) {
            $this->urlParts['scheme'] = ($this->isSecure() ? 'https' : 'http');
        }

        //Is there a host
        if(!isset($this->urlParts['host']) || empty($this->urlParts['host'])) {
            $this->urlParts['host'] = $_SERVER['SERVER_NAME'];
        }

        //Is there a path
        if(!isset($this->urlParts['path']) || empty($this->urlParts['path'])) {
            $this->urlParts['path'] = '/';
        }        

        //Explode the query string
        if(!empty($this->urlParts['query'])) {
            $querystring = explode('&', $this->urlParts['query']);
            $this->urlParts['query'] = array();

            //Loop through the parts
            foreach($querystring as $query) {
                $query = explode('=', $query);
                $this->urlParts['query'][$query[0]] = $query[1];
            }
        } else {
            $this->urlParts['query'] = array();
        }

        //Any file information
        $this->urlFileInfo = pathinfo($this->urlParts['path']);
    }

    /**
     * This functio fixes a bug in version of PHP prior to 5.4.17 where it doesn't recognize // Urls
     *
     * @param string    $url        The url to work with
     *
     * @return url                  With fixed slashes
     */
    private function fixSlashUrl($url) {
        if(strpos($url, "//") === 0) {
            $url = ($this->isSecure() ? 'http:' : 'https:').$url;
        }
        return $url;
    }


    /**
     * Get the extension of the file
     * @return string               The extension type
     */
    public function extension() {
        return isset($this->urlFileInfo['extension']) ? $this->urlFileInfo['extension'] : false;
    }

    /**
     * Deterine if it's a local(relative path) or external URL
     * @return string               The Schema of the url
     */
    public function isFullUrl() {
        return !empty($this->urlParts['scheme']);
    }

    /**
     * Get the full url
     * @param string    $scheme       
     */ 
    public function getFullUrl($scheme = true) {
        return $this->buildUrl($scheme);
    }
   

    /**
     * Return part of the url
     * @param string        $part       The part of the url array required
     * @return string                   Or false if it doesn't exist
     */
    public function getPart($part) {
        return isset($this->urlParts[$part]) ? $this->urlParts[$part] : false;
    }

    /**
     * Is the URL secure
     * @return boolean                  Is the URL https or not
     */
    public function secure() {
        return 
            (isset($this->urlParts['scheme']) && $this->urlParts['scheme'] == 'https')
            ||
            (isset($this->urlParts['port']) && $this->urlParts['port'] == 443);
    }

    /**
     * Is the URL on the same domain as the server
     * @return boolean
     */
    public function sameDomain() {
        //Not worked out yet
        return ($this->urlParts['host'] == $_SERVER['SERVER_NAME']) ? true : false;
    }

    /**
     * Is the URL on the same domain as this server
     * @return boolean
     */
    public function sameTopLevelDomain() {
        //Need to check it
        return $this->topLevelDomain() === $this->topLevelDomain($_SERVER['SERVER_NAME']) ? true : false;
    }

    

    /**
     * Set the schema
     *
     * @param string        $schema         HTTP/HTTPS/FTP etc
     */
    public function setScheme($schema = false) {
        if($schema)
            $this->urlParts['scheme'] = $schema;
    }

    /**
     * Set the host
     *
     * @param string        $host         The host value. e.g. scrmhub.com
     */
    public function setHost($host = false) {
        if($host)
            $this->urlParts['host'] = $host;
    }

    /**
     * Set the port
     * Note: This is mostly optonal for special cases
     *
     * @param string        $port         e.g. 80, 443, 21
     */
    public function setPort($port = false) {
        if($port && !empty($port)) {
            $this->urlParts['port'] = $port;
        }
    }

    /**
     * Set the path
     *
     * @param string        $schema         HTTP/HTTPS/FTP etc
     */
    public function setPath($path = false) {
        if($path && !empty($path)) {
            $this->urlParts['path'] = (substr($path,0,1) != '/' ? '/' : '').$path;
        }
    }

    /**
     * Set the path
     *
     * @param array         $params         Key Value pairs of query strings
     */
    public function setQueryParams(array $params) {
        $this->urlParts['query'] = $params;
    }

    /**
     * Append a folder to the path
     * 
     * @param sting         $path       
     */
    public function appendPath($path = false) {
        if($path) {
            $this->urlParts['path'] .= (substr($this->urlParts['path'], -1) !== '/' ? '/' : '') . $path; 
        }
    }

    /**
     * Add a bit in before the pathway
     * @param string    $path        The folder to add in before the current path
     * 
     */
    public function prependPath($path) {
        if($path) {
            $this->urlParts['path'] = (substr($path, 0, 1) !== '/' ? '/' : '') . $path . $this->urlParts['path'];
        }   
    }


    /**
     * Add items to querystring
     * @param string        $key        The query string key to add
     * @param string        $value      The value to add in
     */
    public function appendQueryString($key, $value = false) {
        if($value === false && !is_array($key))
            return;
        
        if(is_array($key)) {
            foreach($key as $keyname => $value) {
                $this->urlParts['query'][$keyname] = $value;
            }            
        } else {
            $this->urlParts['query'][$key] = $value;
        }
    }

    /**
     * Remove the Query String completely
     */
    public function removeQuery() {
        $this->urlParts['query'] = [];
    }

    /**
     * Remove the Query String value
     * @param string            $key            The Query string to remove
     */
    public function removeQueryString($key) {
        if(isset($this->urlParts['query'][$key])) {
            unset($this->urlParts['query'][$key]);
        }
    }

    /**
     * Remove the path value
     */
    public function removePath() {
        $this->urlParts['path'] = '/';
    }


    /**
     * Get the top level domain of a url
     * @param string        $domain         The url to check against
     * @return url                          The top level domain              
     */
    public function topLevelDomain($url = false) {
        //Split it
        if($url == false) {
            $self = true;
            if($this->topLevelDomain !== null)
                return $this->topLevelDomain;

            $url = $this->urlParts['host'];
        } else {
            $self = false;
        }

        $domainParts = preg_split('|\.|', $url);
        $parts = count($domainParts);

        $domain = $domainParts[$parts - 2].'.'.$domainParts[$parts - 1];

        if($domainParts[$parts - 2] == 'co') {
            $domain = $domainParts[$parts - 3].'.'.$domain;
        }

        //Save it for later
        if($self) {
            $this->topLevelDomain = $url;
        }

        return $domain;
    }
    
    /**
     * Build a URL
     * @param string optional       $scheme         Optional schema to build with
     * @return string                               The processed url
     */
    function buildUrl($scheme = true) {
        $url = $this->buildDomainUrl($scheme);
        $url.= $this->urlParts['path'];

        //Any query data?
        if(!empty($this->urlParts['query'])) {
            $url.= '?'.http_build_query($this->urlParts['query']);
        }

        //Otherwise, return as is
        return $url;
    }

    /**
     * Build the domain url
     * @param string optional       $scheme         Optional schema to build with
     * @return string                               The processed url
     */
    function buildDomainUrl($scheme = true) {
        $url = '';

        //What protocol
        if($scheme) {
            if(empty($this->urlParts['scheme']) || $this->urlParts['scheme'] == 'http' || $this->urlParts['scheme'] == 'https') {
                $url.= ($this->isSecure() ? 'https:' : 'http:');
            } else {
                $url.= $this->urlParts['scheme'].':';
            }
        }

        //Add the host, etc
        $url.='//'.(isset($this->urlParts['host']) ? $this->urlParts['host'] : $_SERVER['SERVER_NAME']);

        //Port number
        if(isset($this->urlParts['port'])) {
            $url.=':'.$this->urlParts['port'];
        }

        //And back again
        return $url;
    }

    /*
     * Makes sure a URL has the right parts to avoid arguments with the browser
     */
    // public function rebuildUrl($url) {
    //     if(!$url) {
    //         $url = $this->url;
    //     }

    //     //Still no URL
    //     if(!$url) {
    //         return false;
    //     }

    //     //Parse the url
    //     $urlBase = parse_url($url);

    //     if(isset($urlBase['host']) && $urlBase['host'] != '') {
    //         $url = (isset($urlBase['scheme']) ? $urlBase['scheme'] : 'http');
    //         $url .= '://'.$urlBase['host'];

    //         if ($urlBase['port'] != 80 && $urlBase['port'] != 443) {
    //             $url .= ':'.$urlBase['port'];
    //         }

    //         $url .= (isset($urlBase['path']) ? $urlBase['path'] : '/');

    //         if(isset($urlBase['query'])) {
    //             $url .= '?'.http_build_query($urlBase['query']);
    //         }

    //         return $url;
    //     }

    //     return false;
    // }


    /**
     * Get the base path of the url
     * @return url              The base path of the url
     */
    public function getBasePath() {
        //build the base path
        if($this->basePath == '') {
            $base = $this->get('urls.app') ? $this->get('urls.app') : $_SERVER['SERVER_NAME'];

            $this->basePath = ($this->request->url->isSecure() || $this->get('forceSSL') ? "https://" : "http://") . $base . '/';  
        }

        //return it
        return $this->basePath;
    }


    /**
     * Checks whether the request is secure or not.
     * Checks all the possible values
     *
     * @return Boolean
     */
    public function isSecure() {
        return (
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')
            ||
            (isset($_SERVER['X_FORWARDED_PROTO']) && strtolower($_SERVER['X_FORWARDED_PROTO']) == 'https')
            ||
            (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1))
            ||
            (isset($_SERVER['SSL_HTTPS']) && (strtolower($_SERVER['SSL_HTTPS']) == 'on' || $_SERVER['SSL_HTTPS'] == 1))
        );
    }


    /**
     * The magical getEverything class
     * Really this is for testing purposes
     * @return array
     */
    public function getEverything() {
        $response = array(
            'url'       => $this->url,
            'extension' => $this->extension(),
            'isFullUrl' => $this->isFullUrl(),
            'secure'    => $this->secure(),
            'topLevelDomain'=> $this->topLevelDomain(),
            'sameDomain'=> $this->sameDomain(),
            'sameTopLevelDomain'=> $this->sameTopLevelDomain(),
            'fullUrl'   => $this->getFullUrl(),
            'parts'     => $this->urlParts,
            'info'      => $this->urlFileInfo,

        );

        return $response;
    }
}