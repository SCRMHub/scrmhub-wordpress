<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

class Help extends _AdminBaseTemplate {
	function render() {
		ob_start();
		include(realpath(__dir__.'/../').'/AdminHelp/Help.php');
		$content = ob_get_clean();
        return $this->pageWrapper([], $content);
    }
}