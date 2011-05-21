<?php if(!class_exists('FlickrPress')) die('Not initialize FlickrPress.');

wp_enqueue_style('thickbox');
wp_enqueue_script('thickbox');

add_action('admin_head', 'fp_add_scripts');

$body_id = 'media-upload';
wp_iframe('media_upload_search_form');

function fp_add_scripts() {
	echo "\n<link rel='stylesheet' href='".FlickrPress::getPluginUrl()."/css/media-upload_search_thumbnail.css?".time()."' media='all' type='text/css'/>";
	echo "\n<link rel='stylesheet' href='".FlickrPress::getPluginUrl()."/css/jquery.tag.css?".time()."' media='all' type='text/css'/>";
	echo "\n<script type='text/javascript' src='".FlickrPress::getPluginUrl()."/js/jquery.tag.js?".time()."'></script>";
	echo "\n<script type='text/javascript'>tb_pathToImage = '../../../wp-includes/js/thickbox/loadingAnimation.gif'; tb_closeImage='../../../wp-includes/js/thickbox/tb-close.png';</script>";
}

function media_upload_search_form() {
?>

<?php include_once dirname(__FILE__).'/inc.header_tab.php' ?>

<?php
}