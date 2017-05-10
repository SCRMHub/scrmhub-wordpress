<?php
namespace SCRMHub\Framework\Utility;

/**
 * A simple bucket class
 *
 * Supports simple keys
 * or JS style path.to.key keys
 *
 * @author Gregory Brine <greg.brine@scrmhub.com>
 */
class Bucket {
    /** 
     * @var Where we store the values
     */
    protected
        $items = array();

    /**
     * @var What type of store is it. e.g. simple array, SESSION, COOKIE, etc
     */
    private
        $storeType = 'array';

    /**
     * @var Do we need to check anything for running?
     */
    private
        $beforeSet = null;


    /**
     * Constructor
     *
     * @param array         $items      Key > Value set of values to pre-set
     */
    function __construct(array $items = array()) {
        if(!empty($items)) {
            $this->items = $items;
        }       
    }

    /**
     * Define a store other than the local array
     * @param object        $store          The object to be used. e.g. SESSION
     */
    protected function setGlobalObjectStore($global) {
        //What the PHP object looks like
        $global = strtoupper($global);

        //Make sure we don't set it again
        if(!$this->isStoreType($global)) {
            $this->storeType = $global;
            $this->items =& $GLOBALS['_'.$global];
        }        
    }

    /**
     * Register before set function
     * @param [mixed] $beforeSet A function to call (usually something like array(&$this, 'functionName'))
     */
    protected function setBeforeSet($beforeSet) {
        $this->beforeSet = $beforeSet;
    }

    /**
     * Get the type of store being used
     * @return [string] What type of store is it 
     */
    protected function getStoreType() {
        return $this->storeType;
    }

    /**
     * Check if the store is of type
     * @param  [string]     $type   What are we after?
     * @return boolean      Is it or not?
     */
    protected function isStoreType($type) {
        return $this->storeType == $type;
    }

    /**
     * Method to check if there's anything in the bucket
     * @return boolean
     */
    function isEmpty() {
        return empty($this->items);
    }

    /**
     * This is called before any get or set
     * @return [boolean] Are we good to go?
     */
    function beforeSet($data) {
        if($this->beforeSet) {
            return call_user_func($this->beforeSet, $data);
        }
        return true;
    }

    /**
     * PHP Magic functions to set and get
     * @return item
     */
    function __get($key) {
        return $this->get($key);
    }
    function __set($key, $value) {
        return $this->set($key, $value);
    }

    /**
     * Clear everything from the bucket
     */
    public function clearAll() {
        $this->items = array();
    }

    /**
     * Replace all values in the bucket
     */
    public function replaceAll(array $items) {
        $this->items = $items;
    }

    /**
     * Bulk set data
     * This will replace any maching keys
     *
     * @param array         $data       What to add in
     */
    public function bulkSet(array $data) {
        //Check if we need to do anything
        if(empty($data) || !$this->beforeSet($data))
            return;
        
        //Update the items
        $this->items = array_replace_recursive($this->items, $data);
    }

    /**
     * Count the number of items top level items in the bcuket
     * Will not include nested items
     * @return int
     */
    public function size() {
        return count($this->items);
    }

    /**
     * Delete an item from the array
     * @param string        $key        The key to search for. Simple or complete path.to.item
     * @return item
     */
    public function delete($key) {
        if(!$this->beforeSet($key))
            return false;
        
        if ( strpos($key, '.') ) {
            // I stole this from stack overflow, so if you're impressed i can't take credit :(
            $keys = explode('.', $key);
            $last_key = array_pop($keys);

            $array_ptr = &$this->items;
            while ($arr_key = array_shift($keys)) {
                if (!array_key_exists($arr_key, $array_ptr)) {
                    $array_ptr[$arr_key] = array();
                }
                $array_ptr = &$array_ptr[$arr_key];
            }

            unset($array_ptr[$last_key]);
        } else {
            unset($this->items[$key]);
        }
    }

    /**
     * Get an item from the bucket
     * @param string        $key        The key to search for. Simple or complete path.to.item
     * @return item
     */ 
    public function get($key) {
        if(!$this->beforeSet($key))
            return false;

        if(empty($key) || is_array($key)) {
            return false;
        }
        if ( strpos($key, '.') ) {
            $chunks = explode('.', $key);
            $val = $this->items;
            foreach($chunks as $chunk) {
                if ( !isset($val[$chunk]) )
                    return false;
                $val = $val[$chunk];
            }
            return $val;
        } else {
            return isset($this->items[$key]) ? $this->items[$key] : false;
        }
    }

    /**
     * Get all items in the bucket
     *
     * @return array
     */
    public function getAll() {
        return $this->items;
    }

    /**
     * Set an item
     * @param string        $key        The key to search for. Simple or complete path.to.item
     * @param string        $value      The value to store
     * @param string        $append     Append will add it to an existing item if the item is an array
     */
    public function set($key, $value, $append = false) {
        if(!$this->beforeSet($key))
            return false;

        if ( strpos($key, '.') ) {
            $keys       = explode('.', $key);
            $last_key = array_pop($keys);

            $array_ptr = &$this->items;
            while ($arr_key = array_shift($keys)) {
                if (!array_key_exists($arr_key, $array_ptr)) {
                    $array_ptr[$arr_key] = array();
                }
                $array_ptr = &$array_ptr[$arr_key];
            }
            //Append an item
            if($append) {
                $array_ptr[$last_key][] = $value;
            } else {
                $array_ptr[$last_key] = $value;
            }
            
        } else {
            //Add it to the end
            if($append) {
                $this->items[$key][] = $value;
            } else {
                $this->items[$key] = $value;
            }
            return $this->items[$key];
        }
    }

    /**
     * Append an item to an existing item
     * @param string        $key        The key to search for. Simple or complete path.to.item
     * @param string        $values     The values to store. This can be a string also
     */
    public function append($key, $values) {
        //Array can be added manually
        if(is_array($values)) {
            foreach($values as $subkey => $value) {
                $this->set($key.'.'.$subkey, $value);
            }
        } else {
            $this->set($key, $values, true);
        }
    }


    /**
     * Load a file into the bucket
     * @param string            $file       Path to the file
     * @param string optional   $type       The file type type is optional. It'll work it out otherwise
     */
    public function load($file, $type = 'php') {
        if (!file_exists($file)) {
            throw new GooException('Unable to load the file: ' . $file);
        }

        $data = array();
        switch ($type) {
            case 'ini':
                $data = parse_ini_file($file);
                break;
            case 'json':
                $fileContents = file_get_contents($file);
                $data = json_decode($fileContents, true);
                break;
            case 'php': 
                $data = require($file);
                break;
        }
        
        //Not array
        if(empty($data) || !is_array($data))
            return;

        //Adding some stuff to the beginning
        if(isset($data['prepend'])) {
            //prepend other file
            $this->load($data['prepend']);

            //Make sure it doesn't try to load it
            unset($data['prepend']);            
        }

        //Add stuff afterwards
        if(isset($data['extend'])) {
            $extend = $data['extend'];
            unset($data['extend']);
        } else {
            $extend = false;
        }


        //Add it
        $this->bulkSet($data);

        //Add onto the end
        if($extend) {
            //add 
            $this->load($extend);
        }
    }    
}
