<?php
class FlickrPress {
	// constants	
	const NAME = 'FlickrPress';
	const PREFIX = 'wpfp_';
	const MEDIA_BUTTON_TYPE = 'flickr_media';
	const CACHE_TYPE = 'fs';
	const CACHE_CONNECTION = '/tmp/';

	private static $sizes = array('m','s','t','z','b');
	private function __construct() {
	}

	public static function init() {
		self::addEvents();
	}

	public static function getDir() {
		return dirname(__FILE__);
	}

	public static function getApiKey() {
		return get_option(self::getKey('api_key'));
	}

        public static function getApiSecret() {
                return get_option(self::getKey('api_secret'));
        }

	public static function getUserId() {
		return get_option(self::getKey('user_id'));
	}

	public static function getPluginUrl() {
		return plugins_url('wp-flickr-press');
	}

	public static function getKey($key) {
		return self::PREFIX . $key;
	}

	private static function addEvents() {
		// load action or filter
		require_once(self::getDir().'/FpPostEvent.php');
		add_action('media_buttons_context', array('FpPostEvent', 'addButtons'));
		add_filter(self::MEDIA_BUTTON_TYPE.'_upload_iframe_src', array('FpPostEvent', 'getUploadIframeSrc'));
		add_action('admin_head-post-new.php', array('FpPostEvent', 'loadScript'));

		require_once(self::getDir().'/FpAdminSettingEvent.php');
		add_action('admin_menu', array('FpAdminSettingEvent', 'addMenu'));
	}

	public static function getPhotoUrl($photo, $size='') {
		$size = in_array($size, self::$sizes) ? "_{$size}" : '';
		$img = "http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}{$size}.jpg";
		return $img;
	}

	public static function getPhotoPageUrl($photo) {
		$url = "http://www.flickr.com/photos/{$photo['owner']}/{$photo['id']}";
		return $url;
	}
}
?>
