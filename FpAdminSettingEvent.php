<?php
class FpAdminSettingEvent {
	private function __construct() {}

	public static function addMenu() {
		$page = add_options_page(FlickrPress::NAME.' Option', FlickrPress::NAME, 8, __FILE__, array('FpAdminSettingEvent','generateOptionForm'));
	}

	public static function addWhitelistOptions($whitelist_options) {
		$whitelist_options['wpfp'] = array(
            FlickrPress::getKey('api_key'),
            FlickrPress::getKey('api_secret'),
            FlickrPress::getKey('user_id'),
            FlickrPress::getKey('username'),
            FlickrPress::getKey('oauth_token'),
            FlickrPress::getKey('default_target'),
            FlickrPress::getKey('default_align'),
            FlickrPress::getKey('default_size'),
            FlickrPress::getKey('insert_template'),
            FlickrPress::getKey('default_sort'),
            FlickrPress::getKey('quick_settings'),
            FlickrPress::getKey('default_search_type'),
            FlickrPress::getKey('default_link_rel'),
            FlickrPress::getKey('default_link_class'),
            FlickrPress::getKey('extend_link_properties'),
		);
		return $whitelist_options;
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
	        $sorts = array(
                'Posted ASC' => 'date-posted-asc',
                'Posted DESC' => 'date-posted-desc',
                'Taken ASC' => 'date-taken-asc',
                'Taken DESC' => 'date-taken-desc',
                'Interestingness ASC' => 'interestingness-asc',
                'Interestingness DESC' => 'interestingness-desc',
	        );
	        $searchTypes = array(
	        	'Search(List)' => 'list',
	        	'Search(Thumbnail)' => 'thumbnail',
	        );
?>
<script type="text/javascript"><!--
(function($){
	$(function(){
		$('#fp-oauth-token-btn').bind('click', function(){
			window.open('<?php echo esc_url(FlickrPress::getPluginUrl().'/flickr_oauth.php') ?>', 'flikcr_oauth', 'width=800,height=600,menubar=no, toolbar=no, scrollbars=yes');
		});

		$('#save_extend_link_property').bind('click', function(){
			var idx = parseInt($('#orig_extend_link_property_idx').val(), 10);
			var properties = JSON.parse($('#extend_link_properties_json').val());
			var prop = {
				"title": encodeURI($('#extend_link_property_title').val()),
				"rel": encodeURI($('#extend_link_property_rel').val()),
				"clazz": encodeURI($('#extend_link_property_clazz').val())
			};
			if (isNaN(idx)) {
				properties.push(prop);
			} else {
				properties[idx] = prop;
			}
			$('#extend_link_properties_json').val(JSON.stringify(properties));
			
			$('#extend_link_property_title').val('');
			$('#extend_link_property_rel').val('');
			$('#extend_link_property_clazz').val('');
			$('#orig_extend_link_property_idx').val('');
			refreshProperties();
			return false;
		});
		$('#remove_extend_link_property').bind('click', function(){
			var idx = parseInt($('#orig_extend_link_property_idx').val(), 10);
			if (isNaN(idx)) {
				return false;
			}
			
			var properties = JSON.parse($('#extend_link_properties_json').val());
			properties.splice(idx, 1);
			$('#extend_link_property_idx').html('');
			$('#extend_link_property_title').val('');
			$('#extend_link_property_rel').val('');
			$('#extend_link_property_clazz').val('');
			$('#orig_extend_link_property_idx').val('');
			$('#extend_link_properties_json').val(JSON.stringify(properties));
			refreshProperties();

			return false;
		});
		$('#clear_extend_link_property').bind('click', function(){
			$('#extend_link_property_idx').html('');
			$('#extend_link_property_title').val('');
			$('#extend_link_property_rel').val('');
			$('#extend_link_property_clazz').val('');
			$('#orig_extend_link_property_idx').val('');

			return false;
		});
		$('#current_extend_link_properties > li > a.link_property').live('click', function(){
			var $self = $(this);
			$('#extend_link_property_idx').html( $self.attr('data-idx') );
			$('#extend_link_property_title').val( decodeURI($self.attr('data-title')) );
			$('#extend_link_property_rel').val( decodeURI($self.attr('data-rel')) );
			$('#extend_link_property_clazz').val( decodeURI($self.attr('data-clazz')) );
			$('#orig_extend_link_property_idx').val( $self.attr('data-idx') );
			console.log($self.attr('data-idx'));

			return false;
		});
		function refreshProperties() {
			var properties = JSON.parse($('#extend_link_properties_json').val());
			var $current = $('#current_extend_link_properties').empty();
			for (var i=0; i<properties.length; i++) {
				var prop = properties[i];
				var $li = $(document.createElement('li'));
				var $a = $('<a href="javascript:void(0)" class="link_property">[' + decodeURI(prop['title']) + '] Rel=' + decodeURI(prop['rel']) + ', Class=' + decodeURI(prop['clazz']) + '</a>');
				$a.attr({
					'data-idx': i,
					'data-title': prop['title'],
					'data-rel': prop['rel'],
					'data-clazz': prop['clazz']
				});
				$li.html($a);
				$current.append($li);
			}
		}
		refreshProperties();
	});
})(jQuery);
function callback_oauth(token) {
	jQuery('#fp-oauth-token').html(token.token);
	jQuery('#fp-oauth-token-hid').val(token.token);
	jQuery('#fp-user-id').html(token.user.nsid);
	jQuery('#fp-user-id-hid').val(token.user.nsid);
	jQuery('#fp-username').html(token.user.username);
	jQuery('#fp-username-hid').val(token.user.username);
	jQuery('#fp-oauth-update').html("<?php echo __('Last Update: ', FlickrPress::TEXT_DOMAIN) ?>"+new Date()+"<br/><p style=\"color:#f00\"><strong><?php echo __('Not yet been update. Please Update.', FlickrPress::TEXT_DOMAIN)?></strong><p>");
}

// --></script>
<div class="wrap">
        <h2><?php echo FlickrPress::NAME ?></h2>
        <form method="post" action="options.php">
                <?php wp_nonce_field('wpfp-options'); ?>
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="option_page" value="wpfp" />
                <table class="form-table">
                        <tr valign="top">
                                <th scope="row">
                                        <p><?php echo __('API KEY/Secret', FlickrPress::TEXT_DOMAIN) ?></p>
					<p><a href="http://www.flickr.com/services/api/misc.api_keys.html" target="_blank">Flickr Services</a></p>
                                </th>
                                <td>
					<p><?php echo __('API KEY:', FlickrPress::TEXT_DOMAIN) ?><br/><input type="text" name="<?php echo FlickrPress::getKey('api_key') ?>" value="<?php echo FlickrPress::getApiKey() ?>" size="70" />
					<p><?php echo __('Secret:', FlickrPress::TEXT_DOMAIN) ?><br/><input type="text" name="<?php echo FlickrPress::getKey('api_secret') ?>" value="<?php echo FlickrPress::getApiSecret() ?>" size="70" />
				</td>
                        </tr>
                        <tr valign="top">
                                <th scope="row">
                                        <p><?php echo __('OAuth Token', FlickrPress::TEXT_DOMAIN) ?></p>
                                </th>
                                <td>
					<input id="fp-oauth-token-hid" type="hidden" name="<?php echo FlickrPress::getKey('oauth_token') ?>" value="<?php echo FlickrPress::getOAuthToken() ?>" />
					<input id="fp-user-id-hid" type="hidden" name="<?php echo FlickrPress::getKey('user_id') ?>" value="<?php echo FlickrPress::getUserId() ?>" />
					<input id="fp-username-hid" type="hidden" name="<?php echo FlickrPress::getKey('username') ?>" value="<?php echo FlickrPress::getUsername() ?>" />
					<p>UserID: <span id="fp-user-id"><?php echo FlickrPress::getUserId() ?></span></p>
					<p>Username: <span id="fp-username"><?php echo FlickrPress::getUsername() ?></span></p>
					<p>Token: <span id="fp-oauth-token"><?php echo FlickrPress::getOAuthToken() ?></span></p>
					<p><a href="javascript:void(0)" class="button" id="fp-oauth-token-btn"><?php echo __('Update OAuth Token', FlickrPress::TEXT_DOMAIN) ?></a></p>
					<p id="fp-oauth-update"><p>
				</td>
                        </tr>

                </table>

		<h3><?php echo __('Default Attachments', FlickrPress::TEXT_DOMAIN) ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><p><?php echo __('Link Target', FlickrPress::TEXT_DOMAIN) ?></p></th>
				<td>
					<?php foreach($targets as $label => $target) { ?>
						<?php $checked = FlickrPress::getDefaultTarget()==$target ? " checked='checked'" : '' ?>
						<input type="radio" name="<?php echo FlickrPress::getKey('default_target') ?>" id="target-<?php echo $target ?>" value="<?php echo $target ?>" <?php echo $checked ?>/><label for="target-<?php echo $target ?>"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
					<?php } ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><p><?php echo __('Alignment', FlickrPress::TEXT_DOMAIN) ?></p></th>
				<td>
					<?php foreach($alignes as $label => $align) { ?>
					<?php $checked = FlickrPress::getDefaultAlign()==$align ? " checked='checked'" : '' ?>
					<input type="radio" name="<?php echo FlickrPress::getKey('default_align') ?>" id="alignment-<?php echo $align ?>" value="<?php echo $align ?>" <?php echo $checked ?>/><label for="alignment-<?php echo $align ?>"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
					<?php } ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><p><?php echo __('Size', FlickrPress::TEXT_DOMAIN) ?></p></th>
				<td>
					<?php foreach(FlickrPress::$SIZE_LABELS as $size => $label) { ?>
					<?php $checked = FlickrPress::getDefaultSize()==$size ? " checked='checked'" : '' ?>
					<p><input type="radio" name="<?php echo FlickrPress::getKey('default_size') ?>" id="size-<?php echo $size ?>" value="<?php echo $size ?>" <?php echo $checked ?>/><label for="size-<?php echo $size ?>"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label></p>
					<?php } ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><p><?php echo __('Default Link Rel and Class Property', FlickrPress::TEXT_DOMAIN) ?></p></th>
				<td>
					<p><?php echo __('Rel:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="<?php echo FlickrPress::getKey('default_link_rel') ?>" value="<?php echo FlickrPress::getDefaultLinkRel() ?>" /></p>
					<p><?php echo __('Class:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="<?php echo FlickrPress::getKey('default_link_class') ?>" value="<?php echo FlickrPress::getDefaultLinkClass() ?>" /></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><p><?php echo __('Extend Link Rel and Class Property', FlickrPress::TEXT_DOMAIN) ?></p></th>
				<td>
					<textarea name="<?php echo FlickrPress::getKey('extend_link_properties')?>" id="extend_link_properties_json" style="display: none;" ><?php echo FlickrPress::getExtendLinkPropertiesJson()?></textarea>
					<input type="text" name="orig_extend_link_property_idx" value="" id="orig_extend_link_property_idx" style="display: none;" />
					<p><?php echo __('ID:', FlickrPress::TEXT_DOMAIN) ?><span id="extend_link_property_idx"></span></p>
					<p><?php echo __('Title:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="extend_link_property_title" value="" id="extend_link_property_title" /></p>
					<p><?php echo __('Rel:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="extend_link_property_rel" value="" id="extend_link_property_rel" /></p>
					<p><?php echo __('Class:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="extend_link_property_clazz" value="" id="extend_link_property_clazz" /></p>
					<p>
						<a href="javascript:void(0)" class="button" id="save_extend_link_property"><?php echo __('Save', FlickrPress::TEXT_DOMAIN) ?></a>
						<a href="javascript:void(0)" class="button" id="remove_extend_link_property"><?php echo __('Remove', FlickrPress::TEXT_DOMAIN) ?></a>
						<a href="javascript:void(0)" class="button" id="clear_extend_link_property"><?php echo __('Clear', FlickrPress::TEXT_DOMAIN) ?></a>
					</p>
					<p>
						<?php echo __('Current Available Extend Link Rel and Class', FlickrPress::TEXT_DOMAIN) ?>
						<ol id="current_extend_link_properties"></ol>
					</p>
				</td>
			</tr>
		</table>
		
		<h3><?php echo __('Advanced Options', FlickrPress::TEXT_DOMAIN) ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<p><?php echo __('Insert Template', FlickrPress::TEXT_DOMAIN) ?></p>
					<h4><?php echo __('Avalable Options', FlickrPress::TEXT_DOMAIN) ?></h4>
					<p>[img]: Image Tag</p>
					<p>[title]: Image Title</p>
				</th>
				<td>
					<textarea name="<?php echo FlickrPress::getKey('insert_template') ?>" cols="70" rows="10"><?php echo FlickrPress::getInsertTemplate() ?></textarea>
					<p><?php echo __('If you put a newline at the beginning or end, &lt;br/&gt; Please write tags.', FlickrPress::TEXT_DOMAIN) ?></p>
					<p><?php echo __('However, &lt;br/&gt; if there is a line break before and after the tag, I been wrapped them too.', FlickrPress::TEXT_DOMAIN) ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<p><?php echo __('Default Sort', FlickrPress::TEXT_DOMAIN) ?></p>
				</th>
				<td>
					<select name="<?php echo FlickrPress::getKey('default_sort') ?>">
					<?php foreach ($sorts as $label => $sort) { ?>
						<option value="<?php echo $sort ?>" <?php if ($sort==FlickrPress::getDefaultSort()) {echo "selected='selected'";} ?>><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<p><?php echo __('Quick Settings', FlickrPress::TEXT_DOMAIN) ?></p>
				</th>
				<td>
					<p><?php echo __('Enable:', FlickrPress::TEXT_DOMAIN) ?><input type="checkbox" name="<?php echo flickrpress::getkey('quick_settings') ?>" value="1" <?php if (FlickrPress::getQuickSettings()=='1') { echo "checked='checked'"; } ?>/></p>
					<p><?php echo __('When enabled, a dialog will appear when you click for a set of check boxes for multiple insertion.', FlickrPress::TEXT_DOMAIN) ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<p><?php echo __('Search Type', FlickrPress::TEXT_DOMAIN) ?></p>
				</th>
				<td>
					<p>
					<?php foreach ($searchTypes as $label => $val) { ?>
						<input type="radio" name="<?php echo FlickrPress::getKey('default_search_type') ?>" id="search_type_<?php echo $val ?>" value="<?php echo $val ?>" <?php echo FlickrPress::getDefaulSearchType()==$val ? 'checked="checked"' : ''; ?>/><label for="search_type_<?php echo $val ?>"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
					<?php } ?>
					</p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php echo __('Save Changes', FlickrPress::TEXT_DOMAIN) ?>" />
		</p>
	</form>
</div>

<?php
	}
}
?>
