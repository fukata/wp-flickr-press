<?php if(!class_exists('FlickrPress')) die('Not initialize FlickrPress.');

add_action('admin_head', 'fp_add_style');

$body_id = 'media-upload';
wp_iframe('media_upload_search_form');

function fp_add_style() {
	echo "\n".'<link rel="stylesheet" href="'.FlickrPress::getPluginUrl().'/css/media-upload_search.css?'.time().'" media="all" type="text/css" />
';
}

function media_upload_search_form() {
	$alignes = array(
		'None'=>'none',
		'Left'=>'left',
		'Center'=>'center',
		'Right'=>'right',
	);
	$targets = array(
		'None'=>'',
		'New Window'=>'_blank',
	);

	$page = isset($_GET['page']) && intval($_GET['page'])>0 ? intval($_GET['page']) : 0;
	
	$filter = isset($_GET['filter']) ? $_GET['filter'] : array();
	$checkedRecent = (!isset($filter['type']) || $filter['type']=='recent') ? " checked='checked'" : '';
	$checkedAdvanced = (isset($filter['type']) && $filter['type']=='advanced') ? " checked='checked'" : '';	
	$advancedFormClass = strlen($checkedAdvanced)==0 ? 'search-form-off' : '';
	$checkedPhotosets = (isset($filter['type']) && $filter['type']=='photosets') ? " checked='checked'" : '';	
	$photosetsFormClass = strlen($checkedPhotosets)==0 ? 'search-form-off' : '';
	
	$flickr = new phpFlickr(FlickrPress::getApiKey());
	$flickr->enableCache(FlickrPress::getCacheType(), FlickrPress::getCacheConnection());

	$photosets = $flickr->photosets_getList(FlickrPress::getUserId());

	$params = array('user_id'=>FlickrPress::getUserId(), 'page'=>$page, 'per_page'=>20, 'sort'=>'date-posted-desc');
	if (strlen($checkedRecent)>0) {
	} else if (strlen($checkedAdvanced)>0) {
		$params['tags'] = $filter['tags'];
	} else if (strlen($checkedPhotosets)>0) {
		$params['photoset_id'] = $filter['photoset'];
	}
	if (strlen($checkedPhotosets)>0) {
		$photos = $flickr->photosets_getPhotos($params);
		$photos = $photos === false ? array('total'=>0,'page'=>1,'perpage'=>20,'photo'=>array()) : $photos;
	} else {
		$photos = $flickr->photos_search($params);
	}

	$pager = new FpPager($photos['total'], $photos['page'], $photos['perpage']);
?>
<div id="media-upload-header">
	<ul id='sidemenu'>
	    <li id='tab-search'><a href='<?php echo FlickrPress::getPluginUrl() ?>/media-upload.php?post_id<?php echo $_GET['post_id'] ?>&type=image&tab=search' class='current'><?php echo __('Search') ?></a></li>
	</ul>
</div>

<form action="<?php echo FlickrPress::getPluginUrl().'/media-upload.php'?>" method="get">
        <input type="hidden" name="post_id" value="<?php echo $_GET['post_id'] ?>" />
        <input type="hidden" name="type" value="<?php echo $_GET['type'] ?>" />
        <input type="hidden" name="mode" value="<?php echo $_GET['mode'] ?>" />
        <input type="hidden" name="TB_iframe" value="<?php echo $_GET['TB_iframe'] ?>" />

	<div class="searchnav">
		<p>
			<input type="radio" name="filter[type]" value="recent" class="search-type" id="filter-type-recent" <?php echo $checkedRecent ?>/><label for="filter-type-recent"><?php echo __('Recent upload') ?></label>
			<input type="radio" name="filter[type]" value="photosets" class="search-type" id="filter-type-photosets" <?php echo $checkedPhotosets ?>/><label for="filter-type-photosets"><?php echo __('Photosets') ?></label>
			<input type="radio" name="filter[type]" value="advanced" class="search-type" id="filter-type-advanced" <?php echo $checkedAdvanced ?>/><label for="filter-type-advanced"><?php echo __('Advanced') ?></label>
		</p>
		<div id="advanced-search-form" class="<?php echo $advancedFormClass?>">
			<p class="field-row"><span class="field-label"><?php echo __('Keyword:') ?></span><input type="text" name="filter[keyword]" value="<?php echo $filter['keyword'] ?>" size="50"/></p>
			<p class="field-row"><span class="field-label"><?php echo __('Tags:') ?></span><input type="text" name="filter[tags]" value="<?php echo $filter['tags'] ?>" size="50"/></p>
		</div>
		<div id="photosets-search-form" class="<?php echo $photosetsFormClass?>">
			<p class="field-row"><span class="field-label"><?php echo __('Photosets:') ?></span>
				<select name="filter[photoset]">
					<?php foreach ($photosets['photoset'] as $photoset) { ?>
						<option value="<?php echo $photoset['id'] ?>"><?php echo $photoset['title'] ?></option>
					<?php } ?>
				</select>
			</p>
		</div>

		<p><input type="submit" value="<?php echo __('Search') ?>" class="button"/></p>
	</div>
</form>

<div class="tablenav"><?php echo $pager->generate() ?></div>
<br class="clear" />
<form action="<?php echo FlickrPress::getPluginUrl().'/media-upload.php'?>" method="post" class="media-upload-form validate">
<div id="media-items">
        <input type="hidden" name="post_id" value="<?php echo $_GET['post_id'] ?>" />
        <input type="hidden" name="type" value="<?php echo $_GET['type'] ?>" />
        <input type="hidden" name="mode" value="<?php echo $_GET['mode'] ?>" />
        <input type="hidden" name="TB_iframe" value="<?php echo $_GET['TB_iframe'] ?>" />
<?php foreach($photos['photo'] as $photo) { ?>
	<div id="media-item-<?php echo $photo['id'] ?>" class="media-item">
		<img class="pinkynail toggle" src="<?php echo FlickrPress::getPhotoUrl($photo, 's') ?>"/>
		<a class="toggle describe-toggle-on" href="#"><?php echo __('Show') ?></a>
		<a class="toggle describe-toggle-off" href="#"><?php echo __('Hide') ?></a>
		<div class="filename new"><span class="title"><?php echo $photo['title'] ?></span></div>
		<table class="slidetoggle describe startclosed">
			<thead class="media-item-info" id="media-head-<?php echo $photo['id'] ?>">
				<tr valign="top">
					<td class="A1B1" id="thumbnail-head-<?php echo $photo['id'] ?>"
						<p><a href="#"><img class="thumbnail" src="<?php echo FlickrPress::getPhotoUrl($photo, 'm') ?>"/></a></p>
						<p><img src="<?php echo admin_url() ?>/images/wpspin_light.gif" class="imgedit-wait-spin" alt=""></p>
					</td>
					<td>
						<p><strong><?php echo __('ID:') . $photo['id'] ?></strong></p>
						<p><strong><?php echo __('File name:') . $photo['title'] ?></strong></p>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr class="post_title">
					<th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][title]"><span class="alignleft"><?php echo __('Title')?></span><span class="alignright"><br class="clear"></label></th>
					<td class="field"><input type="text" class="text" id="attachments[<?php echo $photo['id'] ?>][title]" name="attachments[<?php echo $photo['id'] ?>][title]" value="<?php echo $photo['title'] ?>" aria-required="true"></td>
				</tr>
				<tr class="url">
					<th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][url]"><span class="alignleft"><?php echo __('Link URL')?></span><br class="clear"></label></th>
					<td class="field">
						<input type="text" class="text urlfield" name="attachments[<?php echo $photo['id'] ?>][url]" value="<?php echo FlickrPress::getPhotoPageUrl($photo) ?>"><br>
						<button type="button" class="button urlnone" title=""><?php echo __('None') ?></button>
						<button type="button" class="button urlfile" title="<?php echo FlickrPress::getPhotoUrl($photo) ?>"><?php echo __('File URL') ?></button>
						<button type="button" class="button urlpage" title="<?php echo FlickrPress::getPhotoPageUrl($photo) ?>"><?php echo __('Page URL') ?></button>
						<p class="help"><?php echo __('Enter a link URL or click above for presets.') ?></p>
					</td>
				</tr>
                                <tr class="target">
                                        <th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][target]"><span class="alignleft"><?php echo __('Link Target')?></span><br class="clear"></label></th>
                                        <td class="field">
                                                <?php foreach ($targets as $label => $target) { ?>
							<?php $checked = FlickrPress::getDefaultTarget()==$target ? " checked='checked'" : '' ?>
                                                        <input type="radio" name="attachments[<?php echo $photo['id'] ?>][target]" id="link-target-<?php echo $target ?>-<?php echo $photo['id'] ?>" value="<?php echo $target ?>" <?php echo $checked ?>/><label for="link-target-<?php echo $target ?>-<?php echo $photo['id'] ?>" class="link-target-<?php echo $target ?>-label"><?php echo __($label) ?></label>
                                                <?php } ?>
                                        </td>
                                </tr>
				<tr class="align">
					<th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][align]"><span class="alignleft"><?php echo __('Alignment')?></span><br class="clear"></label></th>
					<td class="field">
						<?php foreach ($alignes as $label => $align) { ?>
							<?php $checked = FlickrPress::getDefaultAlign()==$align ? " checked='checked'" : '' ?>
							<input type="radio" name="attachments[<?php echo $photo['id'] ?>][align]" id="image-align-<?php echo $align ?>-<?php echo $photo['id'] ?>" value="<?php echo $align ?>"<?php echo $checked?> /><label for="image-align-<?php echo $align ?>-<?php echo $photo['id'] ?>" class="align image-align-<?php echo $align ?>-label"/><?php echo __($label) ?></label>
						<?php } ?>
					</td>
				</tr>
				<tr class="image-size">
					<th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><?php echo __('Size')?></span><br class="clear"></label></th>
					<td class="field">
						<?php $sizes = $flickr->photos_getSizes($photo['id']);?>
						<?php foreach($sizes as $size) { ?>
							<?php $checked = FlickrPress::getDefaultSize()==$size['label'] ? " checked='checked'" : '' ?>
							<div class="image-size-item"><input name="attachments[<?php echo $photo['id'] ?>][image-size]" value="<?php echo $size['source'] ?>" type="radio" id="image-size-<?php echo $size['label']?>-<?php echo $photo['id'] ?>"<?php echo $checked?>/><label for="image-size-<?php echo $size['label']?>-<?php echo $photo['id'] ?>"><?php echo __($size['label']) ?></label><label for="image-size-thumbnail-<?php echo $photo['id'] ?>" class="help">(<?php echo $size['width'] ?>&nbsp;×&nbsp;<?php echo $size['height'] ?>)</label></div>
						<?php } ?>
					</td>
				</tr>
				<tr class="submit">
					<td></td>
					<td class="savesend">
						<input type="submit" class="button" name="send[<?php echo $photo['id'] ?>]" value="<?php echo __('Insert into Post')?>">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php } ?>
</div>
</form>
<div class="tablenav"><?php echo $pager->generate() ?></div>
<script type="text/javascript">
jQuery('input.search-type').click(function() {
	var type = jQuery(this).val();
	if (type=='advanced') {
		jQuery('div#advanced-search-form').slideDown();
		jQuery('div#photosets-search-form').slideUp();
	} else if (type=='photosets') {
		jQuery('div#photosets-search-form').slideDown();
		jQuery('div#advanced-search-form').slideUp();
	} else {
		jQuery('div#photosets-search-form').slideUp();
		jQuery('div#advanced-search-form').slideUp();
	}
});

jQuery('div.media-item').each(function() {
        jQuery('a.toggle', this).click(function(){
                 jQuery(this).siblings('.slidetoggle').slideToggle(350, function(){
                         var w = jQuery(window).height(), t = jQuery(this).offset().top, h = jQuery(this).height(), b;

                         if ( w && t && h ) {
                b = t + h;
 
                if ( b > w && (h + 48) < w )
                    window.scrollBy(0, b - w + 13);
                else if ( b > w )
                    window.scrollTo(0, t - 36);
           }
               });
               jQuery(this).siblings('.toggle').andSelf().toggle();
               jQuery(this).siblings('a.toggle').focus();
               return false;
       });
});

// remember the last used image size, alignment and url
jQuery(document).ready(function($){
        $('input[type="radio"]', '#media-items').live('click', function(){
                var tr = $(this).closest('tr');

                if ( $(tr).hasClass('align') )
                        setUserSetting('align', $(this).val());
                else if ( $(tr).hasClass('image-size') )
                        setUserSetting('imgsize', $(this).val());
        });

        $('button.button', '#media-items').live('click', function(){
                var c = this.className || '';
                c = c.match(/url([^ '"]+)/);
                if ( c && c[1] ) {
                        setUserSetting('urlbutton', c[1]);
                        $(this).siblings('.urlfield').val( $(this).attr('title') );
                }
        });
});
</script>
<?php 
}
?>

