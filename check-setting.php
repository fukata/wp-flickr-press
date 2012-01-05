<?php
if (!is_admin()) {
	wp_die("Not in admin zone!");
}

$errors = array();

$cachePath = dirname(__FILE__) . '/cache/';
// check cache exists
if (!is_dir($cachePath)) {
	$errors[] = sprintf(__("Does not exists %s directory. Please create.", FlickrPress::TEXT_DOMAIN), $cachePath);
}

// check cache directory writable
if (!is_writable($cachePath)) {
	$errors[] = sprintf(__("No write permission to the %s directory. Please grant the permission to write.", FlickrPress::TEXT_DOMAIN), $cachePath);
}

// check API Key, API Secret
$apiKey = FlickrPress::getApiKey();
$apiSecret = FlickrPress::getApiSecret();
if (empty($apiKey) || empty($apiSecret)) {
	$errors[] = __('API KEY and API SECRET is required. Please setting API KEY and API SECRET.', FlickrPress::TEXT_DOMAIN);
}

// check User ID
$userId = FlickrPress::getUserId();
if (empty($userId)) {
	$errors[] = __('USER ID is required. Please setting USER ID.', FlickrPress::TEXT_DOMAIN);
}

if (!empty($errors) && count($errors)>0) {
	$buffer = "";
	foreach ($errors as $error) {
		$buffer .= "<p>{$error}</p>";
	}
	wp_die($buffer);
}
?>
