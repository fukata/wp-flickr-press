<?php
/*
Plugin Name: wp-flickr-press
Plugin URI: http://fukata.org/dev/wp-plugin/wp-flickr-press/
Description: Flickr integration for wordpress plugin.
Version: 2.0.0 
Author: Tatsuya Fukata, Alexander Ovsov
Author URI: http://fukata.org
*/

require_once(dirname(__FILE__).'/FlickrPress.php');

FlickrPress::init();
?>
