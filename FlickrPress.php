<?php

// Fix for symlinked plugins from
// http://wordpress.stackexchange.com/questions/15202/plugins-in-symlinked-directories
global $wp_flickr_press_file;
$wp_flickr_press_file = __FILE__;
if ( isset( $mu_plugin ) ) {
    $wp_flickr_press_file = $mu_plugin;
}
if ( isset( $network_plugin ) ) {
    $wp_flickr_press_file = $network_plugin;
}
if ( isset( $plugin ) ) {
    $wp_flickr_press_file = $plugin;
}

if ( ! file_exists(dirname($wp_flickr_press_file).'/libs/phpflickr/phpFlickr.php') ) {
	$wp_flickr_press_file = __FILE__;
}

require_once(dirname($wp_flickr_press_file).'/libs/phpflickr/phpFlickr.php');

class FlickrPress {
	// constants
	const VERSION = '1.9.16';
	const NAME = 'FlickrPress';
	const PREFIX = 'wpfp_';
	const MEDIA_BUTTON_TYPE = 'flickr_media';
	const TEXT_DOMAIN = 'wp-flickr-press';

	private static $flickr;
	
	public static $SIZE_LABELS = array(
		'sq' => 'Square 75 (75x75)',
		'q'  => 'Square 150 (150x150)',
		't'  => 'Thumbnail (100x75)',
		's'  => 'Small 240 (240x180)',
		'n'  => 'Small 320 (320x240)',
		'm'  => 'Medium 500 (500x375)',
		'z'  => 'Medium 640 (640x480)',
		'c'  => 'Medium 800 (800x600)',
		'l'  => 'Large (1024x768)',
		'h'  => 'Large (1600x1060)',
		'k'  => 'Large (2048x1356)',
		'o'  => 'Original',
	);
	public static $SIZES = array(
		'sq' => 'url_sq', // Square 75
		'q'  => 'url_q',  // Square 120
		't'  => 'url_t',  // Thumbnail
		's'  => 'url_s',  // Small 240
		'n'  => 'url_n',  // Small 320
		'm'  => 'url_m',  // Medium 500
		'z'  => 'url_z',  // Medium 640
		'c'  => 'url_c',  // Medium 800
		'l'  => 'url_l',  // Large
		'h'  => 'url_h',  // Large 1600
		'k'  => 'url_k',  // Large 2048
		'o'  => 'url_o',  // Original
	);
	public static $LINK_TYPE_LABELS = array(
		'none' => 'None',
		'file' => 'File URL',
		'page' => 'Page URL',
	);

	private function __construct() {
	}

	public static function init() {
		self::loadLanguages();
		self::addEvents();

		self::$flickr = new phpFlickr(FlickrPress::getApiKey(), FlickrPress::getApiSecret());
		if (self::getOAuthToken()) {
			self::$flickr->token = self::getOAuthToken();
		}
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
		global $wp_flickr_press_file;
		return plugin_dir_path($wp_flickr_press_file);
	}

	public static function getCacheType() {
		return 'fs';
	}

	public static function getCacheConnection() {
		global $wp_flickr_press_file;
		return plugin_dir_path($wp_flickr_press_file).'/cache/';
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

	public static function enablePathAlias() {
		return get_option(self::getKey('enable_path_alias')) == '1';
	}

	public static function getUsername() {
		return get_option(self::getKey('username'));
	}

	public static function getOAuthToken() {
		return get_option(self::getKey('oauth_token'));
	}

	public static function getPluginUrl() {
		global $wp_flickr_press_file;
		return plugins_url('', $wp_flickr_press_file );
	}

	public static function getDefaultTarget() {
		return get_option(self::getKey('default_target'), '');
	}

	public static function getDefaultAlign() {
		return get_option(self::getKey('default_align'), 'none');
	}

	public static function getDefaultSize() {
		return get_option(self::getKey('default_size'), 'm');
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

	public static function getDefaultLink() {
		return get_option(self::getKey('default_link'), 'page');
	}

	public static function getDefaultLinkValue($photo, $photos) {
		$linkType = self::getDefaultLink();
		$link = '';
		switch ($linkType) {
		case 'file':
			$link = self::getPhotoUrl($photo, self::getDefaultFileURLSize());
			break;
		case 'page':
			$link = self::getPhotoPageUrl($photo, $photos);
			break;
		}
		return $link;
	}

	public static function getDefaultLinkRel() {
		return get_option(self::getKey('default_link_rel'), '');
	}

	public static function getDefaultLinkClass() {
		return get_option(self::getKey('default_link_class'), '');
	}

	public static function getDefaultFileURLSize() {
		return get_option(self::getKey('default_file_url_size'), 'm');
	}

	public static function getExtendLinkPropertiesJson() {
		return get_option(self::getKey('extend_link_properties'), '[]');
	}

	public static function getExtendLinkPropertiesArray() {
		$properties = json_decode( self::getExtendLinkPropertiesJson() );
		return is_array($properties) ? $properties : array();
	}
	
	public static function getExtendImagePropertiesJson() {
		return get_option(self::getKey('extend_image_properties'), '[]');
	}

	public static function getExtendImagePropertiesArray() {
		$properties = json_decode( self::getExtendImagePropertiesJson() );
		return is_array($properties) ? $properties : array();
	}

	public static function getKey($key) {
		return self::PREFIX . $key;
	}

	private static function addEvents() {
		// load action or filter
		require_once(self::getDir().'/FpPostEvent.php');
		add_action('media_buttons', array('FpPostEvent', 'addButtons'), 100);
		add_action('media_upload_flickr_media', array('FpPostEvent', 'mediaUploadFlickrMedia'));
		add_filter('wp_fullscreen_buttons', array('FpPostEvent', 'addButtonsFullScreen'));
		add_filter(self::MEDIA_BUTTON_TYPE.'_upload_iframe_src', array('FpPostEvent', 'getUploadIframeSrc'));
		add_action('admin_head-post.php', array('FpPostEvent', 'loadScripts'));
		add_action('admin_head-post-new.php', array('FpPostEvent', 'loadScripts'));

		require_once(self::getDir().'/FpAdminSettingEvent.php');
		add_action('admin_menu', array('FpAdminSettingEvent', 'addMenu'));
		add_filter('whitelist_options', array('FpAdminSettingEvent', 'addWhitelistOptions'));

		// admin actions
		add_action('admin_action_wpfp_media_upload', array(__CLASS__, 'adminActionWpfpMediaUpload'));
		add_action('admin_action_wpfp_flickr_oauth', array(__CLASS__, 'adminActionWpfpFlickrOauth'));
		add_action('admin_action_wpfp_flickr_oauth_callback', array(__CLASS__, 'adminActionWpfpFlickrOauthCallback'));
	}

	public static function adminActionWpfpMediaUpload() {
		require_once(self::getDir().'/media-upload.php');
	}

	public static function adminActionWpfpFlickrOauth() {
		require_once(self::getDir().'/flickr_oauth.php');
	}

	public static function adminActionWpfpFlickrOauthCallback() {
		require_once(self::getDir().'/flickr_oauth_callback.php');
	}

	public static function getPhotoUrl($photo, $size='m') {
		if ( $size != 'o' && empty( $photo[self::$SIZES[$size]] ) ) {
			$keys = array_keys(self::$SIZES);
			$idx = array_search($size, $keys);
			for ($i=$idx+1; $i<count($keys); $i++) {
				$s = $keys[$i];
				if(!empty($photo[self::$SIZES[$s]])) {
					return self::getPhotoUrl($photo, $s);
				}
			}
			return '';
		} else {
			return $photo[self::$SIZES[$size]];
		}
	}

	public static function getPhotoPageUrl($photo, $photos) {
		$id = $photo['id'];
		$pathKey = self::enablePathAlias() ? 'pathalias' : 'owner';
		$owner = isset($photo[$pathKey]) ? $photo[$pathKey] : false;
		if (!$owner && isset($photos[$pathKey])) {
			$owner = $photos[$pathKey];
		}

		$url = "http://www.flickr.com/photos/$owner/$id";
		return $url;
	}
	
	public static function loadLanguages() {
		load_plugin_textdomain(self::TEXT_DOMAIN, false, 'wp-flickr-press/languages');
	}
}
?>
