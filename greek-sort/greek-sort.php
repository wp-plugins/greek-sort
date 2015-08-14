<?php
#<!-- namespace -->
namespace Creative {
/*
Plugin Name: Greek Sort
Description: Easily sort posts by greek phonetics. Useful to sort chapters for fraternity/sorority websites.
Version: 0.0.8
Author: Irongate Creative
Author URI: http://irongatecreative.com
License: GPL2

TAGS
	@todo
	@note
	@hack
	@performance
*/

defined( 'ABSPATH' ) or die;



#<!-- CONSTANTS -->
$NAME = "Greek Sort";
$SLUG = "greek-sort";
$META_KEY = "greek_sort";
$SETTINGS = "greek_sort_settings";

$DEFAULT_SETTINGS = array(
	"enabled"   => false,
	"language"  => "greek phonetic",
	"column"    => true,
);
#<!-- CONSTANTS -->



#<!-- INCLUDES -->
require_once("sort.php");
require_once("postui.php");
require_once("page.php");
#<!-- INCLUDES -->



#<-- helper functions -->
# removes all $removed elements from $array permitting they exist
function array_remove($array, $removed) {
	for($i = 0; $i < count($removed); $i++) {
		$index = array_search($removed[$i], array_values($array));
		if($index != -1) array_splice($array, $index, 1);
	}
	return $array;
}
#<-- helper functions -->



#<-- sorting for WP_Query using our custom field -->
add_filter('pre_get_posts', 'Creative\query_filter');
function query_filter($query) {
	global $META_KEY;

	if(is_404()) return;
	//if(!$query->is_main_query) return; #should we use this?

	#check if the caller wants to use a different orderby
	$orderby = $query->get('orderby');
	$defined_orderby = $orderby != '';
	$matches_meta_key = $orderby == 'title' || $orderby == $META_KEY;
	if($defined_orderby && !$matches_meta_key) return;

	#get the post type
	$post_type = $query->get("post_type");
	#var_dump($query);
	//var_dump($post_type);
	if($post_type == '') {
		//$post_type = get_post_type();
		$post_type = "post";
	}

	#make sure sorting is enabled
	$enabled = getSetting($post_type, "enabled");
	if(!$enabled) return;

	#make sure a language is selected
	$language = getSetting($post_type, "language");
	if(!$language) return;

	#change the query to use our custom field
	$query->set("orderby", "meta_value_num");
	$query->set("meta_key", $META_KEY);
	
	#force default order
	$defined_order = $query->get('order') != '';
	if(!$defined_order) {
		$query->set("order", "ASC");
	}
}
#<-- sorting for WP_Query using our custom field -->



#<-- sort using a meta field -->
#executed when a post is published / updated
function sort_on_post_change($post_id) {
	$post_type = get_post_type($post_id);
	sort_post_type($post_type);
}
add_action( 'save_post', 'Creative\sort_on_post_change');

#executed when the options page is loaded #check menu() for the hook
function sort_on_options_load() {
	$post_types = getUsedPostTypes();
	sort_post_types($post_types);
}

function sort_post_types($post_types) {
	foreach($post_types as $post_type) {
		sort_post_type($post_type);
	}
}

function sort_post_type($post_type) {
	global $META_KEY;

	if(!is_admin()) return;

	$enabled = getSetting($post_type, 'enabled');
	if(!$enabled) $enabled = false;

	#get all posts
   	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => -1,
		'orderby' => "meta_value_num",
		'meta_key', $META_KEY,
		'order' => "ASC",
	);
	$posts = get_posts($args);

	#get language
	$language = getSetting($post_type, 'language');
	{
		if(
			!$enabled ||
			!isset($language) || Sort::getLanguage($language) === null
		) {
			removeCustomField($posts);
			return;
		}

	}

	#declare our sort
	$source     = Sort::getLanguage($language);
	$delimiter  = $source["delimiter"];
	$formatter  = function($string) { return mb_strtolower($string); }; //for case insensitive sorts
	$sorter     = new Sort($source, $delimiter, $formatter);

	#sort all posts
	$sort = $source['sort'];
	$sort_function = Sort::getSortFunction($sort);
	$sorted_posts = $sorter->posts($posts, $sort_function);

	#update custom field to reflect the new sort order
	updateCustomField($sorted_posts);
}
#<-- sort using a meta field -->



#<!-- settings -->
#create the menu page
function menu() {
	global $NAME;
	global $SLUG;
	$hook = add_options_page(
		"{$NAME} Options",  # page title
		"{$NAME}",          # menu title
		'administrator',    # user rights
		"{$SLUG}",          # slug
		'Creative\\getPage' # page function
	);
	add_action( 'load-' . $hook , 'Creative\sort_on_options_load' );
}
add_action('admin_menu', 'Creative\menu');

function onActivate() {
	global $SETTINGS;
	$global_options = get_option($SETTINGS);

	if($global_options === null) {
		resetSettings();
	}

	//@hack #this doesn't work and it should
	//sort_on_options_load();
}
register_activation_hook(__FILE__, "Creative\onActivate" );

function onDeactivate() {
	removeCustomFields();
}
register_deactivation_hook(__FILE__, "Creative\onDeactivate" );

function onUninstall() {
	removeSettings();
}
register_uninstall_hook(__FILE__, "Creative\onUninstall");

#register settings on the menu page to the backend wpdb
function registerSettings() {
	global $SETTINGS;
	global $SLUG;
    add_option($SETTINGS, "", "", "yes");
	register_setting($SETTINGS, $SETTINGS);
}
add_action('admin_init', 'Creative\registerSettings');

#this function could be broken apart into multiple parts, but
#it just made my code so much cleaner that it made sense
function getSetting($post_type, $field) {
	global $SETTINGS;
	$global_options = get_option($SETTINGS);
	if(!array_key_exists($post_type, $global_options)) return null;
	$options = $global_options[$post_type];
	if(!array_key_exists($field, $options)) return null;
	$value = $options[$field];
	return $value;
}

function resetSettings() {
	global $SETTINGS;
	delete_option($SETTINGS);
	update_option($SETTINGS, getSettingsDefaults());
}

function removeSettings() {
	global $SETTINGS;
	delete_option($SETTINGS);
	removeCustomFields();
}

function updateCustomField($posts) {
	global $META_KEY;
	for($i = 0; $i < count($posts); $i++) {
		$post = $posts[$i];
		add_post_meta   ($post->ID, $META_KEY, $i, TRUE);
		update_post_meta($post->ID, $META_KEY, $i); //@note #is this necessary?
	}
}

function removeCustomFields() {
	$post_types = getUsedPostTypes();
	foreach($post_types as $post_type) {
		$args = array('post_type' => $post_type, 'posts_per_page' => -1);
		$posts = get_posts($args);
		removeCustomField($posts);
	}
}

function removeCustomField($posts) {
	# remove our custom field from all posts
	global $META_KEY;
	$key = $META_KEY;
	for($i = 0; $i < count($posts); $i++) {
		$post = $posts[$i];
		delete_post_meta($post->ID, $key);
	}
}

function getSettingsDefaults() {
	global $DEFAULT_SETTINGS;
	$defaults = array();

	$used_post_types = getUsedPostTypes();
	foreach($used_post_types as $post_type) {
		$defaults[$post_type] = $DEFAULT_SETTINGS;
	}

	return $defaults;
}

#gets the post types that will be recognized by Greek Sort
function getUsedPostTypes() {
	$all_post_types = get_post_types(array('public' => true));
	$ignored_post_types = array("attachment", "page");
	$used_post_types = array_remove($all_post_types, $ignored_post_types);

	return $used_post_types;
}
#<!-- settings -->

} #<!-- namespace -->