<?php
require_once(dirname(__FILE__).'/check-oauth.php');

FlickrPress::getClient()->auth("delete");
$token = FlickrPress::getClient()->auth_checkToken();
include_once(dirname(__FILE__).'/inc.flickr_oauth_callback.php');
?>
