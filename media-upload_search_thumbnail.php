<?php if(!class_exists('FlickrPress')) die('Not initialize FlickrPress.');

wp_enqueue_style('thickbox');
wp_enqueue_script('thickbox');

$pluginPath = FlickrPress::getPluginUrl();
wp_enqueue_style("media-upload_search", "$pluginPath/css/media-upload_search_thumbnail.css", array(), FlickrPress::VERSION);
wp_enqueue_style("jquery.tag", "$pluginPath/css/jquery.tag.css", array(), FlickrPress::VERSION);
wp_enqueue_script("jquery.tag", "$pluginPath/js/jquery.tag.js", array(), FlickrPress::VERSION);
wp_enqueue_script("jquery.md5", "$pluginPath/js/jquery.md5.js", array(), FlickrPress::VERSION);
wp_enqueue_script("jquery.flickr-client", "$pluginPath/js/jquery.flickr-client.js", array(), FlickrPress::VERSION);
wp_enqueue_script("search_list_ui", "$pluginPath/js/search_thumbnail_ui.js", array(), FlickrPress::VERSION);

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
	
	$sort = FlickrPress::getDefaultSort();
?>

<?php include_once dirname(__FILE__).'/inc.header_tab.php' ?>

<div id="params" style="display: none;">
	<input type="hidden" name="api_key" id="api_key" value="<?php echo FlickrPress::getApiKey() ?>" />
	<input type="hidden" name="api_secret" id="api_secret" value="<?php echo FlickrPress::getApiSecret() ?>" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo FlickrPress::getUserId() ?>" />
	<input type="hidden" name="oauth_token" id="oauth_token" value="<?php echo FlickrPress::getOAuthToken() ?>" />
	<input type="hidden" name="enable_path_alias" id="enable_path_alias" value="<?php echo FlickrPress::enablePathAlias() ? '1' : '0' ?>" />
</div>

<div id="search-form">
	<input type="hidden" name="post_id" id="post_id" value="<?php echo $_GET['post_id'] ?>" />
	<input type="hidden" name="type" id="type" value="<?php echo $_GET['type'] ?>" />
	<input type="hidden" name="mode" id="mode" value="<?php echo $_GET['mode'] ?>" />
	<input type="hidden" name="TB_iframe" id="TB_iframe" value="<?php echo $_GET['TB_iframe'] ?>" />
	
	<div id="search-header">
		<p>
			<input type="radio" name="filter[type]" value="recent" class="search-type" id="filter-type-recent" checked="checked"/><label for="filter-type-recent"><?php echo __('Recent upload', FlickrPress::TEXT_DOMAIN) ?></label>
			<input type="radio" name="filter[type]" value="photosets" class="search-type" id="filter-type-photosets"/><label for="filter-type-photosets"><?php echo __('Photosets', FlickrPress::TEXT_DOMAIN) ?></label>
			<input type="radio" name="filter[type]" value="advanced" class="search-type" id="filter-type-advanced"/><label for="filter-type-advanced"><?php echo __('Advanced', FlickrPress::TEXT_DOMAIN) ?></label>
		</p>
		<div id="sort-search-form">
			<p class="field-row">
				<span class="field-label"><?php echo __('Sort:', FlickrPress::TEXT_DOMAIN) ?></span>
				<select name="filter[sort]">
				<?php foreach ($sorts as $label => $val) { ?>
					<option value="<?php echo $val ?>" <?php if ($val==$sort) {echo 'selected="selected"';} ?>><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></option>
				<?php } ?>
				</select>
			</p>
		</div>
		<div id="advanced-search-form" class="search-form-off">
			<p class="field-row"><span class="field-label"><?php echo __('Keyword:', FlickrPress::TEXT_DOMAIN) ?></span><input type="text" name="filter[keyword]" value="<?php echo $filter['keyword'] ?>" size="50"/></p>
			<p class="field-row"><span class="field-label"><?php echo __('Tags:', FlickrPress::TEXT_DOMAIN) ?></span><input type="text" name="filter[tags]" value="<?php echo $filter['tags'] ?>" size="50" id="filter-tags" autocomplete="off"/></p>
		</div>
		<div id="photosets-search-form" class="search-form-off">
			<p class="field-row"><span class="field-label"><?php echo __('Photosets:', FlickrPress::TEXT_DOMAIN) ?></span>
				<select name="filter[photoset]"></select>
			</p>
		</div>

		<p><input type="button" value="<?php echo __('Search', FlickrPress::TEXT_DOMAIN) ?>" class="button" id="search-btn"/></p>

	</div>
	
	<div class="pager-container clearfix"></div>
	<div id="search-results"></div>
	<br clear="all"/>
	<div class="pager-container clearfix"></div>
	<div id="buttons-container" class="clearfix">
        <div class="buttons">
            <button class="multiple-insert-btn button"><?php echo __('Batch Insert into Post', FlickrPress::TEXT_DOMAIN); ?></button>
        </div>
    </div>

</div>
	
</form>

<div id="inline-settings-content-container" style="display: none;">
    <div class="inline-container">
        <input type="hidden" name="insert_template" id="inline-insert-template" value="<?php echo htmlspecialchars(FlickrPress::getInsertTemplate()) ?>" />
        <input type="hidden" name="default_size" id="inline-default_size" value="<?php echo FlickrPress::getDefaultSize() ?>" />
        <input type="hidden" name="default_link" id="inline-default_link" value="<?php echo FlickrPress::getDefaultLink() ?>" />
        <input type="hidden" name="default_link_rel" id="inline-default_link_rel" value="<?php echo FlickrPress::getDefaultLinkRel() ?>" />
        <input type="hidden" name="default_link_class" id="inline-default_link_class" value="<?php echo FlickrPress::getDefaultLinkClass() ?>" />
        <input type="hidden" name="default_file_url_size" id="inline-default_file_url_size" value="<?php echo FlickrPress::getDefaultFileURLSize() ?>" />

        <div class="inline-header">
            <ul class="inline-tabs">
                <li><a href="javascript:void(0)" data-type="image" class="current">Image</a></li>
                <li><a href="javascript:void(0)" data-type="player">Player</a></li>
            </ul>
        </div>
       
        <div class="inline-tab-content" id="inline-tab-image">
            <table class="describe">
                <tbody>
                    <tr class="post_title">
                        <th valign="top" scope="row" class="label"><label for="inline-title"><span class="alignleft"><?php echo __('Title', FlickrPress::TEXT_DOMAIN)?></span><span class="alignright"><br class="clear"></label></th>
                        <td class="field"><input type="text" class="text" id="inline-title" name="inline-title" value="" aria-required="true"></td>
                    </tr>
                    <tr class="url">
                        <th valign="top" scope="row" class="label"><label for="inline-url"><span class="alignleft"><?php echo __('Link URL', FlickrPress::TEXT_DOMAIN)?></span><br class="clear"></label></th>
                        <td class="field">
                            <input type="text" id="inline-url" class="text urlfield" name="inline-url" value=""><br>
                            <button type="button" class="button urlnone" title=""><?php echo __('None', FlickrPress::TEXT_DOMAIN) ?></button>
                            <button type="button" class="button urlfile" id="inline-url-file" title=""><?php echo __('File URL', FlickrPress::TEXT_DOMAIN) ?></button>
                            <button type="button" class="button urlpage" id="inline-url-page" title=""><?php echo __('Page URL', FlickrPress::TEXT_DOMAIN) ?></button>
                            <p class="help"><?php echo __('Enter a link URL or click above for presets.', FlickrPress::TEXT_DOMAIN) ?></p>
                        </td>
                    </tr>
                    <tr class="target">
                        <th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span> <?php echo __('Link Target', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
                        <td class="field" style="display:none;">
                            <?php foreach ($targets as $label => $target) { ?>
                            <?php $checked = FlickrPress::getDefaultTarget()==$target ? " checked='checked'" : '' ?>
                                <input type="radio" name="inline-target" id="inline-link-target-<?php echo $target ?>" value="<?php echo $target ?>" <?php echo $checked ?>/><label for="inline-link-target-<?php echo $target ?>" class="link-target-<?php echo $target ?>-label"><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr class="align">
                        <th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span> <?php echo __('Alignment', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
                        <td class="field" style="display:none;">
                            <?php foreach ($alignes as $label => $align) { ?>
                                <?php $checked = FlickrPress::getDefaultAlign()==$align ? " checked='checked'" : '' ?>
                                <input type="radio" name="inline-align" id="inline-image-align-<?php echo $align ?>" value="<?php echo $align ?>"<?php echo $checked?> /><label for="inline-image-align-<?php echo $align ?>" class="align image-align-<?php echo $align ?>-label"/><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr class="image-size">
                        <th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span> <?php echo __('Size', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
                        <td class="field" style="display:none;">
                            <?php foreach(FlickrPress::$SIZES as $size => $url) { ?>
                                <?php $checked = FlickrPress::getDefaultSize()==$size ? " checked='checked'" : '' ?>
                                <div class="image-size-item"><input name="inline-image-size" value="<?php echo $photo[$url] ?>" type="radio" id="inline-image-size-<?php echo $size ?>"<?php echo $checked?>/><label for="inline-image-size-<?php echo $size ?>"><?php echo __(FlickrPress::$SIZE_LABELS[$size], FlickrPress::TEXT_DOMAIN) ?></label></div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr class="image-property">
                        <th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span> <?php echo __('Image Class Property', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
                        <td class="field" style="display:none;">
                            <p><span><?php echo __('Class:', FlickrPress::TEXT_DOMAIN) ?></span><input name="inline-image-clazz" value="" type="text" /></p>
                            <p><?php echo __('Available Charactors: 0-9a-zA-Z [] Space UnderScore Hyphen', FlickrPress::TEXT_DOMAIN) ?></p>
                            <p>
                                <select class="extend-image-properties">
                                <option value="" data-photoid=""></option>
                                <?php for ($i=0; $i<count($extendImageProperties); $i++) { ?>
                                <option value="" data-clazz="<?php echo $extendImageProperties[$i]->clazz ?>"><?php echo $extendImageProperties[$i]->title ?></option>
                                <?php } ?>
                                </select>
                            </p>
                        </td>
                    </tr>

                    <tr class="link-property">
                        <th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span> <?php echo __('Link Rel and Class Property', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
                        <td class="field" style="display:none;">
                            <p><span><?php echo __('Rel:', FlickrPress::TEXT_DOMAIN) ?></span><input name="inline-link-rel" value="<?php echo FlickrPress::getDefaultLinkRel() ?>" type="text" /></p>
                            <p><span><?php echo __('Class:', FlickrPress::TEXT_DOMAIN) ?></span><input name="inline-link-clazz" value="<?php echo FlickrPress::getDefaultLinkClass() ?>" type="text" /></p>
                            <p><?php echo __('Available Charactors: 0-9a-zA-Z [] Space UnderScore Hyphen', FlickrPress::TEXT_DOMAIN) ?></p>
                            <p>
                                <select class="extend-link-properties">
                                <option value="" data-photoid=""></option>
                                <?php for ($i=0; $i<count($extendLinkProperties); $i++) { ?>
                                <option value="" data-rel="<?php echo $extendLinkProperties[$i]->rel ?>" data-clazz="<?php echo $extendLinkProperties[$i]->clazz ?>"><?php echo $extendLinkProperties[$i]->title ?></option>
                                <?php } ?>
                                </select>
                                <a href="javascript:void(0)" class="button load-default-link-property" data-photoid="<?php echo $photo['id'] ?>"><?php echo __('Load Default', FlickrPress::TEXT_DOMAIN) ?></a>
                            </p>
                        </td>
                    </tr>
                    <tr class="submit">
                        <td></td>
                        <td class="savesend">
                            <input type="button" class="inline-ins-btn" data-close="1" class="button" name="send" value="<?php echo __('Insert into Post and Close', FlickrPress::TEXT_DOMAIN)?>">
                            <input type="button" class="inline-ins-btn" data-close="0" class="button" name="send" value="<?php echo __('Insert into Post and Continue', FlickrPress::TEXT_DOMAIN)?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="inline-tab-content" id="inline-tab-player" style="display:none">
            <table class="describe">
                <tbody>
                    <tr class="url">
                        <th valign="top" scope="row" class="label"><label for="inline-player-url"><span class="alignleft"><?php echo __('Player URL', FlickrPress::TEXT_DOMAIN)?></span><br class="clear"></label></th>
                        <td class="field">
                            <input type="text" id="inline-player-url" class="text urlfield" name="inline-player-url" value=""><br>
                            <button type="button" class="button urlphotostream" id="inline-url-photostream"><?php echo __('Photostream', FlickrPress::TEXT_DOMAIN) ?></button>
                            <button type="button" class="button urlset" id="inline-url-set"><?php echo __('Set', FlickrPress::TEXT_DOMAIN) ?></button>
                        </td>
                    </tr>
                    <tr class="align">
                        <th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span> <?php echo __('Alignment', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
                        <td class="field" style="display:none;">
                            <?php foreach ($alignes as $label => $align) { ?>
                                <?php $checked = FlickrPress::getDefaultAlign()==$align ? " checked='checked'" : '' ?>
                                <input type="radio" name="inline-player-align" id="inline-player-align-<?php echo $align ?>" value="<?php echo $align ?>"<?php echo $checked?> /><label for="inline-player-align-<?php echo $align ?>" class="align image-align-<?php echo $align ?>-label"/><?php echo __($label, FlickrPress::TEXT_DOMAIN) ?></label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr class="player-size">
                        <th valign="top" scope="row" class="label"><label for=""><span class="alignleft"><a href="javascript:void(0)" class="toggle-link"><span class="toggle" style="display:none;">[-]</span><span class="toggle">[+]</span> <?php echo __('Size', FlickrPress::TEXT_DOMAIN)?></a></span><br class="clear"></label></th>
                        <td class="field" style="display:none;">
                            <?php foreach(FlickrPress::$SIZES as $size => $url) { ?>
                                <?php $checked = FlickrPress::getDefaultSize()==$size ? " checked='checked'" : '' ?>
                                <div class="player-size-item"><input name="inline-player-size" value="" type="radio" id="inline-player-size-<?php echo $size ?>"<?php echo $checked?>/><label for="inline-player-size-<?php echo $size ?>"><?php echo __(FlickrPress::$SIZE_LABELS[$size], FlickrPress::TEXT_DOMAIN) ?></label></div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr class="submit">
                        <td></td>
                        <td class="savesend">
                            <input type="button" class="inline-player-ins-btn" data-close="1" class="button" name="send" value="<?php echo __('Insert into Post and Close', FlickrPress::TEXT_DOMAIN)?>">
                            <input type="button" class="inline-player-ins-btn" data-close="0" class="button" name="send" value="<?php echo __('Insert into Post and Continue', FlickrPress::TEXT_DOMAIN)?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> 
    </div>
</div>

<?php
}
