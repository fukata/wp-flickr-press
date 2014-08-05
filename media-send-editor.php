<?php
if ( isset($_POST['send']) && isset($_POST['attachments']) ) {
	$keys = array_keys($_POST['send']);
	$send_id = array_shift($keys);
	$attachments = array($send_id => $_POST['attachments'][$send_id]);
	$orders = array($send_id);
} else if ( isset($_POST['batch']) && $_POST['batch'] && isset($_POST['batch_send']) && count($_POST['batch_send'])>0 && isset($_POST['attachments']) ) {
	$attachments = array();
	$valid_orders = array();
	$invalid_orders = array();
	foreach ($_POST['batch_send'] as $id) {
		if (isset($_POST['attachments'][$id])) {
			$attachments[$id] = $_POST['attachments'][$id];
			$order = $attachments[$id]['order'];
			if (preg_match('/^[0-9]{1,2}$/', $order)) {
				$order = intval($order);
				if (!isset($valid_orders[$order])) $valid_orders[$order] = array();
				$valid_orders[$order][] = $id;
			} else {
				$invalid_orders[] = $id;
			}
		}
	}
	ksort($valid_orders);
	$orders = array();
	foreach ($valid_orders as $order => $ids) {
		foreach ($ids as $id) {
			$orders[] = $id;
		}
	}
	foreach ($invalid_orders as $id) {
		$orders[] = $id;
	}
} else {
	wp_die('does not exists key.');
}

fp_media_send_to_editor(fp_create_image_html($attachments, $orders));

function fp_media_send_to_editor($html) {
	$html = str_replace(array("\r\n","\r","\n"), '<br/>', $html);
	$html = addslashes($html);
?>
<script type="text/javascript">
/* <![CDATA[ */
var win = window.dialogArguments || opener || parent || top;
var html = '<?php echo $html; ?>';
html = html.replace(/<br\/>/g,'\n');
win.send_to_editor(html);
/* ]]> */
</script>
<?php
        exit;
}

function fp_create_image_html($attachments, $orders) {
	$html = '';
	foreach ($orders as $id) {
		$attachment = $attachments[$id];
		$photo = FlickrPress::getClient()->photos_getInfo($id);

		$link = esc_url($attachment['url']);
		$target = isset($attachment['target']) ? esc_attr($attachment['target']) : '';
		$target = strlen($target)>0 ? " target='{$target}'" : '';
		$align = isset($attachment['align']) ? esc_attr($attachment['align']) : '';
		$src = isset($attachment['image-size']) ? esc_attr($attachment['image-size']) : '';
		$alt = isset($attachment['title']) ? esc_attr($attachment['title']) : '';
		if (strlen($src)==0) {
			$src = FlickrPress::getPhotoUrl($photo);
		}
		if ( isset($attachment['align']) && strlen($attachment['align']) > 0 ) {
			$align = esc_attr($attachment['align']);
			$class = "align{$align}";
		}
		if ( isset($attachment['image-clazz']) ) {
			$imageClazz = _remove_invalid_link_charactors($attachment['image-clazz']);
			$class = strlen($class) > 0 ? "$class $imageClazz" : $imageClazz;
		}
	
		$class = strlen($class) > 0 ? " class=\"$class\"" : "";
		
		if ( isset($attachment['clazz']) ) {
			$clazz = _remove_invalid_link_charactors($attachment['clazz']);
			if (strlen($clazz) > 0) {
				$aclass = " class=\"{$clazz}\"";
			}
		}
		if ( isset($attachment['rel']) ) {
			$rel = _remove_invalid_link_charactors($attachment['rel']);
			if (strlen($rel) > 0) {
				$rel = " rel=\"$rel\"";
			}
		}

		$_img = "<img src=\"{$src}\" alt=\"{$alt}\"{$class}/>";
		if (strlen($link)>0) {
			$title = " title=\"{$alt}\"";
			$_img = "<a href=\"{$link}\"{$target}{$aclass}{$rel}{$title}>{$_img}</a>";
		}

		$_html = FlickrPress::getInsertTemplate();
		$_html = str_replace('[img]', $_img, $_html);
		$_html = str_replace('[title]', $alt, $_html);
		$_html = str_replace('[url]', $link, $_html);
		$_html = str_replace('[null]', '', $_html);
		$html .= $_html;
	}

	return $html;
}

function _remove_invalid_link_charactors($str) {
	return preg_replace('/[^0-9a-zA-Z\[\]\s_\-]+/', '', $str);
}
?>
