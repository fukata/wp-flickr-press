<?php
$errors = array();
$apiKey = FlickrPress::getApiKey();
$apiSecret = FlickrPress::getApiSecret();
if (empty($apiKey) || empty($apiSecret)) {
	$errors[] = __('API KEY and API SECRET is required. Please setting API KEY and API SECRET.');
}

$userId = FlickrPress::getUserId();
if (empty($userId)) {
	$errors[] = __('USER ID is required. Please setting USER ID.');
}

if (!empty($errors) && count($errors)>0) {
	$buffer = "";
	foreach ($errors as $error) {
		$buffer .= "<p>{$error}</p>";
	}
	wp_die($buffer);
}
?>
