<?php
require_once(dirname(__FILE__).'/../../../wp-admin/admin.php');
require_once(dirname(__FILE__).'/FlickrPress.php');
require_once(dirname(__FILE__).'/check-setting.php');

FlickrPress::getClient()->auth("delete");
$token = FlickrPress::getClient()->auth_checkToken();
include_once(dirname(__FILE__).'/inc.flickr_oauth_callback.php');
?>
