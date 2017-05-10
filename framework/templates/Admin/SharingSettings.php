<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

class SharingSettings extends _AdminBaseTemplate {
    protected 
        $helpfile = 'SharingSettingsHelp.htm';

	function render(array $values) {
        ob_Start();

    if($values['multi']) { ?>
        <h2 class="title"><?php _e( 'Sharing settings for all sites in the network', 'scrmhub' ); ?></h2>
    <?php } else { ?>
        <h2 class="title"><?php _e( 'Sharing settings for this site', 'scrmhub' ); ?></h2>
    <?php }?>
    <table class="form-table">
        <tbody>

        <?php if($values['multi']) { ?>
            <tr>
                <th scope="row">
                    <label for="sharing_multi_enable"><?php _e("Sharing enabled: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="sharing_multi_enable" class="scrmhubpanel" data-scrmhubtarget="#ShareEnabled">
                        <option value="false"><?php _e('Disable for all sites','scrmhub');?></option>
                        <option value="true"<?php if($values['sharing_settings']['enabled'] == 'true') echo ' selected';?>><?php _e('Enabled for all','scrmhub');?></option>
                    </select>
                    <p class="description" id="scrmhub_multi_enable"><?php _e('NOTE: That if you enable this, it will override the settings for all of the sites in the network','scrmhub');?></p>
                </td>
            </tr>
        <?php } else { ?>
            <tr>
                <th scope="row">
                    <label for="sharing_settings[enabled]"><?php _e("Sharing enabled: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="sharing_settings[enabled]" class="scrmhubpanel" data-scrmhubtarget="#ShareEnabled">
                        <option value="false">Disabled</option>
                        <option value="true"<?php if($values['sharing_settings']['enabled'] == 'true') echo ' selected';?>>Enabled</option>
                    </select>
                </td>
            </tr>
        <?php }?>
       
    </tbody>
    </table>

    <div id="ShareEnabled" class="scrmhubAdminPanel">
        <hr>

        <h3 class="title"><?php echo __( 'Options', 'scrmhub' ); ?></h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="sharing_settings[options][position]"><?php _e("Sharing position: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="sharing_settings[options][position]">
                        <option value="top"<?php if($values['sharing_settings']['options']['position'] == 'top') echo ' selected="selected"';?>>Top</option>
                        <option value="bottom"<?php if($values['sharing_settings']['options']['position'] == 'bottom') echo ' selected="selected"';?>>Bottom</option>
                        <option value="both"<?php if($values['sharing_settings']['options']['position'] == 'both') echo ' selected="selected"';?>>Top &amp; Bottom</option>
                        <option value="manual"<?php if($values['sharing_settings']['options']['position'] == 'manual') echo ' selected="selected"';?>>Manual</option>
                    </select>
                </td>
            </tr>
            <tr class="scrmhub-checkbox-list">
                <th scope="row">
                    <label for="sharing_settings[options][types]"><?php _e("Sharing post types: ", 'scrmhub' ); ?></label><br>
                    <a class="scrmhub-select-all">Select All</a> 
                </th>
                <td>
                    <?php foreach($values['postTypes'] as $post_type => $post_type_name) { ?>
                    <input type="checkbox" name="sharing_settings[options][types][]" value="<?php echo $post_type;?>"
                        <?php if(is_array($values['sharing_settings']['options']['types']) && in_array($post_type, $values['sharing_settings']['options']['types'])) echo 'checked="checked"'?>
                    ><?php echo ucfirst($post_type_name);?>
                    <br>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="sharing_settings[options][icononly]"><?php _e("Show icon only: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="sharing_settings[options][icononly]" aria-describedby="scrmhub_icononly">
                        <option value="0">No</option>
                        <option value="1"<?php if(isset($values['sharing_settings']['options']['icononly']) && (bool)$values['sharing_settings']['options']['icononly']) echo ' selected';?>>Yes</option>
                    </select>
                    <p class="description" id="scrmhub_icononly"><?php _e('Show the network icon only.','scrmhub');?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <hr>


        <h3 class="title"><?php echo _e( 'Networks', 'scrmhub' ); ?></h3>
        <table class="form-table">
            <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <h4>Facebook</h4>
                    <label for="sharing_settings[networks][facebook][enabled]"><?php _e("Enabled: " ); ?></label>
                    <select name="sharing_settings[networks][facebook][enabled]">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if(isset($values['sharing_settings']['networks']['facebook']['enabled']) && (bool)$values['sharing_settings']['networks']['facebook']['enabled']) echo ' selected';?>>Enabled</option>
                    </select>
                </td>
                <td style="vertical-align: top;">
                    <h4>Twitter</h4>
                    <label for="sharing_settings[networks][twitter][enabled]"><?php _e("Enabled: " ); ?></label>
                    <select name="sharing_settings[networks][twitter][enabled]">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if(isset($values['sharing_settings']['networks']['twitter']['enabled']) && (bool)$values['sharing_settings']['networks']['twitter']['enabled']) echo ' selected';?>>Enabled</option>
                    </select>
                    <br>
                    <label for="sharing_settings[networks][twitter][via]"><?php _e("Via: @ " ); ?></label>
                    <input type="text" name="sharing_settings[networks][twitter][via]" value="<?php if(isset($values['sharing_settings']['networks']['twitter']['via'])) echo $values['sharing_settings']['networks']['twitter']['via']?>">
                    <br>
                    <label for="sharing_settings[networks][twitter][quotes]"><?php _e("Use quotes?: " ); ?></label>
                    <select name="sharing_settings[networks][twitter][quotes]">
                        <option value="0">No "quotes"</option>
                        <option value="1"<?php if(isset($values['sharing_settings']['networks']['twitter']['quotes']) && (bool)$values['sharing_settings']['networks']['twitter']['quotes']) echo ' selected';?>>Yes, wrap in "quotes"</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <h4>Pinterest</h4>
                    <label for="sharing_settings[networks][pinterest][enabled]"><?php _e("Enabled: " ); ?></label>
                    <select name="sharing_settings[networks][pinterest][enabled]">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if(isset($values['sharing_settings']['networks']['pinterest']['enabled']) && (bool)$values['sharing_settings']['networks']['pinterest']['enabled']) echo ' selected';?>>Enabled</option>
                    </select>
                </td>
                <td style="vertical-align: top;">
                    <h4>LinkedIn</h4>
                    <label for="sharing_settings[networks][linkedin][enabled]"><?php _e("Enabled: " ); ?></label>
                    <select name="sharing_settings[networks][linkedin][enabled]">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if(isset($values['sharing_settings']['networks']['linkedin']['enabled']) && (bool)$values['sharing_settings']['networks']['linkedin']['enabled']) echo ' selected';?>>Enabled</option>
                    </select>
                </td>
            </tr>
            </tbody>
            </table>
        </div>
<?php
        $content = ob_get_clean();
        $content = $this->formWrapper($values, $content);
        return $this->pageWrapper($values, $content);
    }
}