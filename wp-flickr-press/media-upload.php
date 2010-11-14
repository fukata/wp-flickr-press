<?php 
require_once(dirname(__FILE__).'/../../../wp-admin/admin.php');
require_once(dirname(__FILE__).'/FlickrPress.php');
require_once(dirname(__FILE__).'/FpPager.php');
require_once(dirname(__FILE__).'/libs/phpflickr/phpFlickr.php');

$modes = array('recent','search','upload');
if ($_SERVER['REQUEST_METHOD']=='POST') {
	if (!in_array($_POST['mode'], $modes)) {
		wp_die('Dows not exists mode.');
	} else {
		media_send_to_editor('hogehoge');
	}
}

if (!in_array($_GET['mode'], $modes)) {
	wp_die('Does not exists mode.');
}

require_once(dirname(__FILE__).'/media-upload_'.$_GET['mode'].'.php');
?>
