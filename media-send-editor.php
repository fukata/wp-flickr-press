<?php
if ( isset($_POST['send']) && isset($_POST['attachments']) ) {
	$keys = array_keys($_POST['send']);
	$send_id = array_shift($keys);
	$attachments = array($send_id => $_POST['attachments'][$send_id]);
} else if ( isset($_POST['batch']) && $_POST['batch'] && isset($_POST['batch_send']) && count($_POST['batch_send'])>0 && isset($_POST['attachments']) ) {
	$attachments = array();
	foreach ($_POST['batch_send'] as $id) {
		if (isset($_POST['attachments'][$id])) {
			$attachments[$id] = $_POST['attachments'][$id];
		}
	}
} else {
	wp_die('does not exists key.');
}

fp_media_send_to_editor(fp_create_image_html($attachments));

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

function fp_create_image_html($attachments) {

	$html = '';
	foreach ($attachments as $id => $attachment) {
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
	        if ( isset($attachment['align']) ) {
	                $align = esc_attr($attachment['align']);
	                $class = " class='align$align'";
	        }

		$_img = "<img src=\"{$src}\" alt=\"{$alt}\"{$class}/>";
		if (strlen($link)>0) {
			$_img = "<a href=\"{$link}\"{$target}>{$_img}</a>";
		}

		$_html = FlickrPress::getInsertTemplate();
		$_html = str_replace('[img]', $_img, $_html);
		$_html = str_replace('[title]', $alt, $_html);
		$html .= $_html;
	}

	return $html;
}
?>
