<?php
require_once(dirname(__FILE__).'/FlickrPress.php');

class FpPostEvent {
	private function __construct() {
	}

	public static function addButtons() {
		$context = __('Upload/Insert %s');
		$context .= self::_media_button(__('Add flickr media', FlickrPress::TEXT_DOMAIN), FlickrPress::getPluginUrl().'/images/icon-flickr.gif', FlickrPress::MEDIA_BUTTON_TYPE);
		return $context;
	}
	
	public static function loadScripts() {
		$html = '';
		$html = '<script src="'.FlickrPress::getPluginUrl().'/js/media_upload.js'.'" type="text/javascript"></script>'."\n";
		echo $html;
	}

	public static function getUploadIframeSrc($uploadIframeSrc) {
		if (FlickrPress::getDefaulSearchType() == 'thumbnail') {
			return FlickrPress::getPluginUrl().'/'.$uploadIframeSrc.'&mode=search_thumbnail';
		} else {
			return FlickrPress::getPluginUrl().'/'.$uploadIframeSrc.'&mode=search';
		}
	}

	private static function _media_button($title, $icon, $type) {
	        return "<a href='" . esc_url( get_upload_iframe_src($type) ) . "' id='add_$type' class='thickbox' title='$title'><img src='" . esc_url( $icon ) . "' alt='$title' /></a>";
	}
}

?>
