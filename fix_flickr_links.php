<?php

function flickrLinkFixer($content)
{
	# Find FlickrLink Content
	
	$client = FlickrPress::getClient();
	$url = 'www.flickr.com/photos/';
	$photo_pattern = '/www\.flickr\.com\/photos\/([\d]+@[\d\w]+)\/([\d]+).+?src="(.+?)"/s';
	if (strpos( $url , $content) >= 0) {
		
		preg_match_all( $photo_pattern , $content, $matches, PREG_SET_ORDER);
		
		foreach ($matches as $val) {
			
			
			$user_id = $val[1];
			$photo_id = $val[2];
			$old_url = $val[3];
			
			$photo = $client->photos_getInfo($photo_id);
			$new_url = $client->buildPhotoURL($photo['photo']);
			
			if ($old_url != $new_url) {
				$content = str_replace($old_url,$new_url,$content,$count);
			}
			
		}		
	}
	return $content;
}

?>