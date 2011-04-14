<?php
require_once(dirname(__FILE__).'/../../../wp-admin/admin.php');
require_once(dirname(__FILE__).'/FlickrPress.php');
require_once(dirname(__FILE__).'/check-oauth.php');

$token = FlickrPress::getClient()->auth_getToken(@$_GET['frob']);
include_once(dirname(__FILE__).'/inc.flickr_oauth_callback.php');
