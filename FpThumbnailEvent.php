<?php
class FpThumbnailEvent {
	private function __construct() {}

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

    public static function actionAddMetaBoxesPost($post) {
        add_meta_box(
            'wpfp_post_thumbnail', 
            __('Flickr Thumbnail', FlickrPress::TEXT_DOMAIN), 
            array(__CLASS__, 'getMetaBoxHtml'),
            'post', 
            'advanced', 
            'default',
            array($post)
        );
    }
    public static function getMetaBoxHtml($post) {
?>
<label class="selectit"><input value="1" type="checkbox" name="wpfp_use_post_thumbnail"> Use Post Thumbnail</label>
<?php
    }

}
