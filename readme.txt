=== wp-flickr-press ===
Contributors: tatsuya
Donate link: http://fukata.org/
Tags: images,flickr
Requires at least: 3.0.1
Tested up to: 3.3
Stable tag: 1.7.2

Flickr integration for wordpress plugin.

== Description ==

Flickr integration for wordpress plugin.

Latest source: http://github.com/fukata/wp-flickr-press/

Features

1. Insert flickr media in post.
2. Cache search results.
3. Default settings at insert flickr media.
4. Setting value a tag rel and class property.
5. Support the php safe mode.

Dependencies

1. php-curl http://php.net/manual/ja/book.curl.php

== Installation ==

1. Download the plugin, unpack it and upload the 'wp-flickr-press' folder to your wp-content/plugins directory.
2. To grant write access to wp-content/plugins/wp-flickr-press/cache. 
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. [Create Your Flickr App](http://www.flickr.com/services/apps/create/apply/ "Create Your Flickr App") and settings OAuth Callback URL. Default OAuth Callback URL is `http://"Your Blog Domain"/wp-content/plugins/wp-flickr-press/flickr_oauth_callback.php`
5. Go to [Settings] -> [FlickrPress] to configure the options.

== Frequently Asked Questions ==

None

== Screenshots ==

1. Add flickr media 1
2. Add flickr media 2
3. Tag suggest
4. Batch insert into post
5. Quick Settings
6. New UI Search
7. New UI Insert Post
8. Setting config
9. FullScreen Writing

== Changelog ==
= 1.7.2 =
* Fixed bug v3.3.

= 1.7.1 =
* Add check cache directory permission.

= 1.7.0 =
* Add insert flickr media button when writing fullscreen mode.

= 1.6.2 =
* Change photo window height 550px thumbnail search.

= 1.6.1 =
* Don't Use PHP Session.

= 1.6.0 =
* Add Default Link URL Type.
* Add Photo Size Medium 640.

= 1.5.1 =
* Fixed bug undefined variable.

= 1.5.0 =
* Add Default File URL Size.

= 1.4.0 =
* fixed bug use original size when not exists large size.
* add link property rel and class.

= 1.3.3 =
* Add large size photo. Use original size if don't large size.

= 1.3.2 =
* Fixed bug media_upload.js doesn't load post.php.

= 1.3.1 =
* Disabled cache jquery-flickr-client.

= 1.3.0 =
* Search(Thumbnail)時にOAuthトークンを利用したアクセスに対応

= 1.2.2 =
* 不要なconsole.logを削除

= 1.2.1 =
* メッセージが正常に表示されていなかったバグを修正

= 1.2.0 =
* 国際化(日本語)対応

= 1.1.1 =
* jqueryのflickrプラグイン内のconsole.logをコメントアウト

= 1.1.0 =
* 旧UIの速度改善

= 1.0.0 =
* 新UIを実装

= 0.9.0 =
* POSTで呼び出していた部分をGETに変更

= 0.8.0 =
* 画像個別に取得していたサイズ情報を取得しないように修正

= 0.7.1 =
* 特権管理者以外でも更新できるように修正

= 0.7.0 =
* クイック設定を追加

= 0.6.0 =
* 挿入順序を追加

= 0.5.0 =
* デフォルトのソート項目を追加

= 0.4.0 =
* 検索項目にソートを追加

= 0.3.0 =
* ユーザIDの取得方法を直入力からOAuth連携に修正
* 画像挿入時のテンプレートを追加

= 0.2.0 =
* photosetで検索した際に詳細URL内のownerが抜けていたバグを修正
* マルチサイトを適用した場合に、ページングが動作していなかったバグを修正

= 0.1.0 =
* 一括挿入機能を追加

= 0.0.7 =
* タグサジェスト機能を追加

= 0.0.6 =
* Photosetsのプルダウンが選択状態が維持されないバグを修正
* キャッシュクリア用のボタンを追加

= 0.0.5 =
* API KEY、API SECRET、USER IDを未設定チェックを追加

= 0.0.4 =
* 検索機能追加

= 0.0.1 =
* 初回リリース

== Upgrade Notice ==
