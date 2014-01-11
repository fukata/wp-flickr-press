<?php
require_once(dirname(__FILE__).'/FlickrPress.php');

class FpPostEvent {
	private function __construct() {
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
