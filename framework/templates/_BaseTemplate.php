<?php
namespace SCRMHub\WordpressPlugin\Templates;

abstract class _BaseTemplate {
	private $code = 200;

	public function setResponseCode($code = 200) {
		$this->code = $code;

		//allow chaining events
		return $this;
	}

	protected function getResponseCode() {
		status_header($this->code);
	}
}