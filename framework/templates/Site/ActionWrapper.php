<?php
namespace SCRMHub\WordpressPlugin\Templates\Site;

use SCRMHub\WordpressPlugin\Templates\_BaseTemplate;

class ActionWrapper extends _BaseTemplate {
	function render(array $args) {
		ob_start();?><div class="scrmhub-wrapper">
			<div class="scrmhub-action <?php echo $args['class'];?>">
				<?php if(isset($args['title'])) { ?>
					<h2 class="scrmhub-action-title"><?php echo $args['title'];?></h2>
				<?php } ?>
				<div class="scrmhub-actions">
					<?php foreach($args['buttons'] as $button) {
						echo $button;
					}?>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="scrmhub-message-container" style="display: none;"></div>
		</div><?php
		return ob_get_clean();
	}
}
