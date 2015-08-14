<?php
#<-- "posts" GUI -->
add_action('restrict_manage_posts','Creative\restrict_manage_posts_creative_sort');
function restrict_manage_posts_creative_sort() {
	$enabled = get_option('enabled');
	if(!$enabled)
		return;

    $languages = Translate::getLanguageOptions();

	$post_type = get_post_type();
	{
	    $options = get_option("language");
	    $changed = false;

	    if(isset($_GET['sortby'])) {
			$sortby = esc_attr($_GET['sortby']);
			$changed = $options[$post_type] == $sortby;
			$options[$post_type] = $sortby;
		} else {
			$sortby = $options[$post_type];
		}

		update_option("language", $options);

		if($changed) execute_sort();
	}
?>
<form method="post" action="options.php">
    <select id="sortby" name="sortby">
    <option value="none">none</option>
	<?php foreach($languages as $language):
		$is_sort = $language == $sortby;
        $checked_attribute = ($is_sort ? ' selected' : '');
    ?>
        <option value="<?php echo $language; ?>" <?php echo $checked_attribute; ?>><?php echo $language; ?></option>
    <?php endforeach; ?>
    </select>
</form>
<?php
}
#<-- "posts" GUI -->



#<-- WP_Query forced sort -->
function sort_posts_filter($posts) {
	global $wp_query;
	#if not enabled, bail immediately
	$enabled = get_option("enabled");
	if(!$enabled)
		return $posts;

	if(count($posts) == 0)
		return $posts;

	//@hack #assumes that post type is consistent
	$post_type = get_post_type($posts[0]->ID);

	if(!isset(get_option("language")[$post_type]))
		return $posts;

	#get language
	{
		$language = get_option("language")[$post_type];

		if($language == "" || $language == "none")
			return $posts;

		$language  = Translate::getLanguage($language);
		if($language == null)
			return $posts;
	}

	#get sort order
	{
		if( isset($_GET['order']) )
			$order = $_GET['order'];
		else
			$order = "asc";
	}
	
	#get title delimiter: it's what titles are parsed by
	$title_delimiter = get_option("title_delimiter");
	if($title_delimiter == "") $title_delimiter = " "; #white space is trimmed by the options page

	# declare translator
	$intermediary = Translate::getLanguage("english alphabet");
	$translator   = new Translate($language, $intermediary, $title_delimiter);
	
	#sort all posts
	$sorted_posts = Sort::posts($posts, $translator);

	#honor descending order
	if($order == "desc")
		$sorted_posts = array_reverse($sorted_posts);

	return $sorted_posts;
}
add_filter( 'posts_results', 'Creative\sort_posts_filter');
#<-- WP_Query forced sort -->