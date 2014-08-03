<?php
require_once(dirname(__FILE__).'/FlickrPress.php');

class FpPostEvent {
	private function __construct() {
	}

    public static function loadJSStrings($strings,  $post) {
        $strings['wpfpTitle'] = __('FlickrPress', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchButton'] = __('Search', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchTypeFilterRecent'] = __('Recent', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchTypeFilterAlbum'] = __('Album', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchTypeFilterAdvanced'] = __('Advanced', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchSortFilterPostedASC'] = __('Posted ASC', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchSortFilterPostedDESC'] = __('Posted DESC', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchSortFilterTakenASC'] = __('Taken ASC', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchSortFilterTakenDESC'] = __('Taken DESC', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchSortFilterInterestingnessASC'] = __('Interestingness ASC', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchSortFilterInterestingnessDESC'] = __('Interestingness DESC', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchTagFilterPlaceholder'] = __('Tags', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchKeywordFilterPlaceholder'] = __('Keyword', FlickrPress::TEXT_DOMAIN);
        return $strings;
    }

    public static function loadUIScripts() {
        wp_enqueue_style("wpfp", FlickrPress::getPluginUrl("css/media-views.css"), array(), FlickrPress::VERSION);
        wp_enqueue_script('jquery.md5', FlickrPress::getPluginUrl('js/jquery.md5.js'), array(), FlickrPress::VERSION);
        wp_enqueue_script('jquery.flickr-client', FlickrPress::getPluginUrl('js/jquery.flickr-client.js'), array(), FlickrPress::VERSION);
        wp_enqueue_script('wpfp', FlickrPress::getPluginUrl('js/media-views.js'), array('media-views'), false, FlickrPress::VERSION);
    }

    public static function loadJSBridgeParams() {
        $apiKey          = FlickrPress::getApiKey();
        $apiSecret       = FlickrPress::getApiSecret();
        $userId          = FlickrPress::getUserId();
        $oauthToken      = FlickrPress::getOAuthToken();
        $enablePathAlias = FlickrPress::enablePathAlias() ? '1' : '0';
        $defaultLink     = FlickrPress::getDefaultLink();
        $defaultTarget   = FlickrPress::getDefaultTarget();
        $defaultSize     = FlickrPress::getDefaultSize();
        $defaultAlign    = FlickrPress::getDefaultAlign();
        $defaultFileURLSize = FlickrPress::getDefaultFileURLSize();
        $insertTemplate  = FlickrPress::getInsertTemplate();
        $html = <<< HTML
<div style="display:none" id="wpfp_params"
    data-api_key="$apiKey"
    data-api_secret="$apiSecret"
    data-user_id="$userId"
    data-oauth_token="$oauthToken"
    data-enable_path_alias="$enablePathAlias"
    data-default_link="$defaultLink"
    data-default_target="$defaultTarget"
    data-default_size="$defaultSize"
    data-default_align="$defaultAlign"
    data-default_file_url_size="$defaultFileURLSize"
    data-insert_template="$insertTemplate"
/>
HTML;
        echo $html;
    }

	public static function addButtons() {
		echo self::_media_button(__('Add flickr media', FlickrPress::TEXT_DOMAIN), FlickrPress::getPluginUrl().'/images/icon-flickr.gif', FlickrPress::MEDIA_BUTTON_TYPE);
	}
	
	public static function addButtonsFullScreen($buttons) {
		$buttons['wpfp'] = array(
			'title' => __('Insert/Flickr Media'),
			'onclick' => "jQuery('#add_flickr_media').click();",
			'both' => true,
		);
		return $buttons;
	}

	public static function loadScripts() {
		$html = '';
		$html .= '<script src="'.FlickrPress::getPluginUrl().'/js/media_upload.js'.'" type="text/javascript"></script>'."\n";
		$html .= '<link rel="stylesheet" href="'.FlickrPress::getPluginUrl().'/css/admin_post.css" type="text/css" media="all" />' . "\n";
		echo $html;

        self::printTemplate();
	}

    public static function printTemplate() {
        $linkTos= array(
            'None'    =>'urlnone',
            'File URL'=>'urlfile',
            'Page URL'=>'urlpage',
        );
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

?>
    <script type="text/html" id="tmpl-wpfp-photo-container">
        <div class="result-container">
            <div class="result">
                <ul class="photos ui-sortable ui-sortable-disabled"></ul>
                <div class="buttons">
                    <div class="error">SEARCH ERROR. Please retry.</div>
                    <img src="<?php echo FlickrPress::getPluginUrl() ?>/images/ajax-loader.gif" class="loader"/>
                    <button class="more-btn button button-primary">More</button>
                </div>
            </div>
        </div>
    </script>
    <script type="text/html" id="tmpl-wpfp-photo-result">
        <# _.each(data.photos.photo, function(photo, i){ #>
            <li class="photo" data-idx="{{data.fp.lastIndex + i}}">
                <div class="thumbnail-container">
                    <div class="thumbnail">
                        <img src="{{photo["url_" + data.fp.thumbnailSize]}}"/>
                    </div>
                    <a class="order-container" href="#"><div class="order"></div></a>
                </div>
            </li>
        <# }); #>
    </script>
    <script type="text/html" id="tmpl-wpfp-photo-detail">
        <h3>PHOTO DETAIL</h3>
        <p>{{data.title}}</p>

        <label class="setting">
            <span>Link To</span>
            <select name="to">
                <?php foreach($linkTos as $label => $to) { ?>
                <option value="<?php echo $to ?>" <# if("<?php echo $to ?>" == "url"+data.params.defaultLink){ #>selected="selected"<# } #>><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></option>
                <?php } ?>
            </select>
        </label>

        <label class="setting">
            <span>Alignment</span>
            <select name="alignment">
                <?php foreach($alignes as $label => $align) { ?>
                <option value="<?php echo $align ?>" <# if("<?php echo $align ?>" == data.params.defaultAlign){ #>selected="selected"<# } #>><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></option>
                <?php } ?>
            </select>
        </label>

        <label class="setting">
            <span>Link Target</span>
            <select name="target">
                <?php foreach($targets as $label => $target) { ?>
                <option value="<?php echo $target ?>" <# if("<?php echo $target ?>" == data.params.defaultTarget){ #>selected="selected"<# } #>><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></option>
                <?php } ?>
            </select>
        </label>

        <label class="setting">
            <span>Size</span>
            <select name="size">
                <# _.each(data.fp.size_keys, function(size){ #>
                <# if (data["url_"+size]) { #>
                <option value="{{size}}" <# if(size == data.params.defaultSize){ #>selected="selected"<# } #>>{{ data.fp.size_labels[size] + " (" + data["width_"+size] + "x" + data["height_"+size] + ")" }}</option>
                <# } #> 
                <# }); #> 
            </select>
        </label>

    </script>
<?php
    }

	public static function getUploadIframeSrc($uploadIframeSrc) {
		if (FlickrPress::getDefaulSearchType() == 'thumbnail') {
			return $uploadIframeSrc.'&mode=search_thumbnail';
		} else {
			return $uploadIframeSrc.'&mode=search';
		}
	}

	private static function _media_button($title, $icon, $type) {
	        return "<a href='" . esc_url( get_upload_iframe_src($type) ) . "' id='add_$type' class='thickbox' title='$title'><img src='" . esc_url( $icon ) . "' alt='$title' /></a>";
	}

	public static function mediaUploadFlickrMedia() {
		require_once dirname(__FILE__) . '/media-upload.php';	
	}
}

?>
