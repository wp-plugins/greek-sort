=== Greek Sort ===
Contributors: ghcrows13, NickIronGate
Tags: sort, greek, phonetic, fraternity, sorority
Requires at least: 3.0.1
Tested up to: 4.2.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily sort posts by greek phonetics. Useful to sort chapters for fraternity/sorority websites.


== Description ==
Greek Sort can automatically sort posts from a given post type by greek phonetics. Each post type can be configured individually. When enabled, a given post type will only be sorted when a page is loaded or when a post for that post type is added, updated, or trashed. No code required.

A few other languages are available, Spanish and Russian, and more languages can be requested.


== Installation ==
1. Upload the `GreekSort` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the Settings -> Greek Sort in the WordPress sidebar
1. Enable the post types you want and configure them as desired
1. Save your changes!


== Frequently Asked Questions ==


= Do I have to write code? =
No, coding is not required. When a post type is enabled that post type will automatically be sorted.


= Will this slow down my website? =
No. This plugin doesn't run on the front-end of your website.


= When do my posts get sorted? =
Enabled post types will be sorted:

* when a post of an enabled post-type is created, updated, or trashed

* when the "Greek Sort" settings page is loaded


= Does this add anything to my website's database? =
Yes, but not much, and the plugin will clean up when it's deactivated or uninstalled.

Here's what's added:

* A custom field called "greek_sort" is added to posts for enabled post types.

* These custom fields are removed when a post type is disabled.

* All custom fields and settings are erased when the plugin is uninstalled.


= What if I want to write code? =
Here's an example of how to use Greek Sort in your WP_Query:
`<?php
	$args = array(
		'post_type' => 'post',
		'orderby'   => 'greek_sort',
	);
	$the_query = new WP_Query($args);
?>`
or
`<?php
	$args = array(
		'post_type' => 'post',
		'orderby'   => 'title',
	);
	$the_query = new WP_Query($args);
?>`

The previous two examples expand to this WP_Query:
`<?php
	$args = array(
		'post_type' => 'post',
		'meta_key'  => 'greek_sort'
		'orderby'   => 'meta_value_num',
	);
	$the_query = new WP_Query($args);
?>`

NOTE: This means you can't use meta_key or meta_compare with greek_sort. If you need to use meta_compare, please consider using [meta_query](https://codex.wordpress.org/Class_Reference/WP_Meta_Query) instead.


== Screenshots ==
1. This is the Greek Sort options page. All post types can be enabled/disabled, have their column hidden, and their language specified.
2. This is an example posts page. The posts are automatically sorted by your selected language.

== Changelog ==
= 1.0.0 =

* Initial release.


== Upgrade Notice ==
= 1.0.0 =
Initial release.


== Support ==
This plugin is supported by ghcrows13 and NickIronGate. Contact us via the support tab to submit bugs or feature requests.

== Story ==
George (ghcrows13) created this plugin during his internship at Irongate Creative. He's returned to school now, and is busy being a student, but we will continue to provide regular support.