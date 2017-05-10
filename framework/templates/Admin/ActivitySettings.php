<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

class ActivitySettings extends _AdminBaseTemplate {
    protected 
        $helpfile = 'ActivitySettingsHelp.htm';
        
	function render(array $values = array()) {
		ob_start();
    if($values['multi']) { ?>
        <h3 class="title"><?php _e( 'Activity tracking settings for all sites in the network', 'scrmhub' ); ?></h3>
    <?php } else { ?>
        <h3 class="title"><?php _e( 'Activity tracking settings for this site', 'scrmhub' ); ?></h3>
    <?php }?>
    <table class="form-table">
        <tbody>
        <?php if($values['multi']) { ?>
            <tr>
                <th scope="row">
                    <label for="activity_multi_enable"><?php _e("Activity tracking enabled: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="activity_multi_enable" class="scrmhubpanel" data-scrmhubtarget="#ConnectEnabled">
                        <option value="0">Disable for all sites</option>
                        <option value="1">Enable for all sites</option>
                    </select>
                </td>
            </tr>
        <?php } else { ?>
            <tr>
                <th scope="row">
                    <label for="activity_options[enabled]"><?php _e("Activity tracking enabled: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="activity_options[enabled]" class="scrmhubpanel" data-scrmhubtarget="#ActivityEnabled">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if((bool)$values['activity_options']['enabled']) echo ' selected';?>>Enabled</option>
                    </select>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>

    <div id="ActivityEnabled" class="scrmhubAdminPanel">
    </div>

<?php
        $content = ob_get_clean();
        $content = $this->formWrapper($values, $content);
        return $this->pageWrapper($values, $content);
    }
}

