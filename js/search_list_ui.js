(function($) {
	$(function() {
		var flickr = new jQuery.FlickrClient({
			apiKey : jQuery("#api_key").val(),
			apiSecret : jQuery("#api_secret").val(),
			userId : jQuery("#user_id").val(),
			oauthToken : jQuery("#oauth_token").val()
		});
		
		// ===================================
		// photosets
		// ===================================
		flickr.photosets_getList({}, function(res) {
//			console.log("photosets.getList callback");
//			console.log(res);
			var selected_photoset_id = $("#photoset_id").val(); 
			var photosets = res.photosets;
			var $filter_photosets = $("select[name='filter[photoset]']");
			for ( var i = 0; i < photosets.photoset.length; i++) {
				var photoset = photosets.photoset[i];
				var option = '<option value="' + photoset.id + '">'
						+ photoset.title._content + '</option>';
				$filter_photosets.append(option);
			}
			$("option[value='"+selected_photoset_id+"']", $filter_photosets).attr("selected", true);
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


		$('a.toggle-image-properties, a.toggle-link-properties').live("click", function() {
			var $self = $(this);
			$self.find('span.toggle').toggle();
			$self.parents('tr').find('td.field').toggle();
		});
	});
})(jQuery);
