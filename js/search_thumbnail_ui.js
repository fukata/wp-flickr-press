(function($) {
	$(function() {
		var flickr = new jQuery.FlickrClient({
			apiKey : jQuery("#api_key").val(),
			apiSecret : jQuery("#api_secret").val(),
			userId : jQuery("#user_id").val(),
			oauthToken : jQuery("#oauth_token").val(),
			enablePathAlias: jQuery("#enable_path_alias").val() == '1'
		});
		var pager_search = null;
		var OPTIONS = {
			perpage : 20,
			extras : flickr.SIZE_VALUES.join(',') + ",path_alias",
			sort : "date-posted-desc",
			thumbnail_size : "sq"
		};

		// ===================================
		// tab menu
		// ===================================
        $('.inline-tabs > li > a').live('click', function(){
            var $self = $(this);
            if ( $self.hasClass('current') ) return;

            // switch tab
            var type = $self.data('type');
            $('.inline-tabs > li > a.current').removeClass('current');
            $self.addClass('current');

            // switch content
            $('.inline-tab-content').hide();
            $('#inline-tab-' + type).show();
        });

		// ===================================
		// search menu
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
//			console.log(type);
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
//				console.log(options);
				pager_search = function(page) {
					options['page'] = page;
					pre_search_photos();
					flickr.photos_search(options, function(res) {
//						console.log("photos.search callback");
//						console.log(res);
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
//						console.log("photosets_getPhotos callback");
//						console.log(res);
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
//						console.log("photos.search callback");
//						console.log(res);
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
			var title_max = 20;
			$("#search-results").empty();
			
			var ins_popup = function() {
				var $self = $(this);
				var idx = $self.attr('idx');
				var photo = photos.photo[idx];
				var title = photo.title;
				var args = '#TB_inline?width=600&height=500&inlineId=inline-settings-content-container';
				var img_group = false;
				tb_show(title, args, img_group);
				
				draw_inline_content(photo, photos);
			};
			
			for ( var i = 0; i < photos.photo.length; i++) {
				var photo = photos.photo[i];
				var page_url = flickr.getPhotoPageUrl(photo, photos);
				var title = photo.title;
				if (title.length > title_max) {
					title = title.substring(0, title_max) + '...';
				}
				
				var $img = $("<a></a>").addClass("ins-photo").attr({
					href : "javascript:void(0)",
					title : title,
					idx : i
				});
				
				$img.click(ins_popup);
				$img.append(
					$("<img />").attr({
						src : flickr.getPhotoUrl(photo, OPTIONS.thumbnail_size),
						title : photo["title"]
					}).addClass("photo"));
				var $title = $("<div></div>").addClass("title").append(
					$("<a></a>").addClass("ins-photo").attr({
						href : "javascript:void(0)",
						title : title,
						idx : i
					}).click(ins_popup).html(title)
				);
				var $div = $("<div></div>").addClass("thumbnail").append($img).append($title);
				$("#search-results").append($div);
			}
			init_pager(photos);
		}

		function init_pager(photos) {
			var link_num = 10;
			var page = parseInt(photos.page);
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
//				console.log($self.attr('page'));
				if ($.isFunction(pager_search)) {
//					console.log("pager_search");
					pager_search($self.attr('page'));
				}
			}
		);

		function pre_search_photos() {
			$("div.pager-container").empty();
			$("#search-results").empty();
			$("#search-results").append( $("<img/>").attr({
				src: "../wp-content/plugins/wp-flickr-press/images/loader.gif" 
			}) );
		}
		
		// ===================================
		// inline contents
		// ===================================
		function draw_inline_content(photo, photos) {
//			console.log("draw_inline_content");
//			console.log(photo);
			$("#inline-title").val( photo.title );
			$("#inline-url").val( getDefaultLinkValue(photo, photos) );
			$("#inline-url-file").val( flickr.getPhotoUrl(photo, $('#inline-default_file_url_size').val()) );
			$("#inline-url-page").val( flickr.getPhotoPageUrl(photo, photos) );
			$.each(flickr.SIZE_KEYS, function(idx, size){
				var url = flickr.getPhotoUrl(photo, size);
				$("#inline-image-size-"+size).val( url );
			});

            var playerUrlInPhotostream = flickr.getPlayerUrl(photo, photos);
			$("#inline-url-photostream").val( playerUrlInPhotostream );
            if ( $("input[name='filter[type]']:checked").val() == 'photosets' ) {
                var setId = $("select[name='filter[photoset]']").val();
                var playerUrlInSet = flickr.getPlayerUrl(photo, photos, 'set-' + setId );
                $("#inline-url-set").val( playerUrlInSet ).show();
                $("#inline-player-url").val( playerUrlInSet );
            } else {
                $("#inline-url-set").hide();
                $("#inline-player-url").val( playerUrlInPhotostream );
            }
			$.each(flickr.SIZE_KEYS, function(idx, size) {
                var height = photo['height_' + size];
                var width = photo['width_' + size];
                if ( width && height ) {
                    $('#inline-player-size-' + size).val(width + ',' + height);
                }
            });
		}
		function getDefaultLinkValue(photo, photos) {
			var linkType = $('#inline-default_link').val();
			var link = '';
			switch (linkType) {
			case 'file':
				link = flickr.getPhotoUrl(photo, $('#inline-default_file_url_size').val());
				break;
			case 'page':
				link = flickr.getPhotoPageUrl(photo, photos);
				break;
			}
			return link;
		}
		$(".urlnone, .urlfile, .urlpage").live("click", function(){
			$("#inline-url").val( $(this).val() );
		});
		$(".urlphotostream, .urlset").live("click", function(){
			$("#inline-player-url").val( $(this).val() );
		});
		$(".inline-player-ins-btn").live("click", function(){
			var close = $(this).data('close') == '1';
			var align = $("input[name='inline-player-align']:checked").val();
            var size = $("input[name='inline-player-size']:checked").val().split(',');
            var width = size[0];
            var height = size[1];
            var url = $('#inline-player-url').val();
            var clazz = '';
            if ( align ) {
                clazz += 'align' + align;
            }

            var html = '<iframe src="' + url + '" width="' + width + '" height="' + height + '" frameborder="0" class="' + clazz + '" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>';
			
			fp_media_send_to_editor(html, close);
		});
		$(".inline-ins-btn").live("click", function(){
			var link = $("#inline-url").val();
			var target = $("input[name='inline-target']:checked").val();
			target = target ? ' target="' + target + '"' : '';
			var align = $("input[name='inline-align']:checked").val();
			var imageClazz = $("input[name='inline-image-clazz']").val();
			var alt = esc_attr( $("#inline-title").val() );
			var src = $("input[name='inline-image-size']:checked").val();
			var clazz = "";
			var close = $(this).data('close') == '1';
			
			if (align) {
				clazz = 'align' + align;
			}
			if (imageClazz) {
				clazz = clazz ? clazz + ' ' + imageClazz : imageClazz;
			}
			clazz = clazz ? ' class="' + clazz + '"' : '';
			
			var rel = _remove_invalid_link_chars( $('input[name="inline-link-rel"]').val() );
			if ( rel ) {
				rel = ' rel="' + rel + '"';
			}
			
			var aclazz = _remove_invalid_link_chars( $('input[name="inline-link-clazz"]').val() );
			if ( aclazz ) {
				aclazz = ' class="' + aclazz + '"';
			}
			
			var html = '<img src="' + src + '" alt="' + alt + '"' + clazz + '/>';
			if (link) {
				var title = ' title="' + alt + '"';
				html = '<a href="' + link + '"' + target + aclazz + rel + title + '>' + html + '</a>';
			}
			html += "\n";
			
			fp_media_send_to_editor(html, close);
		});
		function esc_attr(str) {
			if (!str || str == '') return '';
			if (!/[&<>"]/.test(str)) return str;

			return str.replace(/&/g, '&amp;')
					  .replace(/</g, '&lt;')
					  .replace(/>/g, '&gt;')
					  .replace(/"/g, '&quot;')
					  ;
		}
		function _remove_invalid_link_chars(str) {
			return str.replace(/[^0-9a-zA-Z\[\]\s_]+/g,'');
		}
		function fp_media_send_to_editor(html, close) {
			close = close || false;
			var win = window.dialogArguments || opener || parent || top;
			win.fp_send_to_editor(html, close);
			
			if (!close) $("#TB_closeWindowButton").trigger("click");
		}
		$('select.extend-image-properties').change(function() {
			var $self = $(this.options[this.selectedIndex]);
			if ($self.attr('data-clazz')) {
				$('input[name="inline-image-clazz"]').val( $self.attr('data-clazz') );
			}
		});

		$('select.extend-link-properties').change(function() {
			var $self = $(this.options[this.selectedIndex]);
			if ($self.attr('data-rel') || $self.attr('data-clazz')) {
				$('input[name="inline-link-rel"]').val( $self.attr('data-rel') );
				$('input[name="inline-link-clazz"]').val( $self.attr('data-clazz') );
			}
		});
		
		$('a.load-default-link-property').live("click", function() {
			var $self = $(this);
			$('input[name="inline-link-rel"]').val( $('#ineline-default_link_rel').val() );
			$('input[name="inline-link-clazz"]').val( $('#ineline-default_link_class').val() );
		});

		$('a.toggle-link').live("click", function() {
			var $self = $(this);
			$self.find('span.toggle').toggle();
			$self.parents('tr').find('td.field').toggle();
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
//			console.log("pager_search: %s", page);
			options['page'] = page;
			pre_search_photos();
			flickr.photos_search(options, function(res) {
//				console.log("photos.search callback");
//				console.log(res);
				init_photos(res.photos);
			});
		};
		pager_search(1);

		// ===================================
		// photosets
		// ===================================
		flickr.photosets_getList({}, function(res) {
//			console.log("photosets.getList callback");
//			console.log(res);
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
//			console.log("tags.getListUser callback");
//			console.log(res);
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
