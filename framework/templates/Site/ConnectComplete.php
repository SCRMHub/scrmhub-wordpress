<?php
namespace SCRMHub\WordpressPlugin\Templates\Site;

use SCRMHub\WordpressPlugin\Templates\_BaseTemplate;

class ConnectComplete extends _BaseTemplate{
	function render(array $values) {
		$this->getResponseCode();
		ob_start();
	?><html>
		<head>
			<?php wp_head(); ?>
		</head>
		<body class="scrmhub_popup scrmhub_connect_complete">
			<div class="scrmhub_popup_header">
				<h1 class="title"><?php _e('Connect to '.$values['networkLabel'].' complete', 'scrmhub');?></h1>
			</div>
			<div class="scrmhub_popup_body">
				<p><?php _e($values['message'], 'scrmhub');?></p>
			</div>
			<div class="scrmhub_popup_actions">
				<div class="info"><a href="https://scrmhub.com/">Powered by SCRM Hub</a></div>
				<a class="button button-primary button-close-window"><?php _e('Close window', 'scrmhub');?></a>
			</div>
			
			<script>
			window.scrmhub.uuid 				= "<?php echo $values['uuid'];?>";
			window.scrmhub.connect_complete 	= <?php echo json_encode($values['response']);?>;
			window.scrmhub.analytics.enabled 	= false; //overrride the global as this is tracked elsewhere
			</script>
			<?php wp_footer(); ?>
		</body>
	</html><?php
		ob_end_flush();
	}
}