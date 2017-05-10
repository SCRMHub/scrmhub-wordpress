<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

class MultiSiteSettings extends _AdminBaseTemplate {
	function render(array $values) {
        ob_start();
?>
        <h3 class="title"><?php _e( 'Network App Keys', 'scrmhub' ); ?></h3>
        <p class="description" id="scrmhub_appkey"><?php _e('Every location (website) is given a unique token by SCRM Hub for tracking, interaction and security to ensure that requests are valid. This will be provided by SCRM Hub for all locations / URLs. Note that for multi-site installs, each site in the network will have it\'s own unique key','scrmhub');?></p>
        <table class="wp-list-table widefat fixed striped media">
            <thead>
            <tr>
                <th class="manage-column column-cb">Domain</th>
                <th class="manage-column column-cb">App Key</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($values['blogs'] as $blog) { ?> 
                <tr class="status-inherit">
                    <th scope="row">
                        <label for="<?php echo $blog['field'];?>"><?php echo $blog['label']; ?></label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo $blog['field'];?>" value="<?php echo $blog['value'];?>" required="required" aria-describedby="scrmhub_appkey" class="regular-text">
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
<?php
        $content = ob_get_clean();
        $content = $this->formWrapper($values, $content);
        return $this->pageWrapper($values, $content);
    }
}