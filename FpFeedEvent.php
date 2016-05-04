<?php
class FpFeedEvent {
  private function __construct() {}

  public static function actionInsertImageNode() {
    global $post;
    if ( FlickrPress::isExtractThumbnailByPostID( $post->ID ) ) {
      $thumbnailSize = FlickrPress::getThumbnailSizeSuffix('m');
      $src = FpThumbnailEvent::get_the_post_thumbnail_src($post->post_content, $thumbnailSize);
      if ( $src ) {
        echo("<image>{$src}</image>\n");
      }
    }
  }
}
