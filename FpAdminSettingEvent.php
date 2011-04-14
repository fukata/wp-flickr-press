<?php
class FpAdminSettingEvent {
	private function __construct() {}

	public static function addMenu() {
		$page = add_options_page(FlickrPress::NAME.' Option', FlickrPress::NAME, 8, __FILE__, array('FpAdminSettingEvent','generateOptionForm'));
	}
	
	private function getPageOptions() {
		return implode(',', array(
			FlickrPress::getKey('api_key'),
			FlickrPress::getKey('api_secret'),
			FlickrPress::getKey('user_id'),
			FlickrPress::getKey('username'),
			FlickrPress::getKey('oauth_token'),
			FlickrPress::getKey('default_target'),
			FlickrPress::getKey('default_align'),
			FlickrPress::getKey('default_size'),
			FlickrPress::getKey('insert_template'),
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
<script type="text/javascript"><!--
(function($){
	$(function(){
		$('#fp-oauth-token-btn').bind('click', function(){
			window.open('<?php echo esc_url(FlickrPress::getPluginUrl().'/flickr_oauth.php') ?>', 'flikcr_oauth', 'width=800,height=600,menubar=no, toolbar=no, scrollbars=yes');
		});
	});
})(jQuery);
function callback_oauth(token) {
	jQuery('#fp-oauth-token').html(token.token);
	jQuery('#fp-oauth-token-hid').val(token.token);
	jQuery('#fp-user-id').html(token.user.nsid);
	jQuery('#fp-user-id-hid').val(token.user.nsid);
	jQuery('#fp-username').html(token.user.username);
	jQuery('#fp-username-hid').val(token.user.username);
	jQuery('#fp-oauth-update').html("<?php _e('Last Update: ') ?>"+new Date()+"<br/><p style=\"color:#f00\"><strong>Not yet been update. Please Update.</strong><p>");
}

// --></script>
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
                                        <p><?php echo _e('OAuth Token') ?></p>
                                </th>
                                <td>
					<input id="fp-oauth-token-hid" type="hidden" name="<?php echo FlickrPress::getKey('oauth_token') ?>" value="<?php echo FlickrPress::getOAuthToken() ?>" />
					<input id="fp-user-id-hid" type="hidden" name="<?php echo FlickrPress::getKey('user_id') ?>" value="<?php echo FlickrPress::getUserId() ?>" />
					<input id="fp-username-hid" type="hidden" name="<?php echo FlickrPress::getKey('username') ?>" value="<?php echo FlickrPress::getUsername() ?>" />
					<p>UserID: <span id="fp-user-id"><?php echo FlickrPress::getUserId() ?></span></p>
					<p>Username: <span id="fp-username"><?php echo FlickrPress::getUsername() ?></span></p>
					<p>Token: <span id="fp-oauth-token"><?php echo FlickrPress::getOAuthToken() ?></span></p>
					<p><a href="javascript:void(0)" class="button" id="fp-oauth-token-btn"><?php _e('Update OAuth Token') ?></a></p>
					<p id="fp-oauth-update"><p>
				</td>
                        </tr>

                </table>

		<h3><?php echo _e('Default Attachments') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><p><?php echo _e('Link Target') ?></p></th>
				<td>
					<?php foreach($targets as $label => $target) { ?>
						<?php $checked = FlickrPress::getDefaultTarget()==$target ? " checked='checked'" : '' ?>
						<input type="radio" name="<?php echo FlickrPress::getKey('default_target') ?>" id="target-<?php echo $target ?>" value="<?php echo $target ?>" <?php echo $checked ?>/><label for="target-<?php echo $target ?>"><?php echo $label ?></label>
					<?php } ?>
				</td>
			</tr>
                        <tr valign="top">
                                <th scope="row"><p><?php echo _e('Alignment') ?></p></th>
                                <td>
                                        <?php foreach($alignes as $label => $align) { ?>
						<?php $checked = FlickrPress::getDefaultAlign()==$align ? " checked='checked'" : '' ?>
                                                <input type="radio" name="<?php echo FlickrPress::getKey('default_align') ?>" id="alignment-<?php echo $align ?>" value="<?php echo $align ?>" <?php echo $checked ?>/><label for="alignment-<?php echo $align ?>"><?php echo $label ?></label>
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
		
		<h3><?php echo _e('Advanced Options') ?></h3>
		<table class="form-table">
                        <tr valign="top">
                                <th scope="row">
					<p><?php echo _e('Insert Template') ?></p>
					<h4><?php echo _e('Avalable Options') ?></h4>
					<p>[img]: Image Tag</p>
					<p>[title]: Image Title</p>
				</th>
                                <td>
					<textarea name="<?php echo FlickrPress::getKey('insert_template') ?>" cols="70" rows="10"><?php echo FlickrPress::getInsertTemplate() ?></textarea>
					<p>If you put a newline at the beginning or end, &lt;br/&gt; Please write tags.</p>
					<p>However, &lt;br/&gt; if there is a line break before and after the tag, I been wrapped them too.</p>
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
