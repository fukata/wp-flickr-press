=== wp-flickr-press ===
Contributors: tatsuya
Donate link: http://fukata.org/
Tags: images,flickr
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: 1.0.0

Flickr画像を記事本文内に挿入する。

== Description ==

当プラグインは、投稿画面よりFlickrの画像を記事本文内に簡単に挿入するためのプラグインです。

最新のソースは、下記より取得できます。
http://github.com/fukata/wp-flickr-press/

PHP依存ライブラリ

1. php-curl http://php.net/manual/ja/book.curl.php

== Installation ==

1. 解凍後、フォルダ「wp-flickr-press/」をディレクトリ「/wp-content/plugins/」にアップロードする。
2. 同ディレクトリ内のcacheディレクトリに対し、書き込み権限を付与する。 
3. 管理画面よりプラグイン「FlickrPress」をアクティベートを行う。
4. 左サイドバーに表示されているFlickrPressの設定画面より、APIキー、ユーザIDについて設定する。

== Frequently Asked Questions ==

現在なし

== Screenshots ==

1. Add flickr media 1
2. Add flickr media 2
3. Tag suggest
4. Batch insert into post
5. Quick Settings
6. New UI Search
7. New UI Insert Post
8. Setting config

== Changelog ==
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
