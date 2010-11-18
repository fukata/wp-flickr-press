<?php

if ( isset($_POST['send']) && isset($_POST['attachments']) ) {
	$keys = array_keys($_POST['send']);
	$send_id = array_shift($keys);
	$attachment = $_POST['attachments'][$send_id];
} else {
	wp_die('does not exists key.');
}

$photo = $flickr->photos_getInfo($send_id);

media_send_to_editor(fp_create_image_html($photo, $attachment));

function fp_create_image_html($photo, $attachment) {

	$link = esc_url($attachment['url']);
	$target = isset($attachment['target']) ? esc_attr($attachment['target']) : '';
	$target = strlen($target)>0 ? " target='{$target}'" : '';
	$align = isset($attachment['align']) ? esc_attr($attachment['align']) : '';
	$src = isset($attachment['image-size']) ? esc_attr($attachment['image-size']) : '';
	$alt = isset($attachment['title']) ? esc_attr($attachment['title']) : '';
	if (strlen($src)==0) {
		$src = FlickrPress::getPhotoUrl($photo);
	}
	if ( isset($attachment['align']) ) {
		$align = esc_attr($attachment['align']);
		$class = " class='align$align'";
	}


	$html = '';
	$html = "<img src='{$src}' alt='{$alt}'{$class} />";
	if (strlen($link)>0) {
		$html = "<a href='{$link}'{$target}>{$html}</a>";
	}
	return $html;
}
?>
