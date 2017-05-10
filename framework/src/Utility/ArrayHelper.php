<?php
namespace SCRMHub\Framework\Utility;

/**
 * Some useful Array enhancement functions
 *
 * Recursive array merging
 *
 * @author Gregory Brine <greg.brine@scrmhub.com>
 */

class ArrayHelper {
	/**
	 * Recuresively merge two arrays
	 * @param  [array] $array1 The base array
	 * @param  [array] $array2 New values to be added
	 * @return [array]         [The merged array]
	 */
	function mergeRecursively(array & $array1, array & $array2) {
	    $merged = $array1;

	    foreach ($array2 as $key => & $value) {
	        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
	            $merged[$key] = $this->mergeRecursively($merged[$key], $value);
	        } else if (is_numeric($key)) {
	            if (!in_array($value, $merged)) {
	             	$merged[] = $value;
	            }	                
	        } else {
	            $merged[$key] = $value;
	        }
	    }

	    return $merged;
	}
}