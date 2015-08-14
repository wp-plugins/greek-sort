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
Greek Sort can automatically sort posts from a given post type by greek phonetics. Each post type can be configured individually. When enabled, a given post types will only be sorted when a page is loaded or when a post for that post type is added, updated, or trashed. A few other alphabets are available and more can be requested. No code required.


== Installation ==
1. Upload the `GreekSort` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the "Greek Sort" page in the WordPress menu.
1. Enable the post types you want and configure them as desired.
1. Don't forget to save your changes!


== Frequently Asked Questions ==
= When do my posts get resorted? =
Enabled post types will be resorted when the "Greek Sort" page is loaded and when a post of that post-type is created, updated, or trashed.


= Do I have to write code? =
No. When a post type is enabled that post type will automatically be sorted.


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

This means you can't use meta_key or meta_compare with greek_sort. If you need to use meta_compare, please consider using [meta_query](https://codex.wordpress.org/Class_Reference/WP_Meta_Query) instead.


= Does this add anything to my database? =
Yes, but not very much.

* A custom field called "greek_sort" is added to posts for enabled post types.

* These custom fields are removed when a post type is disabled.

* All custom fields and settings are erased when the plugin is uninstalled.


= Will this slow down my website? =
No. This plugin is light. Itwdoesn't run on the front-end of your website.


== Screenshots ==
1. added before first submission

== Changelog ==
= 1.0.0 =

* Initial release.


== Upgrade Notice ==
= 1.0.0 =
Initial release.


== Support ==
This plugin is supported by ghcrows13 and NickIronGate. Contact us via email to submit bugs or feature requests.

George created this plugin during an internship at Irongate Creative. He's returned to school now, and is busy being a student, but we will continue to provide regular support.