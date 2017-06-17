=== WordPress Posted Display ===
Contributors: miiitaka
Tags: post, posts, widget, history, cookie, category, tag, shortcode
Requires at least: 4.3.1
Tested up to: 4.8.0
Stable tag: 2.0.7

Plug-in Posted Display Widget & ShortCode Add. You can also save and display your browsing history to Cookie.

== Description ==

Plug-in Posted Display Widget & ShortCode Add. You can also save and display your browsing history to Cookie.

* Save your browsing history of the posts to Cookie, you can view the information in the widget and the short code.
* You can create a widget and a short code that can display the posts in any.
* You can view the information in the widget and the short code posts that belong to any category ID.(Multiple specified)
* You can view the information in the widget and the short code posts that belong to any tag ID.(Multiple specified)
* You can view the information in the widget and the short code posts that belong to any user ID.(Multiple specified)

**In a post page or fixed page**

You can use the short code in the post page or fixed page. It is possible to get a short code with the registered template list, use Copy.
You can specify the maximum number to be displayed by changing the value of the posts.

[ Example ]
`
<?php
if ( shortcode_exists( 'wp-posted-display' ) ) {
	echo do_shortcode( '[wp-posted-display id="1" posts="5" sort="0"]' );
}
?>
`

= ShortCode Params Sorted by =
* sort="0": Input order
* sort="1": Date descending order
* sort="2": Date ascending order
* sort="3": Random

== Installation ==

* A plug-in installation screen is displayed in the WordPress admin panel.
* It installs in `wp-content/plugins`.
* The plug-in is activated.
* Register the widget template.
* Add a widget, you specify the registered template.

== Screenshots ==

1. Create an HTML template to be output in the Widget.

2. "Posted Display" has been added to the Widget. Display to select the template you created.

== Changelog ==

= 2.0.7 (2017-06-17) =
* Checked : WordPress version 4.8.0 operation check.

= 2.0.6 (2017-05-18) =
* Checked : WordPress version 4.7.5 operation check.
* Added : Image selection with media uploader.

= 2.0.5 (2017-04-24) =
* Checked : WordPress version 4.7.4 operation check.
* Updated : Ignore post__in if you do not set an ID.

= 2.0.4 (2017-03-10) =
* Checked : WordPress version 4.7.3 operation check.

= 2.0.3 (2017-02-01) =
* Checked : WordPress version 4.7.2 operation check.

= 2.0.2 (2017-01-12) =
* Checked : WordPress version 4.7.1 operation check.

= 2.0.1 (2017-01-02) =
* Fixed : Fixed minor defects and refurbished.

= 2.0.0 (2016-12-12) =
* Added : Custom post widget templates.

= 1.2.3 (2016-12-07) =
* Checked : WordPress version 4.7.0 operation check.
* Fixed : No data widget link missing.

= 1.2.2 (2016-09-14) =
* Checked : WordPress version 4.6.1 operation check.
* Updated : Code Refactor.

= 1.2.1 (2016-08-17) =
* Check : WordPress version 4.6.0 operation check.
* Fixed : setcookie() Warning Error.
* Added : ScreenShots.

= 1.1.4 (2016-06-25) =
* Check : WordPress version 4.5.3 operation check.

= 1.1.3 (2016-05-09) =
* Check : WordPress version 4.5.2 operation check.
* Check : WordPress version 4.5.1 operation check.
* Check : WordPress version 4.5.0 operation check.

= 1.1.2 (2016-03-23) =
* Fixed : Shortcode output bugfix.

= 1.1.1 (2016-03-22) =
* Fixed : Modifications to the writing of the PHP5.3-based support of the array.

= 1.1.0 (2016-03-20) =
* Added : Template item can be inserted in the click in textarea.
* Updated : Code Refactor.

= 1.0.10 (2016-02-03) =
* Check : WordPress version 4.4.2 operation check.

= 1.0.9 (2016-01-10) =
* Fixed : Update typo miss.

= 1.0.8 (2016-01-10) =
* Added : Adding a template item a "author name".
* Check : WordPress version 4.4.1 operation check.

= 1.0.7 (2015-12-17) =
* Added : Plugin images.
* Fixed : Typo miss.

= 1.0.6 (2015-12-11) =
* Added : Adding a template item a "tag" and "category".

= 1.0.5 (2015-12-09) =
* Check : WordPress version 4.4 operation check.

= 1.0.4 (2015-12-06) =
* Renovation : The common functions.

= 1.0.3 (2015-12-03) =
* Fixed : Fixed a minor bug.

= 1.0.2 (2015-11-18) =
* Fixed : Fixed a minor bug.

= 1.0.1 (2015-11-16) =
* The first release.

== Contact ==

* email to foundationmeister[at]outlook.com
* twitter @miiitaka