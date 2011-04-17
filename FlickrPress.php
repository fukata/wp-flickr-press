<?php
require_once(dirname(__FILE__).'/libs/phpflickr/phpFlickr.php');

class FlickrPress {
	// constants	
	const NAME = 'FlickrPress';
	const PREFIX = 'wpfp_';
	const MEDIA_BUTTON_TYPE = 'flickr_media';

	private static $flickr;

	private static $sizes = array('m','s','t','z','b');
	private function __construct() {
	}

	public static function init() {
		self::addEvents();

		self::$flickr = new phpFlickr(FlickrPress::getApiKey(), FlickrPress::getApiSecret());
		if (self::isCacheEnabled()) {
			self::$flickr->enableCache(FlickrPress::getCacheType(), FlickrPress::getCacheConnection());
		}
	}

	public static function clearCache() {
		$cacheDir = self::getCacheConnection();
		if (strlen($cacheDir)==0 || !is_writeable($cacheDir)) {
			return;
		}

		$dir = dir($cacheDir);
		while ( ($file=$dir->read()) !== false ) {
			if (preg_match('/^.*\.cache$/', $file)) {
				unlink("{$cacheDir}{$file}");
			}
		}
	}

	public static function isCacheEnabled() {
		return true;
	}

	public static function getClient() {
		return self::$flickr;
	}

	public static function getDir() {
		return dirname(__FILE__);
	}

	public static function getCacheType() {
		return 'fs';
	}

	public static function getCacheConnection() {
		return dirname(__FILE__).'/cache/';
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

	public static function getUsername() {
		return get_option(self::getKey('username'));
	}

	public static function getOAuthToken() {
		return get_option(self::getKey('oauth_token'));
	}

	public static function getPluginUrl() {
		return plugins_url('wp-flickr-press');
	}

	public static function getDefaultTarget() {
		return get_option(self::getKey('default_target'), '');
	}

	public static function getDefaultAlign() {
		return get_option(self::getKey('default_align'), 'none');
	}

	public static function getDefaultSize() {
		return get_option(self::getKey('default_size'), 'Medium');
	}

	public static function getInsertTemplate() {
		return get_option(self::getKey('insert_template'), '[img]');
	}

	public static function getDefaultSort() {
		return get_option(self::getKey('default_sort'), 'date-posted-desc');
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

	public static function getPhotoPageUrl($photo, $photos) {
		$id = $photo['id'];
		$owner = isset($photo['owner']) ? $photo['owner'] : false;
		if (!$owner && isset($photos['owner'])) {
			$owner = $photos['owner'];
		}

		$url = "http://www.flickr.com/photos/$owner/$id";
		return $url;
	}
}
?>
