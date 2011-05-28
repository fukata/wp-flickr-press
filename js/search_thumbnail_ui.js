(function($) {
	$(function() {
		var flickr = new jQuery.FlickrClient({
			apiKey : jQuery("#api_key").val(),
			apiSecret : jQuery("#api_secret").val(),
			userId : jQuery("#user_id").val(),
			oauthToken : jQuery("#oauth_token").val()
		});
		var OPTIONS = {
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
		
		$('#search-btn').click(function(){
			var type = $('input.search-type:checked').val();
			console.log(type);
			if (type=='advanced') {
				var options = {
					per_page : OPTIONS.perpage,
					extras : OPTIONS.extras,
					sort : $("select[name='filter[sort]']").val()
				};
				var keyword = $("input[name='filter[keyword]']").val();
				if (keyword) options["text"] = keyword;
				var tags = $("input[name='filter[tags]']").val();
				if (tags) {
					var splited = tags.split(',');
					var joined = [];
					for (var i = 0; i < splited.length; i++) {
						var s = splited[i].replace(/(^\s+)|(\s+$)/g, "");
						if (s) joined.push(s);
					}
					options["tags"] = joined.join();
				}
				console.log(options);
				flickr.photos_search(options, function(res) {
					console.log("photos.search callback");
					console.log(res);
					init_photos(res.photos);
				});
			} else if (type=='photosets') {
				var options = {
						per_page : OPTIONS.perpage,
						extras : OPTIONS.extras,
						photoset_id : $("select[name='filter[photoset]']").val()
				};
				
				flickr.photosets_getPhotos(options, function(res){
					console.log("photosets_getPhotos callback");
					console.log(res);
					init_photos(res.photoset);
				});
				
			} else if(type=='recent') {
				var options = {
						per_page : OPTIONS.perpage,
						extras : OPTIONS.extras,
						sort : $("select[name='filter[sort]']").val()
				};
				
				flickr.photos_search(options, function(res) {
					console.log("photos.search callback");
					console.log(res);
					init_photos(res.photos);
				});
			}
			
		});
		
		function init_photos(photos) {
			$("#search-results").html("");
			for ( var i = 0; i < photos.photo.length; i++) {
				var photo = photos.photo[i];
				
				var page_url = flickr.getPhotoPageUrl(photo, photos);
				var $img = $("<a></a>").attr("href", page_url).append(
						$("<img />").attr(
								{
									src : flickr.getPhotoUrl(photo,
											OPTIONS.thumbnail_size),
											title : photo["title"]
								}).addClass("photo"));
				var $div = $("<div></div>").addClass("thumbnail").append($img);
				$("#search-results").append($div);
			}
		}
		
		// ===================================
		// photo search 
		// ===================================
		flickr.photos_search({
			per_page : OPTIONS.perpage,
			extras : OPTIONS.extras,
			sort : OPTIONS.sort
		}, function(res) {
			console.log("photos.search callback");
			console.log(res);
			init_photos(res.photos);
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