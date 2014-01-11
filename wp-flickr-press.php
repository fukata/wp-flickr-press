<?php
/*
Plugin Name: wp-flickr-press
Plugin URI: http://fukata.org/dev/wp-plugin/wp-flickr-press/
Description: Flickr integration for wordpress plugin.
Version: 1.9.14
Author: Tatsuya Fukata, Alexander Ovsov
Author URI: http://fukata.org
*/

require_once(dirname(__FILE__).'/FlickrPress.php');

FlickrPress::init();
?>
