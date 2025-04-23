=== Simple Featured Image Generator ===
Contributors: 	bomura
Donate link: 	https://blog.donguri3.net
Tags: 		featured-image, eyecatch, image-generator, post-thumbnail, title
Tested up to:	6.8
Stable tag:	0.1
License: 	Apache 2.0
License URI: 	https://www.apache.org/licenses/LICENSE-2.0

This plugin provides an eyecatch (featured image) generator for the Classic Editor post screen.  
It generates a PNG image based on the post title, author avatar and customizable colors/frame,  
and then sets it as the post's featured image automatically.

== Description ==
Simple Featured Image Generator は、投稿タイトルと投稿者情報をもとにアイキャッチを生成し、
クラシックエディタの投稿画面からプレビューおよび保存を行うプラグインです。

主な機能：
* タイトル文字列と投稿者アバターをキャンバスに描画  
* フォント色、背景色、フレーム色をカラーピッカーで設定可能  
* プレビュー作成後、保存で「アイキャッチ画像」にセット  
* AJAX による非同期保存機能

== Installation ==
1. プラグインを `/wp-content/plugins/simple-featured-image-generator/` フォルダにアップロード  
2. WordPress 管理画面「プラグイン」から『Simple Featured Image Generator』を有効化  
3. クラシックエディタの投稿編集画面（`投稿 > 新規追加` または `投稿 > 投稿一覧 > 編集`）  
   のサイドバーに「アイキャッチジェネレータ」メタボックスが表示されます。

== Frequently Asked Questions ==

= Gutenberg でも使えますか？ =
いいえ。本プラグインは Classic Editor（クラシックエディタ）専用です。  
Gutenberg 環境では動作しません。

= 生成した画像はどこに保存されますか？ =
`wp-content/uploads/` 配下に `egf_<timestamp>.png` として保存され、  
投稿のアイキャッチ画像として設定されます。

== Screenshots ==
1. `screenshot-1.png`  
   - 投稿編集画面のサイドバーに表示される「アイキャッチジェネレータ」メタボックス  
2. `screenshot-2.png`  
   - プレビュー後のキャンバス表示例  
3. `screenshot-3.png`  
   - 保存後に「アイキャッチ画像」としてセットされた表示例

== Changelog ==
= 0.1 =
* 初回リリース  
* アイキャッチ生成機能の実装  
* プレビュー・保存機能の追加

== Upgrade Notice ==
= 0.1 =
初回リリースです。特に注意点はありません。
