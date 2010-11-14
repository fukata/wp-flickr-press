<?php if(!class_exists('FlickrPress')) die('Not initialize FlickrPress.');

wp_iframe('media_upload_recent_form');

function media_upload_recent_form() {
	$page = isset($_GET['page']) && intval($_GET['page'])>0 ? intval($_GET['page']) : 0;
	$flickr = new phpFlickr(FlickrPress::getApiKey());
	$recent = $flickr->photos_search(array('user_id'=>FlickrPress::getUserId(), 'page'=>$page, 'per_page'=>20));
	$pager = new FpPager($recent['total'], $recent['page'], $recent['perpage']);
?>
<script src='<?php echo FlickrPress::getPluginUrl().'/js/flickr_press.js' ?>' type='text/javascript'></script>

<?php //MOCK ?>
<form action="<?php echo FlickrPress::getPluginUrl()."/media-upload.php?post_id={$_GET['post_id']}&type={$_GET['type']}&mode={$_GET['mode']}&TB_iframe={$_GET['TB_iframe']}" ?>" method="post">
	<input type="hidden" name="post_id" value="<?php echo $_GET['post_id'] ?>" />
	<input type="hidden" name="type" value="<?php echo $_GET['type'] ?>" />
	<input type="hidden" name="mode" value="<?php echo $_GET['mode'] ?>" />
	<input type="hidden" name="TB_iframe" value="<?php echo $_GET['TB_iframe'] ?>" />
	<input type="submit" value="Insert into post"/>
</form>

<h3>Recent upload your photos</h3>
<div class="fp_photos">
<div class="tablenav"><?php echo $pager->generate() ?></div>

<?php foreach($recent['photo'] as $photo) { ?>
	<?php //$photoInfo = $flickr->photos_getInfo($photo['id']); ?>
	<div class="fp_photo">
		<div class="fp_image"><img src="http://farm<?php echo $photo['farm'] ?>.static.flickr.com/<?php echo $photo['server'] ?>/<?php echo $photo['id'] ?>_<?php echo $photo['secret'] ?>_s.jpg"/></div>
		<div class="fp_info">
			<ul>
				<li><?php echo __('ID:') . ' ' . $photo['id'] ?></li>
				<li><?php echo __('Title:') . ' ' . $photo['title'] ?></li>
			</ul>
			<p class="fp_select_btn"><a href="javascript:fp.insertImage('http://farm<?php echo $photo['farm'] ?>.static.flickr.com/<?php echo $photo['server'] ?>/<?php echo $photo['id'] ?>_<?php echo $photo['secret'] ?>_m.jpg')"><?php echo __('Select') ?></a></p>
		</div>
	</div>
<?php } ?>

<?php echo $pager->generate() ?>
</div>

<?php 
}
?>
