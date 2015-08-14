<?php
/*
	Adds Greek Sort to the "posts" admin page.
	For the given post type, if enabled in the settings, does the following:
		adds a custom column
		makes that column sortable
		outputs the "greek_sort" value for each post in that column
		forces the default sort of the "posts" admin page to Greek Sort
*/

namespace Creative {

#<-- custom column -->
#add our custom column
function columns($columns) {
	global $META_KEY, $NAME;
    $columns[$META_KEY] = $NAME;
    return $columns;
}

#make our custom column sortable
function sortable_columns( $sortable_columns ) {
	global $META_KEY;
	$sortable_columns[$META_KEY] = $META_KEY;
	return $sortable_columns;
}

#called in menu()
#adds greek_sort columns and makes them sortable
add_action('current_screen', "Creative\column_hooks");
function column_hooks() {
	if(!is_admin()) return;

	$used_post_types = getUsedPostTypes();
	foreach($used_post_types as $post_type) {
		column_hook($post_type);
	}
}

#necessary due to WP not having a generic manage_edit_sortable_columns filter
function column_hook($post_type) {
	if(!is_admin()) return;

	$enabled = getSetting($post_type, "enabled");
	if(!$enabled) return;

	$column = getSetting($post_type, "column");
	if(!$column) return;

	// add the column itself
	$hook = "manage_{$post_type}_posts_columns";
	add_filter($hook, 'Creative\columns');

	// make the column sortable column
	$hook = "manage_edit-{$post_type}_sortable_columns";
	add_filter($hook, 'Creative\sortable_columns');
}

#outputs menu_order for each post in our custom column
add_action('manage_posts_custom_column', 'Creative\column_content', 10, 2); #@note #I don't understand these paramters
function column_content($column_name, $post_ID) {
	global $META_KEY;
    if ($column_name == $META_KEY) {
    	echo get_post_meta($post_ID, $META_KEY, true);
    }
}
#<-- custom column -->



#<-- sorting for the admin GUI -->
#forces admin pages to use Greek Sort by default
add_action('current_screen','Creative\set_default_sort');
function set_default_sort() {
	if(!is_admin()) return;

	#get the post_type of the page
	$screen = get_current_screen();
	$post_type = $screen->post_type;

	#get enabled
	$enabled = getSetting($post_type, "enabled");
	if(!$enabled) return;

	#force orderby and order to our defaults
	if(!isset($_GET['orderby'])) {
		global $META_KEY;
		$_GET['orderby'] = $META_KEY;

		if(!isset($_GET['order'])) {
			$_GET['order'] = 'asc';
		}
	}
}

} #namespace

?>