(function($) {
	$(function() {
		var flickr = new jQuery.FlickrClient({
			apiKey : jQuery("#api_key").val(),
			apiSecret : jQuery("#api_secret").val(),
			userId : jQuery("#user_id").val(),
			oauthToken : jQuery("#oauth_token").val()
		});
		var options = {
			perpage : 20,
			extras : "url_sq,url_t,url_s,url_m,url_o",
			sort : "date-posted-desc",
			thumbnail_size : "sq"
		};

		// ===================================
		// header menu 
		// ===================================
		$('input.search-type').click(function() {
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
		
		// ===================================
		// photo search 
		// ===================================
		flickr.photos_search({
			per_page : options.perpage,
			extras : options.extras,
			sort : options.sort
		}, function(res) {
			console.log("photos.search callback");
			var photos = res.photos;
			for ( var i = 0; i < photos.photo.length; i++) {
				var photo = photos.photo[i];

				var page_url = flickr.getPhotoPageUrl(photo, photos);
				var $img = $("<a></a>").attr("href", page_url).append(
						$("<img />").attr(
								{
									src : flickr.getPhotoUrl(photo,
											options.thumbnail_size),
									title : photo["title"]
								}).addClass("photo"));
				var $div = $("<div></div>").addClass("thumbnail").append($img);
				$("#search-results").append($div);
			}
			console.log(res);
		});
		
		// ===================================
		// photosets 
		// ===================================
		flickr.photosets_getList({}, function(res) {
			console.log("photosets.getList callback");
			console.log(res);
			var photosets = res.photosets;
			var $filter_photosets = $("select[name='filter[photoset]']");
			for ( var i = 0; i < photosets.photoset.length; i++) {
				var photoset = photosets.photoset[i];
				var option = '<option value="'+photoset.id+'">'+photoset.title._content+'</option>';
				$filter_photosets.append(option);
			}
		});
		
		// ===================================
		// tags 
		// ===================================
		flickr.tags_getListUser({}, function(res) {
			console.log("tags.getListUser callback");
			console.log(res);
			var tags = [];
			for (var i = 0; i < res.who.tags.tag.length; i++) {
				tags.push(res.who.tags.tag[i]._content);
			}
			
			$('#filter-tags').tagSuggest({
				'separator': ',',
				'tagContainer' : 'div',
				'tags': tags
			});
		});
	});
})(jQuery);