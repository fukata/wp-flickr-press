<?php if(!class_exists('FlickrPress')) die('Not initialize FlickrPress.');

wp_enqueue_style('thickbox');
wp_enqueue_script('thickbox');

$pluginPath = FlickrPress::getPluginUrl();
wp_enqueue_style("media-upload_search", "$pluginPath/css/media-upload_search.css", array(), FlickrPress::VERSION);
wp_enqueue_style("jquery.tag", "$pluginPath/css/jquery.tag.css", array(), FlickrPress::VERSION);
wp_enqueue_script("jquery.tag", "$pluginPath/js/jquery.tag.js", array(), FlickrPress::VERSION);
wp_enqueue_script("jquery.md5", "$pluginPath/js/jquery.md5.js", array(), FlickrPress::VERSION);
wp_enqueue_script("jquery.flickr-client", "$pluginPath/js/jquery.flickr-client.js", array(), FlickrPress::VERSION);
wp_enqueue_script("search_list_ui", "$pluginPath/js/search_list_ui.js", array(), FlickrPress::VERSION);

add_action('admin_head', 'fp_add_scripts');

$GLOBALS['body_id'] = 'media-upload';
wp_iframe('media_upload_search_form');

function fp_add_scripts() {
	echo "\n<script type='text/javascript'>tb_pathToImage = '../../../wp-includes/js/thickbox/loadingAnimation.gif'; tb_closeImage='../../../wp-includes/js/thickbox/tb-close.png';</script>";
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
	$sorts = array(
		'Posted ASC' => 'date-posted-asc',
		'Posted DESC' => 'date-posted-desc',
		'Taken ASC' => 'date-taken-asc',
		'Taken DESC' => 'date-taken-desc',
		'Interestingness ASC' => 'interestingness-asc',
		'Interestingness DESC' => 'interestingness-desc',
	);
	$extendLinkProperties = FlickrPress::getExtendLinkPropertiesArray();
	$extendImageProperties = FlickrPress::getExtendImagePropertiesArray();

	$page = isset($_GET['paged']) && intval($_GET['paged'])>0 ? intval($_GET['paged']) : 0;
	
	$filter = isset($_GET['filter']) ? $_GET['filter'] : array();
	if (isset($_GET['clear_cache']) && $_GET['clear_cache']) {
		FlickrPress::clearCache();
	}
	
	$filter['keyword'] = (isset($filter['keyword'])) ? $filter['keyword'] : '';
	$filter['tags'] = (isset($filter['tags'])) ? $filter['tags'] : '';

	$checkedRecent = (!isset($filter['type']) || $filter['type']=='recent') ? " checked='checked'" : '';
	$checkedAdvanced = (isset($filter['type']) && $filter['type']=='advanced') ? " checked='checked'" : '';
	$advancedFormClass = strlen($checkedAdvanced)==0 ? 'search-form-off' : '';
	$checkedPhotosets = (isset($filter['type']) && $filter['type']=='photosets') ? " checked='checked'" : '';	
	$photosetsFormClass = strlen($checkedPhotosets)==0 ? 'search-form-off' : '';
	$sortFormClass = strlen($checkedPhotosets)==0 ? '' : 'search-form-off';
	
	$sort = ( (!isset($filter['sort'])) || strlen($filter['sort']) == 0 ) ? FlickrPress::getDefaultSort() : $filter['sort'];
	if (!in_array($sort, $sorts)) $sort = FlickrPress::getDefaultSort();

	$params = array(
		'user_id' => FlickrPress::getUserId(), 
		'page' => $page, 
		'per_page' => 20, 
		'sort' => 'date-posted-desc', 
		'extras' => join(',', array_values(FlickrPress::$SIZES)) . ',path_alias'
	);
	if (strlen($checkedRecent)>0) {
		$params['sort'] = $sort;
	} else if (strlen($checkedAdvanced)>0) {
		$splited = split(',', $filter['tags']);
		$joined = array();
		foreach ($splited as $tag) {
			if (strlen(trim($tag))>0) {
				$joined[] = $tag;
			}
		}
		$params['tags'] = join(',', $joined);
		$params['text'] = $filter['keyword'];
		$params['sort'] = $sort;
	} else if (strlen($checkedPhotosets)>0) {
		$params['photoset_id'] = $filter['photoset'];
	}
	if (strlen($checkedPhotosets)>0) {
		$photos = FlickrPress::getClient()->photosets_getPhotos($params['photoset_id'], $params['extras'], NULL, $params['per_page'], $params['page']);
		$photos = $photos === false ? array('total'=>0,'page'=>1,'perpage'=>20,'photo'=>array()) : $photos['photoset'];
	} else {
		$photos = FlickrPress::getClient()->photos_search($params);
		$photos = $photos === false ? array('total'=>0,'page'=>1,'perpage'=>20,'photo'=>array()) : $photos;
	}
	$pager = new FpPager($photos['total'], $photos['page'], $photos['perpage']);
?>

<?php include_once dirname(__FILE__).'/inc.header_tab.php' ?>

<div id="params" style="display: none;">
	<input type="hidden" name="api_key" id="api_key" value="<?php echo FlickrPress::getApiKey() ?>" />
	<input type="hidden" name="api_secret" id="api_secret" value="<?php echo FlickrPress::getApiSecret() ?>" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo FlickrPress::getUserId() ?>" />
	<input type="hidden" name="oauth_token" id="oauth_token" value="<?php echo FlickrPress::getOAuthToken() ?>" />
	<input type="hidden" name="photoset_id" id="photoset_id" value="<?php echo @$filter['photoset'] ?>" />
	<input type="hidden" name="default_link_rel" id="default_link_rel" value="<?php echo FlickrPress::getDefaultLinkRel() ?>" />
	<input type="hidden" name="default_link_class" id="default_link_class" value="<?php echo FlickrPress::getDefaultLinkClass() ?>" />
</div>

<form action="<?php echo admin_url() . "admin.php" ?>" method="get" id="search-form">
        <input type="hidden" name="action" value="wpfp_media_upload" />
        <input type="hidden" name="post_id" value="<?php echo $_GET['post_id'] ?>" />
        <input type="hidden" name="type" value="<?php echo $_GET['type'] ?>" />
        <input type="hidden" name="mode" value="<?php echo $_GET['mode'] ?>" />
        <input type="hidden" name="TB_iframe" value="<?php echo $_GET['TB_iframe'] ?>" />
        <input type="hidden" name="clear_cache" value="0" id="clear-cache"/>

	<div class="searchnav">
		<p>
			<input type="radio" name="filter[type]" value="recent" class="search-type" id="filter-type-recent" <?php echo $checkedRecent ?>/><label for="filter-type-recent"><?php echo __('Recent upload', FlickrPress::TEXT_DOMAIN) ?></label>
			<input type="radio" name="filter[type]" value="photosets" class="search-type" id="filter-type-photosets" <?php echo $checkedPhotosets ?>/><label for="filter-type-photosets"><?php echo __('Photosets', FlickrPress::TEXT_DOMAIN) ?></label>
			<input type="radio" name="filter[type]" value="advanced" class="search-type" id="filter-type-advanced" <?php echo $checkedAdvanced ?>/><label for="filter-type-advanced"><?php echo __('Advanced', FlickrPress::TEXT_DOMAIN) ?></label>
		</p>
		<div id="sort-search-form" class="<?php echo $sortFormClass ?>">
			<p class="field-row">
				<span class="field-label"><?php echo __('Sort:', FlickrPress::TEXT_DOMAIN) ?></span>
				<select name="filter[sort]">
				<?php foreach ($sorts as $label => $val) { ?>
					<option value="<?php echo $val ?>" <?php if ($val==$sort) {echo 'selected="selected"';} ?>><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></option>
				<?php } ?>
				</select>
			</p>
		</div>
		<div id="advanced-search-form" class="<?php echo $advancedFormClass?>">
			<p class="field-row"><span class="field-label"><?php echo __('Keyword:', FlickrPress::TEXT_DOMAIN) ?></span><input type="text" name="filter[keyword]" value="<?php echo $filter['keyword'] ?>" size="50"/></p>
			<p class="field-row"><span class="field-label"><?php echo __('Tags:', FlickrPress::TEXT_DOMAIN) ?></span><input type="text" name="filter[tags]" value="<?php echo $filter['tags'] ?>" size="50" id="filter-tags" autocomplete="off"/></p>
		</div>
		<div id="photosets-search-form" class="<?php echo $photosetsFormClass?>">
			<p class="field-row"><span class="field-label"><?php echo __('Photosets:', FlickrPress::TEXT_DOMAIN) ?></span>
				<select name="filter[photoset]"></select>
			</p>
		</div>

		<p><input type="submit" value="<?php echo __('Search', FlickrPress::TEXT_DOMAIN) ?>" class="button"/> <a href="javascript:void(0);" class="button" id="clear-cache-btn"><?php echo __('Search and clear cache', FlickrPress::TEXT_DOMAIN) ?></a></p>
	</div>
</form>

<div class="tablenav"><?php echo $pager->generate() ?></div>
<br class="clear" />
<form action="<?php echo admin_url('admin.php?action=wpfp_media_upload')?>" method="post" class="media-upload-form validate" id="media-form">
<div id="media-items">
        <input type="hidden" name="post_id" value="<?php echo $_GET['post_id'] ?>" />
        <input type="hidden" name="type" value="<?php echo $_GET['type'] ?>" />
        <input type="hidden" name="mode" value="<?php echo $_GET['mode'] ?>" />
        <input type="hidden" name="TB_iframe" value="<?php echo $_GET['TB_iframe'] ?>" />
	<input type="hidden" name="batch" value="0" id="batch" />

<?php foreach($photos['photo'] as $photo) { ?>
	<div id="media-item-<?php echo $photo['id'] ?>" class="media-item">
		<input type="checkbox" name="batch_send[]" value="<?php echo $photo['id'] ?>" class="batch-send"/>
		<input type="text" name="attachments[<?php echo $photo['id'] ?>][order]" value="" maxlength="2" size="1" class="order" />
		<img class="pinkynail toggle" src="<?php echo FlickrPress::getPhotoUrl($photo, 'sq') ?>"/>
		<a class="toggle describe-toggle-on" href="#"><?php echo __('Show', FlickrPress::TEXT_DOMAIN) ?></a>
		<a class="toggle describe-toggle-off" href="#"><?php echo __('Hide', FlickrPress::TEXT_DOMAIN) ?></a>
		<div class="filename new"><span class="title"><?php echo $photo['title'] ?></span></div>
		<table class="slidetoggle describe startclosed">
			<thead class="media-item-info" id="media-head-<?php echo $photo['id'] ?>">
				<tr valign="top">
					<td class="A1B1" id="thumbnail-head-<?php echo $photo['id'] ?>"
						<p><a href="#"><img class="thumbnail" src="<?php echo FlickrPress::getPhotoUrl($photo, 'm') ?>"/></a></p>
						<p><img src="<?php echo admin_url() ?>/images/wpspin_light.gif" class="imgedit-wait-spin" alt=""></p>
					</td>
					<td>
						<p><strong><?php echo __('ID:', FlickrPress::TEXT_DOMAIN) . $photo['id'] ?></strong></p>
						<p><strong><?php echo __('File name:', FlickrPress::TEXT_DOMAIN) . $photo['title'] ?></strong></p>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr class="post_title">
					<th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][title]"><span class="alignleft"><?php echo __('Title', FlickrPress::TEXT_DOMAIN)?></span><span class="alignright"><br class="clear"></label></th>
					<td class="field"><input type="text" class="text" id="attachments[<?php echo $photo['id'] ?>][title]" name="attachments[<?php echo $photo['id'] ?>][title]" value="<?php echo esc_attr($photo['title']) ?>" aria-required="true"></td>
				</tr>
				<tr class="url">
					<th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][url]"><span class="alignleft"><?php echo __('Link URL', FlickrPress::TEXT_DOMAIN)?></span><br class="clear"></label></th>
					<td class="field">
						<input type="text" class="text urlfield" name="attachments[<?php echo $photo['id'] ?>][url]" value="<?php echo FlickrPress::getDefaultLinkValue($photo, $photos) ?>"><br>
						<button type="button" class="button urlnone" title=""><?php echo __('None', FlickrPress::TEXT_DOMAIN) ?></button>
						<button type="button" class="button urlfile" title="<?php echo FlickrPress::getPhotoUrl($photo, FlickrPress::getDefaultFileURLSize()) ?>"><?php echo __('File URL', FlickrPress::TEXT_DOMAIN) ?></button>
						<button type="button" class="button urlpage" title="<?php echo FlickrPress::getPhotoPageUrl($photo, $photos) ?>"><?php echo __('Page URL', FlickrPress::TEXT_DOMAIN) ?></button>
						<p class="help"><?php echo __('Enter a link URL or click above for presets.', FlickrPress::TEXT_DOMAIN) ?></p>
					</td>
				</tr>
                                <tr class="target">
                                        <th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][target]"><span class="alignleft"><?php echo __('Link Target', FlickrPress::TEXT_DOMAIN)?></span><br class="clear"></label></th>
                                        <td class="field">
                                                <?php foreach ($targets as $label => $target) { ?>
							<?php $checked = FlickrPress::getDefaultTarget()==$target ? " checked='checked'" : '' ?>
                                                        <input type="radio" name="attachments[<?php echo $photo['id'] ?>][target]" id="link-target-<?php echo $target ?>-<?php echo $photo['id'] ?>" value="<?php echo $target ?>" <?php echo $checked ?>/><label for="link-target-<?php echo $target ?>-<?php echo $photo['id'] ?>" class="link-target-<?php echo $target ?>-label"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
                                                <?php } ?>
                                        </td>
                                </tr>
				<tr class="align">
					<th valign="top" scope="row" class="label"><label for="attachments[<?php echo $photo['id'] ?>][align]"><span class="alignleft"><?php echo __('Alignment', FlickrPress::TEXT_DOMAIN)?></span><br class="clear"></label></th>
					<td class="field">
						<?php foreach ($alignes as $label => $align) { ?>
							<?php $checked = FlickrPress::getDefaultAlign()==$align ? " checked='checked'" : '' ?>
							<input type="radio" name="attachments[<?php echo $photo['id'] ?>][align]" id="image-align-<?php echo $align ?>-<?php echo $photo['id'] ?>" value="<?php echo $align ?>"<?php echo $checked?> /><label for="image-align-<?php echo $align ?>-<?php echo $photo['id'] ?>" class="align image-align-<?php echo $align ?>-label"/><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
						<?php } ?>
					</td>
				</tr>
				<tr class="image-size">
					<th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><?php echo __('Size', FlickrPress::TEXT_DOMAIN)?></span><br class="clear"></label></th>
					<td class="field">
						<?php foreach(FlickrPress::$SIZES as $size => $url) { ?>
							<?php $checked = FlickrPress::getDefaultSize()==$size ? " checked='checked'" : '' ?>
							<div class="image-size-item"><input name="attachments[<?php echo $photo['id'] ?>][image-size]" value="<?php echo FlickrPress::getPhotoUrl($photo, $size) ?>" type="radio" id="image-size-<?php echo $size ?>-<?php echo $photo['id'] ?>"<?php echo $checked?>/><label for="image-size-<?php echo $size ?>-<?php echo $photo['id'] ?>"><?php echo __(FlickrPress::$SIZE_LABELS[$size], FlickrPress::TEXT_DOMAIN) ?></label></div>
						<?php } ?>
					</td>
				</tr>
				<tr class="image-property">
					<th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link-properties"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span><?php echo __('Image Class Property', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
					<td class="field" style="display:none;">
						<p><span><?php echo __('Class:', FlickrPress::TEXT_DOMAIN) ?></span><input name="attachments[<?php echo $photo['id'] ?>][image-clazz]" value="" type="text" /></p>
						<p><?php echo __('Available Charactors: 0-9a-zA-Z [] Space UnderScore Hyphen', FlickrPress::TEXT_DOMAIN) ?></p>
						<p>
							<select class="extend-image-properties">
							<option value="" data-photoid=""></option>
							<?php for ($i=0; $i<count($extendImageProperties); $i++) { ?>
							<option value="" data-photoid="<?php echo $photo['id'] ?>" data-clazz="<?php echo $extendImageProperties[$i]->clazz ?>"><?php echo $extendImageProperties[$i]->title ?></option>
							<?php } ?>
							</select>
						</p>
					</td>
				</tr>
				<tr class="link-property">
					<th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link-properties"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span><?php echo __('Link Rel and Class Property', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
					<td class="field" style="display:none;">
						<p><span><?php echo __('Rel:', FlickrPress::TEXT_DOMAIN) ?></span><input name="attachments[<?php echo $photo['id'] ?>][rel]" value="<?php echo FlickrPress::getDefaultLinkRel() ?>" type="text" /></p>
						<p><span><?php echo __('Class:', FlickrPress::TEXT_DOMAIN) ?></span><input name="attachments[<?php echo $photo['id'] ?>][clazz]" value="<?php echo FlickrPress::getDefaultLinkClass() ?>" type="text" /></p>
						<p><?php echo __('Available Charactors: 0-9a-zA-Z [] Space UnderScore Hyphen', FlickrPress::TEXT_DOMAIN) ?></p>
						<p>
							<select class="extend-link-properties">
							<option value="" data-photoid=""></option>
							<?php for ($i=0; $i<count($extendLinkProperties); $i++) { ?>
							<option value="" data-photoid="<?php echo $photo['id'] ?>" data-rel="<?php echo $extendLinkProperties[$i]->rel ?>" data-clazz="<?php echo $extendLinkProperties[$i]->clazz ?>"><?php echo $extendLinkProperties[$i]->title ?></option>
							<?php } ?>
							</select>
							<a href="javascript:void(0)" class="button load-default-link-property" data-photoid="<?php echo $photo['id'] ?>"><?php echo __('Load Default', FlickrPress::TEXT_DOMAIN) ?></a>
						</p>
					</td>
				</tr>
				<tr class="submit">
					<td></td>
					<td class="savesend">
						<input type="submit" class="button" name="send[<?php echo $photo['id'] ?>]" value="<?php echo __('Insert into Post', FlickrPress::TEXT_DOMAIN)?>">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php } ?>
</div>
<p><a href="javascript:void(0)" class="button" id="batch-insert-btn"><?php echo __('Batch Insert into Post', FlickrPress::TEXT_DOMAIN); ?></a></p>
</form>
<div class="tablenav"><?php echo $pager->generate() ?></div>
<div id="inline-settings-content-container">
	<input type="hidden" name="inline_photo_id" id="inline-photo-id"/>
	<div id="inline-settings-order">
		<p><?php echo __('Order:', FlickrPress::TEXT_DOMAIN) ?><input type="text" name="order" maxlength="2" size="2" id="inline-order" /></p>
	</div>
	<div id="inline-settings-image-size">
	</div>
	<div class="save-buttons">
		<input type="button" value="<?php echo __('Update', FlickrPress::TEXT_DOMAIN) ?>" class="button" id="inline-update-button"/>
	</div>
</div>
<script type="text/javascript">
jQuery('input.search-type').click(function() {
	var type = jQuery(this).val();
	if (type=='advanced') {
		jQuery('div#advanced-search-form').slideDown();
		jQuery('div#sort-search-form').slideDown();
		jQuery('div#photosets-search-form').slideUp();
	} else if (type=='photosets') {
		jQuery('div#photosets-search-form').slideDown();
		jQuery('div#advanced-search-form').slideUp();
		jQuery('div#sort-search-form').slideUp();
	} else if(type=='recent') {
		jQuery('div#sort-search-form').slideDown();
		jQuery('div#advanced-search-form').slideUp();
		jQuery('div#photosets-search-form').slideUp();
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

	$('#clear-cache-btn').click(function() {
		$('#clear-cache').val('1');
		$('#search-form').submit();
	});

	$('#batch-insert-btn').click(function() {
		$('#batch').val('1');
		$('#media-form').submit();
	});

	$('select.extend-image-properties').change(function() {
		var $self = $(this.options[this.selectedIndex]);
		var photo_id = $self.attr('data-photoid');
		if (photo_id) {
			$('input[name="attachments['+photo_id+'][image-clazz]"]').val( $self.attr('data-clazz') );
		}
	});

	$('select.extend-link-properties').change(function() {
		var $self = $(this.options[this.selectedIndex]);
		var photo_id = $self.attr('data-photoid');
		if (photo_id) {
			$('input[name="attachments['+photo_id+'][rel]"]').val( $self.attr('data-rel') );
			$('input[name="attachments['+photo_id+'][clazz]"]').val( $self.attr('data-clazz') );
		}
	});
	
	$('a.load-default-link-property').click(function() {
		var $self = $(this);
		var photo_id = $self.attr('data-photoid');
		if (photo_id) {
			$('input[name="attachments['+photo_id+'][rel]"]').val( $('#default_link_rel').val() );
			$('input[name="attachments['+photo_id+'][clazz]"]').val( $('#default_link_class').val() );
		}
	});

<?php if (FlickrPress::getQuickSettings()=='1') { ?>
	$('.batch-send').click(function(){
		if (!$(this).attr('checked')) return;

		var photo_id = $(this).val();
		var title = $('div.filename > span.title', 'div#media-item-'+photo_id).text();
		if (title.length>25) title = title.substring(0,25)+'...';

		var title = 'Quick Settings: ' + title;
		var args = '#TB_inline?width=350&inlineId=inline-settings-content-container';
		var img_group = false;
		tb_show(title, args, img_group);

		var photo_id = $(this).val();
		draw_settings_content(photo_id);
	});

	$('#inline-update-button').click(function(){
		// image_size
		var $content_image_size = $('#inline-settings-image-size');
		var photo_id = $('#inline-photo-id').val();
		var image_size_selector = 'input[name="attachments['+photo_id+'][image-size]"]';
		var image_size = $(image_size_selector+':checked',$content_image_size).val();
		$(image_size_selector, 'div#media-item-'+photo_id).val([image_size]);

		// order 
		var order = $('#inline-order').val();
		$('input[name="attachments['+photo_id+'][order]"]').val(order);

		tb_remove();
	});
	
	function draw_settings_content(photo_id) {
		$('#inline-photo-id').val(photo_id);

		var $content_image_size = $('#inline-settings-image-size');
		var $content_order = $('#inline-settings-order');
		$content_image_size.empty();

		// image_size
		var image_size_content = $('div#media-item-'+photo_id+' > table.describe > tbody > tr.image-size > td.field').html();
		image_size_content = '<p><?php echo __('Size:', FlickrPress::TEXT_DOMAIN) ?></p>'+image_size_content;
		$content_image_size.append(image_size_content);
		var image_size_selector = 'input[name="attachments['+photo_id+'][image-size]"]';
		var image_size = $(image_size_selector+':checked', 'div#media-item-'+photo_id).val();
		$(image_size_selector,$content_image_size).val([image_size]);
		$('input[type=radio]', $content_image_size).each(function(){
			var $self = $(this);
			var id = $self.attr('id');
//			console.log("id=%s", id);
			$('label[for="'+id+'"]').attr('for', 'inline-'+id);
			$self.attr('id', 'inline-'+id);
		});

		// order
		var order = $('input[name="attachments['+photo_id+'][order]"]').val();
		$('#inline-order').val(order);
	}
<?php } ?>
});
</script>
<?php 
}
?>

