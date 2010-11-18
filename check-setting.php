<?php
$apiKey = FlickrPress::getApiKey();
$apiSecret = FlickrPress::getApiSecret();
if (empty($apiKey) || empty($apiSecret)) {
	wp_die(__('API KEY and API SECRET is required. Please setting API KEY and API SECRET.'));
}

$userId = FlickrPress::getUserId();
if (empty($userId)) {
	wp_die(__('USER ID is required. Please setting USER ID.'));
}
?>
