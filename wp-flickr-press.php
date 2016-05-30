<?php
/*
Plugin Name: wp-flickr-press
Plugin URI: https://github.com/fukata/wp-flickr-press
Description: Flickr integration for wordpress plugin.
Version: 2.3.2
Author: Tatsuya Fukata, Alexander Ovsov
Author URI: https://fukata.org
*/

require_once(dirname(__FILE__).'/FlickrPress.php');

FlickrPress::init();
?>
