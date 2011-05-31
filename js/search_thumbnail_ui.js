(function($) {
	$(function() {
		var flickr = new jQuery.FlickrClient({
			apiKey : jQuery("#api_key").val(),
			apiSecret : jQuery("#api_secret").val(),
			userId : jQuery("#user_id").val(),
			oauthToken : jQuery("#oauth_token").val()
		});
		var pager_search = null;
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
			if (type == 'advanced') {
				jQuery('div#advanced-search-form').slideDown();
				jQuery('div#sort-search-form').slideDown();
				jQuery('div#photosets-search-form').slideUp();
			} else if (type == 'photosets') {
				jQuery('div#photosets-search-form').slideDown();
				jQuery('div#advanced-search-form').slideUp();
				jQuery('div#sort-search-form').slideUp();
			} else if (type == 'recent') {
				jQuery('div#sort-search-form').slideDown();
				jQuery('div#advanced-search-form').slideUp();
				jQuery('div#photosets-search-form').slideUp();
			} else {
				jQuery('div#photosets-search-form').slideUp();
				jQuery('div#advanced-search-form').slideUp();
			}
		});

		$('#search-btn').click(function() {
			var type = $('input.search-type:checked').val();
			console.log(type);
			if (type == 'advanced') {
				var options = {
					per_page : OPTIONS.perpage,
					extras : OPTIONS.extras,
					sort : $("select[name='filter[sort]']").val()
				};
				var keyword = $("input[name='filter[keyword]']").val();
				if (keyword)
					options["text"] = keyword;
				var tags = $("input[name='filter[tags]']").val();
				if (tags) {
					var splited = tags.split(',');
					var joined = [];
					for ( var i = 0; i < splited.length; i++) {
						var s = splited[i].replace(/(^\s+)|(\s+$)/g, "");
						if (s)
							joined.push(s);
					}
					options["tags"] = joined.join();
				}
				console.log(options);
				pager_search = function(page) {
					options['page'] = page;
					pre_search_photos();
					flickr.photos_search(options, function(res) {
						console.log("photos.search callback");
						console.log(res);
						init_photos(res.photos);
					});
				};
				pager_search(1);
			} else if (type == 'photosets') {
				var options = {
					per_page : OPTIONS.perpage,
					extras : OPTIONS.extras,
					photoset_id : $("select[name='filter[photoset]']").val()
				};

				pager_search = function(page) {
					options['page'] = page;
					pre_search_photos();
					flickr.photosets_getPhotos(options, function(res) {
						console.log("photosets_getPhotos callback");
						console.log(res);
						init_photos(res.photoset);
					});
				};
				pager_search(1);
			} else if (type == 'recent') {
				var options = {
					per_page : OPTIONS.perpage,
					extras : OPTIONS.extras,
					sort : $("select[name='filter[sort]']").val()
				};

				pager_search = function(page) {
					options['page'] = page;
					pre_search_photos();
					flickr.photos_search(options, function(res) {
						console.log("photos.search callback");
						console.log(res);
						init_photos(res.photos);
					});
				};
				pager_search(1);
			}

		});

		// ===================================
		// init photos
		// ===================================
		function init_photos(photos) {
			$("#search-results").html("");
			for ( var i = 0; i < photos.photo.length; i++) {
				var photo = photos.photo[i];

				var page_url = flickr.getPhotoPageUrl(photo, photos);
				var title = photo.title;
				if (title.length > 15) {
					title = title.substring(0, 15) + '...';
				}
				var $img = $("<a></a>").addClass("ins-photo").attr({
					href : "javascript:void(0)",
					title : title
				}).append(
						$("<img />").attr(
								{
									src : flickr.getPhotoUrl(photo,
											OPTIONS.thumbnail_size),
									title : photo["title"]
								}).addClass("photo"));
				var $title = $("<div></div>").addClass("title").append(
						$("<a></a>").addClass("ins-photo").attr({
							href : "javascript:void(0)",
							title : title
						}).html(title));
				var $div = $("<div></div>").addClass("thumbnail").append($img)
						.append($title);
				$("#search-results").append($div);
			}
			init_pager(photos);
		}

		function init_pager(photos) {
			var link_num = 10;
			var page = photos.page;
			var pages = photos.pages;
			var half_link_num = link_num / 2;
			var start_link = page - half_link_num + 1;
			var end_link = page + half_link_num - 1;

			if (page <= half_link_num) {
				start_link = 1;
				end_link = (pages > link_num) ? link_num : pages;
			} else if (page + half_link_num > pages) {
				start_link = page - half_link_num - 1;
				end_link = pages;
			}

			var $pager = $("<div></div>").addClass("pager");
			if (page > 1) {
				var $first = $("<a></a>").addClass("first").attr({
					href : "javascript:void(0)",
					page : 1
				}).text("<<");
				$pager.append($first);
				
				var $prev = $("<a></a>").addClass("prev").attr({
					href : "javascript:void(0)",
					page : page - 1
				}).text("<");
				$pager.append($prev);
			}
			
			for ( var i = start_link; i <= end_link; i++) {
				if (i == page) {
					var $link = $("<span></span>").addClass('current').text(i);
					$pager.append($link);
				} else {
					var $link = $("<a></a>").addClass("page").attr({
						href : "javascript:void(0)",
						page : i
					}).text(i);
					$pager.append($link);
				}
			}
			
			if (page < pages) {
				var $next = $("<a></a>").addClass("next").attr({
					href : "javascript:void(0)",
					page : page + 1
				}).text(">");
				$pager.append($next);

				var $end = $("<a></a>").addClass("end").attr({
					href : "javascript:void(0)",
					page : pages
				}).text(">>");
				$pager.append($end);
			}
			$(".pager-container").html($pager);
		}

		$("div.pager > a.page, div.pager > a.prev, div.pager > a.next, div.pager > a.first, div.pager > a.end").live(
				'click', function() {
					var $self = $(this);
					console.log($self.attr('page'));
					if ($.isFunction(pager_search)) {
						console.log("pager_search");
						pager_search($self.attr('page'));
					}
				});

		function pre_search_photos() {
			$("#search-results").html("");
			$("#search-results").append( $("<img/>").attr({
				src: "images/loader.gif" 
			}) );
		}
		
		$(".ins-photo").live('click', function(){
			var $self = $(this);
			var title = $self.attr('title');
			var args = '#TB_inline?width=600&inlineId=inline-settings-content-container';
			var img_group = false;
			tb_show(title, args, img_group);
		});
		
		// ===================================
		// photo search
		// ===================================
		var options = {
			per_page : OPTIONS.perpage,
			extras : OPTIONS.extras,
			sort : OPTIONS.sort
		};
		pager_search = function(page) {
			console.log("pager_search: %s", page);
			options['page'] = page;
			pre_search_photos();
			flickr.photos_search(options, function(res) {
				console.log("photos.search callback");
				console.log(res);
				init_photos(res.photos);
			});
		};
		pager_search(1);

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
				var option = '<option value="' + photoset.id + '">'
						+ photoset.title._content + '</option>';
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
			for ( var i = 0; i < res.who.tags.tag.length; i++) {
				tags.push(res.who.tags.tag[i]._content);
			}

			$('#filter-tags').tagSuggest({
				'separator' : ',',
				'tagContainer' : 'div',
				'tags' : tags
			});
		});
	});
})(jQuery);