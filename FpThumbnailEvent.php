<?php
class FpThumbnailEvent {
  private function __construct() {}

  public static function get_the_post_thumbnail_src($img, $size) {
    $img = (preg_match('~\bsrc="(https?:\/\/farm[0-9]+\.staticflickr\.com\/[^"]+\.(?:jpg|JPG|jpeg|jpeg|gif|GIF|png|PNG))"~', $img, $matches)) ? $matches[1] : '';
    if (!$img) {
      return '';
    }

    $suffix = '' === $size ? '' : "_$size";
    if ( preg_match('/_\w{1,2}\.\w+$/', $img) ) {
      $img = preg_replace('/^(.+)_(\w+)(\.\w+)$/', '${1}' . $suffix. '${3}', $img);
    } else {
      $img = preg_replace('/^(.+)(\.\w+)$/', '${1}' . $suffix . '${2}', $img);
    }

    return $img;
  }

  public static function filterPostThumbnailHtml($html, $post_id, $post_thumbnail_id, $size, $attr) {
    global $post;
    if ( $html ) {
      return $html;
    }

    $thumbnailSize = FlickrPress::getThumbnailSizeSuffix($size);
    if ( null === $thumbnailSize ) {
      $thumbnailSize = FlickrPress::getThumbnailSizeSuffix();
    }

    $src = self::get_the_post_thumbnail_src($post->post_content, $thumbnailSize);
    if ( $src ) {
      return '<img src="' . $src . '" class="attachment-post-thumbnail wp-post-image"/>';
    } else {
      return '';
    }
  }

  public static function filterGetPostMetadata($metadata, $object_id, $meta_key, $single) {
    $meta_cache = wp_cache_get($object_id, 'post_meta');
    if ( FlickrPress::isExtractThumbnailByMetadata($meta_cache) && '_thumbnail_id' === $meta_key ) {
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
    $use = FlickrPress::isExtractThumbnailByPostID($post->ID); 
?>
  <p>Use Post Thumbnail: <label class="selectit">Yes <input value="1" type="radio" name="wpfp_use_post_thumbnail" <?php echo $use ? 'checked="checked"' : ''; ?>></label>
  <label class="selectit">No <input value="0" type="radio" name="wpfp_use_post_thumbnail" <?php echo !$use ? 'checked="checked"' : ''; ?>></label></p>
<?php
  }

  public static function filterWpInsertPostData($data, $postarr) {
    update_post_meta($postarr['ID'], 'wpfp_use_post_thumbnail', $postarr['wpfp_use_post_thumbnail']);
    return $data;
  }

}
