<?php
namespace SCRMHub\WordpressPlugin\Interfaces;

abstract class _BaseInterface {
	protected static $app;

	protected static function app() {
		if(empty(self::$app)) {
			global $scrmhub;
			self::$app = $scrmhub;
		}
		return self::$app;
	}
}