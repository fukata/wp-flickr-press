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
			FlickrPress::getKey('default_target'),
			FlickrPress::getKey('default_align'),
			FlickrPress::getKey('default_size'),
		));
	}

	public static function generateOptionForm() {
	        $alignes = array(
	                'None'=>'none',
	                'Left'=>'left',
	                'Center'=>'center',
	                'Right'=>'right',
	        );
	        $targets = array(
	                'None'=>'',
	                'New Window'=>'_blank',
        	);
		$sizes = array(
			'Square' => 'Square',
			'Thumbnail' => 'Thumbnail',
			'Small' => 'Small',
			'Medium' => 'Medium',
			'Medium 640' => 'Medium 640',
			'Large' => 'Large',
			'Original' => 'Original',
		);
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

		<h3><?php echo _e('Default Attachments') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><p><?php echo _e('Link Target') ?></p></th>
				<td>
					<?php foreach($targets as $label => $target) { ?>
						<?php $checked = FlickrPress::getDefaultTarget()==$target ? " checked='checked'" : '' ?>
						<input type="radio" name="<?php echo FlickrPress::getKey('default_target') ?>" id="target" value="<?php echo $target ?>" <?php echo $checked ?>/><label for="target"><?php echo $label ?></label>
					<?php } ?>
				</td>
			</tr>
                        <tr valign="top">
                                <th scope="row"><p><?php echo _e('Alignment') ?></p></th>
                                <td>
                                        <?php foreach($alignes as $label => $align) { ?>
						<?php $checked = FlickrPress::getDefaultAlign()==$align ? " checked='checked'" : '' ?>
                                                <input type="radio" name="<?php echo FlickrPress::getKey('default_align') ?>" id="alignment" value="<?php echo $align ?>" <?php echo $checked ?>/><label for="alignment"><?php echo $label ?></label>
                                        <?php } ?>
                                </td>
                        </tr>
                        <tr valign="top">
                                <th scope="row"><p><?php echo _e('Size') ?></p></th>
                                <td>
                                        <?php foreach($sizes as $label => $size) { ?>
						<?php $checked = FlickrPress::getDefaultSize()==$size ? " checked='checked'" : '' ?>
                                                <p><input type="radio" name="<?php echo FlickrPress::getKey('default_size') ?>" id="size-<?php echo $size ?>" value="<?php echo $size ?>" <?php echo $checked ?>/><label for="size-<?php echo $size ?>"><?php echo $label ?></label></p>
                                        <?php } ?>
                                </td>
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
