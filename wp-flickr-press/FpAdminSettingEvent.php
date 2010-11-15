<?php
class FpAdminSettingEvent {
	private function __construct() {}

	public static function addMenu() {
		add_options_page(FlickrPress::NAME.' Option', FlickrPress::NAME, 8, __FILE__, array('FpAdminSettingEvent','generateOptionForm'));
	}

	private function getPageOptions() {
		return implode(',', array(
			FlickrPress::getKey('api_key'),
			FlickrPress::getKey('api_secret'),
			FlickrPress::getKey('user_id'),
		));
	}

	public static function generateOptionForm() {
?>
<div class="wrap">
        <h2><?php echo FlickrPress::NAME ?></h2>
        <form method="post" action="options.php">
                <?php wp_nonce_field('update-options'); ?>
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="<?php echo self::getPageOptions() ?>" />
                <table class="form-table">
                        <tr valign="top">
                                <th scope="row">
                                        <p><?php echo _e('API KEY/Secret') ?></p>
					<p><a href="http://www.flickr.com/services/api/misc.api_keys.html" target="_blank">Flickr Services</a></p>
                                </th>
                                <td>
					<p><?php echo _e('API KEY:') ?><br/><input type="text" name="<?php echo FlickrPress::getKey('api_key') ?>" value="<?php echo FlickrPress::getApiKey() ?>" size="70" />
					<p><?php echo _e('Secret:') ?><br/><input type="text" name="<?php echo FlickrPress::getKey('api_secret') ?>" value="<?php echo FlickrPress::getApiSecret() ?>" size="70" />
				</td>
                        </tr>
                        <tr valign="top">
                                <th scope="row">
                                        <p><?php echo _e('USER ID') ?></p>
					<p><a href="http://idgettr.com/" target="_blank">idGettr — Find your Flickr ID</a></p>
                                </th>
                                <td><input type="text" name="<?php echo FlickrPress::getKey('user_id') ?>" value="<?php echo FlickrPress::getUserId() ?>" size="70" /></td>
                        </tr>

                </table>
                <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                </p>
        </form>
</div>

<?php
	}
}
?>
