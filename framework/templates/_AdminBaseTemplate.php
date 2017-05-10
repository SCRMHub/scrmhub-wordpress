<?php
namespace SCRMHub\WordpressPlugin\Templates;

use SCRMHub\WordpressPlugin\Templates\_BaseTemplate;

abstract class _AdminBaseTemplate extends _BaseTemplate {
	protected 
		$helpfile = null;

	protected function header() {
		return '<h1 class="title">SCRM Hub</h1>';
	}

	protected function formWrapper(array $values, $content) {
		ob_start(); ?>
		<form name="scrmub-form-<?php echo $values['actionname'];?>" id="scrmub-form-<?php echo $values['actionname'];?>" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" class="validate<?php if(isset($values['multi']) && $values['multi'] == true) echo ' scrmhub-network-update';?>">
        <?php
        	wp_nonce_field($values['actionname'], $values['actionname'].'-nonce' );
        	echo $content;
        ?>
        <br>
        <?php if(isset($values['multi']) && $values['multi'] == true) {
        	echo $this->formSubmitNetwork();
        } else {
        	echo $this->formSubmit();
        }?>
        </form><?php
        return ob_get_clean();
	}

	protected function formSubmit() {
		return '<p class="submit">
        	<input type="submit" name="Submit" value="'. __('Update Options', 'scrmhub' ).'" id="submit" class="button button-primary" />
        </p>';
	}

	protected function formSubmitNetwork() {
		return '<p class="submit">
        	<input type="submit" name="Submit" value="'. __('Update Network Settings', 'scrmhub' ).'" id="submit" class="button button-primary" />
        </p>';
	}

	/**
	 * Output hte page wrapper
	 * @param  array 	$values  	Some values that do something
	 * @param  string 	$content 	The HTML to render
	 * @return string          		The rendered HTML
	 */
	protected function pageWrapper($values, $content) {
		ob_start();?>
		<div class="scrmhub-admin-wrap wrap">
			<?php echo $this->header(); ?>
			<div class="scrmhub-admin-inner">
				<div class="content<?php echo ($this->helpfile ? ' content-form' : ''); ?>">
					<?php echo $content?>
				<?php if($this->helpfile) {
					echo '</div><div class="content-help"><div class="content-help-inner">';
					require_once(realpath(__dir__).'/AdminHelp/'.$this->helpfile);
					echo '</div>';
				}?>
				</div>
			</div>
		</div>

		<?php return ob_get_clean();
	}
}