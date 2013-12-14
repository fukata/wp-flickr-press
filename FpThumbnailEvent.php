<?php
class FpThumbnailEvent {
	private function __construct() {}

    // http://farm3.staticflickr.com/2855/11325807274_8a1183618c_m.jpg
    static function get_the_post_thumbnail_src($img) {
        return (preg_match('~\bsrc="(https?:\/\/farm[0-9]+\.staticflickr\.com\/[^"]+\.(?:jpg|JPG|jpeg|jpeg|gif|GIF|png|PNG))"~', $img, $matches)) ? $matches[1] : '';
    }

	public static function filterPostThumbnailHtml($html, $post_id, $post_thumbnail_id, $size, $attr) {
        global $post;
        if ( $html ) {
            return $html;
        }

        $src = self::get_the_post_thumbnail_src($post->post_content);
        return '<img src="' . $src . '"/>';
    }

    public static function filterGetPostMetadata($metadata, $object_id, $meta_key, $single) {
        if ($meta_key === "_thumbnail_id") {
            return PHP_INT_MAX;
        } else {
            return null;
        }
    }
}
