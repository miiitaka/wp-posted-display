# WordPress Posted Display
https://wordpress.org/plugins/wp-posted-display/

Plug-in Posted Display Widget & ShortCode Add.  
You can also save and display your browsing history to Cookie.

* Save your browsing history of the posts to Cookie, you can view the information in the widget and the short code.
* You can create a widget and a short code that can display the posts in any.
* You can view the information in the widget and the short code posts that belong to any category ID.(Multiple specified)
* You can view the information in the widget and the short code posts that belong to any tag ID.(Multiple specified)
* You can view the information in the widget and the short code posts that belong to any user ID.(Multiple specified)

投稿記事情報をウィジェットやショートコードで表示させるプラグインです。閲覧履歴をCookieに保存して表示することもできます。

* 投稿記事の閲覧履歴をCookieに保存して、ウィジェットとショートコードで情報を表示できます。
* 投稿記事を任意で表示できるウィジェットとショートコードを作成できます。
* 任意のカテゴリーIDに属する記事をウィジェットとショートコードで情報を表示できます。（複数指定可）
* 任意のタグIDに属する記事をウィジェットとショートコードで情報を表示できます。（複数指定可）
* 任意のユーザーIDに属する記事をウィジェットとショートコードで情報を表示できます。（複数指定可）

## ShortCode
You can use the short code in the post page or fixed page. It is possible to get a short code with the registered template list, use Copy.  
You can specify the maximum number to be displayed by changing the value of the posts.

投稿ページや固定ページでショートコードを使用できます。登録したテンプレート一覧でショートコードを取得できるので、コピーして使用して下さい。  
postsの値を変更することで表示する最大件数を指定できます。

```
<?php
if ( shortcode_exists( 'wp-posted-display' ) ) {
	echo do_shortcode( '[wp-posted-display id="1" posts="5" sort="0"]' );
}
?>
```

### ShortCode Params Sorted by
* sort="0": Input order（入力順）
* sort="1": Date descending order（日付（降順））
* sort="2": Date ascending order（日付（昇順））
* sort="3": Random（ランダム）

## Change Log

### 1.2.1 (2016-08-17)
- Check : WordPress version 4.6.0 operation check.
- Fixed : setcookie() Warning Error.
- Added : ScreenShots.

### 1.1.4 (2016-06-25)
- Check : WordPress version 4.5.3 operation check.

### 1.1.3 (2016-05-09)
- Check : WordPress version 4.5.2 operation check.
- Check : WordPress version 4.5.1 operation check.
- Check : WordPress version 4.5.0 operation check.

### 1.1.2 (2016-03-23)
- Fixed : Shortcode output bugfix.

### 1.1.1 (2016-03-22)
- Fixed : Modifications to the writing of the PHP5.3-based support of the array.

### 1.1.0 (2016-03-20)
- Added : Template item can be inserted in the click in textarea.
- Updated : Code Refactor.

### 1.0.10 (2016-02-03)
- Check : WordPress version 4.4.2 operation check.

### 1.0.9 (2016-01-10)
- Fixed : Update typo miss.

### 1.0.8 (2016-01-10)
- Added : Adding a template item a "author name".
- Check : WordPress version 4.4.1 operation check.

### 1.0.7 (2015-12-17)
- Added : Plugin images.
- Fixed : Typo miss.

### 1.0.6 (2015-12-11)
- Added : Adding a template item a "tag" and "category".

### 1.0.5 (2015-12-09)
- Check : WordPress version 4.4 operation check.

### 1.0.4 (2015-12-06)
- Renovation : The common functions.

### 1.0.3 (2015-12-03)
- Fixed : Fixed a minor bug.

### 1.0.2 (2015-11-18)
- Fixed : Fixed a minor bug.

### 1.0.1 (2015-11-16)
- The first release.