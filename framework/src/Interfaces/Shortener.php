<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

use SCRMHub\WordpressPlugin\Interfaces\_BaseInterface;
use SCRMHub\SDK\API\ShortUrl;

class Shortener extends _BaseInterface {
	/**
	 * Outputs a short url
	 * @param  [string] $link 	The URL to shorten
	 */
	public static function doShortUrlFromLink($link) {
		echo self::getShortUrlFromLink($link);
	}

	/**
	 * Returns a short url
	 * @param  [string] $link    The link to shorten
	 * @return [string]          The final short URL
	 */
	public static function getShortUrlFromLink($link) {
		if(empty($link)) {
			return false;
		}

		//Got a URL back so return it
		if($url = self::app()->shorturls->getLinkShortUrl($link)) {
			return $url;
		}

		//If link shortening fails, return the original one
		return $link;
	}
}