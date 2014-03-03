<?php
require_once(dirname(__FILE__).'/FlickrPress.php');

class FpPostEvent {
	private function __construct() {
	}

    public static function loadJSStrings($strings,  $post) {
        $strings['wpfpTitle'] = __('FlickrPress', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchTypeFilterRecent'] = __('Recent', FlickrPress::TEXT_DOMAIN);
        $strings['wpfpSearchTypeFilterPhotosets'] = __('Photosets', FlickrPress::TEXT_DOMAIN);
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
        $html = <<< HTML
<div style="display:none">
    <input type="hidden" id="wpfp_api_key" value="$apiKey"/>
    <input type="hidden" id="wpfp_api_secret" value="$apiSecret"/>
    <input type="hidden" id="wpfp_user_id" value="$userId"/>
    <input type="hidden" id="wpfp_oauth_token" value="$oauthToken"/>
    <input type="hidden" id="wpfp_enable_path_alias" value="$enablePathAlias"/>
<div>
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
		$html .= '<link rel="stylesheet" href="'.FlickrPress::getPluginUrl().'/css/admin_post.css" type="text/css" media="all" />';
		echo $html;
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
