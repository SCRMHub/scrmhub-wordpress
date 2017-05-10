<?php
namespace SCRMHub\WordpressPlugin\Templates\Site;

class Header {
	function render(array $values) {
		$header = "<script>";
		$header.= "window.scrmhub=".json_encode($values).";";
		$header.= "</script>";

		return $header;
	}
}