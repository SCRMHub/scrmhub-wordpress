<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

/**
 * Getting Started template
 */
class GettingStarted extends _AdminBaseTemplate {
	function render(array $values = []) {
        ob_start();
        require_once(realpath(__dir__.'/../').'/AdminHelp/GettingStarted.php');
        $content = ob_get_clean();

        return $this->pageWrapper($values, $content);
    }
}