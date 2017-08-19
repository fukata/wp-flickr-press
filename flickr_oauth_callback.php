<?php
$frob = @$_GET['frob'];
unset($_GET['frob']);

require_once(dirname(__FILE__).'/check-oauth.php');
FlickrPress::getClient()->getAccessToken();
$oauth_token = FlickrPress::getClient()->getOauthToken();
$token = FlickrPress::getClient()->auth_oauth_checkToken();
include_once(dirname(__FILE__).'/inc.flickr_oauth_callback.php');
