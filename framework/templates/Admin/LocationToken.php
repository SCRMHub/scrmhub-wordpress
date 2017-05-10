<?php
namespace SCRMHub\WordpressPlugin\Templates\Admin;

use SCRMHub\WordpressPlugin\Templates\_AdminBaseTemplate;

class LocationToken extends _AdminBaseTemplate {
    protected 
        $helpfile = 'LocationTokenHelp.htm';

	function render(array $values) {
        ob_start();
?>
    <h3 class="title"><?php _e( 'SCRM Hub Location Token', 'scrmhub' ); ?></h3>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label for="scrmhub_site_appkey"><?php _e("APP Key: ", 'scrmhub' ); ?></label>
            </th>
            <td>
                <input type="text" name="scrmhub_site_appkey" value="<?php echo $values['scrmhub_site_appkey']; ?>" size="40" required aria-describedby="scrmhub_appkey" class="regular-text">
                <p class="description" id="scrmhub_appkey"><?php _e('Your site\'s unique location token','scrmhub');?></p>
            </td>
        </tr>
        </tbody>
    </table>    

<?php
        $content = ob_get_clean();
        $content = $this->formWrapper($values, $content);
        return $this->pageWrapper($values, $content);
    }
}