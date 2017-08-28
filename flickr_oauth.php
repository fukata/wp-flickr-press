<?php
require_once(dirname(__FILE__).'/check-oauth.php');

FlickrPress::getClient()->getRequestToken(admin_url('admin.php?action=wpfp_flickr_oauth_callback'), 'write');
?>
