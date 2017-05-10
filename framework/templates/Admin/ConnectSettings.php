<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

class ConnectSettings extends _AdminBaseTemplate {
    protected 
        $helpfile = 'ConnectSettingsHelp.htm';
        
	function render(array $values) {
        ob_start();
    if($values['multi']) { ?>
        <h3 class="title"><?php _e( 'Connect settings for all sites in the network', 'scrmhub' ); ?></h3>
    <?php } else { ?>
        <h3 class="title"><?php _e( 'Connect settings for this site', 'scrmhub' ); ?></h3>
    <?php }?>    

    <table class="form-table">
        <tbody>
        <?php if($values['multi']) { ?>
            <tr>
                <th scope="row">
                    <label for="connect_multi_enable"><?php _e("Connect enabled: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="connect_multi_enable" class="scrmhubpanel" data-scrmhubtarget="#ConnectEnabled">
                        <option value="0">Disable for all sites</option>
                        <option value="1"<?php if((bool)$values['connect_options']['enabled']) echo ' selected';?>><?php _e('Enable for all sites','scrmhub');?></option>
                    </select>
                </td>
            </tr>
        <?php } else { ?>
            <tr>
                <th scope="row">
                    <label for="connect_options[enabled]"><?php _e("Connect enabled: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="connect_options[enabled]" class="scrmhubpanel" data-scrmhubtarget="#ConnectEnabled">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if((bool)$values['connect_options']['enabled']) echo ' selected';?>><?php _e('Enabled','scrmhub');?></option>
                    </select>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
    
    <div id="ConnectEnabled" class="scrmhubAdminPanel">
        <hr>
        <h3 class="title">Options</h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row" nowrap>
                    <label for="connect_options[options][redirect]"><?php _e("Redirect on success: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="connect_options[options][redirect]" aria-describedby="scrmhub_connectparentrefresh" data-scrmhubpanelvalue="#RedirectCustom" class="scrmhubpanelvalue">
                        <option value="admin">Admin Dashboard</option>
                        <option value="home"<?php if(isset($values['connect_options']['options']['redirect']) && $values['connect_options']['options']['redirect'] == 'home') echo ' selected';?>>Site homepage</option>
                        <option value="custom"<?php if(isset($values['connect_options']['options']['redirect']) && $values['connect_options']['options']['redirect'] == 'custom') echo ' selected';?>>Custom URL</option>
                    </select>

                    <br>
                    <br>
                    <div id="RedirectCustom">
                        <div class="panel-custom hide">
                            <label for="connect_options[options][redirecturl]"><?php _e("Custom URL ", 'scrmhub' ); ?></label><br>
                            <input type="text" name="connect_options[options][redirecturl]" value="<?php echo $values['connect_options']['options']['redirecturl']; ?>" style="width: 80%;">
                        </div>
                    </div>
                    
                    
                    
                    
                </td>
            </tr>            

            <tr>
                <th scope="row">
                    <label for="connect_options[options][loginform]"><?php _e("Add to login: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="connect_options[options][loginform]" aria-describedby="scrmhub_loginform">
                        <option value="0">No</option>
                        <option value="1"<?php if(isset($values['connect_options']['options']['loginform']) && (bool)$values['connect_options']['options']['loginform']) echo ' selected';?>>Yes</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="connect_options[options][commentconnect]"><?php _e("Require Connect to comment: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="connect_options[options][commentconnect]" aria-describedby="scrmhub_connectcomments">
                        <option value="0">No</option>
                        <option value="1"<?php if(isset($values['connect_options']['options']['commentconnect']) && (bool)$values['connect_options']['options']['commentconnect']) echo ' selected';?>>Yes</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="connect_options[options][user_photo]"><?php _e("Set User Photo: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="connect_options[options][user_photo]" aria-describedby="scrmhub_user_photo">
                        <option value="0">No</option>
                        <option value="1"<?php if(isset($values['connect_options']['options']['user_photo']) && (bool)$values['connect_options']['options']['user_photo']) echo ' selected';?>>Yes</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="connect_options[options][icononly]"><?php _e("Show icon only: ", 'scrmhub' ); ?></label>
                </th>
                <td>
                    <select name="connect_options[options][icononly]" aria-describedby="scrmhub_icononly">
                        <option value="0">No</option>
                        <option value="1"<?php if(isset($values['connect_options']['options']['icononly']) && (bool)$values['connect_options']['options']['icononly']) echo ' selected';?>>Yes</option>
                    </select>
                    <p class="description" id="scrmhub_icononly"><?php _e('Show the network icon only.','scrmhub');?></p>
                </td>
            </tr>
        </tbody>
        </table>


        <hr>
        <h3 class="title"><?php echo __( 'Networks', 'scrmhub' ); ?></h3>
        <table class="form-table">
            <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <h4>Facebook</h4>
                    <label for="connect_options[networks][facebook][enabled]"><?php _e("Enabled: ", 'scrmhub' ); ?></label>
                    <select name="connect_options[networks][facebook][enabled]">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if(isset($values['connect_options']['networks']['facebook']['enabled']) && (bool)$values['connect_options']['networks']['facebook']['enabled']) echo ' selected';?>>Enabled</option>
                    </select>
                </td>
                <td style="vertical-align: top;">
                    <h4>LinkedIn</h4>
                    <label for="connect_options[networks][linkedin][enabled]"><?php _e("Enabled: ", 'scrmhub' ); ?></label>
                    <select name="connect_options[networks][linkedin][enabled]">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if(isset($values['connect_options']['networks']['linkedin']['enabled']) && (bool)$values['connect_options']['networks']['linkedin']['enabled']) echo ' selected';?>>Enabled</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <h4>Twitter</h4>
                    <label for="connect_options[networks][twitter][enabled]"><?php _e("Enabled: ", 'scrmhub' ); ?></label>
                    <select name="connect_options[networks][twitter][enabled]">
                        <option value="0">Disabled</option>
                        <option value="1"<?php if(isset($values['connect_options']['networks']['twitter']['enabled']) && (bool)$values['connect_options']['networks']['twitter']['enabled']) echo ' selected';?>>Enabled</option>
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