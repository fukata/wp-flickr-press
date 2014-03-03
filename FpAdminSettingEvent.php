<?php
class FpAdminSettingEvent {
    private function __construct() {}

    public static function addMenu() {
        $page = add_options_page(FlickrPress::NAME.' Options', FlickrPress::NAME, 'manage_options', __FILE__, array('FpAdminSettingEvent','generateOptionForm'));
    }

    public static function addWhitelistOptions($whitelist_options) {
        $whitelist_options['wpfp'] = array(
            FlickrPress::getKey('api_key'),
            FlickrPress::getKey('api_secret'),
            FlickrPress::getKey('user_id'),
            FlickrPress::getKey('enable_path_alias'),
            FlickrPress::getKey('username'),
            FlickrPress::getKey('oauth_token'),
            FlickrPress::getKey('default_target'),
            FlickrPress::getKey('default_align'),
            FlickrPress::getKey('default_size'),
            FlickrPress::getKey('insert_template'),
            FlickrPress::getKey('default_sort'),
            FlickrPress::getKey('quick_settings'),
            FlickrPress::getKey('default_search_type'),
            FlickrPress::getKey('default_link'),
            FlickrPress::getKey('default_link_rel'),
            FlickrPress::getKey('default_link_class'),
            FlickrPress::getKey('default_file_url_size'),
            FlickrPress::getKey('extend_link_properties'),
            FlickrPress::getKey('extend_image_properties'),
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
        $('#copy_callback_url').click(function() {
            $(this).select();
        });

        $('#fp-oauth-token-btn').bind('click', function(){
            window.open('<?php echo esc_url(admin_url('admin.php?action=wpfp_flickr_oauth')) ?>', 'flikcr_oauth', 'width=800,height=600,menubar=no, toolbar=no, scrollbars=yes');
        });

        $('#fp-reset-oauth-token-btn').bind('click', function(){
            callback_oauth({token: "", user: { nsid: "", username: "" } });
        });

        // ===============================================
        // Extend Link Properties
        // ===============================================
        $('#save_extend_link_property').bind('click', function(){
            var idx = parseInt($('#orig_extend_link_property_idx').val(), 10);
            var properties = JSON.parse($('#extend_link_properties_json').val());
            var prop = {
                "title": _remove_invalid_link_chars($('#extend_link_property_title').val()),
                "rel": _remove_invalid_link_chars($('#extend_link_property_rel').val()),
                "clazz": _remove_invalid_link_chars($('#extend_link_property_clazz').val())
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
            refresh_properties();
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
            refresh_properties();
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
        $('#current_extend_link_properties > li > a.property').live('click', function(){
            var $self = $(this);
            $('#extend_link_property_idx').html( $self.attr('data-idx') );
            $('#extend_link_property_title').val( $self.attr('data-title') );
            $('#extend_link_property_rel').val( $self.attr('data-rel') );
            $('#extend_link_property_clazz').val( $self.attr('data-clazz') );
            $('#orig_extend_link_property_idx').val( $self.attr('data-idx') );
            return false;
        });
        function _remove_invalid_link_chars(str) {
            return str.replace(/[^0-9a-zA-Z\[\]\s_\-]+/g,'');
        }
        function refresh_properties() {
            var properties = JSON.parse($('#extend_link_properties_json').val());
            var $current = $('#current_extend_link_properties').empty();
            for (var i=0; i<properties.length; i++) {
                var prop = properties[i];
                var $li = $(document.createElement('li'));
                var $a = $('<a href="javascript:void(0)" class="property">[' + prop['title'] + '] Rel=' + prop['rel'] + ', Class=' + prop['clazz'] + '</a>');
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

        // ===============================================
        // Extend Image Properties
        // ===============================================
        $('#save_extend_image_property').bind('click', function(){
            var idx = parseInt($('#orig_extend_image_property_idx').val(), 10);
            var properties = JSON.parse($('#extend_image_properties_json').val());
            var prop = {
                "title": _remove_invalid_link_chars($('#extend_image_property_title').val()),
                "clazz": _remove_invalid_link_chars($('#extend_image_property_clazz').val())
            };
            if (isNaN(idx)) {
                properties.push(prop);
            } else {
                properties[idx] = prop;
            }
            $('#extend_image_properties_json').val(JSON.stringify(properties));
            
            $('#extend_image_property_title').val('');
            $('#extend_image_property_clazz').val('');
            $('#orig_extend_image_property_idx').val('');
            refresh_image_properties();
            return false;
        });
        $('#remove_extend_image_property').bind('click', function(){
            var idx = parseInt($('#orig_extend_image_property_idx').val(), 10);
            if (isNaN(idx)) {
                return false;
            }
            
            var properties = JSON.parse($('#extend_image_properties_json').val());
            properties.splice(idx, 1);
            $('#extend_image_property_idx').html('');
            $('#extend_image_property_title').val('');
            $('#extend_image_property_clazz').val('');
            $('#orig_extend_image_property_idx').val('');
            $('#extend_image_properties_json').val(JSON.stringify(properties));
            refresh_image_properties();
            return false;
        });
        $('#clear_extend_image_property').bind('click', function(){
            $('#extend_image_property_idx').html('');
            $('#extend_image_property_title').val('');
            $('#extend_image_property_clazz').val('');
            $('#orig_extend_image_property_idx').val('');
            return false;
        });
        $('#current_extend_image_properties > li > a.property').live('click', function(){
            var $self = $(this);
            $('#extend_image_property_idx').html( $self.attr('data-idx') );
            $('#extend_image_property_title').val( $self.attr('data-title') );
            $('#extend_image_property_clazz').val( $self.attr('data-clazz') );
            $('#orig_extend_image_property_idx').val( $self.attr('data-idx') );
            return false;
        });
        function refresh_image_properties() {
            var properties = JSON.parse($('#extend_image_properties_json').val());
            var $current = $('#current_extend_image_properties').empty();
            for (var i=0; i<properties.length; i++) {
                var prop = properties[i];
                var $li = $(document.createElement('li'));
                var $a = $('<a href="javascript:void(0)" class="property">[' + prop['title'] + '] Class=' + prop['clazz'] + '</a>');
                $a.attr({
                    'data-idx': i,
                    'data-title': prop['title'],
                    'data-clazz': prop['clazz']
                });
                $li.html($a);
                $current.append($li);
            }
        }

        refresh_properties();
        refresh_image_properties();
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
        <h3><?php echo __('OAuth Information', FlickrPress::TEXT_DOMAIN) ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <p><?php echo __('API KEY/Secret', FlickrPress::TEXT_DOMAIN) ?></p>
                    <p><a href="http://www.flickr.com/services/api/misc.api_keys.html" target="_blank">Flickr Services</a></p>
                </th>
                <td>
                    <p><?php echo __('API KEY:', FlickrPress::TEXT_DOMAIN) ?><br/><input type="text" name="<?php echo FlickrPress::getKey('api_key') ?>" value="<?php echo FlickrPress::getApiKey() ?>" size="70" />
                    <p><?php echo __('Secret:', FlickrPress::TEXT_DOMAIN) ?><br/><input type="text" name="<?php echo FlickrPress::getKey('api_secret') ?>" value="<?php echo FlickrPress::getApiSecret() ?>" size="70" />
                    <p><?php echo __('Flickr App CallbackURL:', FlickrPress::TEXT_DOMAIN) ?><br/><input type="text" value="<?php echo admin_url('admin.php?action=wpfp_flickr_oauth_callback') ?>" size="70" id="copy_callback_url" />
                        <br/><? echo __('Please use the copy to the callback URL of the Flickr apps created.', FlickrPress::TEXT_DOMAIN) ?></p>
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
                    <p>
                        <a href="javascript:void(0)" class="button" id="fp-oauth-token-btn"><?php echo __('Update OAuth Token', FlickrPress::TEXT_DOMAIN) ?></a>
                        <a href="javascript:void(0)" class="button" id="fp-reset-oauth-token-btn"><?php echo __('Reset OAuth Token', FlickrPress::TEXT_DOMAIN) ?></a>
                    </p>
                    <p id="fp-oauth-update"><p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p><?php echo __('UserID Alias', FlickrPress::TEXT_DOMAIN) ?></p>
                </th>
                <td>
                    <p><?php echo __('Enable:', FlickrPress::TEXT_DOMAIN) ?><input type="checkbox" name="<?php echo FlickrPress::getKey('enable_path_alias') ?>" value="1" <?php if (FlickrPress::enablePathAlias()) { echo "checked='checked'"; } ?> /></p>
                    <p><?php echo __("Photo URL contains \"UserID\" the \"UserID Alias\" and replace.", FlickrPress::TEXT_DOMAIN) ?></p>
                </td>
            </tr>
        </table>

        <h3><?php echo __('Default Attachments', FlickrPress::TEXT_DOMAIN) ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><p><?php echo __('Link URL Type', FlickrPress::TEXT_DOMAIN) ?></p></th>
                <td>
                    <?php foreach(FlickrPress::$LINK_TYPE_LABELS as $type => $label) { ?>
                    <?php $checked = FlickrPress::getDefaultLink()==$type ? " checked='checked'" : '' ?>
                    <p><input type="radio" name="<?php echo FlickrPress::getKey('default_link') ?>" id="link-<?php echo $type?>" value="<?php echo $type ?>" <?php echo $checked ?>/><label for="link-<?php echo $type?>"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label></p>
                    <?php } ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><p><?php echo __('File URL Size', FlickrPress::TEXT_DOMAIN) ?></p></th>
                <td>
                    <?php foreach(FlickrPress::$SIZE_LABELS as $size => $label) { ?>
                    <?php $checked = FlickrPress::getDefaultFileURLSize()==$size ? " checked='checked'" : '' ?>
                    <p><input type="radio" name="<?php echo FlickrPress::getKey('default_file_url_size') ?>" id="file_url_size-<?php echo $size ?>" value="<?php echo $size ?>" <?php echo $checked ?>/><label for="file_url_size-<?php echo $size ?>"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label></p>
                    <?php } ?>
                </td>
            </tr>
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
                    <p><?php echo __('Available Charactors: 0-9a-zA-Z [] Space UnderScore Hyphen', FlickrPress::TEXT_DOMAIN) ?></p>
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
                    <p><?php echo __('Available Charactors: 0-9a-zA-Z [] Space UnderScore Hyphen', FlickrPress::TEXT_DOMAIN) ?></p>
                    <p>
                        <?php echo __('Current Available Extend Link Rel and Class', FlickrPress::TEXT_DOMAIN) ?>
                        <ol id="current_extend_link_properties"></ol>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><p><?php echo __('Extend Image Class Property', FlickrPress::TEXT_DOMAIN) ?></p></th>
                <td>
                    <textarea name="<?php echo FlickrPress::getKey('extend_image_properties')?>" id="extend_image_properties_json" style="display: none;" ><?php echo FlickrPress::getExtendImagePropertiesJson()?></textarea>
                    <input type="text" name="orig_extend_image_property_idx" value="" id="orig_extend_image_property_idx" style="display: none;" />
                    <p><?php echo __('ID:', FlickrPress::TEXT_DOMAIN) ?><span id="extend_image_property_idx"></span></p>
                    <p><?php echo __('Title:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="extend_image_property_title" value="" id="extend_image_property_title" /></p>
                    <p><?php echo __('Class:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="extend_image_property_clazz" value="" id="extend_image_property_clazz" /></p>
                    <p>
                        <a href="javascript:void(0)" class="button" id="save_extend_image_property"><?php echo __('Save', FlickrPress::TEXT_DOMAIN) ?></a>
                        <a href="javascript:void(0)" class="button" id="remove_extend_image_property"><?php echo __('Remove', FlickrPress::TEXT_DOMAIN) ?></a>
                        <a href="javascript:void(0)" class="button" id="clear_extend_image_property"><?php echo __('Clear', FlickrPress::TEXT_DOMAIN) ?></a>
                    </p>
                    <p><?php echo __('Available Charactors: 0-9a-zA-Z [] Space UnderScore Hyphen', FlickrPress::TEXT_DOMAIN) ?></p>
                    <p>
                        <?php echo __('Current Available Extend Image Class', FlickrPress::TEXT_DOMAIN) ?>
                        <ol id="current_extend_image_properties"></ol>
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
                    <p>[url]: Image URL</p>
                    <p>[null]: Null Character</p>
                </th>
                <td>
                    <textarea name="<?php echo FlickrPress::getKey('insert_template') ?>" cols="70" rows="10"><?php echo FlickrPress::getInsertTemplate() ?></textarea>
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
                    <p><?php echo __('Enable:', FlickrPress::TEXT_DOMAIN) ?><input type="checkbox" name="<?php echo FlickrPress::getKey('quick_settings') ?>" value="1" <?php if (FlickrPress::getQuickSettings()=='1') { echo "checked='checked'"; } ?>/></p>
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
