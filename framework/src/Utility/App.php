<?php
namespace SCRMHub\Framework\Utility;

use SCRMHub\Framework\Utility\Bucket;

/**
 * A simple bucket class
 *
 * Supports simple keys
 * or JS style path.to.key keys
 *
 * @author Gregory Brine <greg.brine@scrmhub.com>
 */
class App Extends Bucket {
	protected
		$methods = array();

	public function __call($key, $arguments) {
		if($callable = parent::get($key)) {
			if (!is_callable($callable))
				throw new BadMethodCallException("Method {$key} does not exists");

			return call_user_func_array($callable, $arguments);
		}
		return false;		
	}

	function __get($key) {
        return $this->get($key);
    }

	public function get($key, $arguments = array()) {
		if($value = parent::get($key)) {
			if (is_callable($value)) {
				//Make it 
				$function = call_user_func_array($value, $arguments);

				//Make sure it's constructed only once
				if(is_object($function)) {					
					$this->set($key, $function);
				}

				//Return it
				return $function;
			}
		}
		return $value;
	}
}