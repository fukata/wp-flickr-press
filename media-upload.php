<?php 
require_once(dirname(__FILE__).'/FpPager.php');
require_once(dirname(__FILE__).'/check-setting.php');

$modes = array('search','search_thumbnail','upload');
if ($_SERVER['REQUEST_METHOD']=='POST') {
	if (!in_array($_POST['mode'], $modes)) {
		wp_die('Dows not exists mode.');
	} else {
		require_once(dirname(__FILE__).'/media-send-editor.php');
	}
}

if (!in_array($_GET['mode'], $modes)) {
	wp_die('Does not exists mode.');
}

require_once(dirname(__FILE__).'/media-upload_'.$_GET['mode'].'.php');
?>
