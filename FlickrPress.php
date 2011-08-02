<?php
require_once(dirname(__FILE__).'/libs/phpflickr/phpFlickr.php');

class FlickrPress {
	// constants	
	const NAME = 'FlickrPress';
	const PREFIX = 'wpfp_';
	const MEDIA_BUTTON_TYPE = 'flickr_media';
	const TEXT_DOMAIN = 'wp-flickr-press';

	private static $flickr;
	
	public static $SIZE_LABELS = array(
		'sq' => 'Square',
		't' => 'Thumbnail',
		's' => 'Small',
		'm' => 'Medium',
		'l' => 'Large',
		'o' => 'Original',
	);
	public static $SIZES = array(
		'sq' => 'url_sq',
		't' => 'url_t',
		's' => 'url_s',
		'm' => 'url_m',
		'l' => 'url_l',
		'o' => 'url_o',
	);
	private function __construct() {
	}

	public static function init() {
		self::loadLanguages();
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

	public static function getDefaulSearchType() {
		return get_option(self::getKey('default_search_type'), 'list');
	}

	public static function getInsertTemplate() {
		return get_option(self::getKey('insert_template'), '[img]');
	}

	public static function getDefaultSort() {
		return get_option(self::getKey('default_sort'), 'date-posted-desc');
	}

	public static function getQuickSettings() {
		return get_option(self::getKey('quick_settings'), 0);
	}

	public static function getDefaultLinkRel() {
		return get_option(self::getKey('default_link_rel'), '');
	}

	public static function getDefaultLinkClass() {
		return get_option(self::getKey('default_link_class'), '');
	}

	public static function getExtendLinkPropertiesJson() {
		return get_option(self::getKey('extend_link_properties'), '[]');
	}

	public static function getExtendLinkPropertiesArray() {
		$properties = json_decode( self::getLinkPropertiesJson() );
		return is_array($properties) ? $properties : array();
	}
	
	public static function getKey($key) {
		return self::PREFIX . $key;
	}

	private static function addEvents() {
		// load action or filter
		require_once(self::getDir().'/FpPostEvent.php');
		add_action('media_buttons_context', array('FpPostEvent', 'addButtons'));
		add_filter(self::MEDIA_BUTTON_TYPE.'_upload_iframe_src', array('FpPostEvent', 'getUploadIframeSrc'));
		add_action('admin_head-post.php', array('FpPostEvent', 'loadScripts'));
		add_action('admin_head-post-new.php', array('FpPostEvent', 'loadScripts'));

		require_once(self::getDir().'/FpAdminSettingEvent.php');
		add_action('admin_menu', array('FpAdminSettingEvent', 'addMenu'));
		add_filter('whitelist_options', array('FpAdminSettingEvent', 'addWhitelistOptions'));
	}

	public static function getPhotoUrl($photo, $size='m') {
		return $photo[self::$SIZES[$size]];
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
	
	public static function loadLanguages() {
		load_plugin_textdomain(self::TEXT_DOMAIN, false, 'wp-flickr-press/languages');
	}
}
?>
