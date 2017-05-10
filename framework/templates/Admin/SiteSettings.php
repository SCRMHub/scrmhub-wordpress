<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

class SiteSettings extends _AdminBaseTemplate {
    protected 
        $helpfile = 'SiteSettingsHelp.htm';

	function render(array $values) {
        ob_start();
?>
        <h3 class="title"><?php _e( 'Site Settings', 'scrmhub' ); ?></h3>
        <table class="form-table">
            <tbody>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="scrmhub_appid"><?php _e("App Id: ", 'scrmhub' ); ?>*</label>
                </th>
                <td>
                    <input type="text" name="scrmhub_appid" required value="<?php echo $values['scrmhub_appid']; ?>" size="40" aria-describedby="scrmhub_appid" aria-required="true" autocapitalize="none" autocorrect="off" class="regular-text">
                    <p class="description" id="scrmhub_appid"><?php _e('Your Application ID as provided by SCRM Hub','scrmhub');?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="scrmhub_secret"><?php _e("API Secret: ", 'scrmhub' ); ?>*</label>
                </th>
                <td>
                    <input type="text" name="scrmhub_secret" required value="<?php echo $values['scrmhub_secret']; ?>" size="40" aria-describedby="scrmhub_appsecret" class="regular-text">
                    <p class="description" id="scrmhub_appsecret"><?php _e('Your Application Secret as provided by SCRM Hub. This is used in all server side transactions and should not be shared with anybody or exposed in the website.','scrmhub');?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <hr>

        <h3 class="title"><?php _e( 'Delete all settings on deactivate', 'scrmhub' ); ?></h3>
        <table class="form-table">
        <tr>
            <th scope="row">
                <label for="scrmhub_fulluninstall"><?php _e("Remove settings: ", 'scrmhub' ); ?></label>
            </th>
            <td>
                <select name="scrmhub_fulluninstall" class="scrmhubpanel" data-scrmhubtarget="#scrmhub_fulluninstall" aria-describedby="scrmhub_fulluninstall_desc">
                    <option value="0">No, DO NOT remove settings on deactivate</option>
                    <option value="1"<?php if((bool)$values['scrmhub_fulluninstall']) echo ' selected';?>>Yes, DO removed all setting in deactivate</option>
                </select>
                <p class="description" id="scrmhub_fulluninstall_desc"><?php _e('If you decide to deactivate the plugin, this will remove all options from the database as well. This means if you reactivate it, you will have to add everything back in.','scrmhub');?></p>
            </td>
        </tr>
        </table>

        
        
        <!-- 
        <h3 class="title"><?php _e( 'Use SCRM Hub URLs', 'scrmhub' ); ?></h3>
        <table class="form-table">
            <tbody>
            <tr>
                    <th scope="row">
                        <label for="scrmhub_urls"><?php _e("Enable SCRM Hub URLs: ", 'scrmhub' ); ?></label>
                    </th>
                    <td>
                        <select name="scrmhub_urls" aria-describedby="scrmhub_urls">
                            <option value="false">Disabled</option>
                            <option value="true"<?php if((bool)$values['scrmhub_urls'] === true) echo ' selected';?>>Enabled</option>
                        </select>
                        <p class="description" id="scrmhub_urls"><?php _e('Use SCRM Hub\'s URL tracking system to track click through and referrals.','scrmhub');?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr> -->

        <!-- <h3 class="title"><?php _e( 'Auto Update Settings', 'scrmhub' ); ?></h3>
        <table class="form-table">
        <tr>
            <th scope="row">
                <label for="scrmhub_autoupdate"><?php _e("Auto update: ", 'scrmhub' ); ?></label>
            </th>
            <td>
                <select name="scrmhub_autoupdate" class="scrmhubpanel" data-scrmhubtarget="#scrmhub_autoupdate" aria-describedby="scrmhub_autoupdate_desc">
                    <option value="0">Disabled</option>
                    <option value="1"<?php if((bool)$values['scrmhub_autoupdate']) echo ' selected';?>>Enabled</option>
                </select>
                <p class="description" id="scrmhub_autoupdate_desc"><?php _e('Enable the auto-updater. Currently this is connected to BitBucket, so you will need your username and password. We store the details in an encrypted format for your protection.','scrmhub');?></p>
            </td>
        </tr>
        </table> -->
<?php
        $content = ob_get_clean();
        $content = $this->formWrapper($values, $content);
        return $this->pageWrapper($values, $content);
    }
}