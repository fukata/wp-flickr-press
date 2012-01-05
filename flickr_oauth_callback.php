<?php
$frob = @$_GET['frob'];
unset($_GET['frob']);

require_once(dirname(__FILE__).'/check-oauth.php');

$token = FlickrPress::getClient()->auth_getToken($frob);
include_once(dirname(__FILE__).'/inc.flickr_oauth_callback.php');
