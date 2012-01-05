<?php
if (!is_admin()) {
	wp_die("Not in admin zone!");
}

$errors = array();
$apiKey = FlickrPress::getApiKey();
$apiSecret = FlickrPress::getApiSecret();
if (empty($apiKey) || empty($apiSecret)) {
	$errors[] = __('API KEY and API SECRET is required. Please setting API KEY and API SECRET.', FlickrPress::TEXT_DOMAIN);
}

if (!empty($errors) && count($errors)>0) {
	$buffer = "";
	foreach ($errors as $error) {
		$buffer .= "<p>{$error}</p>";
	}
	wp_die($buffer);
}
?>
